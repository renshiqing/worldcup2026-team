# B 部分实现文档：前台页面与用户交互

> 课程：互联网数据库开发　项目：2026 世界杯赛事信息与球迷交流平台
> 负责人：B 张雅程（前台页面与用户交互）　整理日期：2026-11-11
> 代码基线：团队仓库 `main` 分支 `5b61de7`（A 的数据库/比赛模块、C 的后台模块已合并）
> B 的分支：`dev-frontend`

---

## 一、我负责的范围

计划文档给 B 的固定范围是：**前台页面和留言**。本次交付对应如下：

| 模块 | 完成内容 | 对应验收 |
|---|---|---|
| 首页 | 项目主视觉、4 项数据库统计、最新新闻、近期比赛、团队成员、已通过留言、快速入口 | 首页展示项目名称、世界杯主题、三名成员和分工 |
| 新闻前台 | 列表（分类筛选、分页）、详情（正文、发布信息、相关新闻） | 只显示已发布，草稿不出现 |
| 留言前台 | 表单校验、蜜罐、频率限制、成功页、留言墙、“我的留言”状态跟踪 | 提交进数据库；空内容有提示；后台审核后前台状态变化 |
| 团队介绍 | 团队资料、三名成员、三人分工、协作流程、技术栈 | 团队主页可读；成员和分工来自后台 |
| 球队/比赛前台 | 沿用 A 的列表和详情，球队详情补一句话简介，首页做近期比赛摘要 | 球队、比赛页面可观 |
| 页面质量 | 8 项统一导航、空数据提示、中文 404、CSS 响应式 | 导航无死链、文字不重叠、错误链接可读 |
| 中文体验 | 前台 `language = zh-CN`、`timeZone = Asia/Shanghai`，Yii 校验提示为中文 | 表单出错时提示“昵称不能为空”等中文 |
| 文档 | 本实现文档 + 用户操作手册 + `README-B.md` | 用户操作说明 1 份 |
| 数据 | 一条幂等的数据迁移，补团队资料和三名成员 | 全新安装后首页不空 |

> B **不负责**后台 CRUD、留言审核和数据库表结构；审核状态只由 C 的后台修改。

---

## 二、运行环境与启动方式

### 2.1 环境

- Windows + PHP 8.x（需要 `pdo_mysql`、`mbstring`）
- MySQL 8 / MariaDB 10.x，数据库名 `worldcup2026_team`
- 前台端口 8081，后台端口 8082
- 浏览器：Chrome / Edge

### 2.2 首次安装

```bat
composer install --ignore-platform-reqs
php init
php yii migrate
php -S 127.0.0.1:8081 -t frontend\web
php -S 127.0.0.1:8082 -t backend\web
```

- `php init` 选 `0 Development`；
- `common/config/main-local.php` 里把 `dsn` 指向本地数据库；
- 管理后台账号由 C 的命令创建：`php yii admin/create admin admin@example.com 123456`。

### 2.3 入口地址

| 页面 | 地址 |
|---|---|
| 首页 | `http://127.0.0.1:8081/index.php?r=site/index` |
| 新闻列表 | `?r=news/index` |
| 新闻详情 | `?r=news/view&id=1` |
| 留言板 | `?r=message/index` |
| 我的留言 | `?r=message/mine` |
| 团队介绍 | `?r=site/about` |
| 中文 404 | `?r=news/view&id=999` |

---

## 三、数据库与数据来源

### 3.1 原则：不改表结构，只读 A/C 已经建好的表

B 没有新增或修改任何字段，完全复用现有表：

| 表 | 用到的字段 | 用在哪个页面 |
|---|---|---|
| `football_match` | id、stage_id、group_id、home_team_id、away_team_id、stadium_id、kickoff_at、home_score、away_score、status | 首页近期比赛 |
| `match_stage` | stage_name | 首页比赛阶段 |
| `tournament_group` | group_name | 首页小组 |
| `football_team` | id、name、country、group_id | 首页球队链接、统计 |
| `stadium` | id、name、city_id | 首页场馆、统计 |
| `host_city` | name | 首页场馆城市 |
| `news_article` | id、title、summary、content、category、status、published_at、author_id | 新闻列表/详情、首页新闻 |
| `fan_message` | id、nickname、email、subject、message、status、created_at、updated_at、admin_reply、replied_at | 留言板、成功页、我的留言 |
| `team_profile` | team_name、slogan、description、contact_email | 首页团队区、团队介绍 |
| `team_member` | name、student_id、role、bio、display_order | 首页成员卡、团队介绍 |
| `user` | username（新闻发布人） | 新闻详情 |

