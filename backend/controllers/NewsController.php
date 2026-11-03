<?php
/**
 * 世界杯赛事信息与球迷交流平台
 * 文件：backend/controllers/NewsController.php
 * 功能：后台新闻管理（草稿、发布、撤回、删除）
 * 作者：C（后台管理与统计）
 * 日期：2026-11-01
 */

namespace backend\controllers;

use backend\components\AdminController;
use backend\components\OperationLogger;
use common\models\NewsArticle;
use common\models\OperationLog;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

/**
 * 后台新闻管理：草稿/发布、编辑、删除。
 *
 * 对应数据表 news_article；author_id 由 C 的迁移新增，自动记录当前管理员。
 * 前台只应显示 status = published 的新闻。
 */
class NewsController extends AdminController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['verbs'] = [
            'class' => VerbFilter::class,
            'actions' => [
                'delete' => ['POST'],
                'publish' => ['POST'],
                'draft' => ['POST'],
            ],
        ];

        return $behaviors;
    }

    /**
     * 新闻列表，支持标题关键词、分类、状态筛选。
     *
     * @return string
     */
    public function actionIndex()
    {
        $q = trim((string) Yii::$app->request->get('q', ''));
        $status = (string) Yii::$app->request->get('status', '');
        $category = (string) Yii::$app->request->get('category', '');

        $query = NewsArticle::find()->with('author');

        if ($q !== '') {
            $query->andWhere([
                'or',
                ['like', 'title', $q],
                ['like', 'summary', $q],
            ]);
        }

        if (array_key_exists($status, NewsArticle::statuses())) {
            $query->andWhere(['status' => $status]);
        }

        if ($category !== '') {
            $query->andWhere(['category' => $category]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 10],
            'sort' => [
                'defaultOrder' => ['created_at' => SORT_DESC],
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'q' => $q,
            'status' => $status,
            'category' => $category,
            'statusOptions' => NewsArticle::statuses(),
            'categoryOptions' => NewsArticle::categories(),
        ]);
    }

    /**
     * 查看新闻。
     *
     * @param int $id
     * @return string
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * 新建新闻，默认保存为草稿。
     *
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new NewsArticle();

        if ($model->load(Yii::$app->request->post())) {
            $model->author_id = Yii::$app->user->id;

            if ($model->save()) {
                OperationLogger::log(
                    OperationLog::ACTION_CREATE,
                    'news',
                    $model->id,
                    "新建新闻：{$model->title}（{$model->statusLabel}）"
                );
                Yii::$app->session->setFlash('success', "新闻“{$model->title}”已保存。");

                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
            'statusOptions' => NewsArticle::statuses(),
            'categoryOptions' => NewsArticle::categories(),
        ]);
    }

    /**
     * 编辑新闻。
     *
     * @param int $id
     * @return string|\yii\web\Response
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $model->author_id = $model->author_id === null
                ? Yii::$app->user->id
                : $model->author_id;

            if ($model->save()) {
                OperationLogger::log(
                    OperationLog::ACTION_UPDATE,
                    'news',
                    $model->id,
                    "修改新闻：{$model->title}（{$model->statusLabel}）"
                );
                Yii::$app->session->setFlash('success', "新闻“{$model->title}”已更新。");

                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
            'statusOptions' => NewsArticle::statuses(),
            'categoryOptions' => NewsArticle::categories(),
        ]);
    }

    /**
     * 一键发布。
     *
     * @param int $id
     * @return \yii\web\Response
     */
    public function actionPublish($id)
    {
        $model = $this->findModel($id);
        $model->status = NewsArticle::STATUS_PUBLISHED;
        $model->save(false);

        OperationLogger::log(
            OperationLog::ACTION_PUBLISH,
            'news',
            $model->id,
            "发布新闻：{$model->title}"
        );

        Yii::$app->session->setFlash('success', "新闻“{$model->title}”已发布。");

        return $this->redirect(['index']);
    }

    /**
     * 一键撤回为草稿。
     *
     * @param int $id
     * @return \yii\web\Response
     */
    public function actionDraft($id)
    {
        $model = $this->findModel($id);
        $model->status = NewsArticle::STATUS_DRAFT;
        $model->save(false);

        OperationLogger::log(
            OperationLog::ACTION_DRAFT,
            'news',
            $model->id,
            "撤回新闻：{$model->title}"
        );

        Yii::$app->session->setFlash('success', "新闻“{$model->title}”已撤回为草稿，前台不再显示。");

        return $this->redirect(['index']);
    }

    /**
     * 删除新闻。
     *
     * @param int $id
     * @return \yii\web\Response
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $title = $model->title;
        $newsId = $model->id;
        $model->delete();

        OperationLogger::log(
            OperationLog::ACTION_DELETE,
            'news',
            $newsId,
            "删除新闻：{$title}"
        );

        Yii::$app->session->setFlash('success', "新闻“{$title}”已删除。");

        return $this->redirect(['index']);
    }

    /**
     * @param int $id
     * @return NewsArticle
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        $model = NewsArticle::findOne((int) $id);

        if ($model === null) {
            throw new NotFoundHttpException('没有找到这篇新闻。');
        }

        return $model;
    }
}
