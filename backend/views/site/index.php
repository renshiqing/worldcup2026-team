<?php
/**
 * 世界杯赛事信息与球迷交流平台
 * 文件：backend/views/site/index.php
 * 功能：后台统计概览页面
 * 作者：C（后台管理与统计）
 * 日期：2026-11-01
 */

/* @var $this yii\web\View */
/* @var $teamCount int */
/* @var $matchCount int */
/* @var $newsCount int */
/* @var $playerCount int */
/* @var $pendingCount int */
/* @var $newsPublishedCount int */
/* @var $newsDraftCount int */
/* @var $messageCounts array */
/* @var $messageTotal int */
/* @var $messageTrend array */
/* @var $trendTotal int */
/* @var $trendMax int */
/* @var $dbName string */
/* @var $tableCount int */
/* @var $migrationCount int */
/* @var $visibleGroups array */
/* @var $groupedTeamCount int */
/* @var $ungroupedCount int */
/* @var $maxTeamCount int */
/* @var $recentMessages common\models\FanMessage[] */
/* @var $recentNews common\models\NewsArticle[] */

use common\models\FanMessage;
use common\models\NewsArticle;
use yii\helpers\Html;

$this->title = '统计概览';
$this->params['breadcrumbs'][] = $this->title;

$messageTotalSafe = max(1, (int) $messageTotal);
?>
<div class="site-index">

    <div class="page-head">
        <h1>统计概览</h1>
        <p class="text-muted">
            欢迎，<?= Html::encode(Yii::$app->user->identity->username) ?>。
            以下数据全部来自数据库实时查询，新增或删除数据后刷新本页即可看到变化。
        </p>
    </div>

    <div class="row stat-row">
        <div class="col-md-3 col-sm-6">
            <div class="stat-card stat-blue">
                <div class="stat-icon"><span class="glyphicon glyphicon-globe"></span></div>
                <div class="stat-number"><?= (int) $teamCount ?></div>
                <div class="stat-label">球队总数</div>
                <?= Html::a('管理球队 &raquo;', ['/team/index'], ['class' => 'stat-link']) ?>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card stat-green">
                <div class="stat-icon"><span class="glyphicon glyphicon-calendar"></span></div>
                <div class="stat-number"><?= (int) $matchCount ?></div>
                <div class="stat-label">比赛总数</div>
                <span class="stat-link">由 A 的比赛模块维护</span>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card stat-purple">
                <div class="stat-icon"><span class="glyphicon glyphicon-file"></span></div>
                <div class="stat-number"><?= (int) $newsCount ?></div>
                <div class="stat-label">新闻总数</div>
                <?= Html::a('管理新闻 &raquo;', ['/news/index'], ['class' => 'stat-link']) ?>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card stat-orange">
                <div class="stat-icon"><span class="glyphicon glyphicon-comment"></span></div>
                <div class="stat-number"><?= (int) $pendingCount ?></div>
                <div class="stat-label">待审核留言</div>
                <?= Html::a('处理留言 &raquo;', ['/message/index', 'status' => FanMessage::STATUS_PENDING], ['class' => 'stat-link']) ?>
            </div>
        </div>
    </div>

    <div class="row mini-row">
        <div class="col-md-3 col-sm-6">
            <div class="mini-stat">
                <span class="mini-number"><?= (int) $playerCount ?></span>
                <span class="mini-label">球员总数</span>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="mini-stat">
                <span class="mini-number"><?= (int) $messageCounts[FanMessage::STATUS_APPROVED] ?></span>
                <span class="mini-label">已通过留言</span>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="mini-stat">
                <span class="mini-number"><?= (int) $newsDraftCount ?></span>
                <span class="mini-label">草稿新闻</span>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="mini-stat">
                <span class="mini-number"><?= (int) $groupedTeamCount ?></span>
                <span class="mini-label">已分组球队<?= $ungroupedCount > 0 ? '（未分组 ' . (int) $ungroupedCount . '）' : '' ?></span>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-7">
            <div class="panel panel-default">
                <div class="panel-heading">
                    各小组球队数量
                    <span class="badge">共 <?= (int) $groupedTeamCount ?> 支</span>
                </div>
                <div class="panel-body">
                    <?php if (empty($visibleGroups)): ?>
                        <p class="text-muted">还没有分配到小组的球队。请先在球队管理里为球队选择小组。</p>
                    <?php else: ?>
                        <div class="bar-chart">
                            <?php foreach ($visibleGroups as $row): ?>
                                <?php
                                $count = (int) $row['team_count'];
                                $width = (int) round($count / $maxTeamCount * 100);
                                $share = $groupedTeamCount > 0 ? round($count / $groupedTeamCount * 100) : 0;
                                ?>
                                <div class="bar-row">
                                    <div class="bar-label"><?= Html::encode($row['group_name']) ?>组</div>
                                    <div class="bar-track">
                                        <div class="bar-fill" style="width: <?= $width ?>%"></div>
                                    </div>
                                    <div class="bar-value"><?= $count ?> 支 · <?= $share ?>%</div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <p class="text-muted small-note">
                            只显示有球队的小组；未分组球队 <?= (int) $ungroupedCount ?> 支。
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="panel panel-default">
                <div class="panel-heading">
                    留言处理情况
                    <span class="badge">共 <?= (int) $messageTotal ?> 条</span>
                </div>
                <div class="panel-body">
                    <?php
                    $messageMeta = [
                        FanMessage::STATUS_PENDING => ['label' => '待审核', 'class' => 'pending'],
                        FanMessage::STATUS_APPROVED => ['label' => '已通过', 'class' => 'approved'],
                        FanMessage::STATUS_REJECTED => ['label' => '已拒绝', 'class' => 'rejected'],
                    ];
                    ?>
                    <?php foreach ($messageMeta as $status => $meta): ?>
                        <?php
                        $count = (int) $messageCounts[$status];
                        $width = (int) round($count / $messageTotalSafe * 100);
                        ?>
                        <div class="msg-row">
                            <div class="msg-label"><?= Html::encode($meta['label']) ?></div>
                            <div class="msg-track">
                                <div class="msg-fill <?= $meta['class'] ?>" style="width: <?= $width ?>%"></div>
                            </div>
                            <div class="msg-count"><?= $count ?></div>
                        </div>
                    <?php endforeach; ?>
                    <p class="text-muted small-note">
                        待审核留言会显示在前台“待处理”列表中，通过或回复后前台即可展示。
                    </p>
                </div>
                <div class="panel-footer text-right">
                    <?= Html::a('处理留言 &raquo;', ['/message/index'], ['class' => 'btn btn-warning btn-sm']) ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    近 7 天留言趋势
                    <span class="badge">共 <?= (int) $trendTotal ?> 条</span>
                </div>
                <div class="panel-body">
                    <div class="trend-chart">
                        <?php foreach ($messageTrend as $item): ?>
                            <?php
                            $height = $item['count'] > 0
                                ? max(4, (int) round($item['count'] / $trendMax * 100))
                                : 2;
                            ?>
                            <div class="trend-col">
                                <div class="trend-count"><?= (int) $item['count'] ?></div>
                                <div class="trend-track">
                                    <div class="trend-bar" style="height: <?= $height ?>%"></div>
                                </div>
                                <div class="trend-label"><?= Html::encode($item['label']) ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <p class="text-muted small-note">
                        按留言提交日期统计，今天在最右侧；没有留言的日期显示 0。
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="panel panel-default quick-panel">
                <div class="panel-heading">快捷操作</div>
                <div class="panel-body quick-actions">
                    <?= Html::a('<span class="glyphicon glyphicon-plus"></span> 新增球队', ['/team/create'], ['class' => 'btn btn-primary']) ?>
                    <?= Html::a('<span class="glyphicon glyphicon-plus"></span> 新增球员', ['/player/create'], ['class' => 'btn btn-info']) ?>
                    <?= Html::a('<span class="glyphicon glyphicon-plus"></span> 新建新闻', ['/news/create'], ['class' => 'btn btn-success']) ?>
                    <?= Html::a('<span class="glyphicon glyphicon-comment"></span> 处理留言', ['/message/index', 'status' => FanMessage::STATUS_PENDING], ['class' => 'btn btn-warning']) ?>
                    <?= Html::a('<span class="glyphicon glyphicon-cog"></span> 团队资料', ['/team-profile/index'], ['class' => 'btn btn-default']) ?>
                    <?= Html::a('<span class="glyphicon glyphicon-user"></span> 成员管理', ['/team-member/index'], ['class' => 'btn btn-default']) ?>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading">系统信息</div>
                <ul class="list-group system-info">
                    <li class="list-group-item">数据库 <span class="pull-right"><?= Html::encode($dbName) ?></span></li>
                    <li class="list-group-item">数据表 <span class="pull-right"><?= (int) $tableCount ?> 张</span></li>
                    <li class="list-group-item">迁移记录 <span class="pull-right"><?= (int) $migrationCount ?> 条</span></li>
                    <li class="list-group-item">Yii 版本 <span class="pull-right"><?= Html::encode(Yii::getVersion()) ?></span></li>
                    <li class="list-group-item">PHP 版本 <span class="pull-right"><?= Html::encode(PHP_VERSION) ?></span></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    最近留言
                    <?= Html::a('全部 &raquo;', ['/message/index'], ['class' => 'pull-right']) ?>
                </div>
                <?php if (empty($recentMessages)): ?>
                    <div class="panel-body text-muted">还没有留言。</div>
                <?php else: ?>
                    <table class="table table-condensed table-hover">
                        <thead>
                        <tr>
                            <th>昵称</th>
                            <th>主题</th>
                            <th>状态</th>
                            <th>时间</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($recentMessages as $message): ?>
                            <tr>
                                <td><?= Html::a(Html::encode($message->nickname), ['/message/view', 'id' => $message->id]) ?></td>
                                <td><?= Html::encode($message->subject === null ? '（无主题）' : $message->subject) ?></td>
                                <td><span class="label <?= $message->statusBadge ?>"><?= Html::encode($message->statusLabel) ?></span></td>
                                <td><?= date('m-d H:i', (int) $message->created_at) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    最近新闻
                    <?= Html::a('全部 &raquo;', ['/news/index'], ['class' => 'pull-right']) ?>
                </div>
                <?php if (empty($recentNews)): ?>
                    <div class="panel-body text-muted">还没有新闻。</div>
                <?php else: ?>
                    <table class="table table-condensed table-hover">
                        <thead>
                        <tr>
                            <th>标题</th>
                            <th>状态</th>
                            <th>时间</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($recentNews as $news): ?>
                            <tr>
                                <td><?= Html::a(Html::encode($news->title), ['/news/view', 'id' => $news->id]) ?></td>
                                <td>
                                    <span class="label <?= $news->status === NewsArticle::STATUS_PUBLISHED ? 'label-success' : 'label-default' ?>">
                                        <?= Html::encode($news->statusLabel) ?>
                                    </span>
                                </td>
                                <td><?= date('m-d H:i', (int) $news->created_at) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>
