<?php
/**
 * 世界杯赛事信息与球迷交流平台
 * 文件：backend/views/news/index.php
 * 功能：新闻管理列表页
 * 作者：C（后台管理与统计）
 * 日期：2026-11-01
 */

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $q string */
/* @var $status string */
/* @var $category string */
/* @var $statusOptions array */
/* @var $categoryOptions array */

use common\models\NewsArticle;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = '新闻管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="news-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p><?= Html::a('+ 新建新闻', ['create'], ['class' => 'btn btn-success']) ?></p>

    <form class="form-inline filter-form" method="get" action="<?= Url::to(['index']) ?>">
        <div class="form-group">
            <label for="news-q">标题/摘要</label>
            <input type="text" id="news-q" class="form-control" name="q" value="<?= Html::encode($q) ?>">
        </div>
        <div class="form-group">
            <label for="news-status">状态</label>
            <select id="news-status" class="form-control" name="status">
                <option value="">全部状态</option>
                <?php foreach ($statusOptions as $value => $label): ?>
                    <option value="<?= Html::encode($value) ?>" <?= $status === $value ? 'selected' : '' ?>>
                        <?= Html::encode($label) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="news-category">分类</label>
            <select id="news-category" class="form-control" name="category">
                <option value="">全部分类</option>
                <?php foreach ($categoryOptions as $value => $label): ?>
                    <option value="<?= Html::encode($value) ?>" <?= $category === $value ? 'selected' : '' ?>>
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
                'attribute' => 'title',
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::a(Html::encode($model->title), ['view', 'id' => $model->id]);
                },
            ],
            'category',
            [
                'attribute' => 'status',
                'format' => 'raw',
                'value' => function ($model) {
                    $class = $model->status === NewsArticle::STATUS_PUBLISHED
                        ? 'label-success'
                        : 'label-default';

                    return Html::tag('span', Html::encode($model->statusLabel), ['class' => 'label ' . $class]);
                },
            ],
            [
                'attribute' => 'author_id',
                'label' => '发布人',
                'value' => function ($model) {
                    return $model->author === null ? '—' : $model->author->username;
                },
            ],
            [
                'attribute' => 'published_at',
                'label' => '发布时间',
                'value' => function ($model) {
                    return $model->publishedAtText;
                },
            ],
            [
                'attribute' => 'created_at',
                'format' => 'datetime',
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {publish} {draft} {delete}',
                'buttons' => [
                    'view' => function ($url) {
                        return Html::a('查看', $url, ['class' => 'btn btn-xs btn-default']);
                    },
                    'update' => function ($url) {
                        return Html::a('编辑', $url, ['class' => 'btn btn-xs btn-primary']);
                    },
                    'publish' => function ($url, $model) {
                        if ($model->status === NewsArticle::STATUS_PUBLISHED) {
                            return '';
                        }

                        return Html::a('发布', $url, [
                            'class' => 'btn btn-xs btn-success',
                            'data-confirm' => '确定发布这篇新闻吗？发布后会出现在前台新闻列表。',
                            'data-method' => 'post',
                        ]);
                    },
                    'draft' => function ($url, $model) {
                        if ($model->status !== NewsArticle::STATUS_PUBLISHED) {
                            return '';
                        }

                        return Html::a('撤回', $url, [
                            'class' => 'btn btn-xs btn-warning',
                            'data-confirm' => '确定撤回为草稿吗？撤回后前台不再显示。',
                            'data-method' => 'post',
                        ]);
                    },
                    'delete' => function ($url) {
                        return Html::a('删除', $url, [
                            'class' => 'btn btn-xs btn-danger',
                            'data-confirm' => '确定删除这篇新闻吗？',
                            'data-method' => 'post',
                        ]);
                    },
                ],
                'contentOptions' => ['class' => 'text-nowrap'],
            ],
        ],
    ]); ?>

</div>
