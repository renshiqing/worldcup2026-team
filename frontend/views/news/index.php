<?php
/**
 * 世界杯赛事信息与球迷交流平台
 * 文件：frontend/views/news/index.php
 * 功能：新闻列表页（分类筛选 + 分页，只显示已发布新闻）
 * 作者：B（前台页面与用户交互）
 * 日期：2026-11-11
 */

use yii\helpers\Html;
use yii\widgets\LinkPager;

/* @var $this yii\web\View */
/* @var $articles common\models\NewsArticle[] */
/* @var $pagination yii\data\Pagination */
/* @var $categories array */
/* @var $selectedCategory string|null */
/* @var $totalCount int */

$this->title = '新闻资讯';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="wc-news-index">
    <div class="page-header">
        <h1>
            新闻资讯
            <small>共 <?= (int) $totalCount ?> 篇已发布</small>
        </h1>
    </div>

    <p class="text-muted">
        这里只展示后台标记为“已发布”的新闻，草稿不会出现在前台。
        管理员在后台“内容管理 → 新闻管理”发布或撤回新闻后，刷新本页即可看到变化。
    </p>

    <ul class="nav nav-pills wc-filter">
        <li class="<?= $selectedCategory === null ? 'active' : '' ?>">
            <?= Html::a('全部', ['/news/index']) ?>
        </li>
        <?php foreach ($categories as $key => $label): ?>
            <li class="<?= $selectedCategory === $key ? 'active' : '' ?>">
                <?= Html::a($label, ['/news/index', 'category' => $key]) ?>
            </li>
        <?php endforeach; ?>
    </ul>

    <?php if (empty($articles)): ?>
        <div class="wc-empty">
            <p><strong>暂无新闻。</strong></p>
            <p>
                <?= $selectedCategory === null
                    ? '后台还没有发布任何新闻。'
                    : '该分类下还没有已发布的新闻。' ?>
                管理员可在后台“内容管理 → 新闻管理”中发布新闻。
            </p>
            <?php if ($selectedCategory !== null): ?>
                <p><?= Html::a('查看全部新闻', ['/news/index'], ['class' => 'btn btn-default btn-sm']) ?></p>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($articles as $article): ?>
                <div class="col-sm-6 col-md-4">
                    <?= $this->render('_card', ['article' => $article]) ?>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="text-center">
            <?= LinkPager::widget([
                'pagination' => $pagination,
                'options' => ['class' => 'pagination'],
                'prevPageLabel' => '上一页',
                'nextPageLabel' => '下一页',
                'firstPageLabel' => '首页',
                'lastPageLabel' => '末页',
                'hideOnSinglePage' => true,
            ]) ?>
        </div>
    <?php endif; ?>
</div>