### 3.2 B 新增的数据迁移

`console/migrations/m261111_120000_seed_team_demo_data.php`

C 的 `m261101_130000` 只建了 `team_profile`/`team_member` 两张表，并插入了一条
“世界杯赛事信息与球迷交流平台”的通用资料，`team_member` 是空表。
如果全新安装后直接打开首页，成员区域会是空的。B 用一条**只写数据、不改结构**的迁移补上：

- `team_profile` 为空 → 插入“越位三剑客 / 代码不越位，需求必进球！”；
- `team_profile` 仍是默认名称 → 更新为课程团队资料；
- 管理员已经改过团队名称 → **不覆盖**；
- `team_member` 为空 → 插入任诗清（组长·数据库与比赛）、张雅程（前台页面与用户交互）、
  钟一成（后台管理与统计）；学号留空，后台可补；
- `team_member` 已有数据 → **不重复插入**。

`down()` 只删除这三名成员，不删除可能被管理员改过的团队资料。

> 用 `data/install.sql` 恢复的库，`migration` 表里没有这条版本号，
> 再执行一次 `php yii migrate` 就会补上，不影响 A 的 15 张表。

---

## 四、代码结构

```
frontend/controllers/
  SiteController.php        首页（数据库统计/新闻/比赛/成员/留言）、团队介绍
  NewsController.php        新闻列表（分类+分页）、新闻详情（相关新闻、发布人）
  MessageController.php     留言板、提交留言、成功页、我的留言
frontend/models/
  FanMessageForm.php        留言表单：继承 FanMessage，加蜜罐和前台校验，待审核初始化
frontend/config/main.php    前台应用名、中文语言、时区、日期格式
frontend/views/
  layouts/main.php          8 项导航、登录/注册/退出、页脚、页面标题
  site/index.php            首页
  site/about.php            团队介绍
  site/_member.php          成员卡片（首页与团队介绍共用）
  site/error.php            中文 404/500
  news/index.php            新闻列表（nav-pills 分类筛选 + LinkPager）
  news/view.php             新闻详情 + 相关新闻
  news/_card.php            新闻卡片
  message/index.php         留言表单 + 留言墙
  message/success.php       留言成功页
  message/mine.php          我的留言
  message/_item.php         单条留言卡片
  team/view.php             球队详情（B 补一句话简介）
frontend/web/css/site.css   前台样式（hero、统计卡、新闻卡、留言卡、成员卡、空状态）
console/migrations/
  m261111_120000_seed_team_demo_data.php
```

每个 PHP 文件都有统一注释头（项目、文件、功能、作者、日期），
MVC 三层都有 B 的代码：Model（FanMessageForm）、Controller（News/Message/Site）、View（news/message/site）。

---

## 五、功能说明

### 5.1 首页：所有数字来自数据库

`SiteController::actionIndex()` 执行四类查询：

```php
$stats = [
    'teams'    => (int) FootballTeam::find()->count(),
    'matches'  => (int) (new Query())->from('{{%football_match}}')->count(),
    'news'     => (int) NewsArticle::findPublished()->count(),
    'stadiums' => (int) (new Query())->from('{{%stadium}}')->count(),
];

$latestNews = NewsArticle::findPublished()
    ->orderBy(['published_at' => SORT_DESC, 'id' => SORT_DESC])
    ->limit(3)
    ->all();

$highlightMatches = $this->createMatchQuery()
    ->where(['m.status' => ['scheduled', 'live']])
    ->orderBy(['m.kickoff_at' => SORT_ASC])
    ->limit(4)
    ->all();
```

- 没有待开赛/进行中的比赛时，自动退回到最近 4 场已结束的比赛，避免首页空着；
- `createMatchQuery()` 统一了“比赛 + 阶段 + 小组 + 双方球队 + 场馆 + 城市”的 join，
  和 A 的比赛列表字段保持一致；
- 成员、团队资料、已通过留言分别来自 `TeamMember::ordered()`、`TeamProfile::current()`
  和 `FanMessage` 的 `approved` 查询；
