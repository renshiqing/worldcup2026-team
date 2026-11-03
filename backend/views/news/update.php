<?php
/**
 * 世界杯赛事信息与球迷交流平台
 * 文件：backend/views/news/update.php
 * 功能：新闻管理编辑页
 * 作者：C（后台管理与统计）
 * 日期：2026-11-01
 */

/* @var $this yii\web\View */
/* @var $model common\models\NewsArticle */
/* @var $statusOptions array */
/* @var $categoryOptions array */

use yii\helpers\Html;

$this->title = '编辑新闻：' . $model->title;
$this->params['breadcrumbs'][] = ['label' => '新闻管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '编辑';
?>
<div class="news-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'statusOptions' => $statusOptions,
        'categoryOptions' => $categoryOptions,
    ]) ?>

</div>
