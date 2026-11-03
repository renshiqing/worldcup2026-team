<?php
/**
 * 世界杯赛事信息与球迷交流平台
 * 文件：backend/views/news/view.php
 * 功能：新闻管理详情页
 * 作者：C（后台管理与统计）
 * 日期：2026-11-01
 */

/* @var $this yii\web\View */
/* @var $model common\models\NewsArticle */

use common\models\NewsArticle;
use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = '新闻：' . $model->title;
$this->params['breadcrumbs'][] = ['label' => '新闻管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="news-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('编辑', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php if ($model->status === NewsArticle::STATUS_PUBLISHED): ?>
            <?= Html::a('撤回为草稿', ['draft', 'id' => $model->id], [
                'class' => 'btn btn-warning',
                'data-confirm' => '确定撤回为草稿吗？撤回后前台不再显示。',
                'data-method' => 'post',
            ]) ?>
        <?php else: ?>
            <?= Html::a('发布', ['publish', 'id' => $model->id], [
                'class' => 'btn btn-success',
                'data-confirm' => '确定发布这篇新闻吗？',
                'data-method' => 'post',
            ]) ?>
        <?php endif; ?>
        <?= Html::a('返回列表', ['index'], ['class' => 'btn btn-default']) ?>
        <?= Html::a('删除', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data-confirm' => '确定删除这篇新闻吗？',
            'data-method' => 'post',
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'title',
            'category',
            [
                'attribute' => 'status',
                'format' => 'raw',
                'value' => Html::tag(
                    'span',
                    Html::encode($model->statusLabel),
                    ['class' => 'label ' . ($model->status === NewsArticle::STATUS_PUBLISHED ? 'label-success' : 'label-default')]
                ),
            ],
            [
                'attribute' => 'author_id',
                'label' => '发布人',
                'value' => $model->author === null ? '—' : $model->author->username,
            ],
            'summary:ntext',
            [
                'attribute' => 'published_at',
                'label' => '发布时间',
                'value' => $model->publishedAtText,
            ],
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

    <div class="panel panel-default">
        <div class="panel-heading">正文</div>
        <div class="panel-body news-content">
            <?= nl2br(Html::encode($model->content)) ?>
        </div>
    </div>

</div>