- 页面上的 4 个统计数字、新闻、比赛、成员、留言全部来自数据库，没有写死的演示数字
  （改版前的首页写死了 14/8/4/2）。

### 5.2 新闻：前台只认 `published`

`NewsController::actionIndex($category)`：

1. 分类只接受 `NewsArticle::categories()` 里的值（赛事新闻/球队动态/公告），
   手改地址传非法分类时自动回到“全部”，不会报错；
2. 基础查询固定为 `NewsArticle::findPublished()`，即 `status = 'published'`；
3. 用 `yii\data\Pagination` 分页，每页 6 条，`LinkPager` 输出上一页/下一页/首页/末页；
4. 列表页显示分类、发布时间、标题、摘要（摘要为空时从正文截 120 字）。

`NewsController::actionView($id)`：

1. 仍然只查 `published`，草稿 id 直接 404，不会泄露未发布内容；
2. 查同分类的 3 条相关新闻；
3. 发布人优先取 `news_article.author_id` 关联的 `user.username`，取不到时显示“编辑部”；
4. 正文用 `nl2br(Html::encode($content))` 输出，保留换行并防 XSS。

> 联调效果：C 在后台发布 → 前台出现；C 撤回为草稿 → 前台消失、直接访问详情显示中文 404。

### 5.3 留言：校验、待审核、成功页

`frontend/models/FanMessageForm.php` 继承 C 的 `FanMessage`，不复制字段：

```php
[['nickname', 'email', 'subject', 'message'], 'trim'],
[['nickname'], 'string', 'min' => 2, 'max' => 80],
[['message'], 'string', 'min' => 5, 'max' => 2000],
[['website'], function ($attribute) {   // 蜜罐：正常用户看不到
    if ($this->$attribute !== null && $this->$attribute !== '') {
        $this->addError($attribute, '检测到异常提交，请刷新页面后重试。');
    }
}],
```

保存逻辑：

```php
public function saveMessage()
{
    $now = time();
    $this->status = self::STATUS_PENDING;   // 前台提交一律待审核
    $this->created_at = $now;
    $this->updated_at = $now;
    $this->admin_reply = null;
    $this->replied_at = null;

    return $this->save(false);              // 控制器已经 validate() 过
}
```

`MessageController::actionCreate()` 的处理顺序：

1. `load()` + `validate()`：失败时回到留言板，字段下方显示中文错误，已填内容保留；
2. 10 秒内再次提交 → 提示“留言提交太频繁”；
3. `saveMessage()` 成功 → 把留言编号记到会话（最近 20 条）→ `redirect(['success'])`；
4. 成功页从会话取最近一次编号，显示留言编号、昵称、主题、提交时间、状态“待审核”；
5. 直接访问 `message/success` 但没有提交记录 → 自动回到留言板，不会显示别人的留言。

留言板右侧只查 `status = approved` 的留言，带管理员回复；空状态提示“留言墙还是空的”。

### 5.4 我的留言：证明审核状态变化

`MessageController::actionMine()` 从会话取出本浏览器提交过的编号：

```php
$ids = Yii::$app->session->get(self::SESSION_IDS, []);
$messages = empty($ids) ? [] : FanMessage::find()
    ->where(['id' => $ids])
    ->orderBy(['created_at' => SORT_DESC, 'id' => SORT_DESC])
    ->all();
```

- 显示昵称、时间、状态标签（待审核/已通过/已拒绝）、留言正文、管理员回复和回复时间；
- 只显示本会话提交的留言，最多 20 条，不暴露其他球迷的待审核内容；
- C 在后台改成“已通过”并回复后，B 刷新 `message/mine` 就能看到变化，
  这就是计划文档要求的“前台提交 → 后台审核 → 前台显示”闭环。

### 5.5 团队介绍

`SiteController::actionAbout()` 读取 `TeamProfile::current()` 和 `TeamMember::ordered()`，
页面包含：

1. 团队名称、口号、简介（后台“团队资料”维护）；
2. 4 个统计数字：成员数、球队数、比赛数、已通过留言数；
3. 三名成员卡：头像取姓名首字、姓名、分工、学号、个人简介；
4. 三人分工说明（A 数据库与比赛 / B 前台与留言 / C 后台与统计）；
5. 协作流程（先定数据库 → 前后台并行 → 联调 → 统一合并）；
6. 技术栈和本页对应的代码目录。

### 5.6 导航、空状态与错误页

