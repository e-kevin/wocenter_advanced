<?php

use wocenter\backend\modules\extension\models\Module;
use wonail\adminlte\grid\GridView;
use yii\data\ArrayDataProvider;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider ArrayDataProvider */
/* @var $runModuleList array */
/* @var $app string */

$headerToolbar = '';
// full_page:START
if ($this->context->isFullPageLoad()) {
    $this->title = '模块管理';
    $this->params['breadcrumbs'][] = $this->title;
    $this->params['navSelectPage'] = '/extension/module/index';
}
// full_page:END
?>

<div class="nav-tabs-custom">
    <ul class="nav nav-tabs">
        <?php
        foreach (Yii::$app->params['appList'] as $k => $name) {
            if ($k == $app) {
                echo Html::tag('li', Html::a($name, "#{$k}", ['data-toggle' => 'tab']), [
                    'class' => 'active',
                ]);
            } else {
                echo Html::tag('li', Html::a($name, ['index', 'app' => $k], ['data-pjax' => 1]));
            }
        }
        ?>
        <li class="pull-right">
            <?= Html::a('清理缓存', ['clear-cache', 'app' => $app], [
                'class' => 'text-yellow',
                'data-method' => 'post',
            ]) ?>
        </li>
    </ul>
    <div class="tab-content">
        <div class="active tab-pane" id="avatar">
            <?php
            $column = [
                [
                    'label' => '扩展名',
                    'format' => 'raw',
                    'value' => function ($model, $key) use ($app) {
                        return $model['infoInstance']->canInstall
                            ? $key
                            : Html::a($key, ['update', 'id' => $key, 'app' => $app], ['data-pjax' => 1]);
                    },
                ],
                [
                    'attribute' => 'name',
                    'format' => 'html',
                    'value' => function ($model) {
                        if ($model['infoInstance']->isSystem) {
                            return '<span class="text-danger">' . $model['infoInstance']->name . '</span>';
                        } else {
                            if ($model['infoInstance']->canInstall) {
                                return '<span class="text-success">' . $model['infoInstance']->name . '</span>';
                            } else {
                                return '<span class="text-warning">' . $model['infoInstance']->name . '</span>';
                            }
                        }
                    },
                    'label' => '名称',
                ],
                [
                    'attribute' => 'description',
                    'value' => function ($model) {
                        return $model['infoInstance']->description;
                    },
                    'label' => '描述',
                ],
                [
                    'class' => 'kartik\grid\BooleanColumn',
                    'value' => function ($model) {
                        return $model['infoInstance']->isSystem;
                    },
                    'label' => '系统扩展',
                ],
                [
                    'format' => 'html',
                    'label' => '运行模块',
                    'value' => function ($model) use ($runModuleList) {
                        switch ($model['run']) {
                            case Module::RUN_MODULE_EXTENSION:
                                return '<span class="text-danger">' . $runModuleList[$model['run']] . '</span>';
                                break;
                            case Module::RUN_MODULE_DEVELOPER:
                                return '<span class="text-warning">' . $runModuleList[$model['run']] . '</span>';
                                break;
                            default:
                                return '未安装';
                        }
                    },
                ],
                [
                    'attribute' => 'author',
                    'value' => function ($model) {
                        return $model['infoInstance']->developer;
                    },
                    'label' => '开发者',
                ],
                [
                    'attribute' => 'version',
                    'value' => function ($model) {
                        return $model['infoInstance']->version;
                    },
                    'label' => '版本',
                ],
                [
                    'class' => \wonail\adminlte\grid\ActionColumn::className(),
                    'template' => '{install} {uninstall} {tips}',
                    'visibleButtons' => [
                        'install' => function ($model, $key, $index) {
                            return $model['infoInstance']->canInstall;
                        },
                        'uninstall' => function ($model, $key, $index) {
                            return $model['infoInstance']->canUninstall;
                        },
                        'tips' => function ($model, $key, $index) {
                            return !$model['infoInstance']->canInstall && !$model['infoInstance']->canUninstall;
                        },
                    ],
                    'buttons' => [
                        'install' => function ($url, $model, $key) use ($app) {
                            return Html::a('安装', ['install', 'id' => $key, 'app' => $app], ['data-pjax' => 1]);
                        },
                        'uninstall' => function ($url, $model, $key) use ($app) {
                            return Html::a('卸载', ['uninstall', 'id' => $key, 'app' => $app], [
                                'data-method' => 'post',
                                'data-confirm' => '确定要卸载所选模块吗？'
                            ]);
                        },
                        'tips' => function ($url, $model, $key) {
                            return 'N/A';
                        },
                    ],
                ],
            ];

            echo GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => $column,
                'layout' => "{items}\n{pager}",
            ]);
            ?>
        </div>
        <!-- /.tab-pane -->
    </div>
    <!-- /.tab-content -->
</div>