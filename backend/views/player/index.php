<?php
/**
 * 世界杯赛事信息与球迷交流平台
 * 文件：backend/views/player/index.php
 * 功能：球员管理列表页
 * 作者：C（后台管理与统计）
 * 日期：2026-11-01
 */

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $q string */
/* @var $teamId string|int */
/* @var $teamOptions array */

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = '球员管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="player-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p><?= Html::a('+ 新增球员', ['create'], ['class' => 'btn btn-success']) ?></p>

    <form class="form-inline filter-form" method="get" action="<?= Url::to(['index']) ?>">
        <div class="form-group">
            <label for="player-q">姓名</label>
            <input type="text" id="player-q" class="form-control" name="q" value="<?= Html::encode($q) ?>">
        </div>
        <div class="form-group">
            <label for="player-team">球队</label>
            <select id="player-team" class="form-control" name="team_id">
                <option value="">全部球队</option>
                <?php foreach ($teamOptions as $id => $label): ?>
                    <option value="<?= (int) $id ?>" <?= (string) $teamId === (string) $id ? 'selected' : '' ?>>
                        <?= Html::encode($label) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <?= Html::submitButton('筛选', ['class' => 'btn btn-primary']) ?>
        <?= Html::a('重置', ['index'], ['class' => 'btn btn-default']) ?>
    </form>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => 'table table-striped table-bordered table-hover'],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            [
                'attribute' => 'name',
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::a(Html::encode($model->name), ['view', 'id' => $model->id]);
                },
            ],
            [
                'attribute' => 'team_id',
                'label' => '所属球队',
                'value' => function ($model) {
                    return $model->team === null ? '—' : $model->team->name;
                },
            ],
            'shirt_number',
            'position',
            'nationality',
            [
                'attribute' => 'is_captain',
                'format' => 'boolean',
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {delete}',
                'buttons' => [
                    'view' => function ($url) {
                        return Html::a('查看', $url, ['class' => 'btn btn-xs btn-default']);
                    },
                    'update' => function ($url) {
                        return Html::a('编辑', $url, ['class' => 'btn btn-xs btn-primary']);
                    },
                    'delete' => function ($url) {
                        return Html::a('删除', $url, [
                            'class' => 'btn btn-xs btn-danger',
                            'data-confirm' => '确定删除这位球员吗？',
                            'data-method' => 'post',
                        ]);
                    },
                ],
                'contentOptions' => ['class' => 'text-nowrap'],
            ],
        ],
    ]); ?>

</div>