- 导航固定 8 项：首页、比赛赛程、小组积分榜、球队信息、新闻资讯、球迷留言、场馆信息、团队介绍，
  全部对应真实控制器动作；
- 所有列表都有空状态：新闻“暂无新闻”、留言“留言墙还是空的”、成员“成员信息尚未录入”、
  比赛“暂时没有比赛数据”；
- `site/error.php` 改成中文：说明可能原因（地址错误/数据删除/新闻撤回），
  并提供首页、赛程、球队、新闻、留言入口；
- 窄屏（768～1100px）收紧导航字号，避免 8 项菜单换行压住内容；手机宽度自动折叠为汉堡菜单。

---

## 六、页面流程说明

```
首页 site/index
 ├─ 比赛赛程 match/index ──► 比赛详情 match/view&id  ──► 球队详情 team/view&id
 │      └─ 小组积分榜 match/standings
 ├─ 球队信息 team/index ──► 球队详情 team/view&id ──► 球员详情 player/view&id
 ├─ 新闻资讯 news/index ──► 新闻详情 news/view&id ──► 相关新闻 news/view&id
 ├─ 球迷留言 message/index
 │      ├─ 提交成功 message/success
 │      ├─ 我的留言 message/mine（查看审核状态、管理员回复）
 │      └─ 留言墙（只显示 approved）
 ├─ 场馆信息 stadium/index
 └─ 团队介绍 site/about
```

错误路径：

```
不存在的球队/比赛/新闻 id → site/error（中文 404 + 返回入口）
新闻已撤回为草稿         → site/error（提示“这篇新闻不存在，或者已经被撤回为草稿”）
直接访问 message/success → 302 回 message/index
```

---

## 七、安全设计（答辩可讲）

| 风险 | 处理 |
|---|---|
| XSS | 所有用户输入输出都经过 `Html::encode()`；留言/回复/新闻正文用 `nl2br()` 保留换行 |
| CSRF | 留言表单用 `yii\bootstrap\ActiveForm`，自动带 `_csrf-frontend`；Yii 默认校验 |
| 通过 GET 写入留言 | `VerbFilter` 限制 `message/create` 只接受 POST |
| 机器人刷留言 | 隐藏蜜罐字段 `website` + 同一会话 10 秒提交间隔 |
| 待审核留言被公开 | 留言墙和首页只查 `status = approved`；`my` 页只查本会话编号 |
| 新闻草稿泄露 | 列表和详情都基于 `findPublished()`，草稿 id 返回 404 |
| 越权审核 | 前台没有任何修改留言状态的入口，审核只在 C 的后台 |
| 手改分类参数 | 分类参数用 `isset($categories[$category])` 白名单校验，非法值回落到“全部” |

---

## 八、自测记录

### 8.1 已完成：静态检查（本工作区无 PHP 运行时）

| # | 检查项 | 方法 | 结果 |
|---|---|---|---|
| 1 | PHP 语法解析 | 用 phply 语法分析器逐文件解析（`::class`、`??` 做等价替换后），覆盖 19 个 B 相关文件 | 通过 |
| 2 | PHP 结构（括号、字符串、注释、`<?php`/`?>` 标签） | 自写词法扫描脚本 | 通过 |
| 3 | 路由一致性 | 提取视图里的 `['/controller/action']`，与控制器方法比对（20 条路由） | 通过 |
| 4 | 视图渲染目标 | 检查 `$this->render()` 对应的视图文件都存在（含 `_member`、`_card`、`_item`） | 通过 |
| 5 | 带参路由 | `news/view`、`team/view`、`match/view` 等链接都传了必填参数 | 通过 |
| 6 | 类引用 | 新文件里 `Html`/`LinkPager`/`FanMessageForm` 等都有 `use` 引入 | 通过 |
| 7 | HTML 标签平衡 | 统计 13 个视图的成对标签开闭数量 | 通过 |
| 8 | 字段一致性 | 视图/控制器只使用 `data/install.sql` 中存在的列 | 通过 |

### 8.2 待本地执行：运行验证清单

> 本工作区（clone 下来的仓库）没有安装 PHP，无法启动 Yii 应用，
> 所以下面的浏览器实测需要在装有 PHP 8 + MySQL 的机器上执行并打勾。

