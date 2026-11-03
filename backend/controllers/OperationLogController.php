<?php
/**
 * 世界杯赛事信息与球迷交流平台
 * 文件：backend/controllers/OperationLogController.php
 * 功能：后台操作日志查询
 * 作者：C（后台管理与统计）
 * 日期：2026-11-01
 */

namespace backend\controllers;

use backend\components\AdminController;
use common\models\OperationLog;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;

/**
 * 操作日志：只读查询，不提供修改和删除入口。
 */
class OperationLogController extends AdminController
{
    /**
     * 日志列表，支持按操作类型、对象类型、关键词筛选。
     *
     * @return string
     */
    public function actionIndex()
    {
        $action = (string) Yii::$app->request->get('action', '');
        $target = (string) Yii::$app->request->get('target', '');
        $q = trim((string) Yii::$app->request->get('q', ''));

        $query = OperationLog::find();

        if (array_key_exists($action, OperationLog::actionLabels())) {
            $query->andWhere(['action' => $action]);
        }

        if (array_key_exists($target, OperationLog::targetLabels())) {
            $query->andWhere(['target_type' => $target]);
        }

        if ($q !== '') {
            $query->andWhere([
                'or',
                ['like', 'username', $q],
                ['like', 'summary', $q],
            ]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 20],
            'sort' => [
                'defaultOrder' => ['created_at' => SORT_DESC, 'id' => SORT_DESC],
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'action' => $action,
            'target' => $target,
            'q' => $q,
            'actionOptions' => OperationLog::actionLabels(),
            'targetOptions' => OperationLog::targetLabels(),
        ]);
    }

    /**
     * 查看一条日志。
     *
     * @param int $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        $model = OperationLog::findOne((int) $id);

        if ($model === null) {
            throw new NotFoundHttpException('没有找到这条操作日志。');
        }

        return $this->render('view', [
            'model' => $model,
        ]);
    }
}
