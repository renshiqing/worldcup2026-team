<?php
namespace frontend\controllers;

use Yii;
use common\models\TeamPlayer;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class PlayerController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['create', 'update', 'delete'],
                'rules' => [
                    ['allow' => true, 'roles' => ['@']],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => ['delete' => ['POST']],
            ],
        ];
    }

    public function actionIndex($team_id = null)
    {
        $teamId = ($team_id !== null && $team_id !== '') ? (int) $team_id : null;

        $query = (new Query())
            ->select([
                'p.id', 'p.team_id', 'p.name', 'p.shirt_number',
                'p.position', 'p.nationality', 'p.birth_date', 'p.is_captain',
                'team_name' => 't.name',
            ])
            ->from(['p' => 'team_player'])
            ->leftJoin(['t' => 'football_team'], 't.id = p.team_id');

        if ($teamId !== null) {
            $query->andWhere(['p.team_id' => $teamId]);
        }

        $players = $query->orderBy([
            't.name' => SORT_ASC,
            'p.shirt_number' => SORT_ASC,
            'p.name' => SORT_ASC,
        ])->all();

        $teams = (new Query())
            ->select(['id', 'name'])
            ->from('football_team')
            ->orderBy(['name' => SORT_ASC])
            ->all();

        return $this->render('index', [
            'players' => $players,
            'teams' => $teams,
            'teamId' => $teamId,
        ]);
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);
        $team = (new Query())
            ->select(['id', 'name', 'country'])
            ->from('football_team')
            ->where(['id' => $model->team_id])
            ->one();

        return $this->render('view', ['model' => $model, 'team' => $team]);
    }

    public function actionCreate($team_id = null)
    {
        $model = new TeamPlayer();
        $model->is_captain = 0;
        $model->created_at = time();

        if ($team_id !== null) {
            $model->team_id = (int) $team_id;
        }

        if ($model->load(Yii::$app->request->post())) {
            $model->created_at = time();
            $this->normalizeOptionalFields($model);

            if ($model->save()) {
                Yii::$app->session->setFlash('success', '球员已成功添加。');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
            'teams' => $this->getTeamOptions(),
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $createdAt = $model->created_at;

        if ($model->load(Yii::$app->request->post())) {
            $model->created_at = $createdAt;
            $this->normalizeOptionalFields($model);

            if ($model->save()) {
                Yii::$app->session->setFlash('success', '球员信息已更新。');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
            'teams' => $this->getTeamOptions(),
        ]);
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $teamId = $model->team_id;
        $model->delete();

        Yii::$app->session->setFlash('success', '球员记录已删除。');
        return $this->redirect(['index', 'team_id' => $teamId]);
    }

    private function normalizeOptionalFields($model)
    {
        foreach (['shirt_number', 'position', 'nationality', 'birth_date'] as $attribute) {
            if ($model->$attribute === '') {
                $model->$attribute = null;
            }
        }
    }

    private function findModel($id)
    {
        $model = TeamPlayer::findOne((int) $id);
        if ($model !== null) {
            return $model;
        }
        throw new NotFoundHttpException('没有找到该球员。');
    }

    private function getTeamOptions()
    {
        $rows = (new Query())
            ->select(['id', 'name'])
            ->from('football_team')
            ->orderBy(['name' => SORT_ASC])
            ->all();

        return ArrayHelper::map($rows, 'id', 'name');
    }
}
