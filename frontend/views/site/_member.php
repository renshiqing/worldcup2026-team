<?php
/**
 * 世界杯赛事信息与球迷交流平台
 * 文件：frontend/views/site/_member.php
 * 功能：团队成员卡片（首页与团队介绍页共用）
 * 作者：B（前台页面与用户交互）
 * 日期：2026-11-11
 */

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $member common\models\TeamMember */

$initial = mb_substr(trim((string) $member->name), 0, 1, 'UTF-8');
?>

<div class="wc-member-card">
    <div class="wc-avatar"><?= Html::encode($initial !== '' ? $initial : '队') ?></div>

    <h4 class="wc-member-name"><?= Html::encode($member->name) ?></h4>

    <p class="wc-member-role"><?= Html::encode($member->role ?: '团队成员') ?></p>

    <?php if (!empty($member->student_id)): ?>
        <p class="wc-meta">学号：<?= Html::encode($member->student_id) ?></p>
    <?php endif; ?>

    <?php if (!empty($member->bio)): ?>
        <p class="wc-member-bio"><?= Html::encode($member->bio) ?></p>
    <?php endif; ?>
</div>
