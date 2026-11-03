# C 部分实现文档：后台管理与统计

> 课程：互联网数据库开发　项目：2026 世界杯赛事信息与球迷交流平台
> 负责人：C（后台管理与统计）　整理日期：2026-11-01
> 代码基线：GitHub `renshiqing/worldcup2026-team` main 分支，提交 `f2e1d2b`（2026-10-25）

---

## 一、我负责的范围

| 模块 | 完成内容 | 对应验收 |
|---|---|---|
| 后台入口与菜单 | 管理员登录、未登录跳转、内容管理/团队设置/系统管理三组菜单 | 未登录不能进管理页；登录后菜单可点 |
| 统计概览 | 球队/比赛/新闻/待审核留言四个数字 + 小组球队柱状图 + 最近记录 | 新增数据后刷新数字 +1 |
| 球队 CRUD | 列表/搜索/筛选/新增/查看/编辑/删除 | 四种操作成功并截图 |
| 球员 CRUD（扩展） | 按球队筛选、号码、位置、国籍、队长 | 新增/编辑/删除成功 |
| 新闻 CRUD | 新建、编辑、草稿/发布切换、删除 | 发布后前台可见；撤回后前台消失 |
| 留言处理 | 查看、通过/拒绝、回复、删除 | 前台提交→后台审核→状态变化 |
| 团队资料 | 团队名称、口号、简介、联系邮箱 | 保存后前台可读取 |
| 团队成员 | 姓名、学号、分工、简介、展示顺序 | 增删改查成功 |
| 留言趋势 | 近 7 天每天的留言数量柱状图 | 与数据库记录一致 |
| CSV 导出 | 球队、留言按当前筛选条件导出 CSV | Excel 打开中文正常 |
| 操作日志 | 记录谁在何时新增/修改/删除/审核/发布/导出 | 操作后日志出现记录 |
| 交付物 | 实现文档、15 页展示 PPT、代码目录、Git 提交 | 截图 + 提交记录 |

> 课程要求“后台包括团队/个人资料编辑”，原计划只写了球队/新闻/留言，本实现补上了
> `team_profile`（团队资料）和 `team_member`（成员管理）两个模块。

---

## 二、运行环境与启动方式

### 2.1 环境

- Windows + PHP 8.2（`D:\php\php\php.exe`）
- MariaDB 10.4（XAMPP，端口 3307）或 MySQL 8
- 数据库名：`worldcup2026_team`
- 后台端口：8082（前台可用 8081）

### 2.2 首次安装（在项目根目录）

```bat
composer install
php init                     :: 选 0 Development，输入 yes
php yii migrate              :: 建表 + 演示数据 + C 的三个迁移
php yii admin/create admin admin@example.com 123456
php -S 127.0.0.1:8082 -t backend\web
```

浏览器打开：`http://127.0.0.1:8082/index.php?r=site/login`
账号：`admin`　密码：`123456`

> `php yii admin/create` 是 C 新增的控制台命令，用来创建/重置管理员账号。
> 普通注册用户 `is_admin = 0`，登录后也不能进入后台。

### 2.3 常见问题

1. **composer 报 `doctrine/lexer requires php ^7.2`**
   仓库里的 `composer.lock` 是旧环境生成的，PHP 8.2 直接 `install` 会失败。
   解决：让 A 执行一次 `composer update` 并提交新 lock；或本机临时用
   `composer install --ignore-platform-reqs`。
2. **后台页面报 “directory is not writable”**
   是 `backend/web/assets` 目录权限问题，给 Web 进程写权限即可。
3. **后台登录后 403**
   当前账号 `is_admin` 不是 1。用 `php yii admin/promote 用户名` 提升。

---

## 三、数据库改动

C 的迁移都只新增内容，不改 A 已建好的表结构，可重复执行。三个迁移在 `console/migrations/`：

### 3.1 `m261101_120000_add_admin_flag_and_news_author`

