<?php
/**
 * 世界杯赛事信息与球迷交流平台
 * 文件：console/controllers/AdminController.php
 * 功能：管理员账号命令（创建、提升）
 * 作者：C（后台管理与统计）
 * 日期：2026-11-01
 */

namespace console\controllers;

use common\models\User;
use Yii;
use yii\console\Controller;
use yii\console\ExitCode;

/**
 * 管理员账号维护命令。
 *
 * 用法：
 *   php yii admin/create admin admin@example.com 123456
 *   php yii admin/promote 已存在用户名
 *
 * 普通注册用户 is_admin = 0，不能进入后台；只有这里创建或提升的账号可以。
 */
class AdminController extends Controller
{
    /**
     * 创建管理员；用户名已存在时重置密码并提升为管理员。
     *
     * @param string $username 用户名
     * @param string $email 邮箱
     * @param string|null $password 密码，不填则随机生成
     * @return int
     */
    public function actionCreate($username, $email, $password = null)
    {
        $user = User::findOne(['username' => $username]);

        if ($user === null) {
            $user = new User();
            $user->username = $username;
            $user->generateAuthKey();
        }

        $emailOwner = User::findOne(['email' => $email]);

        if ($emailOwner !== null && $emailOwner->id !== $user->id) {
            $this->stderr("邮箱 {$email} 已被其他账号使用。\n");

            return ExitCode::UNSPECIFIED_ERROR;
        }

        $user->email = $email;
        $user->status = User::STATUS_ACTIVE;
        $user->is_admin = 1;

        $plainPassword = empty($password)
            ? Yii::$app->security->generateRandomString(10)
            : $password;

        $user->setPassword($plainPassword);

        if (!$user->save()) {
            $this->stderr("保存失败：\n");
            foreach ($user->getFirstErrors() as $error) {
                $this->stderr(" - {$error}\n");
            }

            return ExitCode::UNSPECIFIED_ERROR;
        }

        $this->stdout("管理员账号已就绪。\n");
        $this->stdout("用户名：{$username}\n");
        $this->stdout("密码：{$plainPassword}\n");
        $this->stdout("后台入口：http://127.0.0.1:8082/index.php?r=site/login\n");

        return ExitCode::OK;
    }

    /**
     * 把已有普通账号提升为管理员。
     *
     * @param string $username 用户名
     * @return int
     */
    public function actionPromote($username)
    {
        $user = User::findOne(['username' => $username]);

        if ($user === null) {
            $this->stderr("没有找到用户 {$username}。\n");

            return ExitCode::UNSPECIFIED_ERROR;
        }

        $user->is_admin = 1;
        $user->status = User::STATUS_ACTIVE;
        $user->save(false);

        $this->stdout("用户 {$username} 已提升为管理员。\n");

        return ExitCode::OK;
    }
}
