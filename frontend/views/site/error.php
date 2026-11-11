<?php
/**
 * 世界杯赛事信息与球迷交流平台
 * 文件：frontend/views/site/error.php
 * 功能：前台错误页（404 / 500 的中文提示与返回入口）
 * 作者：B（前台页面与用户交互）
 * 日期：2026-11-11
 */

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\helpers\Html;

$this->title = $name;
?>

<div class="site-error">
    <div class="page-header">
        <h1><?= Html::encode($name) ?></h1>
    </div>

    <div class="alert alert-warning">
        <?= nl2br(Html::encode($message)) ?>
    </div>

    <p>
        可能的原因：页面地址输入有误、数据已被管理员删除，或者这篇新闻已经撤回为草稿。
    </p>

    <p>你可以继续浏览：</p>

    <ul>
        <li><?= Html::a('返回首页', ['/site/index']) ?></li>
        <li><?= Html::a('查看比赛赛程', ['/match/index']) ?></li>
        <li><?= Html::a('查看球队信息', ['/team/index']) ?></li>
        <li><?= Html::a('阅读新闻资讯', ['/news/index']) ?></li>
        <li><?= Html::a('去球迷留言板', ['/message/index']) ?></li>
    </ul>

    <p style="margin-top: 18px;">
        <?= Html::a('返回首页', ['/site/index'], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('新闻资讯', ['/news/index'], ['class' => 'btn btn-default']) ?>
        <?= Html::a('球迷留言', ['/message/index'], ['class' => 'btn btn-default']) ?>
    </p>
</div>
