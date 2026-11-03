<?php
/**
 * 世界杯赛事信息与球迷交流平台
 * 文件：backend/views/operation-log/view.php
 * 功能：后台操作日志详情页
 * 作者：C（后台管理与统计）
 * 日期：2026-11-01
 */

/* @var $this yii\web\View */
/* @var $model common\models\OperationLog */

use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = '日志 #' . $model->id;
$this->params['breadcrumbs'][] = ['label' => '操作日志', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="operation-log-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p><?= Html::a('返回列表', ['index'], ['class' => 'btn btn-default']) ?></p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'attribute' => 'created_at',
                'format' => 'datetime',
            ],
            'username',
            [
                'attribute' => 'action',
                'value' => $model->actionLabel,
            ],
            [
                'attribute' => 'target_type',
                'value' => $model->targetLabel,
            ],
            'target_id',
            'summary',
            'ip',
        ],
    ]) ?>

</div>