| 表 | 字段 | 类型 | 用途 |
|---|---|---|---|
| `user` | `is_admin` | smallint，默认 0 | 区分管理员和普通用户 |
| `news_article` | `author_id` | int，可空，外键→user.id | 记录新闻发布人 |

### 3.2 `m261101_120100_add_reply_to_fan_message`

| 表 | 字段 | 类型 | 用途 |
|---|---|---|---|
| `fan_message` | `admin_reply` | text，可空 | 管理员回复内容 |
| `fan_message` | `replied_at` | int，可空 | 回复时间戳 |

### 3.3 `m261101_130000_create_team_profile_tables`

| 表 | 主要字段 | 用途 |
|---|---|---|
| `team_profile` | team_name、slogan、description、contact_email | 课程团队基本资料（单行） |
| `team_member` | name、student_id、role、bio、avatar、display_order | 三名成员的资料 |

新增后数据库共 **15 张表**，满足课程“> 10 张表”的要求。`data/install.sql` 已同步为
15 张表、13 条迁移记录，新库直接导入即可。

### 3.4 `m261101_140000_create_operation_log_table`

| 表 | 主要字段 | 用途 |
|---|---|---|
| `operation_log` | username、action、target_type、target_id、summary、ip、created_at | 后台操作审计（只追加） |

---

## 四、代码结构

```
common/models/
  FootballTeam.php        球队模型（football_team）
  TournamentGroup.php     小组模型（tournament_group），提供下拉选项
  NewsArticle.php         新闻模型（news_article），草稿/发布、自动时间
  FanMessage.php          留言模型（fan_message），状态常量
  TeamProfile.php         团队资料模型（team_profile）
  TeamMember.php          团队成员模型（team_member）
  OperationLog.php        操作日志模型（operation_log）
  TeamPlayer.php          增加 team 关联（球员后台使用）
  User.php                增加 is_admin 字段和 isAdmin()
backend/components/
  AdminController.php     后台基类：登录 + is_admin 双重校验
  OperationLogger.php     统一写入操作日志
  CsvExporter.php         CSV 导出（BOM + 公式注入防护）
backend/controllers/
  SiteController.php      统计首页、近 7 天留言趋势
  TeamController.php      球队 CRUD + CSV 导出
  PlayerController.php    球员 CRUD
  NewsController.php      新闻 CRUD + 发布/撤回
  MessageController.php   留言审核/回复/删除 + CSV 导出
  TeamProfileController.php 团队资料编辑
  TeamMemberController.php  团队成员 CRUD
  OperationLogController.php 操作日志查询
backend/views/
  layouts/main.php        后台导航（内容管理、团队设置、系统管理）
  site/index.php          统计概览
  site/login.php          管理员登录
  team/                    index、view、_form、create、update
  player/                  index、view、_form、create、update
  news/                    index、view、_form、create、update
  message/                 index、view
  team-profile/            index
  team-member/             index、view、_form、create、update
  operation-log/           index、view
backend/web/css/site.css  统计卡片、柱状图、筛选栏样式
console/controllers/
  AdminController.php     admin/create、admin/promote 命令
console/migrations/       四个新迁移
data/install.sql          已同步新结构（15 张表、13 条迁移）
docs/                     本实现文档
```

每个 PHP 文件都有统一注释头（项目、文件、功能、作者、日期）；
每个模块都包含 Model、Controller、View 三层。

---

## 五、功能说明

### 5.1 后台访问控制

`backend/components/AdminController.php` 用 Yii 的 `AccessControl`：

1. `roles => ['@']`：必须登录；
2. `matchCallback`：`user.is_admin === 1`；
3. 未登录 → 跳转 `site/login`，并记住原地址；
4. 已登录但不是管理员 → 403，提示“当前账号不是管理员”。

> 不使用“注册用户都能进后台”的默认行为，避免普通用户越权。

### 5.2 统计概览

后台首页 `site/index` 实时查询：

- 球队总数：`FootballTeam::find()->count()`
- 比赛总数：`football_match` 表 `COUNT(*)`
- 新闻总数：`NewsArticle::find()->count()`
- 待审核留言：`fan_message` 中 `status = 'pending'`
- 小组球队分布：`tournament_group LEFT JOIN football_team` 分组统计，用 CSS 条形图展示
- 最近 5 条留言、最近 5 条新闻

