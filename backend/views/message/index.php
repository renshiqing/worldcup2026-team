<?php
/**
 * 世界杯赛事信息与球迷交流平台
 * 文件：backend/views/message/index.php
 * 功能：留言管理列表页
 * 作者：C（后台管理与统计）
 * 日期：2026-11-01
 */

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $status string */
/* @var $q string */
/* @var $statusOptions array */

use common\models\FanMessage;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii\helpers\Url;

$this->title = '留言管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="message-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p class="text-muted">前台提交的留言会以“待审核”状态出现在这里，通过后即可在前台展示。</p>

    <form class="form-inline filter-form" method="get" action="<?= Url::to(['index']) ?>">
        <div class="form-group">
            <label for="message-q">昵称/内容</label>
            <input type="text" id="message-q" class="form-control" name="q" value="<?= Html::encode($q) ?>">
        </div>
        <div class="form-group">
            <label for="message-status">状态</label>
            <select id="message-status" class="form-control" name="status">
                <option value="">全部状态</option>
                <?php foreach ($statusOptions as $value => $label): ?>
                    <option value="<?= Html::encode($value) ?>" <?= $status === $value ? 'selected' : '' ?>>
                        <?= Html::encode($label) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <?= Html::submitButton('筛选', ['class' => 'btn btn-primary']) ?>
        <?= Html::a('重置', ['index'], ['class' => 'btn btn-default']) ?>
        <?= Html::a('导出 CSV', ['export', 'q' => $q, 'status' => $status], ['class' => 'btn btn-default']) ?>
    </form>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => 'table table-striped table-bordered table-hover'],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            'nickname',
            [
                'attribute' => 'subject',
                'value' => function ($model) {
                    return $model->subject === null || $model->subject === '' ? '（无主题）' : $model->subject;
                },
            ],
            [
                'attribute' => 'message',
                'value' => function ($model) {
                    return StringHelper::truncate($model->message, 40);
                },
            ],
            [
                'attribute' => 'status',
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::tag(
                        'span',
                        Html::encode($model->statusLabel),
                        ['class' => 'label ' . $model->statusBadge]
                    );
                },
            ],
            [
                'attribute' => 'created_at',
                'format' => 'datetime',
            ],
            [
                'label' => '快捷处理',
                'format' => 'raw',
                'value' => function ($model) {
                    if ($model->status !== FanMessage::STATUS_PENDING) {
                        return '<span class="text-muted">—</span>';
                    }

                    $approve = Html::beginForm(
                        ['status', 'id' => $model->id, 'status' => FanMessage::STATUS_APPROVED],
                        'post',
                        ['class' => 'inline-block']
                    )
                        . Html::submitButton('通过', ['class' => 'btn btn-xs btn-success'])
                        . Html::endForm();

                    $reject = Html::beginForm(
                        ['status', 'id' => $model->id, 'status' => FanMessage::STATUS_REJECTED],
                        'post',
                        ['class' => 'inline-block']
                    )
                        . Html::submitButton('拒绝', ['class' => 'btn btn-xs btn-warning'])
                        . Html::endForm();

                    return $approve . ' ' . $reject;
                },
                'contentOptions' => ['class' => 'text-nowrap'],
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {delete}',
                'buttons' => [
                    'view' => function ($url, $model) {
                        return Html::a('处理', $url, ['class' => 'btn btn-xs btn-primary']);
                    },
                    'delete' => function ($url) {
                        return Html::a('删除', $url, [
                            'class' => 'btn btn-xs btn-danger',
                            'data-confirm' => '确定删除这条留言吗？',
                            'data-method' => 'post',
                        ]);
                    },
                ],
                'contentOptions' => ['class' => 'text-nowrap'],
            ],
        ],
    ]); ?>

</div>
