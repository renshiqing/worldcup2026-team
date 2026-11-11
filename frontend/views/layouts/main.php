<?php
/**
 * 世界杯赛事信息与球迷交流平台
 * 文件：frontend/views/layouts/main.php
 * 功能：前台公共布局（导航、提示消息、页脚）
 * 作者：B（前台页面与用户交互）
 * 日期：2026-11-11
 */

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use common\widgets\Alert;

AppAsset::register($this);

$pageTitle = empty($this->title) ? '' : $this->title . ' · ';
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($pageTitle) ?>2026 世界杯赛事信息与球迷交流平台</title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => 'WORLD CUP 2026',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);

    $menuItems = [
        ['label' => '首页', 'url' => ['/site/index']],
        ['label' => '比赛赛程', 'url' => ['/match/index']],
        ['label' => '小组积分榜', 'url' => ['/match/standings']],
        ['label' => '球队信息', 'url' => ['/team/index']],
        ['label' => '新闻资讯', 'url' => ['/news/index']],
        ['label' => '球迷留言', 'url' => ['/message/index']],
        ['label' => '场馆信息', 'url' => ['/stadium/index']],
        ['label' => '团队介绍', 'url' => ['/site/about']],
    ];

    echo Nav::widget([
        'options' => ['class' => 'navbar-nav'],
        'items' => $menuItems,
    ]);

    if (Yii::$app->user->isGuest) {
        $authItems = [
            ['label' => '登录', 'url' => ['/site/login']],
            ['label' => '注册', 'url' => ['/site/signup']],
        ];
    } else {
        $authItems = [
            '<li>'
            . Html::beginForm(['/site/logout'], 'post')
            . Html::submitButton(
                '退出登录（' . Yii::$app->user->identity->username . '）',
                ['class' => 'btn btn-link logout']
            )
            . Html::endForm()
            . '</li>',
        ];
    }

    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => $authItems,
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
        <p class="pull-left">
            &copy; <?= date('Y') ?> 越位三剑客 · 2026 世界杯课程项目
        </p>

        <p class="pull-right">
            <?= Html::a('团队介绍', ['/site/about']) ?> ·
            <?= Html::a('球迷留言', ['/message/index']) ?>
            <span class="text-muted">｜演示数据，仅供课程展示</span>
        </p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