所有数字来自数据库，新增/删除后刷新即变化。

页面还展示球员总数、已通过留言、草稿新闻、已分组球队，以及留言状态分布
（待审核/已通过/已拒绝）；右下角“系统信息”显示数据库名、表数量、迁移数量、
Yii 和 PHP 版本。小组柱状图只显示有球队的小组，避免空组把页面撑得很空。

### 5.3 球队管理

- 字段严格按实际表：`name`、`country`、`group_id`、`created_at`
- 支持按名称/国家搜索、按小组筛选
- 删除用 POST + CSRF；球队被比赛引用时外键 `RESTRICT` 会阻止删除，页面提示“已被比赛数据引用”，不会 500

### 5.4 球员管理（扩展）

- 复用 A 的 `team_player` 表，字段：球队、姓名、号码、位置、国籍、生日、队长
- 列表按球队筛选、按姓名搜索
- 同一球队号码唯一（模型唯一校验）
- 空号码/生日会转成 NULL，避免唯一索引冲突

### 5.5 新闻管理

- 字段：`title`、`category`、`summary`、`content`、`status`、`published_at`、`author_id`
- `status`：`draft` 草稿 / `published` 已发布
- 发布时自动写 `published_at`；撤回草稿时清空 `published_at`
- 列表支持标题/摘要搜索、状态筛选、分类筛选
- 发布人自动取当前登录管理员，不需要手填

### 5.6 留言处理

- 列表支持昵称/内容搜索、状态筛选
- 详情页可改状态（待审核/已通过/已拒绝）、填写回复
- 填了回复且状态仍为待审核时，自动改为已通过并记录回复时间
- 提供“通过审核 / 拒绝”快捷按钮
- 删除用 POST + CSRF

### 5.7 团队资料与成员管理

- **团队资料**：团队名称、口号、简介、联系邮箱，单行记录；前台团队主页读取 `TeamProfile::current()`
- **成员管理**：姓名、学号、分工、简介、头像地址、展示顺序，完整增删改查
- 对应课程要求“后台包括团队/个人资料编辑”
- 前台展示建议：`TeamMember::ordered()` 按展示顺序输出

### 5.8 留言趋势、CSV 导出与操作日志

- **近 7 天留言趋势**：后台首页按天统计最近 7 天留言数，用柱状图展示，今天在最右侧
- **CSV 导出**：球队列表、留言列表可按当前筛选条件导出 CSV；带 UTF-8 BOM，Excel 打开中文不乱码；对 `= + - @` 开头的单元格做公式注入防护
- **操作日志**：记录操作人、操作类型（新增/修改/删除/审核/发布/撤回/导出）、对象类型与编号、说明、IP、时间；日志只追加，不提供修改和删除入口
- 日志写入用 `OperationLogger::log()`，内部捕获异常，日志失败不影响主业务

---

## 六、安全设计（答辩可讲）

| 风险 | 处理 |
|---|---|
| 普通用户进后台 | `is_admin` 字段 + AccessControl 双重校验 |
| CSRF | 所有写操作走 POST，Yii 表单自带 `_csrf-backend` |
| XSS | 所有用户输入输出用 `Html::encode()`，新闻正文 `nl2br(Html::encode())` |
| 误删有比赛引用的球队 | 数据库外键 RESTRICT + 捕获 `IntegrityException` 给友好提示 |
| 删除被 GET 触发 | `VerbFilter` 限制 delete/status/publish 只能 POST |
| 密码泄露 | 不把密码写进代码；管理员用命令行创建，密码哈希存库 |

---

## 七、本地实测记录（2026-11-01）

