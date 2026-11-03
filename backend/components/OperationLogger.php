<?php
/**
 * 世界杯赛事信息与球迷交流平台
 * 文件：backend/components/OperationLogger.php
 * 功能：后台操作日志记录工具
 * 作者：C（后台管理与统计）
 * 日期：2026-11-01
 */

namespace backend\components;

use common\models\OperationLog;
use Yii;
use Throwable;

/**
 * 统一写入后台操作日志。
 *
 * 日志失败不能影响主业务，所以内部捕获所有异常，只记到应用日志里。
 */
class OperationLogger
{
    /**
     * 写一条操作日志。
     *
     * @param string $action 操作类型，见 OperationLog::ACTION_*
     * @param string $targetType 对象类型，见 OperationLog::targetLabels()
     * @param int|null $targetId 对象主键
     * @param string $summary 操作说明，例如“新增球队：北星队”
     * @return bool
     */
    public static function log($action, $targetType, $targetId, $summary)
    {
        try {
            $identity = Yii::$app->user->identity;

            $log = new OperationLog();
            $log->user_id = $identity === null ? null : $identity->id;
            $log->username = $identity === null ? 'system' : $identity->username;
            $log->action = $action;
            $log->target_type = $targetType;
            $log->target_id = $targetId === null ? null : (int) $targetId;
            $log->summary = mb_substr((string) $summary, 0, 240);
            $log->ip = Yii::$app->request->userIP;
            $log->created_at = time();

            return $log->save(false);
        } catch (Throwable $e) {
            Yii::error('操作日志写入失败：' . $e->getMessage(), __METHOD__);

            return false;
        }
    }
}
