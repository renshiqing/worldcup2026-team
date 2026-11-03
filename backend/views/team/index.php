<?php
/**
 * 世界杯赛事信息与球迷交流平台
 * 文件：backend/views/team/index.php
 * 功能：球队管理列表页
 * 作者：C（后台管理与统计）
 * 日期：2026-11-01
 */

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $q string */
/* @var $groupId string|int */
/* @var $groupOptions array */

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = '球队管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="team-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p><?= Html::a('+ 新增球队', ['create'], ['class' => 'btn btn-success']) ?></p>

    <form class="form-inline filter-form" method="get" action="<?= Url::to(['index']) ?>">
        <div class="form-group">
            <label for="team-q">名称/国家</label>
            <input type="text" id="team-q" class="form-control" name="q" value="<?= Html::encode($q) ?>">
        </div>
        <div class="form-group">
            <label for="team-group">小组</label>
            <select id="team-group" class="form-control" name="group_id">
                <option value="">全部小组</option>
                <?php foreach ($groupOptions as $id => $label): ?>
                    <option value="<?= (int) $id ?>" <?= (string) $groupId === (string) $id ? 'selected' : '' ?>>
                        <?= Html::encode($label) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <?= Html::submitButton('筛选', ['class' => 'btn btn-primary']) ?>
        <?= Html::a('重置', ['index'], ['class' => 'btn btn-default']) ?>
        <?= Html::a('导出 CSV', ['export', 'q' => $q, 'group_id' => $groupId], ['class' => 'btn btn-default']) ?>
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
            'country',
            [
                'attribute' => 'group_id',
                'label' => '所在小组',
                'value' => function ($model) {
                    return $model->groupName;
                },
            ],
            [
                'attribute' => 'created_at',
                'format' => 'datetime',
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
                            'data-confirm' => '确定删除这支球队吗？如果已有比赛引用，数据库会阻止删除。',
                            'data-method' => 'post',
                        ]);
                    },
                ],
                'contentOptions' => ['class' => 'text-nowrap'],
            ],
        ],
    ]); ?>

</div>