| # | 测试项 | 预期 | 结果 |
|---|---|---|---|
| 1 | 首页 `site/index` | 200，4 个数字与数据库一致 | □ |
| 2 | 后台新增一支球队，刷新首页 | 球队数字 +1 | □ |
| 3 | 新闻列表 `news/index` | 只显示 3 条已发布新闻 | □ |
| 4 | 后台发布一条新闻，刷新前台 | 显示新新闻 | □ |
| 5 | 后台撤回为草稿，刷新前台 | 新闻消失 | □ |
| 6 | 访问草稿的 `news/view&id=` | 中文 404，无 PHP 报错 | □ |
| 7 | 新闻分类筛选 | 只显示该分类，非法分类回落全部 | □ |
| 8 | 留言板空内容提交 | 显示“留言内容不能为空” | □ |
| 9 | 昵称 1 个字提交 | 显示“昵称至少 2 个字符” | □ |
| 10 | 正常提交留言 | 跳转成功页，显示“待审核”，数据库新增记录 | □ |
| 11 | 10 秒内连续提交 | 提示“提交太频繁” | □ |
| 12 | 后台留言管理看到该留言 | 状态 pending | □ |
| 13 | 后台通过并回复 | 留言墙出现该留言和回复 | □ |
| 14 | 刷新 `message/mine` | 状态“待审核”→“已通过”，显示回复 | □ |
| 15 | 直接访问 `message/success` | 302 回留言板 | □ |
| 16 | 团队介绍 `site/about` | 团队名称、口号、3 名成员 | □ |
| 17 | 后台修改团队资料/成员 | 前台首页和团队介绍同步变化 | □ |
| 18 | 不存在的球队/比赛 id | 中文 404 + 返回入口 | □ |
| 19 | 首页逐个点 8 项导航 | 全部 200，无死链 | □ |
| 20 | 手机宽度（375px） | 导航折叠、卡片单列、文字不重叠 | □ |

---

## 九、与 A、C 的联调约定

1. **字段以 A 的迁移为准**：B 没有新增字段；新闻用 `news_article`，留言用 `fan_message`，
   不按计划文档里的旧表名 `news`、`contact_message` 写代码。
2. **复用 C 的模型**：`NewsArticle::findPublished()`、`FanMessage::STATUS_*`、
   `TeamProfile::current()`、`TeamMember::ordered()`，避免前后台字段不一致。
3. **审核只走后台**：B 的前台不提供改状态入口；C 的后台通过/拒绝后，B 的
   `message/mine` 和留言墙自动反映。
4. **团队资料由 C 维护**：B 只读；C 改完刷新前台即可，无需改代码。
5. **合并顺序**：A 把 `dev-frontend` 合并到 `main` 后，从干净环境重新执行
   `php yii migrate` 并验证前台，B 再补截图。

---

## 十、已知限制

1. 当前工作区没有 PHP/MySQL 运行环境，页面实测和截图需要本地补；
2. “我的留言”基于浏览器会话，换浏览器看不到历史记录；
3. 留言没有图形验证码，用蜜罐 + 10 秒间隔做最低限度的防刷；
4. 新闻正文是纯文本，没有富文本编辑器；
5. 球队表没有简介、图片字段，详情页的简介由名称/国家/小组拼出；
6. 前台没有做会员中心、收藏、评论回复等扩展功能（计划文档明确暂不做）。

---

## 十一、Git 提交

B 的代码在 `dev-frontend` 分支，按模块分 6 次提交：

| # | 提交信息 | 内容 |
|---|---|---|
| 1 | `feat(db): 补充团队资料与三名成员的演示数据` | `console/migrations/m261111_120000_seed_team_demo_data.php` |
| 2 | `feat(frontend): 新闻列表与详情页` | `NewsController`、`views/news/*` |
| 3 | `feat(frontend): 留言表单、成功页与我的留言` | `FanMessageForm`、`MessageController`、`views/message/*` |
| 4 | `feat(frontend): 首页与团队介绍改为数据库驱动` | `SiteController`、`views/site/index.php`、`about.php`、`_member.php` |
| 5 | `style(frontend): 统一导航、页面样式与中文错误页` | `layouts/main.php`、`site.css`、`error.php`、`team/view.php`、`config/main.php` |
| 6 | `docs: B 前台实现文档、用户操作手册与交付说明` | `README-B.md`、`docs/*` |

提交后执行 `git log --oneline --graph dev-frontend` 截图，作为 B 的提交证据。


