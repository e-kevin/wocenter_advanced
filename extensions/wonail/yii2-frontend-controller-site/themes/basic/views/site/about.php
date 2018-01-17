<?php

/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = '简介';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-about">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        WoCenter Advanced 是基于
        <a href="https://github.com/Wonail/wocenter" target="_blank">WoCenter</a> 开发的一款优秀的<strong>高度可扩展</strong>的高级项目模板。
    </p>
    <blockquote>
        WoCenter Advanced 充分发挥了WoCenter的所有特性，基于WoCenter的<strong>扩展机制</strong>，
        系统核心模块和默认主题均使用扩展中心提供的扩展插件来满足系统的高定制化需求。你可以根据需要自由开发私有或公有的扩展，
        也可以使用其他开发者的扩展来定制你的业务系统，避免重复造轮子，开箱即用。
    </blockquote>

    <p>
        WoCenter Advanced 默认提供了一套集成扩展中心、系统管理、菜单管理等多个模块的后台管理系统，遵循
        <a href="https://github.com/Wonail/wocenter_advanced/blob/master/LICENSE" target="_blank">BSD-3-Clause协议</a>，
        意味着你可以免费的部署你的线上项目。
    </p>
    <p>
        系统默认使用AdminLTE主题， 系统核心模块功能很大程度上可以满足你的基本所需，避免重复造轮子，开箱即用。
    </p>

    <h2>推荐扩展</h2>
    <ul>
        <li>用户管理模块：wonail/yii2-module-account</li>
        <li>WoCenter Gii模块：wonail/yii2-module-gii</li>
        <li>系统通知模块：wonail/yii2-module-notification</li>
        <li>运营管理模块：wonail/yii2-module-operate</li>
        <li>通行证管理模块：wonail/yii2-module-passport</li>
        <li>安全管理模块：wonail/yii2-module-security</li>
        <li>后台管理员功能扩展：wonail/yii2-controller-admin</li>
    </ul>

    <blockquote>
        <p> 我们都在成长，WoCenter感谢一路有你</p>
    </blockquote>
</div>