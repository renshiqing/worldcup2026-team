<?php
/**
 * 世界杯赛事信息与球迷交流平台
 * 文件：frontend/views/news/view.php
 * 功能：新闻详情页（正文、发布信息、相关新闻）
 * 作者：B（前台页面与用户交互）
 * 日期：2026-11-11
 */

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $article common\models\NewsArticle */
/* @var $related common\models\NewsArticle[] */
/* @var $authorName string */

$this->title = $article->title;
$this->params['breadcrumbs'][] = ['label' => '新闻资讯', 'url' => ['/news/index']];
$this->params['breadcrumbs'][] = $article->title;

$publishedAt = empty($article->published_at)
    ? '未发布'
    : date('Y-m-d H:i', strtotime($article->published_at));
?>

<div class="wc-news-view">
    <p>
        <?= Html::a('← 返回新闻列表', ['/news/index'], ['class' => 'btn btn-default btn-sm']) ?>
        <?= Html::a(
            '查看「' . $article->category . '」分类',
            ['/news/index', 'category' => $article->category],
            ['class' => 'btn btn-link btn-sm']
        ) ?>
    </p>

    <article class="wc-card wc-article">
        <p class="wc-meta">
            <span class="wc-tag"><?= Html::encode($article->category ?: '未分类') ?></span>
            <span class="wc-meta-time">发布于 <?= Html::encode($publishedAt) ?></span>
            <span class="wc-meta-author">发布人：<?= Html::encode($authorName) ?></span>
        </p>

        <h1 class="wc-article-title"><?= Html::encode($article->title) ?></h1>

        <?php if (!empty($article->summary)): ?>
            <p class="lead wc-article-summary"><?= Html::encode($article->summary) ?></p>
        <?php endif; ?>

        <hr>

        <div class="wc-article-content">
            <?= nl2br(Html::encode($article->content)) ?>
        </div>

        <hr>

        <p class="text-muted small">
            本文由课程项目成员在后台发布，内容为演示数据，不代表官方赛事信息。
        </p>
    </article>

    <h2 class="wc-section-title">相关新闻</h2>

    <?php if (empty($related)): ?>
        <div class="wc-empty">
            暂时没有同分类的其他新闻，
            <?= Html::a('返回新闻列表', ['/news/index']) ?>查看更多内容。
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($related as $item): ?>
                <div class="col-sm-6 col-md-4">
                    <?= $this->render('_card', ['article' => $item]) ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
