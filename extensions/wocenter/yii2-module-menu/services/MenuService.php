<?php

namespace wocenter\backend\modules\menu\services;

use wocenter\backend\modules\menu\models\Menu;
use wocenter\core\FunctionInfo;
use wocenter\core\ModularityInfo;
use wocenter\core\Service;
use wocenter\Wc;
use wocenter\helpers\ArrayHelper;
use Yii;

/**
 * 菜单服务类
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class MenuService extends Service
{
    
    /**
     * @var string|array|callable|Menu 菜单类
     */
    public $menuModel = '\wocenter\backend\modules\menu\models\Menu';
    
    /**
     * @var integer|false 缓存时间间隔。当为`false`时，则删除缓存数据，默认缓存`一天`
     */
    public $cacheDuration = 86400;
    
    /**
     * @inheritdoc
     */
    public function getId()
    {
        return 'menu';
    }
    
    /**
     * 根据查询条件获取指定（单个或多个）分类的菜单数据
     *
     * @param string|array $category 分类ID
     * @param array $condition 查询条件
     *
     * @return array 如果$category分类ID为字符串，则返回该分类的一维数组，否则返回二维数组['backend' => [], 'frontend' => [], 'main' => []]
     */
    public function getMenus($category = '', array $condition = [])
    {
        $menus = $this->getMenusByCategoryWithFilter($category, $condition, false);
        
        return is_string($category) ? ($menus[$category] ?? []) : $menus;
    }
    
    /**
     * 根据查询条件和$filterCategory指标 [获取|不获取] 指定分类（单个或多个）的菜单数据
     *
     * @param string|array $category 分类ID
     * @param array $condition 查询条件
     * @param bool $filterCategory 过滤指定$category分类的菜单，默认：不过滤
     *
     * @return array ['backend' => [], 'frontend' => [], 'main' => []]
     */
    public function getMenusByCategoryWithFilter($category = '', array $condition = [], $filterCategory = false)
    {
        /** @var Menu $menuModel */
        $menuModel = Yii::createObject($this->menuModel);
        $menus = ArrayHelper::listSearch(
            $menuModel->getAll($this->cacheDuration),
            array_merge([
                'category_id' => [
                    $filterCategory ?
                        (is_array($category) ? 'not in' : 'neq') :
                        (is_array($category) ? 'in' : 'eq'),
                    $category,
                ],
            ], $condition)
        );
        
        return $menus ? ArrayHelper::index($menus, 'id', 'category_id') : [];
    }
    
    /**
     * 获取所有已经安装的扩展菜单配置数据
     *
     * @return array
     * [
     *  {app} => []
     * ]
     */
    private function getAllInstalledMenus()
    {
        $arr = [];
        foreach (Wc::$service->getExtension()->getLoad()->getAllConfig(true) as $app => $item) {
            foreach ($item as $type => $row) {
                if (in_array($type, ['controllers', 'modules'])) {
                    unset($row['config']);
                    foreach ($row as $uniqueName => $config) {
                        /* @var $infoInstance ModularityInfo|FunctionInfo */
                        $infoInstance = $config['infoInstance'];
                        $arr[$infoInstance->app] = ArrayHelper::merge(
                            $arr[$infoInstance->app] ?? [],
                            $this->formatMenuConfig($infoInstance->getMenus(), $infoInstance->app)
                        );
                    }
                }
            }
        }
        
        return $arr;
    }
    
    /**
     * 同步扩展菜单项
     *
     * @return bool
     */
    public function syncMenus()
    {
        // 获取所有应用扩展菜单项
        $menus = $this->getAllInstalledMenus();
        // 不获取缓存中的菜单项
        $this->cacheDuration = false;
        /** @var Menu $menuModel */
        $menuModel = $this->menuModel;
        // 获取数据库里的所有菜单数据，不包括用户自建数据
        $menuInDatabase = $this->getMenus(array_keys(Yii::$app->params['appList']), [
            'created_type' => $menuModel::CREATE_TYPE_BY_EXTENSION,
        ]);
        $updateDbMenus = [];
        foreach ($menus as $app => $item) {
            $updateDbMenus = ArrayHelper::merge($updateDbMenus, $this->_convertMenuDataInDb($item, 0, $menuInDatabase[$app]));
        }
        $this->_fixMenuData($menuInDatabase, $updateDbMenus);
        // 操作数据库
        $this->_updateMenus($updateDbMenus);
        
        return true;
    }
    
    /**
     * 转换菜单数据，用以插入数据库
     *
     * @param array $menus 需要转换的菜单配置信息
     * @param integer $parentId 数组父级ID
     * @param array &$menuInDatabase 数据库里原有的菜单数据
     *
     * @return array ['create', 'update']
     * @throws \yii\db\Exception
     */
    protected function _convertMenuDataInDb(array $menus, $parentId = 0, &$menuInDatabase = [])
    {
        if (empty($menus)) {
            return [];
        }
        $arr = [];
        /** @var Menu $menuModel */
        $menuModel = $this->menuModel;
        foreach ($menus as $row) {
            // 排除没有设置归属模块的数据以及中断该数据的子数据
            // todo 改为系统日志记录该错误或抛出系统异常便于更正?
            if (empty($row['modularity'])) {
                continue;
            }
            
            $items = ArrayHelper::remove($row, 'items', []);
            $row['parent_id'] = $parentId; // 添加菜单父级ID
            $arr['menuConfig'][] = $row;
            $condition = [
                'category_id' => $row['category_id'],
                'name' => $row['name'],
                'modularity' => $row['modularity'],
                'url' => $row['url'],
                'parent_id' => $row['parent_id'],
            ];
            
            if (!empty($items)) {
                // 数据库里存在数据
                if (($data = ArrayHelper::listSearch($menuInDatabase, $condition, true))) {
                    $row['id'] = $data[0]['id'];
                    // 检测数据是否改变
                    foreach ($row as $key => $value) {
                        if ($data[0][$key] != $value) {
                            $arr['update'][$row['id']][$key] = $value;
                        }
                    }
                } else {
                    // 不存在父级菜单则递归新建父级菜单
                    if (Yii::$app->getDb()->createCommand()->insert($menuModel::tableName(), $row)->execute()) {
                        $row['id'] = $menuModel::find()->select('id')->where($row)->scalar();
                        // 同步更新数据库已有数据
                        $menuInDatabase[] = $row;
                    }
                }
                $arr = ArrayHelper::merge($arr, $this->_convertMenuDataInDb($items, $row['id'], $menuInDatabase));
            } else {
                // 数据库里存在数据
                if (
                    ($data = ArrayHelper::listSearch($menuInDatabase, $condition, true))
                    // 最底层菜单可以修改`name`字段
                    || ($data = ArrayHelper::listSearch($menuInDatabase, [
                        'category_id' => $row['category_id'],
                        'modularity' => $row['modularity'],
                        'url' => $row['url'],
                        'parent_id' => $row['parent_id'],
                    ], true))
                ) {
                    $row['id'] = $data[0]['id'];
                    // 检测数据是否改变
                    foreach ($row as $key => $value) {
                        if ($data[0][$key] != $value) {
                            $arr['update'][$row['id']][$key] = $value;
                        }
                    }
                } else {
                    // 不存在子类菜单则列入待新建数组里
                    // 排序，保持键序一致，便于批量插入数据库
                    ksort($row);
                    $arr['create'][] = $row;
                    // 同步更新数据库已有数据
                    $menuInDatabase[] = $row;
                }
            }
        }
        
        return $arr;
    }
    
    /**
     * 对比数据库已有数据，修正待写入数据库的菜单数据
     *
     * @param array $menuInDatabase 数据库里的菜单数据
     * @param array $arr 待处理数组 ['create', 'update']
     */
    private function _fixMenuData($menuInDatabase = [], &$arr = [])
    {
        foreach ($menuInDatabase as $app => $items) {
            if ($items) {
                foreach ($items as $row) {
                    // 配置数据里已删除，则删除数据库对应数据
                    if (
                        isset($arr['menuConfig'])
                        && !ArrayHelper::listSearch($arr['menuConfig'], [
                            'category_id' => $row['category_id'],
                            'name' => $row['name'],
                            'modularity' => $row['modularity'],
                            'url' => $row['url'],
                        ], true)
                        && (!key_exists($row['id'], $arr['update'] ?? []))
                    ) {
                        $arr['delete'][$row['id']] = $row['id'];
                    }
                }
            }
        }
    }
    
    /**
     * 执行所有菜单操作
     *
     * @param array $array 需要操作的数据 ['delete', 'create', 'update']
     */
    private function _updateMenus($array)
    {
        /** @var Menu $menuModel */
        $menuModel = $this->menuModel;
        if (!empty($array['delete'])) {
            Yii::$app->getDb()->createCommand()->delete($menuModel::tableName(), ['id' => $array['delete']])
                ->execute();
        }
        if (!empty($array['create'])) {
            Yii::$app->getDb()->createCommand()->batchInsert($menuModel::tableName(), array_keys($array['create'][0]), $array['create'])
                ->execute();
        }
        if (!empty($array['update'])) {
            foreach ($array['update'] as $id => $row) {
                Yii::$app->getDb()->createCommand()->update($menuModel::tableName(), $row, ['id' => $id])
                    ->execute();
            }
        }
        // 删除菜单缓存
        $this->clearCache();
    }
    
    /**
     * 删除缓存
     */
    public function clearCache()
    {
        /** @var Menu $menuModel */
        $menuModel = Yii::createObject($this->menuModel);
        $menuModel->clearCache();
    }
    
    /**
     * 格式化菜单配置数据
     * 1. 把键值`name`转换成键名，方便使用\yii\helpers\ArrayHelper::merge合并相同键名的数组到同一分组下
     * 2. 补全修正菜单数组。可用字段必须存在于$this->menuModel数据表里
     *
     * @param array $menus 菜单数据
     * @param string $app 所属应用ID，默认为`null`，该值则为当前应用ID（Yii::$app->id）
     *
     * @return array
     */
    public function formatMenuConfig($menus, $app = null)
    {
        if (empty($menus)) {
            return [];
        }
        $arr = [];
        foreach ($menus as $key => $menu) {
            $key = $menu['name'] ?? $key;
            $this->_initMenuConfig($menu, $app);
            $arr[$key] = $menu;
            if (isset($menu['items'])) {
                $arr[$key]['items'] = $this->formatMenuConfig($menu['items'], $app);
            }
        }
        unset($menus);
        
        return $arr;
    }
    
    /**
     * 初始化菜单配置数据，用于补全修正菜单数组。可用字段必须存在于$this->menuModel数据表里
     *
     * @param array $menu
     * @param string $app
     */
    private function _initMenuConfig(&$menu = [], $app)
    {
        $menu['category_id'] = $menu['category_id'] ?? ($app ?: Yii::$app->id);
        $menu['url'] = $menu['url'] ?? 'javascript:;';
        $menu['params'] = isset($menu['params']) ? serialize($menu['params']) : '';
        // 模块ID
        if (!isset($menu['modularity']) && $menu['url'] != 'javascript:;') {
            preg_match('/\w+/', $menu['url'], $modularity);
            $menu['modularity'] = $modularity[0];
        }
        $menu['created_type'] = $menu['created_type'] ?? Menu::CREATE_TYPE_BY_EXTENSION;
        $menu['show_on_menu'] = isset($menu['show_on_menu']) ? 1 : 0;
        $menu['alias_name'] = $menu['alias_name'] ?? $menu['name'];
        $menu['sort_order'] = $menu['sort_order'] ?? 0;
        // 需要补全的字段
        $fields = ['icon_html', 'description'];
        foreach ($fields as $field) {
            if (!isset($menu[$field])) {
                $menu[$field] = '';
            }
        }
    }
    
}
