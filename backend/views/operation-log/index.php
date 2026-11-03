<?php
/**
 * 世界杯赛事信息与球迷交流平台
 * 文件：backend/views/operation-log/index.php
 * 功能：后台操作日志列表页
 * 作者：C（后台管理与统计）
 * 日期：2026-11-01
 */

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $action string */
/* @var $target string */
/* @var $q string */
/* @var $actionOptions array */
/* @var $targetOptions array */

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = '操作日志';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="operation-log-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p class="text-muted">
        记录管理员在后台的新增、修改、删除、审核、发布、撤回和导出操作，只读，不提供删除入口。
    </p>

    <form class="form-inline filter-form" method="get" action="<?= Url::to(['index']) ?>">
        <div class="form-group">
            <label for="log-action">操作类型</label>
            <select id="log-action" class="form-control" name="action">
                <option value="">全部操作</option>
                <?php foreach ($actionOptions as $value => $label): ?>
                    <option value="<?= Html::encode($value) ?>" <?= $action === $value ? 'selected' : '' ?>>
                        <?= Html::encode($label) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="log-target">对象类型</label>
            <select id="log-target" class="form-control" name="target">
                <option value="">全部对象</option>
                <?php foreach ($targetOptions as $value => $label): ?>
                    <option value="<?= Html::encode($value) ?>" <?= $target === $value ? 'selected' : '' ?>>
                        <?= Html::encode($label) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="log-q">操作人/说明</label>
            <input type="text" id="log-q" class="form-control" name="q" value="<?= Html::encode($q) ?>">
        </div>
        <?= Html::submitButton('筛选', ['class' => 'btn btn-primary']) ?>
        <?= Html::a('重置', ['index'], ['class' => 'btn btn-default']) ?>
    </form>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'emptyText' => '暂无操作日志。',
        'tableOptions' => ['class' => 'table table-striped table-bordered table-hover'],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            [
                'attribute' => 'created_at',
                'format' => 'datetime',
            ],
            'username',
            [
                'attribute' => 'action',
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::tag(
                        'span',
                        Html::encode($model->actionLabel),
                        ['class' => 'label ' . $model->actionBadge]
                    );
                },
            ],
            [
                'attribute' => 'target_type',
                'label' => '对象',
                'value' => function ($model) {
                    return $model->targetLabel . ($model->target_id === null ? '' : ' #' . $model->target_id);
                },
            ],
            [
                'attribute' => 'summary',
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::a(
                        Html::encode($model->summary),
                        ['view', 'id' => $model->id]
                    );
                },
            ],
            'ip',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
                'buttons' => [
                    'view' => function ($url) {
                        return Html::a('详情', $url, ['class' => 'btn btn-xs btn-default']);
                    },
                ],
                'contentOptions' => ['class' => 'text-nowrap'],
            ],
        ],
    ]); ?>

</div>
