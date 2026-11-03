<?php
/**
 * 世界杯赛事信息与球迷交流平台
 * 文件：backend/views/layouts/main.php
 * 功能：后台布局与导航菜单
 * 作者：C（后台管理与统计）
 * 日期：2026-11-01
 */

/* @var $this \yii\web\View */
/* @var $content string */

use backend\assets\AppAsset;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use common\widgets\Alert;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => '世界杯后台管理',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    $menuItems = [
        ['label' => '统计概览', 'url' => ['/site/index']],
        [
            'label' => '内容管理',
            'items' => [
                ['label' => '球队管理', 'url' => ['/team/index']],
                ['label' => '球员管理', 'url' => ['/player/index']],
                ['label' => '新闻管理', 'url' => ['/news/index']],
                ['label' => '留言管理', 'url' => ['/message/index']],
            ],
        ],
        [
            'label' => '团队设置',
            'items' => [
                ['label' => '团队资料', 'url' => ['/team-profile/index']],
                ['label' => '成员管理', 'url' => ['/team-member/index']],
            ],
        ],
        [
            'label' => '系统管理',
            'items' => [
                ['label' => '操作日志', 'url' => ['/operation-log/index']],
            ],
        ],
    ];
    if (Yii::$app->user->isGuest) {
        $menuItems[] = ['label' => '登录', 'url' => ['/site/login']];
    } else {
        $menuItems[] = '<li>'
            . Html::beginForm(['/site/logout'], 'post')
            . Html::submitButton(
                '退出（' . Html::encode(Yii::$app->user->identity->username) . '）',
                ['class' => 'btn btn-link logout']
            )
            . Html::endForm()
            . '</li>';
    }
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => $menuItems,
    ]);
    NavBar::end();
    ?>

    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; <?= Html::encode(Yii::$app->name) ?> <?= date('Y') ?></p>

        <p class="pull-right"><?= Yii::powered() ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
