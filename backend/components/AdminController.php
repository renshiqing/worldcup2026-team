<?php
/**
 * 世界杯赛事信息与球迷交流平台
 * 文件：backend/components/AdminController.php
 * 功能：后台访问控制基类（登录 + 管理员校验）
 * 作者：C（后台管理与统计）
 * 日期：2026-11-01
 */

namespace backend\components;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;

/**
 * 后台控制器基类。
 *
 * 两个条件同时满足才放行：
 * 1. 已登录（roles => @）；
 * 2. 当前账号 user.is_admin = 1。
 *
 * 这样普通注册用户即使登录了前台，也不能直接输入后台 URL 进入管理页面。
 */
class AdminController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            $identity = Yii::$app->user->identity;

                            return $identity !== null && $identity->isAdmin();
                        },
                    ],
                ],
                'denyCallback' => function ($rule, $action) {
                    if (Yii::$app->user->isGuest) {
                        // 未登录：跳转到后台登录页，登录后再回到原地址。
                        // 注意 loginRequired() 只写入跳转响应，不会终止请求，
                        // 这里必须 return，交给 AccessControl 返回 false 阻止动作执行。
                        Yii::$app->user->loginRequired();

                        return;
                    }

                    throw new ForbiddenHttpException('当前账号不是管理员，不能进入后台管理页面。');
                },
            ],
        ];
    }
}
