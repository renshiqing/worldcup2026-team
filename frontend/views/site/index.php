<?php
/**
 * 世界杯赛事信息与球迷交流平台
 * 文件：frontend/views/site/index.php
 * 功能：前台首页（数据库统计、最新新闻、近期比赛、团队成员、留言墙入口）
 * 作者：B（前台页面与用户交互）
 * 日期：2026-11-11
 */

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $stats array */
/* @var $latestNews common\models\NewsArticle[] */
/* @var $highlightMatches array */
/* @var $profile common\models\TeamProfile */
/* @var $members common\models\TeamMember[] */
/* @var $latestMessages common\models\FanMessage[] */

$this->title = '首页';

$statusLabels = [
    'finished' => '已结束',
    'scheduled' => '待开赛',
    'live' => '进行中',
    'postponed' => '已延期',
    'cancelled' => '已取消',
];

$statusClasses = [
    'finished' => 'label-default',
    'scheduled' => 'label-info',
    'live' => 'label-danger',
    'postponed' => 'label-warning',
    'cancelled' => 'label-default',
];
?>

<div class="wc-home">

    <!-- 首页主视觉 -->
    <section class="wc-hero">
        <p class="wc-eyebrow">WORLD CUP 2026 · 课程团队项目</p>

        <h1 class="wc-hero-title">2026 世界杯赛事信息与球迷交流平台</h1>

        <p class="wc-hero-sub">
            赛程查询、小组积分榜、球队与场馆资料、赛事新闻和球迷留言，
            前台浏览与后台管理共用同一个数据库。
        </p>

        <?php if (!empty($profile->slogan)): ?>
            <p class="wc-hero-slogan">「<?= Html::encode($profile->slogan) ?>」</p>
        <?php endif; ?>

        <p class="wc-hero-actions">
            <?= Html::a('查看比赛赛程', ['/match/index'], ['class' => 'btn btn-success btn-lg']) ?>
            <?= Html::a('最新新闻', ['/news/index'], ['class' => 'btn btn-default btn-lg']) ?>
            <?= Html::a('球迷留言', ['/message/index'], ['class' => 'btn btn-default btn-lg']) ?>
        </p>

        <ul class="wc-hero-facts">
            <li><strong><?= (int) $stats['teams'] ?></strong> 支球队</li>
            <li><strong><?= (int) $stats['matches'] ?></strong> 场比赛</li>
            <li><strong><?= (int) $stats['news'] ?></strong> 篇新闻</li>
            <li><strong><?= (int) $stats['stadiums'] ?></strong> 个场馆</li>
        </ul>
    </section>

    <!-- 数据概览 -->
    <div class="wc-section-head">
        <h2 class="wc-section-title">赛事概览</h2>
        <span class="wc-section-note">数字由数据库实时统计，后台增删数据后刷新本页即可变化</span>
    </div>

    <div class="row wc-stats">
        <div class="col-xs-6 col-md-3">
            <div class="wc-stat">
                <p class="wc-stat-label">参赛球队</p>
                <p class="wc-stat-number"><?= (int) $stats['teams'] ?></p>
                <p class="wc-stat-note">football_team 表</p>
            </div>
        </div>

        <div class="col-xs-6 col-md-3">
            <div class="wc-stat">
                <p class="wc-stat-label">比赛场次</p>
                <p class="wc-stat-number"><?= (int) $stats['matches'] ?></p>
                <p class="wc-stat-note">football_match 表</p>
            </div>
        </div>

        <div class="col-xs-6 col-md-3">
            <div class="wc-stat">
                <p class="wc-stat-label">已发布新闻</p>
                <p class="wc-stat-number"><?= (int) $stats['news'] ?></p>
                <p class="wc-stat-note">只统计已发布状态</p>
            </div>
        </div>

        <div class="col-xs-6 col-md-3">
            <div class="wc-stat">
                <p class="wc-stat-label">比赛场馆</p>
                <p class="wc-stat-number"><?= (int) $stats['stadiums'] ?></p>
                <p class="wc-stat-note">stadium 表</p>
            </div>
        </div>
    </div>

    <!-- 最新新闻 -->
    <div class="wc-section-head">
        <h2 class="wc-section-title">最新新闻</h2>
        <?= Html::a('全部新闻 →', ['/news/index'], ['class' => 'wc-section-more']) ?>
    </div>

    <?php if (empty($latestNews)): ?>
        <div class="wc-empty">
            <p><strong>暂时没有已发布的新闻。</strong></p>
            <p>管理员可以在后台“内容管理 → 新闻管理”发布新闻，发布后这里会自动显示。</p>
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($latestNews as $article): ?>
                <?php
                $summary = trim((string) $article->summary);

                if ($summary === '') {
                    $plainContent = trim(strip_tags((string) $article->content));
                    $summary = mb_strlen($plainContent, 'UTF-8') > 100
                        ? mb_substr($plainContent, 0, 100, 'UTF-8') . '…'
                        : $plainContent;
                } elseif (mb_strlen($summary, 'UTF-8') > 100) {
                    $summary = mb_substr($summary, 0, 100, 'UTF-8') . '…';
                }

                $publishedAt = empty($article->published_at)
                    ? '未发布'
                    : date('Y-m-d H:i', strtotime($article->published_at));
                ?>
                <div class="col-sm-6 col-md-4">
                    <div class="wc-card wc-news-card">
                        <p class="wc-meta">
                            <span class="wc-tag"><?= Html::encode($article->category ?: '未分类') ?></span>
                            <span class="wc-meta-time"><?= Html::encode($publishedAt) ?></span>
                        </p>

                        <h3 class="wc-news-title">
                            <?= Html::a($article->title, ['/news/view', 'id' => $article->id]) ?>
                        </h3>

                        <p class="wc-news-summary"><?= Html::encode($summary) ?></p>

                        <p class="wc-news-more">
                            <?= Html::a('阅读全文 →', ['/news/view', 'id' => $article->id], [
                                'class' => 'btn btn-default btn-sm',
                                'encode' => false,
                            ]) ?>
                        </p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- 近期比赛 -->
    <div class="wc-section-head">
        <h2 class="wc-section-title">近期比赛</h2>
        <?= Html::a('全部赛程 →', ['/match/index'], ['class' => 'wc-section-more']) ?>
    </div>

    <?php if (empty($highlightMatches)): ?>
        <div class="wc-empty">
            <p><strong>暂时没有比赛数据。</strong></p>
            <p>数据库准备比赛数据后，这里会显示对阵双方、时间和场馆。</p>
        </div>
    <?php else: ?>
        <div class="wc-card wc-table-card">
            <div class="table-responsive">
                <table class="table table-hover wc-match-table">
                    <thead>
                        <tr>
                            <th>比赛时间</th>
                            <th>阶段 / 小组</th>
                            <th>对阵</th>
                            <th>场馆</th>
                            <th>状态</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($highlightMatches as $match): ?>
                            <?php
                            $hasScore = $match['home_score'] !== null && $match['away_score'] !== null;
                            $score = $hasScore
                                ? $match['home_score'] . ' : ' . $match['away_score']
                                : 'VS';

                            $stageText = $match['stage_name'] ?: '赛程';
                            if (!empty($match['group_name'])) {
                                $stageText .= ' / ' . $match['group_name'] . ' 组';
                            }

                            $venue = $match['stadium_name'] ?: '待定';
                            if (!empty($match['city_name'])) {
                                $venue .= ' / ' . $match['city_name'];
                            }

                            $statusText = $statusLabels[$match['status']] ?? $match['status'];
                            $statusClass = $statusClasses[$match['status']] ?? 'label-default';
                            ?>
                            <tr>
                                <td class="text-nowrap">
                                    <?= Html::encode(date('Y-m-d H:i', strtotime($match['kickoff_at']))) ?>
                                </td>
                                <td class="wc-cell-muted"><?= Html::encode($stageText) ?></td>
                                <td class="wc-match-teams">
                                    <?= Html::a($match['home_team'], ['/team/view', 'id' => $match['home_team_id']]) ?>
                                    <span class="wc-score"><?= Html::encode($score) ?></span>
                                    <?= Html::a($match['away_team'], ['/team/view', 'id' => $match['away_team_id']]) ?>
                                </td>
                                <td class="wc-cell-muted"><?= Html::encode($venue) ?></td>
                                <td>
                                    <span class="label <?= Html::encode($statusClass) ?>">
                                        <?= Html::encode($statusText) ?>
                                    </span>
                                </td>
                                <td class="text-right">
                                    <?= Html::a('详情', ['/match/view', 'id' => $match['id']], [
                                        'class' => 'btn btn-xs btn-default',
                                    ]) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>

    <!-- 团队介绍 -->
    <div class="wc-section-head">
        <h2 class="wc-section-title">团队与分工</h2>
        <?= Html::a('团队介绍 →', ['/site/about'], ['class' => 'wc-section-more']) ?>
    </div>

    <div class="panel panel-default wc-team-panel">
        <div class="panel-body">
            <h3 class="wc-team-name"><?= Html::encode($profile->team_name) ?></h3>

            <?php if (!empty($profile->slogan)): ?>
                <p class="wc-team-slogan"><?= Html::encode($profile->slogan) ?></p>
            <?php endif; ?>

            <?php if (!empty($profile->description)): ?>
                <p class="wc-team-desc"><?= nl2br(Html::encode($profile->description)) ?></p>
            <?php endif; ?>

            <?php if (empty($members)): ?>
                <div class="wc-empty wc-empty-inline">
                    <p>团队成员信息尚未录入。</p>
                    <p>管理员可以在后台“团队设置 → 成员管理”中添加三名成员及分工。</p>
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
        </div>
    </div>

    <!-- 留言墙 -->
    <div class="wc-section-head">
        <h2 class="wc-section-title">球迷留言</h2>
        <?= Html::a('去留言 →', ['/message/index'], ['class' => 'wc-section-more']) ?>
    </div>

    <?php if (empty($latestMessages)): ?>
        <div class="wc-empty">
            <p><strong>留言墙还是空的。</strong></p>
            <p>留言不需要登录，提交后由管理员审核，通过后显示在这里。</p>
            <p><?= Html::a('写下第一条留言', ['/message/index'], ['class' => 'btn btn-success']) ?></p>
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($latestMessages as $message): ?>
                <?php
                // 首页只展示摘要，完整内容留在留言板，避免卡片被长留言撑乱。
                $messageText = trim((string) $message->message);
                if (mb_strlen($messageText, 'UTF-8') > 120) {
                    $messageText = mb_substr($messageText, 0, 120, 'UTF-8') . '…';
                }

                $replyText = trim((string) $message->admin_reply);
                if (mb_strlen($replyText, 'UTF-8') > 80) {
                    $replyText = mb_substr($replyText, 0, 80, 'UTF-8') . '…';
                }
                ?>
                <div class="col-md-4">
                    <div class="wc-message-card">
                        <p class="wc-meta">
                            <strong><?= Html::encode($message->nickname) ?></strong>
                            <?php if (!empty($message->created_at)): ?>
                                · <?= Html::encode(date('Y-m-d', (int) $message->created_at)) ?>
                            <?php endif; ?>
                        </p>

                        <?php if (!empty($message->subject)): ?>
                            <p class="wc-message-subject"><?= Html::encode($message->subject) ?></p>
                        <?php endif; ?>

                        <p class="wc-message-body"><?= nl2br(Html::encode($messageText)) ?></p>

                        <?php if ($replyText !== ''): ?>
                            <div class="wc-message-reply">
                                <p class="wc-reply-title">管理员回复</p>
                                <p><?= nl2br(Html::encode($replyText)) ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <p class="text-center">
            <?= Html::a('查看更多留言 / 我要留言', ['/message/index'], ['class' => 'btn btn-primary']) ?>
        </p>
    <?php endif; ?>

    <!-- 快速入口 -->
    <div class="wc-section-head">
        <h2 class="wc-section-title">快速入口</h2>
    </div>

    <div class="row wc-quick-links">
        <div class="col-xs-6 col-md-3">
            <?= Html::a(
                '<span class="glyphicon glyphicon-calendar" aria-hidden="true"></span>'
                . '<strong>比赛赛程</strong><small>按小组和状态筛选</small>',
                ['/match/index'],
                ['class' => 'wc-quick-link', 'encode' => false]
            ) ?>
        </div>

        <div class="col-xs-6 col-md-3">
            <?= Html::a(
                '<span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span>'
                . '<strong>小组积分榜</strong><small>胜平负与积分排名</small>',
                ['/match/standings'],
                ['class' => 'wc-quick-link', 'encode' => false]
            ) ?>
        </div>

        <div class="col-xs-6 col-md-3">
            <?= Html::a(
                '<span class="glyphicon glyphicon-flag" aria-hidden="true"></span>'
                . '<strong>球队信息</strong><small>球队详情与球员名单</small>',
                ['/team/index'],
                ['class' => 'wc-quick-link', 'encode' => false]
            ) ?>
        </div>

        <div class="col-xs-6 col-md-3">
            <?= Html::a(
                '<span class="glyphicon glyphicon-map-marker" aria-hidden="true"></span>'
                . '<strong>场馆信息</strong><small>城市、容量与地址</small>',
                ['/stadium/index'],
                ['class' => 'wc-quick-link', 'encode' => false]
            ) ?>
        </div>
    </div>

</div>
