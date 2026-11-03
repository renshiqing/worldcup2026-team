# C 部分（后台管理与统计）交付说明

> 代码基线：团队仓库 `renshiqing/worldcup2026-team` main 分支（2026-10-25，`f2e1d2b`）。
> C 的改动尚未提交到 GitHub。

---

## 一、合并到你的项目

1. clone 团队仓库，例如 `D:\worldcup2026-team`（或让 A 发 zip 解压）。
2. 解压本包 `C-后台模块-源码.zip`，把里面的目录**覆盖**到项目根目录。
3. 复制 `vendor`（如果手头有能跑的副本；vendor 不进 Git，可省去 composer 下载）。
4. 建库、迁移、创建管理员：

```bat
php init
php yii migrate
php yii admin/create admin admin@example.com 123456
```

5. 启动后台：

```bat
php -S 127.0.0.1:8082 -t backend\web
```

6. 打开 `http://127.0.0.1:8082/index.php?r=site/login`，账号 `admin / 123456`。

### 数据库配置

`common/config/main-local.php` 的 `dsn` 要指向你的库，例如：

```php
'dsn' => 'mysql:host=127.0.0.1;port=3307;dbname=worldcup2026_team',
'username' => 'root',
'password' => '',
```

`php init` 默认生成 3306 / `yii2advanced`，需要手动改。

### composer 注意

团队仓库的 `composer.lock` 是旧环境生成的，在 PHP 8.2 上直接 `composer install`
会报 `doctrine/lexer requires php ^7.2`。建议让 A 跑一次 `composer update` 提交新 lock；
本机临时可用 `composer install --ignore-platform-reqs`。

---

## 二、本机已有可运行副本（只用于看效果/截图）

目录：`D:\dsh\repo\worldcup2026-team-main`

- 已包含 `vendor`、测试库配置、演示数据、C 的全部模块
- 双击 `本地演示-启动后台.bat` 即可启动（MariaDB 3307 + PHP 8082）
- 登录：`admin / 123456`

> 这个 bat 只适用于这台电脑，不要提交进团队仓库。

---

## 三、这个交付里改了什么

**新增模块**

- 统计概览（四个数字 + 小组柱状图 + 近 7 天留言趋势 + 最近留言/新闻）
- 球队 CRUD
- 球员 CRUD（扩展）
- 新闻 CRUD（草稿/发布/撤回）
- 留言审核、回复、删除
- 团队资料编辑（课程要求）
- 团队成员管理（课程要求）
- 球队/留言 CSV 导出
- 操作日志（谁在何时改了什么）

**新增文件**

- `common/models/FootballTeam.php`、`TournamentGroup.php`、`NewsArticle.php`、`FanMessage.php`
- `common/models/TeamProfile.php`、`TeamMember.php`、`OperationLog.php`
- `backend/components/AdminController.php`（登录 + 管理员双重校验）
- `backend/components/OperationLogger.php`、`CsvExporter.php`
- `backend/controllers/TeamController.php`、`PlayerController.php`、`NewsController.php`、`MessageController.php`、`TeamProfileController.php`、`TeamMemberController.php`、`OperationLogController.php`
- `backend/views/team/`、`player/`、`news/`、`message/`、`team-profile/`、`team-member/`、`operation-log/`
- `console/controllers/AdminController.php`（创建/提升管理员）
- `console/migrations/m261101_120000_add_admin_flag_and_news_author.php`
- `console/migrations/m261101_120100_add_reply_to_fan_message.php`
- `console/migrations/m261101_130000_create_team_profile_tables.php`
- `console/migrations/m261101_140000_create_operation_log_table.php`
- `docs/C-后台实现文档.md`

**修改**

- `common/models/User.php`：加 `is_admin` 和 `isAdmin()`
- `common/models/TeamPlayer.php`：加 `team` 关联
- `backend/controllers/SiteController.php`：首页改为统计概览
- `backend/views/site/index.php`：统计页面
- `backend/views/site/login.php`：中文登录页
- `backend/views/layouts/main.php`：内容管理/团队设置/系统管理三组菜单
- `backend/config/main.php`：后台语言/时区
- `backend/web/css/site.css`：统计卡片、柱状图、趋势图样式
- `data/install.sql`：按最新结构重新导出（15 张表、13 条迁移记录）

每个 PHP 文件都加了统一注释头（项目、文件、功能、作者、日期）。

---

## 四、需要和队友对齐

### 1. 告诉 A：`data/install.sql` 已同步

C 新增了四个迁移（`user.is_admin`、`news_article.author_id`、
`fan_message.admin_reply/replied_at`、`team_profile`、`team_member`、
`operation_log`）。`data/install.sql` 已按最新结构重新导出，A 合并后确认一次即可。

### 2. 告诉 B：前台直接复用 C 的模型

```php
// 前台新闻：只查已发布
NewsArticle::findPublished()->orderBy(['published_at' => SORT_DESC])->all();

// 前台提交留言：默认待审核
$message->status = FanMessage::STATUS_PENDING;

// 前台展示：只显示 approved，并展示 admin_reply
FanMessage::find()->where(['status' => FanMessage::STATUS_APPROVED])->all();

// 前台团队主页：团队资料 + 成员
$profile = TeamProfile::current();
$members = TeamMember::ordered();
```

当前仓库还没有 B 的前台新闻页、留言页和团队主页，需要 B 完成后做最终联调。

---

## 五、验收截图清单

1. 后台登录页
2. 统计概览（四个数字 + 柱状图）
3. 球队列表
4. 新增球队表单
5. 球队详情
6. 球员列表
7. 新闻列表（草稿/已发布）
8. 新闻编辑页
9. 留言管理列表
10. 留言审核与回复页
11. 团队资料编辑页
12. 成员管理列表
13. 操作日志列表
14. 导出的 CSV（Excel 打开）
15. Git 提交记录
16. 代码目录

---

## 六、截图地址（本机运行后）

统一前缀：`http://127.0.0.1:8082/index.php?r=`

| 截图 | 地址 |
|---|---|
| 登录页 | `site/login` |
| 统计概览 | `site/index` |
| 球队列表 / 新增 / 详情 | `team/index`、`team/create`、`team/view&id=1` |
| 球员列表 | `player/index` |
| 新闻列表 / 编辑 | `news/index`、`news/update&id=1` |
| 留言列表 / 审核 | `message/index`、`message/view&id=1` |
| 团队资料 | `team-profile/index` |
| 成员管理 | `team-member/index` |
| 操作日志 | `operation-log/index` |
| 导出球队/留言 CSV | `team/export`、`message/export` |

登录后浏览器会保持会话，逐个打开截图即可。

---

## 七、自测结果

26 项全部通过：未登录跳转、管理员登录、菜单、球队/球员/新闻/留言/团队资料/成员的增删改查、
外键删除保护、新闻发布/撤回、留言通过/回复、留言趋势、CSV 导出、操作日志、统计数字、
普通用户 403、404 友好页、退出登录、install.sql 新库导入。详见 `docs/C-后台实现文档.md` 第七节。