| # | 测试项 | 结果 |
|---|---|---|
| 1 | 未登录访问 `/team/index` | 302 跳转登录 ✔ |
| 2 | 管理员登录 | 302 进入后台 ✔ |
| 3 | 统计首页 | 200，四个数字 + 柱状图 ✔ |
| 4 | 后台菜单（内容管理/团队设置/系统管理） | 下拉菜单正常 ✔ |
| 5 | 球队/新闻/留言列表 | 200，演示数据正常显示 ✔ |
| 6 | 新增球队 | 成功，详情页显示 ✔ |
| 7 | 编辑球队 | 成功，名称更新 ✔ |
| 8 | 删除球队 | 成功，数据库记录为 0 ✔ |
| 9 | 删除被比赛引用的球队 | 友好报错，记录保留 ✔ |
| 10 | 新建并发布新闻 | 成功，状态“已发布”，发布人 admin ✔ |
| 11 | 撤回为草稿 | 成功，状态“草稿” ✔ |
| 12 | 留言通过审核 | 成功，状态“已通过” ✔ |
| 13 | 留言回复 | 成功，回复内容和时间显示 ✔ |
| 14 | 统计数字 | 球队 8、比赛 14、新闻 3、待审核 1，与数据库一致 ✔ |
| 15 | 普通用户访问后台 | 403 拒绝 ✔ |
| 16 | 不存在的球队 id | 404 友好页 ✔ |
| 17 | 退出登录后再访问后台 | 302 跳转登录 ✔ |
| 18 | 保存团队资料 | 成功，内容更新 ✔ |
| 19 | 新增/编辑/删除团队成员 | 全部成功 ✔ |
| 20 | 新增球员并查看 | 成功，显示所属球队 ✔ |
| 21 | 删除球员 | 成功，数据库记录为 0 ✔ |
| 22 | 导入 install.sql 到新库 | 15 张表、13 条迁移记录、数据一致 ✔ |
| 23 | 近 7 天留言趋势 | 7 列柱状图，与数据库一致 ✔ |
| 24 | 导出球队 CSV | 200，内容含球队，Excel 中文正常 ✔ |
| 25 | 导出留言 CSV | 200，含留言与管理员回复 ✔ |
| 26 | 新增/删除球队后查看操作日志 | 日志出现对应记录 ✔ |

---

## 八、截图清单（提交前补齐）

1. 后台登录页
2. 统计概览（四个卡片 + 柱状图）
3. 球队列表
4. 新增球队表单
5. 球队详情
6. 球员列表
7. 新闻列表（含草稿/已发布）
8. 新闻编辑页
9. 留言管理列表
10. 留言审核与回复页
11. 团队资料编辑页
12. 成员管理列表
13. Git 提交记录
14. 代码目录（backend/controllers、views、common/models）
15. 操作日志列表
16. 导出的球队/留言 CSV 在 Excel 中打开

---

## 九、与 B 的联调约定

B 做前台新闻、留言、团队主页时，直接复用 C 的模型，避免字段不一致：

```php
use common\models\NewsArticle;
use common\models\FanMessage;
use common\models\TeamProfile;
use common\models\TeamMember;

// 前台新闻列表：只查已发布
$news = NewsArticle::findPublished()
    ->orderBy(['published_at' => SORT_DESC])
    ->all();

// 前台提交留言：默认待审核
$message = new FanMessage();
$message->nickname = $nickname;
$message->message  = $content;
$message->status   = FanMessage::STATUS_PENDING;
$message->created_at = time();
$message->save();

// 前台展示留言：只展示已通过，并显示管理员回复
$list = FanMessage::find()
    ->where(['status' => FanMessage::STATUS_APPROVED])
    ->orderBy(['created_at' => SORT_DESC])
    ->all();

// 前台团队主页：团队资料 + 成员
$profile = TeamProfile::current();
$members = TeamMember::ordered();
```

联调验收：
1. C 发布新闻 → B 刷新前台能看到；C 撤回 → 前台消失。
2. B 提交留言 → C 后台看到 pending → 通过/回复 → B 前台显示回复。
3. C 修改团队资料/成员 → B 前台团队主页同步变化。

---

## 十、已知限制

