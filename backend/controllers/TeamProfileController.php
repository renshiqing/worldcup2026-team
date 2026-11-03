<?php
/**
 * 世界杯赛事信息与球迷交流平台
 * 文件：backend/controllers/TeamProfileController.php
 * 功能：后台团队资料编辑
 * 作者：C（后台管理与统计）
 * 日期：2026-11-01
 */

namespace backend\controllers;

use backend\components\AdminController;
use backend\components\OperationLogger;
use common\models\OperationLog;
use common\models\TeamProfile;
use Yii;

/**
 * 后台团队资料编辑。
 *
 * 课程要求“后台包括团队/个人资料编辑”，这里维护 team_profile 单行数据，
 * 前台团队主页由 B 读取展示。
 */
class TeamProfileController extends AdminController
{
    /**
     * 编辑团队资料。
     *
     * @return string|\yii\web\Response
     */
    public function actionIndex()
    {
        $model = TeamProfile::current();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            OperationLogger::log(
                OperationLog::ACTION_UPDATE,
                'team_profile',
                $model->id,
                "修改团队资料：{$model->team_name}"
            );
            Yii::$app->session->setFlash('success', '团队资料已保存。');

            return $this->redirect(['index']);
        }

        return $this->render('index', [
            'model' => $model,
        ]);
    }
}
