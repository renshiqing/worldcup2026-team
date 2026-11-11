<?php
/**
 * 世界杯赛事信息与球迷交流平台
 * 文件：frontend/views/message/_item.php
 * 功能：单条留言卡片（留言墙与“我的留言”共用）
 * 作者：B（前台页面与用户交互）
 * 日期：2026-11-11
 */

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $message common\models\FanMessage */

$initial = mb_substr(trim((string) $message->nickname), 0, 1, 'UTF-8');
$createdAt = empty($message->created_at) ? '—' : date('Y-m-d H:i', (int) $message->created_at);
$statusLabel = $message->getStatusLabel();
$statusBadge = $message->getStatusBadge();
?>

<div class="wc-message-card">
    <div class="wc-message-head">
        <span class="wc-avatar wc-avatar-sm"><?= Html::encode($initial !== '' ? $initial : '球') ?></span>

        <div class="wc-message-meta">
            <strong><?= Html::encode($message->nickname) ?></strong>
            <span class="wc-meta"><?= Html::encode($createdAt) ?></span>
            <span class="label <?= Html::encode($statusBadge) ?>"><?= Html::encode($statusLabel) ?></span>
        </div>
    </div>

    <?php if (!empty($message->subject)): ?>
        <p class="wc-message-subject"><?= Html::encode($message->subject) ?></p>
    <?php endif; ?>

    <p class="wc-message-body"><?= nl2br(Html::encode($message->message)) ?></p>

    <?php if (!empty($message->admin_reply)): ?>
        <div class="wc-message-reply">
            <p class="wc-reply-title">管理员回复</p>
            <p><?= nl2br(Html::encode($message->admin_reply)) ?></p>

            <?php if (!empty($message->replied_at)): ?>
                <p class="wc-meta">回复时间：<?= Html::encode(date('Y-m-d H:i', (int) $message->replied_at)) ?></p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
