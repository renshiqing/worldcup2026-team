<?php
/**
 * 世界杯赛事信息与球迷交流平台
 * 文件：backend/views/team/view.php
 * 功能：球队管理详情页
 * 作者：C（后台管理与统计）
 * 日期：2026-11-01
 */

/* @var $this yii\web\View */
/* @var $model common\models\FootballTeam */

use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = '球队：' . $model->name;
$this->params['breadcrumbs'][] = ['label' => '球队管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="team-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('编辑', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('返回列表', ['index'], ['class' => 'btn btn-default']) ?>
        <?= Html::a('删除', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data-confirm' => '确定删除这支球队吗？如果已有比赛引用，数据库会阻止删除。',
            'data-method' => 'post',
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            'country',
            [
                'attribute' => 'group_id',
                'label' => '所在小组',
                'value' => $model->groupName,
            ],
            [
                'attribute' => 'created_at',
                'format' => 'datetime',
            ],
        ],
    ]) ?>

</div>
