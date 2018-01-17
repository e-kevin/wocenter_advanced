<?php
use wocenter\helpers\ArrayHelper;
use wocenter\Wc;

$sidebarMenus = Wc::$service->getMenu()->getMenus('backend', [
    ['modularity' => ['not in', Wc::$service->getExtension()->getModularity()->getUninstalledModuleId()]],
    'show_on_menu' => 1,
    'status' => 1,
]);
$sidebarMenus = ArrayHelper::listToTree($sidebarMenus);
?>
<aside class="main-sidebar">

    <section class="sidebar">

        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="<?= $directoryAsset ?>/img/user2-160x160.jpg" class="img-circle" alt="User Image"/>
            </div>
            <div class="pull-left info">
                <p>
                <?php
                    if (Yii::$app->hasModule('passport')) {
                        echo Yii::$app->getUser()->getIdentity()->username;
                    } else {
                        echo 'N/A';
                    }
                ?>
                </p>

                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>

        <!-- search form -->
        <form action="#" method="get" class="sidebar-form">
            <div class="input-group">
                <input type="text" name="q" class="form-control" placeholder="Search..."/>
              <span class="input-group-btn">
                <button type='submit' name='search' id='search-btn' class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
            </div>
        </form>
        <!-- /.search form -->

        <?= wocenter\backend\themes\adminlte\widgets\Menu::widget(
            [
                'options' => ['class' => 'sidebar-menu'],
                'items' => array_merge([
                    ['name' => 'NAVIGATION', 'options' => ['class' => 'header']],
                    ['name' => '首页', 'url' => ['/site/index'], 'icon_html' => 'dashboard'],
                ], $sidebarMenus),
            ]
        ) ?>

    </section>

</aside>