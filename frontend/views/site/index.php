
<?php

use yii\helpers\Html;

$this->title = '2026 世界杯赛事中心';

?>

<div class="worldcup-home">

    <!-- 首页主视觉区域 -->
    <div style="
        background: #061A40;
        color: #ffffff;
        padding: 40px 30px;
        border-radius: 8px;
        margin-bottom: 25px;
    ">
        <p style="color: #B8D5FF; letter-spacing: 2px;">
            WORLD CUP 2026 · DEMO
        </p>

        <h1 style="font-size: 34px; font-weight: bold;">
            2026 世界杯赛事中心
        </h1>

        <p style="font-size: 16px; margin: 20px 0;">
            赛程查询、小组积分榜与赛事数据，一站式查看。
        </p>

        <p>
            <?= Html::a(
                '查看比赛赛程',
                ['/match/index'],
                [
                    'class' => 'btn btn-success btn-lg',
                ]
            ) ?>

            <?= Html::a(
                '查看小组积分榜',
                ['/match/standings'],
                [
                    'class' => 'btn btn-default btn-lg',
                    'style' => 'margin-left: 8px;',
                ]
            ) ?>
        </p>
    </div>

    <!-- 数据概览 -->
    <h2>赛事概览</h2>

    <div class="row">

        <div class="col-sm-6 col-md-3">
            <div class="panel panel-default">
                <div class="panel-body">
                    <p class="text-muted">演示比赛</p>
                    <h2>14</h2>
                    <p>场比赛记录</p>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-md-3">
            <div class="panel panel-default">
                <div class="panel-body">
                    <p class="text-muted">演示球队</p>
                    <h2>8</h2>
                    <p>A、B 两组球队</p>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-md-3">
            <div class="panel panel-default">
                <div class="panel-body">
                    <p class="text-muted">演示场馆</p>
                    <h2>4</h2>
                    <p>三国场馆数据</p>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-md-3">
            <div class="panel panel-default">
                <div class="panel-body">
                    <p class="text-muted">已有功能</p>
                    <h2>2</h2>
                    <p>赛程查询与积分榜</p>
                </div>
            </div>
        </div>

    </div>

    <!-- 功能入口 -->
    <h2>赛事服务</h2>

    <div class="row">

        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3>比赛赛程</h3>
                </div>

                <div class="panel-body">
                    <p>
                        浏览全部比赛，按小组和比赛状态筛选，
                        查看比赛时间、比分及场馆信息。
                    </p>

                    <?= Html::a(
                        '进入比赛列表 →',
                        ['/match/index'],
                        ['class' => 'btn btn-primary']
                    ) ?>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3>小组积分榜</h3>
                </div>

                <div class="panel-body">
                    <p>
                        查看球队比赛场次、胜平负、进失球、
                        净胜球和积分排名。
                    </p>

                    <?= Html::a(
                        '查看积分榜 →',
                        ['/match/standings'],
                        ['class' => 'btn btn-primary']
                    ) ?>
                </div>
            </div>
        </div>

    </div>

    <!-- 项目说明 -->
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3>关于本站</h3>
        </div>

        <div class="panel-body">
            <p>
                本网站是基于 Yii2 和 MySQL 开发的
                2026 世界杯主题课程项目。
            </p>

            <ul>
                <li>使用数据库保存球队、场馆和比赛信息。</li>
                <li>支持按小组及比赛状态查询赛程。</li>
                <li>根据已结束的小组赛自动计算演示积分榜。</li>
                <li>后续将继续开发球队详情、场馆信息及后台管理功能。</li>
            </ul>

            <p class="text-muted">
                注意：当前比赛、球队及比分均为演示数据，
                不代表官方赛事信息。
            </p>
        </div>
    </div>

</div>
