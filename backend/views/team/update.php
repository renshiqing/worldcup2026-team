<?php
/**
 * 世界杯赛事信息与球迷交流平台
 * 文件：backend/views/team/update.php
 * 功能：球队管理编辑页
 * 作者：C（后台管理与统计）
 * 日期：2026-11-01
 */

/* @var $this yii\web\View */
/* @var $model common\models\FootballTeam */
/* @var $groupOptions array */

use yii\helpers\Html;

$this->title = '编辑球队：' . $model->name;
$this->params['breadcrumbs'][] = ['label' => '球队管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '编辑';
?>
<div class="team-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'groupOptions' => $groupOptions,
    ]) ?>

</div>
