<?php
/**
 * 世界杯赛事信息与球迷交流平台
 * 文件：frontend/views/message/mine.php
 * 功能：我的留言页（按会话显示自己提交的留言及审核状态）
 * 作者：B（前台页面与用户交互）
 * 日期：2026-11-11
 */

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $messages common\models\FanMessage[] */

$this->title = '我的留言';
$this->params['breadcrumbs'][] = ['label' => '球迷留言', 'url' => ['/message/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="wc-message-mine">
    <div class="page-header">
        <h1>
            我的留言
            <small>查看审核状态与管理员回复</small>
        </h1>
    </div>

    <p class="text-muted">
        本页只显示当前浏览器提交过的留言（最多保留最近 20 条），不会显示其他球迷的留言。
        管理员在后台审核后，刷新本页即可看到状态变化和回复内容。
    </p>

    <?php if (empty($messages)): ?>
        <div class="wc-empty">
            <p><strong>你还没有提交过留言。</strong></p>
            <p>留言不需要登录，提交后可以在这里跟踪审核状态。</p>
            <p><?= Html::a('去留言板写一条', ['/message/index'], ['class' => 'btn btn-success']) ?></p>
        </div>
    <?php else: ?>
        <?php foreach ($messages as $message): ?>
            <?= $this->render('_item', ['message' => $message]) ?>
        <?php endforeach; ?>

        <p class="text-muted small">
            “待审核”表示后台还没有处理；“已通过”的留言会出现在留言墙上；
            “已拒绝”的留言不会公开展示。
        </p>
        <p>
            <?= Html::a('继续留言', ['/message/index'], ['class' => 'btn btn-success']) ?>
            <?= Html::a('返回首页', ['/site/index'], ['class' => 'btn btn-default']) ?>
        </p>
    <?php endif; ?>
</div>
