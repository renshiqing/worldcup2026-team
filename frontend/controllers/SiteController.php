<?php
namespace frontend\controllers;

use frontend\models\ResendVerificationEmailForm;
use frontend\models\VerifyEmailForm;
use Yii;
use yii\base\InvalidArgumentException;
use yii\db\Query;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\FanMessage;
use common\models\FootballTeam;
use common\models\LoginForm;
use common\models\NewsArticle;
use common\models\TeamMember;
use common\models\TeamProfile;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * 首页数据全部来自数据库：统计数字、最新新闻、近期比赛、
     * 团队资料与成员、已通过审核的留言，刷新页面即可看到后台的改动。
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $stats = [
            'teams' => (int) FootballTeam::find()->count(),
            'matches' => (int) (new Query())->from('{{%football_match}}')->count(),
            'news' => (int) NewsArticle::findPublished()->count(),
            'stadiums' => (int) (new Query())->from('{{%stadium}}')->count(),
        ];

        $latestNews = NewsArticle::findPublished()
            ->orderBy(['published_at' => SORT_DESC, 'id' => SORT_DESC])
            ->limit(3)
            ->all();

        // 优先展示待开赛/进行中的比赛；没有时退回到最近结束的比赛。
        $highlightMatches = $this->createMatchQuery()
            ->where(['m.status' => ['scheduled', 'live']])
            ->orderBy(['m.kickoff_at' => SORT_ASC, 'm.id' => SORT_ASC])
            ->limit(4)
            ->all();

        if (empty($highlightMatches)) {
            $highlightMatches = $this->createMatchQuery()
                ->where(['m.status' => 'finished'])
                ->orderBy(['m.kickoff_at' => SORT_DESC, 'm.id' => SORT_DESC])
                ->limit(4)
                ->all();
        }

        return $this->render('index', [
            'stats' => $stats,
            'latestNews' => $latestNews,
            'highlightMatches' => $highlightMatches,
            'profile' => TeamProfile::current(),
            'members' => TeamMember::ordered(),
            'latestMessages' => FanMessage::find()
                ->where(['status' => FanMessage::STATUS_APPROVED])
                ->orderBy(['created_at' => SORT_DESC, 'id' => SORT_DESC])
                ->limit(3)
                ->all(),
        ]);
    }

    /**
     * 比赛公共查询：比赛 + 阶段 + 小组 + 双方球队 + 场馆 + 城市。
     *
     * @return Query
     */
    private function createMatchQuery()
    {
        return (new Query())
            ->select([
                'm.id',
                'm.kickoff_at',
                'm.home_score',
                'm.away_score',
                'm.status',
                'home_team_id' => 'home.id',
                'away_team_id' => 'away.id',
                'home_team' => 'home.name',
                'away_team' => 'away.name',
                'stage_name' => 'stage.stage_name',
                'group_name' => 'grp.group_name',
                'stadium_name' => 'stadium.name',
                'city_name' => 'city.name',
            ])
            ->from(['m' => '{{%football_match}}'])
            ->innerJoin(['stage' => '{{%match_stage}}'], 'stage.id = m.stage_id')
            ->leftJoin(['grp' => '{{%tournament_group}}'], 'grp.id = m.group_id')
            ->innerJoin(['home' => '{{%football_team}}'], 'home.id = m.home_team_id')
            ->innerJoin(['away' => '{{%football_team}}'], 'away.id = m.away_team_id')
            ->leftJoin(['stadium' => '{{%stadium}}'], 'stadium.id = m.stadium_id')
            ->leftJoin(['city' => '{{%host_city}}'], 'city.id = stadium.city_id');
    }

    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            $model->password = '';

            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return mixed
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending your message.');
            }

            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }

    /**
     * 团队介绍页：团队资料、三名成员和项目协作说明。
     *
     * @return mixed
     */
    public function actionAbout()
    {
        return $this->render('about', [
            'profile' => TeamProfile::current(),
            'members' => TeamMember::ordered(),
            'stats' => [
                'teams' => (int) FootballTeam::find()->count(),
                'matches' => (int) (new Query())->from('{{%football_match}}')->count(),
                'news' => (int) NewsArticle::findPublished()->count(),
                'messages' => (int) FanMessage::find()
                    ->where(['status' => FanMessage::STATUS_APPROVED])
                    ->count(),
            ],
        ]);
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post()) && $model->signup()) {
            Yii::$app->session->setFlash('success', 'Thank you for registration. Please check your inbox for verification email.');
            return $this->goHome();
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for the provided email address.');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    /**
     * Verify email address
     *
     * @param string $token
     * @throws BadRequestHttpException
     * @return yii\web\Response
     */
    public function actionVerifyEmail($token)
    {
        try {
            $model = new VerifyEmailForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
        if ($user = $model->verifyEmail()) {
            if (Yii::$app->user->login($user)) {
                Yii::$app->session->setFlash('success', 'Your email has been confirmed!');
                return $this->goHome();
            }
        }

        Yii::$app->session->setFlash('error', 'Sorry, we are unable to verify your account with provided token.');
        return $this->goHome();
    }

    /**
     * Resend verification email
     *
     * @return mixed
     */
    public function actionResendVerificationEmail()
    {
        $model = new ResendVerificationEmailForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');
                return $this->goHome();
            }
            Yii::$app->session->setFlash('error', 'Sorry, we are unable to resend verification email for the provided email address.');
        }

        return $this->render('resendVerificationEmail', [
            'model' => $model
        ]);
    }
}
