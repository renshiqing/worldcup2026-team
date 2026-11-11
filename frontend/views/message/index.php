<?php
/**
 * 世界杯赛事信息与球迷交流平台
 * 文件：frontend/views/message/index.php
 * 功能：球迷留言板（留言表单 + 已通过审核的留言墙）
 * 作者：B（前台页面与用户交互）
 * 日期：2026-11-11
 */

use frontend\models\FanMessageForm;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\FanMessageForm */
/* @var $messages common\models\FanMessage[] */

$this->title = '球迷留言';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="wc-message-index">
    <div class="page-header">
        <h1>
            球迷留言
            <small>聊聊你期待的 2026 世界杯</small>
        </h1>
    </div>

    <div class="row">
        <div class="col-md-7">
            <div class="wc-card">
                <h2 class="wc-section-title">我要留言</h2>

                <p class="text-muted">
                    留言不需要登录。提交后由管理员审核，
                    审核通过的留言会显示在右侧留言墙；
                    含链接或广告内容的留言可能被拒绝。
                </p>

                <?php $form = ActiveForm::begin([
                    'id' => 'fan-message-form',
                    'action' => ['/message/create'],
                    'method' => 'post',
                    'options' => ['class' => 'wc-form'],
                ]); ?>

                <?= $form->field($model, 'nickname')->textInput([
                    'maxlength' => 80,
                    'placeholder' => '例如：北看台球迷',
                    'autocomplete' => 'nickname',
                ]) ?>

                <?= $form->field($model, 'email')->input('email', [
                    'maxlength' => 120,
                    'placeholder' => '选填，方便管理员回复你',
                ]) ?>

                <?= $form->field($model, 'subject')->textInput([
                    'maxlength' => 200,
                    'placeholder' => '选填，例如：希望增加按日期筛选赛程',
                ]) ?>

                <?= $form->field($model, 'message')->textarea([
                    'rows' => 6,
                    'maxlength' => FanMessageForm::MESSAGE_MAX_LENGTH,
                    'placeholder' => '请文明留言，' . FanMessageForm::MESSAGE_MAX_LENGTH . ' 字以内……',
                ])->hint('至少 5 个字，提交后状态为“待审核”。') ?>

                <div class="wc-honeypot" aria-hidden="true">
                    <?= $form->field($model, 'website')->textInput([
                        'tabindex' => -1,
                        'autocomplete' => 'off',
                    ])->label(false) ?>
                </div>

                <div class="form-group">
                    <?= Html::submitButton(
                        '<span class="glyphicon glyphicon-send"></span> 提交留言',
                        ['class' => 'btn btn-success btn-lg', 'encode' => false]
                    ) ?>
                    <?= Html::a('查看我提交的留言', ['/message/mine'], ['class' => 'btn btn-link']) ?>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>

        <div class="col-md-5">
            <h2 class="wc-section-title">留言墙</h2>

            <p class="text-muted">
                仅显示管理员审核通过的留言和回复。
            </p>

            <?php if (empty($messages)): ?>
                <div class="wc-empty">
                    <p><strong>留言墙还是空的。</strong></p>
                    <p>成为第一个留言的球迷吧。</p>
                </div>
            <?php else: ?>
                <?php foreach ($messages as $message): ?>
                    <?= $this->render('_item', ['message' => $message]) ?>
                <?php endforeach; ?>

                <p class="text-muted small">
                    最多显示最近 <?= (int) FanMessageForm::WALL_LIMIT ?> 条已通过审核的留言。
                </p>
            <?php endif; ?>

            <p>
                <?= Html::a('查看我提交的留言 →', ['/message/mine'], [
                    'class' => 'btn btn-default btn-block',
                    'encode' => false,
                ]) ?>
            </p>
        </div>
    </div>
</div>