1. 权限只做了“管理员/非管理员”两级，没有完整 RBAC。
2. 新闻正文是纯文本 textarea，没有富文本编辑器（有意避免 XSS）。
3. 图表是 CSS 条形图，不是 Chart.js；离线也能用，后续要“动态图形”加分可替换。
4. 头像只存图片地址，没有做文件上传。
5. 前台新闻页、留言页、团队主页由 B 负责，需要 B 完成后做最终联调。

---

## 十一、Git 提交建议

```bat
git checkout -b dev-admin

git add console/migrations common/models/User.php
git commit -m "feat(db): 新增管理员标记、新闻作者、留言回复字段"

git add console/migrations/m261101_130000_create_team_profile_tables.php common/models/TeamProfile.php common/models/TeamMember.php
git commit -m "feat(db): 新增团队资料与团队成员表"

git add backend/components backend/controllers/SiteController.php backend/views/site backend/views/layouts backend/web/css
git commit -m "feat(admin): 后台访问控制与统计概览"

git add common/models/FootballTeam.php common/models/TournamentGroup.php backend/controllers/TeamController.php backend/views/team
git commit -m "feat(admin): 球队增删改查"

git add common/models/TeamPlayer.php backend/controllers/PlayerController.php backend/views/player
git commit -m "feat(admin): 球员管理"

git add common/models/NewsArticle.php backend/controllers/NewsController.php backend/views/news
git commit -m "feat(admin): 新闻管理与发布/撤回"

git add common/models/FanMessage.php backend/controllers/MessageController.php backend/views/message
git commit -m "feat(admin): 留言审核与回复"

git add backend/controllers/TeamProfileController.php backend/controllers/TeamMemberController.php backend/views/team-profile backend/views/team-member
git commit -m "feat(admin): 团队资料与成员管理"

git add common/models/OperationLog.php backend/components/CsvExporter.php backend/components/OperationLogger.php backend/controllers/OperationLogController.php backend/views/operation-log console/migrations/m261101_140000_create_operation_log_table.php
git commit -m "feat(admin): 留言趋势、CSV 导出与操作日志"

git add console/controllers/AdminController.php data/install.sql docs
git commit -m "docs: 后台实现文档、install.sql 同步与管理员命令"
```

---

## 十二、展示 PPT

已生成 15 页 `世界杯团队项目展示.pptx`，结构：

1. 封面
2. 项目概述
3. 课程要求对照
4. 系统架构
5. 数据库设计（15 张表）
6. 前台功能（B，截图占位）
7. 后台功能（C，8 个模块）
8. 后台业务流程
9. 统计与可视化
10. 代码结构与 MVC
11. 三人分工与工作量
12. Git 与项目管理（截图占位）
13. 测试与验收
14. 部署与数据恢复
15. 总结

需要补图的位置已用虚线框标出：前台页面、后台操作、留言审核、统计截图、任务看板、Git 记录。

---

## 十三、开发中遇到的问题与解决

| 问题 | 解决办法 |
|---|---|
| 计划文档写的是 `news`、`contact_message`，仓库实际是 `news_article`、`fan_message`，字段也不同 | 以 A 的迁移为准，后台表单只使用实际存在的字段，并在文档中记录差异 |
| `news_article` 没有 `author_id`，`fan_message` 没有回复字段 | 新增两个迁移补字段，并同步重新导出 `data/install.sql` |
| 普通注册用户登录后也能进入后台 | 给 `user` 加 `is_admin`，后台基类同时校验登录状态和管理员标记 |
| 删除被比赛引用的球队触发外键异常，页面 500 | 捕获 `IntegrityException`，提示“已被比赛数据引用，请先处理相关比赛” |
| 新闻状态下拉框传 `prompt => false` 导致 Yii 报错 | 去掉无效选项，状态使用模型默认值 |
| PHP 8.2 下旧 `composer.lock` 安装失败 | 复制可用 `vendor`，或 `composer update` / `composer install --ignore-platform-reqs` |
| 课程要求“团队/个人资料编辑”，原计划没有对应表 | 新增 `team_profile`、`team_member` 两张表和后台模块 |
