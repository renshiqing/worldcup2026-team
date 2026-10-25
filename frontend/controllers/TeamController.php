<?php
namespace frontend\controllers;

use yii\db\Query;
use yii\web\Controller;

class TeamController extends Controller
{
    public function actionIndex()
    {
        // 查询球队，并关联所属小组
        $teams = (new Query())
            ->select([
                't.id',
                't.name',
                't.country',
                'g.group_name',
            ])
            ->from(['t' => 'football_team'])
            ->leftJoin(
                ['g' => 'tournament_group'],
                'g.id = t.group_id'
            )
            ->orderBy([
                'g.group_name' => SORT_ASC,
                't.name' => SORT_ASC,
            ])
            ->all();

        return $this->render('index', [
            'teams' => $teams,
        ]);
    }
}
