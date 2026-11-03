<?php
/**
 * 世界杯赛事信息与球迷交流平台
 * 文件：backend/views/player/create.php
 * 功能：球员管理新增页
 * 作者：C（后台管理与统计）
 * 日期：2026-11-01
 */

/* @var $this yii\web\View */
/* @var $model common\models\TeamPlayer */
/* @var $teamOptions array */

use yii\helpers\Html;

$this->title = '新增球员';
$this->params['breadcrumbs'][] = ['label' => '球员管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="player-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'teamOptions' => $teamOptions,
    ]) ?>

</div>
