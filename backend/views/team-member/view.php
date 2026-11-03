<?php
/**
 * 世界杯赛事信息与球迷交流平台
 * 文件：backend/views/team-member/view.php
 * 功能：团队成员管理详情页
 * 作者：C（后台管理与统计）
 * 日期：2026-11-01
 */

/* @var $this yii\web\View */
/* @var $model common\models\TeamMember */

use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = '成员：' . $model->name;
$this->params['breadcrumbs'][] = ['label' => '成员管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="team-member-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('编辑', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('返回列表', ['index'], ['class' => 'btn btn-default']) ?>
        <?= Html::a('删除', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data-confirm' => '确定删除这位成员吗？',
            'data-method' => 'post',
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            'student_id',
            'role',
            'display_order',
            [
                'attribute' => 'avatar',
                'format' => 'raw',
                'value' => empty($model->avatar)
                    ? '—'
                    : Html::img($model->avatar, ['style' => 'max-width:120px;border-radius:6px;']),
            ],
            'bio:ntext',
            [
                'attribute' => 'created_at',
                'format' => 'datetime',
            ],
            [
                'attribute' => 'updated_at',
                'format' => 'datetime',
            ],
        ],
    ]) ?>

</div>
