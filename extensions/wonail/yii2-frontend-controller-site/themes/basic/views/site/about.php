<?php

/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = '简介';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-about">
    <h1><?= Html::encode($this->title) ?></h1>

    <blockquote>
        <p>
            WoCenter是基于php Yii2开发的一款优秀的<strong>高度模块化</strong>的框架，系统在设计之初就非常重视二次开发的
            <strong>便捷性</strong>、<strong>易用性</strong>和<strong>低干扰性</strong>。
            在保留Yii2原有操作习惯的基础上，扩展出对二次开发更加友好的<strong>调度层（Dispatch）</strong>和<strong>服务层（Service）</strong>，
            <strong>而易于扩展和重构的系统设计有助于你定制出称心如意的项目</strong>。
        </p>
    </blockquote>
    <p>
        WoCenter目前提供了一套集成用户管理、安全管理、扩展中心、系统管理、运营管理等多个模块的后台管理系统，遵循[BSD-3-Clause协议]，
        意味着你可以免费的部署你的线上项目。
    </p>
    <p>
        系统默认使用AdminLTE主题， 系统核心模块功能很大程度上可以满足你的基本所需，避免重复造轮子，开箱即用。
    </p>

    <h2>加入WoCenter</h2>
    <p>WoCenter目前的定位是为了让程序员们有一个方便学习、扩展和开发的框架系统。</p>
    <p>你有以下两种方式加入到WoCenter中来，为广大开发者提供更加优质的免费开源的服务：</p>
    <ul>
        <li>贡献代码：WoCenter的核心代码位于<a href="https://github.com/Wonail/wocenter">Wonail/wocenter</a>，你可以提交PR，
            包括但不限于可以优化或调整的代码或组织结构、任何bug、安全或性能问题。</li>
        <li>翻译或补充文档：WoCenter的文档在<a href="https://github.com/Wonail/wocenter_doc">Wonail/wocenter_doc</a>，
            你可以选择补充文档或者参与英文文档的翻译。</li>
    </ul>
    <blockquote>
        <p>  我们都在成长，WoCenter感谢一路有你</p>
    </blockquote>
</div>