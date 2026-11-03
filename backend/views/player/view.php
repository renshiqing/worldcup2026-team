<?php
/**
 * 世界杯赛事信息与球迷交流平台
 * 文件：backend/views/player/view.php
 * 功能：球员管理详情页
 * 作者：C（后台管理与统计）
 * 日期：2026-11-01
 */

/* @var $this yii\web\View */
/* @var $model common\models\TeamPlayer */

use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = '球员：' . $model->name;
$this->params['breadcrumbs'][] = ['label' => '球员管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="player-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('编辑', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('返回列表', ['index'], ['class' => 'btn btn-default']) ?>
        <?= Html::a('删除', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data-confirm' => '确定删除这位球员吗？',
            'data-method' => 'post',
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'attribute' => 'team_id',
                'label' => '所属球队',
                'value' => $model->team === null ? '—' : $model->team->name,
            ],
            'name',
            'shirt_number',
            'position',
            'nationality',
            'birth_date',
            [
                'attribute' => 'is_captain',
                'format' => 'boolean',
            ],
            [
                'attribute' => 'created_at',
                'format' => 'datetime',
            ],
        ],
    ]) ?>

</div>
