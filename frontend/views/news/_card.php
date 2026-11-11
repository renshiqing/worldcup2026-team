<?php
/**
 * 世界杯赛事信息与球迷交流平台
 * 文件：frontend/views/news/_card.php
 * 功能：新闻卡片（新闻列表与相关新闻共用）
 * 作者：B（前台页面与用户交互）
 * 日期：2026-11-11
 */

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $article common\models\NewsArticle */

// 摘要为空时，从正文提取一段作为列表摘要，避免卡片出现大片空白。
$summary = trim((string) $article->summary);

if ($summary === '') {
    $plainContent = trim(strip_tags((string) $article->content));
    $summary = mb_strlen($plainContent, 'UTF-8') > 120
        ? mb_substr($plainContent, 0, 120, 'UTF-8') . '…'
        : $plainContent;
} elseif (mb_strlen($summary, 'UTF-8') > 120) {
    // 后台填写的摘要可能很长，列表里截断，避免把卡片撑得过高。
    $summary = mb_substr($summary, 0, 120, 'UTF-8') . '…';
}

$publishedAt = empty($article->published_at)
    ? '未发布'
    : date('Y-m-d H:i', strtotime($article->published_at));
?>

<div class="wc-card wc-news-card">
    <p class="wc-meta">
        <span class="wc-tag"><?= Html::encode($article->category ?: '未分类') ?></span>
        <span class="wc-meta-time"><?= Html::encode($publishedAt) ?></span>
    </p>

    <h3 class="wc-news-title">
        <?= Html::a($article->title, ['/news/view', 'id' => $article->id]) ?>
    </h3>

    <p class="wc-news-summary">
        <?= Html::encode($summary) ?>
    </p>

    <p class="wc-news-more">
        <?= Html::a('阅读全文 →', ['/news/view', 'id' => $article->id], ['class' => 'btn btn-default btn-sm']) ?>
    </p>
</div>
