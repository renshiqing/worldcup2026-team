<?php
namespace frontend\controllers;

use yii\db\Query;
use yii\web\Controller;

class StadiumController extends Controller
{
    public function actionIndex()
    {
        // 查询场馆，并关联所在城市
        $stadiums = (new Query())
            ->select([
                's.id',
                's.name',
                's.capacity',
                's.address',
                'c.name AS city_name',
                'c.country',
            ])
            ->from(['s' => 'stadium'])
            ->leftJoin(
                ['c' => 'host_city'],
                'c.id = s.city_id'
            )
            ->orderBy([
                'c.country' => SORT_ASC,
                'c.name' => SORT_ASC,
                's.name' => SORT_ASC,
            ])
            ->all();

        return $this->render('index', [
            'stadiums' => $stadiums,
        ]);
    }
}
