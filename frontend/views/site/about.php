<?php
/**
 * 世界杯赛事信息与球迷交流平台
 * 文件：frontend/views/site/about.php
 * 功能：团队介绍页（团队资料、成员、三人分工、协作流程、技术栈）
 * 作者：B（前台页面与用户交互）
 * 日期：2026-11-11
 */

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $profile common\models\TeamProfile */
/* @var $members common\models\TeamMember[] */
/* @var $stats array */

$this->title = '团队介绍';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="wc-about">
    <section class="wc-hero wc-hero-team">
        <p class="wc-eyebrow">TEAM PROFILE · 课程团队</p>

        <h1 class="wc-hero-title"><?= Html::encode($profile->team_name) ?></h1>

        <?php if (!empty($profile->slogan)): ?>
            <p class="wc-hero-slogan">「<?= Html::encode($profile->slogan) ?>」</p>
        <?php endif; ?>

        <?php if (!empty($profile->description)): ?>
            <p class="wc-hero-sub"><?= nl2br(Html::encode($profile->description)) ?></p>
        <?php endif; ?>
    </section>

    <div class="row wc-stats">
        <div class="col-xs-6 col-md-3">
            <div class="wc-stat">
                <p class="wc-stat-label">项目成员</p>
                <p class="wc-stat-number"><?= count($members) ?></p>
                <p class="wc-stat-note">前台、后台与数据库分工</p>
            </div>
        </div>

        <div class="col-xs-6 col-md-3">
            <div class="wc-stat">
                <p class="wc-stat-label">参赛球队</p>
                <p class="wc-stat-number"><?= (int) $stats['teams'] ?></p>
                <p class="wc-stat-note">数据库演示数据</p>
            </div>
        </div>

        <div class="col-xs-6 col-md-3">
            <div class="wc-stat">
                <p class="wc-stat-label">比赛场次</p>
                <p class="wc-stat-number"><?= (int) $stats['matches'] ?></p>
                <p class="wc-stat-note">含小组赛与淘汰赛</p>
            </div>
        </div>

        <div class="col-xs-6 col-md-3">
            <div class="wc-stat">
                <p class="wc-stat-label">已通过留言</p>
                <p class="wc-stat-number"><?= (int) $stats['messages'] ?></p>
                <p class="wc-stat-note">审核通过后公开显示</p>
            </div>
        </div>
    </div>

    <div class="wc-section-head">
        <h2 class="wc-section-title">团队成员</h2>
        <span class="wc-section-note">姓名、分工和学号由后台“成员管理”维护</span>
    </div>

    <?php if (empty($members)): ?>
        <div class="wc-empty">
            <p><strong>成员信息尚未录入。</strong></p>
            <p>管理员可以在后台“团队设置 → 成员管理”中添加成员，保存后刷新本页即可显示。</p>
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($members as $member): ?>
                <div class="col-sm-4">
                    <?= $this->render('_member', ['member' => $member]) ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="wc-section-head">
        <h2 class="wc-section-title">三人分工</h2>
        <span class="wc-section-note">对应计划文档中的 A / B / C 三条工作线</span>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="wc-card wc-role-card">
                <h4>数据库与比赛模块</h4>
                <p class="wc-meta">A 组长</p>
                <ul>
                    <li>设计 15 张数据表，提交 migration 与 install.sql</li>
                    <li>准备球队、比赛、场馆和演示数据</li>
                    <li>比赛列表与详情、小组积分榜</li>
                    <li>合并三人代码并做部署验证</li>
                </ul>
            </div>
        </div>

        <div class="col-md-4">
            <div class="wc-card wc-role-card">
                <h4>前台页面与用户交互</h4>
                <p class="wc-meta">B</p>
                <ul>
                    <li>首页、球队 / 比赛 / 新闻前台页面</li>
                    <li>新闻列表与详情（只显示已发布）</li>
                    <li>球迷留言表单、成功页与“我的留言”</li>
                    <li>用户操作手册与页面截图</li>
                </ul>
            </div>
        </div>

        <div class="col-md-4">
            <div class="wc-card wc-role-card">
                <h4>后台管理与统计</h4>
                <p class="wc-meta">C</p>
                <ul>
                    <li>后台登录与菜单、权限校验</li>
                    <li>球队、球员、新闻、留言的增删改查</li>
                    <li>留言审核与回复、统计卡片与图表</li>
                    <li>后台实现文档与展示 PPT 素材</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="wc-section-head">
        <h2 class="wc-section-title">协作流程</h2>
    </div>

    <ol class="wc-steps">
        <li>
            <strong>先定数据库。</strong>
            A 完成表结构、外键和演示数据，B、C 使用同一份字段开发，避免前台和后台字段对不上。
        </li>
        <li>
            <strong>前台与后台并行。</strong>
            B 根据表结构完成浏览和留言，C 完成管理入口与 CRUD，两人不修改同一页面文件。
        </li>
        <li>
            <strong>前后台联调。</strong>
            C 后台发布新闻，B 刷新前台确认显示；B 提交留言，C 后台审核，B 在“我的留言”看到状态变化。
        </li>
        <li>
            <strong>统一合并发布。</strong>
            A 把三个人的分支合并到 main，在干净环境里重新启动前台、后台并恢复数据库。
        </li>
    </ol>

    <div class="wc-section-head">
        <h2 class="wc-section-title">技术栈与代码结构</h2>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="wc-card">
                <h4>技术栈</h4>
                <ul class="wc-tech-list">
                    <li>Yii 2 高级模板（frontend / backend / console 三层应用）</li>
                    <li>PHP 8.x + MySQL / MariaDB</li>
                    <li>Bootstrap 3 + jQuery（Yii 自带前端资源）</li>
                    <li>ActiveRecord + Query Builder 访问数据库</li>
                    <li>MVC 分层：Model / Controller / View</li>
                </ul>
            </div>
        </div>

        <div class="col-md-6">
            <div class="wc-card">
                <h4>本页对应的代码</h4>
                <pre class="wc-code">frontend/controllers/SiteController.php
frontend/controllers/NewsController.php
frontend/controllers/MessageController.php
frontend/models/FanMessageForm.php
frontend/views/site/
frontend/views/news/
frontend/views/message/</pre>
            </div>
        </div>
    </div>

    <div class="wc-section-head">
        <h2 class="wc-section-title">关于本站</h2>
    </div>

    <div class="wc-card">
        <p>
            本站是“互联网数据库开发”课程团队项目，数据库包含球队、比赛、场馆、新闻、留言、
            球队资料和操作日志等 15 张表。前台展示的内容全部来自数据库，
            后台修改后刷新前台即可看到变化。
        </p>
        <p class="text-muted">
            页面中的球队、赛程、比分和新闻均为演示数据，不代表官方赛事信息。
        </p>

        <p>
            <?php if (!empty($profile->contact_email)): ?>
                联系邮箱：<?= Html::encode($profile->contact_email) ?>　
            <?php endif; ?>
            <?= Html::a('去留言板留言', ['/message/index'], ['class' => 'btn btn-success btn-sm']) ?>
            <?= Html::a('返回首页', ['/site/index'], ['class' => 'btn btn-default btn-sm']) ?>
        </p>
    </div>
</div>
