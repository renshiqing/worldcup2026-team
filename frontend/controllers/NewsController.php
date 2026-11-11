<?php
/**
 * 世界杯赛事信息与球迷交流平台
 * 文件：frontend/controllers/NewsController.php
 * 功能：前台新闻列表与新闻详情（只展示后台已发布的新闻）
 * 作者：B（前台页面与用户交互）
 * 日期：2026-11-11
 */

namespace frontend\controllers;

use common\models\NewsArticle;
use yii\data\Pagination;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * 新闻前台控制器。
 *
 * 数据全部来自 news_article 表：列表和详情都只查询
 * status = published 的记录，草稿不会出现在前台。
 */
class NewsController extends Controller
{
    /**
     * 每页展示的新闻条数。
     */
    const PAGE_SIZE = 6;

    /**
     * 新闻列表。
     *
     * 支持按分类筛选（赛事新闻 / 球队动态 / 公告）和分页，
     * 分类参数非法时自动回落到“全部”，不会因为手改地址而报错。
     *
     * @param string|null $category
     * @return string
     */
    public function actionIndex($category = null)
    {
        $categories = NewsArticle::categories();
        $selectedCategory = null;

        if (is_string($category) && isset($categories[$category])) {
            $selectedCategory = $category;
        }

        $query = NewsArticle::findPublished();

        if ($selectedCategory !== null) {
            $query->andWhere(['category' => $selectedCategory]);
        }

        $totalCount = (int) (clone $query)->count();

        $pagination = new Pagination([
            'totalCount' => $totalCount,
            'pageSize' => self::PAGE_SIZE,
            'pageParam' => 'page',
        ]);

        $articles = $query
            ->orderBy(['published_at' => SORT_DESC, 'id' => SORT_DESC])
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();

        return $this->render('index', [
            'articles' => $articles,
            'pagination' => $pagination,
            'categories' => $categories,
            'selectedCategory' => $selectedCategory,
            'totalCount' => $totalCount,
        ]);
    }

    /**
     * 新闻详情。
     *
     * @param int $id
     * @return string
     * @throws NotFoundHttpException 新闻不存在或仍是草稿
     */
    public function actionView($id)
    {
        $article = NewsArticle::findPublished()
            ->andWhere(['id' => (int) $id])
            ->one();

        if ($article === null) {
            throw new NotFoundHttpException('这篇新闻不存在，或者已经被撤回为草稿。');
        }

        $related = NewsArticle::findPublished()
            ->andWhere(['category' => $article->category])
            ->andWhere(['<>', 'id', $article->id])
            ->orderBy(['published_at' => SORT_DESC, 'id' => SORT_DESC])
            ->limit(3)
            ->all();

        $authorName = '编辑部';

        if (!empty($article->author_id) && $article->author !== null) {
            $authorName = $article->author->username;
        }

        return $this->render('view', [
            'article' => $article,
            'related' => $related,
            'authorName' => $authorName,
        ]);
    }
}
