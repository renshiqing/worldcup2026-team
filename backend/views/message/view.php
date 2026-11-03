<?php
/**
 * 世界杯赛事信息与球迷交流平台
 * 文件：backend/views/message/view.php
 * 功能：留言管理详情页
 * 作者：C（后台管理与统计）
 * 日期：2026-11-01
 */

/* @var $this yii\web\View */
/* @var $model common\models\FanMessage */
/* @var $statusOptions array */

use common\models\FanMessage;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = '留言：' . $model->nickname;
$this->params['breadcrumbs'][] = ['label' => '留言管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="message-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('返回列表', ['index'], ['class' => 'btn btn-default']) ?>
        <?= Html::a('删除留言', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data-confirm' => '确定删除这条留言吗？',
            'data-method' => 'post',
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'nickname',
            [
                'attribute' => 'email',
                'value' => $model->email === null || $model->email === '' ? '—' : $model->email,
            ],
            [
                'attribute' => 'subject',
                'value' => $model->subject === null || $model->subject === '' ? '（无主题）' : $model->subject,
            ],
            [
                'attribute' => 'status',
                'format' => 'raw',
                'value' => Html::tag('span', Html::encode($model->statusLabel), ['class' => 'label ' . $model->statusBadge]),
            ],
            [
                'attribute' => 'created_at',
                'format' => 'datetime',
            ],
            [
                'attribute' => 'replied_at',
                'label' => '回复时间',
                'value' => empty($model->replied_at) ? '—' : date('Y-m-d H:i', (int) $model->replied_at),
            ],
        ],
    ]) ?>

    <div class="panel panel-default">
        <div class="panel-heading">留言内容</div>
        <div class="panel-body">
            <?= nl2br(Html::encode($model->message)) ?>
        </div>
    </div>

    <div class="panel panel-primary">
        <div class="panel-heading">审核与回复</div>
        <div class="panel-body">

            <?php if (!empty($model->admin_reply)): ?>
                <div class="alert alert-info">
                    <strong>当前回复：</strong><br>
                    <?= nl2br(Html::encode($model->admin_reply)) ?>
                </div>
            <?php endif; ?>

            <?php $form = ActiveForm::begin([
                'action' => ['update', 'id' => $model->id],
                'method' => 'post',
            ]); ?>

            <?= $form->field($model, 'status')->dropDownList($statusOptions) ?>

            <?= $form->field($model, 'admin_reply')->textarea([
                'rows' => 5,
                'placeholder' => '填写回复内容后保存；状态为“待审核”时会自动改为“已通过”。',
            ]) ?>

            <div class="form-group">
                <?= Html::submitButton('保存处理结果', ['class' => 'btn btn-success']) ?>
            </div>

            <?php ActiveForm::end(); ?>

            <hr>

            <p class="text-muted">快捷操作：</p>
            <?= Html::beginForm(['status', 'id' => $model->id, 'status' => FanMessage::STATUS_APPROVED], 'post', ['class' => 'inline-block']) ?>
            <?= Html::submitButton('通过审核', ['class' => 'btn btn-success']) ?>
            <?= Html::endForm() ?>

            <?= Html::beginForm(['status', 'id' => $model->id, 'status' => FanMessage::STATUS_REJECTED], 'post', ['class' => 'inline-block']) ?>
            <?= Html::submitButton('拒绝', ['class' => 'btn btn-warning']) ?>
            <?= Html::endForm() ?>
        </div>
    </div>

</div>
