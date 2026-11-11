<?php
/**
 * 世界杯赛事信息与球迷交流平台
 * 文件：frontend/views/message/success.php
 * 功能：留言提交成功页（显示刚提交的内容和审核状态）
 * 作者：B（前台页面与用户交互）
 * 日期：2026-11-11
 */

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $message common\models\FanMessage */

$this->title = '留言提交成功';
$this->params['breadcrumbs'][] = ['label' => '球迷留言', 'url' => ['/message/index']];
$this->params['breadcrumbs'][] = $this->title;

$createdAt = empty($message->created_at) ? '—' : date('Y-m-d H:i', (int) $message->created_at);
?>

<div class="wc-message-success">
    <div class="wc-card text-center wc-success-card">
        <div class="wc-success-icon">
            <span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
        </div>

        <h1>留言提交成功</h1>

        <p class="lead">感谢你的留言，我们已经收到。</p>

        <div class="alert alert-info text-left">
            留言需要管理员审核通过后才会出现在留言墙。
            你可以在“我的留言”中随时查看审核状态和管理员回复。
        </div>

        <table class="table table-condensed wc-success-table">
            <tbody>
                <tr>
                    <th>留言编号</th>
                    <td>#<?= (int) $message->id ?></td>
                </tr>
                <tr>
                    <th>昵称</th>
                    <td><?= Html::encode($message->nickname) ?></td>
                </tr>
                <?php if (!empty($message->subject)): ?>
                    <tr>
                        <th>留言主题</th>
                        <td><?= Html::encode($message->subject) ?></td>
                    </tr>
                <?php endif; ?>
                <tr>
                    <th>提交时间</th>
                    <td><?= Html::encode($createdAt) ?></td>
                </tr>
                <tr>
                    <th>处理状态</th>
                    <td>
                        <span class="label <?= Html::encode($message->getStatusBadge()) ?>">
                            <?= Html::encode($message->getStatusLabel()) ?>
                        </span>
                    </td>
                </tr>
            </tbody>
        </table>

        <p class="wc-success-actions">
            <?= Html::a('查看我的留言', ['/message/mine'], ['class' => 'btn btn-primary btn-lg']) ?>
            <?= Html::a('返回留言板', ['/message/index'], ['class' => 'btn btn-default']) ?>
            <?= Html::a('返回首页', ['/site/index'], ['class' => 'btn btn-link']) ?>
        </p>
    </div>
</div>
