# B 部分（前台页面与用户交互）交付说明

> 课程：互联网数据库开发　项目：2026 世界杯赛事信息与球迷交流平台
> 负责人：B 张雅程（前台页面与用户交互）　整理日期：2026-11-11
> 代码基线：团队仓库 `main` 分支 `5b61de7`（A 的数据库/比赛模块、C 的后台模块已合并）
> B 的分支：`dev-frontend`

本交付只做前台页面和用户交互，不修改 A 的数据库结构，也不改 C 的后台控制器。
所有展示数据都从数据库读取，后台发布/审核后刷新前台即可看到变化。

---

## 一、这次完成了什么

| 计划文档要求 | 完成情况 |
|---|---|
| 首页和团队介绍：项目名称、世界杯主题、三名成员和分工 | 首页改为数据库驱动：4 项统计、最新新闻、近期比赛、团队成员、已通过留言；导航可到球队/比赛/新闻/留言 |
| 球队页面 | 沿用 A 的列表，补充球队详情页的一句话简介；空数据提示保留 |
| 比赛页面 | 沿用 A 的列表和详情，首页增加“近期比赛”摘要 |
| 新闻页面：列表和详情，只显示已发布 | 新增 `NewsController` + 列表/详情/卡片视图，支持分类筛选、分页、相关新闻；草稿不出现在前台 |
| 留言功能：填写姓名和内容、成功和错误提示、进入数据库 | 新增留言表单（姓名/邮箱/主题/内容）、校验提示、蜜罐防刷、10 秒提交间隔、成功页、留言墙、“我的留言”状态跟踪 |
| 页面质量：导航一致、空数据提示、错误链接可读 | 统一 8 项导航、中文错误页、所有列表都有空数据提示、统一卡片/表格样式 |
| 用户手册、页面截图 | 完成实现文档与用户操作手册；截图清单和地址已列在第五节，需本地启动后补拍 |

### 新增的前台页面

| 页面 | 地址（`r=` 参数） |
|---|---|
| 首页（改版） | `site/index` |
| 团队介绍 | `site/about` |
| 新闻列表 | `news/index`（可加 `&category=赛事新闻`） |
| 新闻详情 | `news/view&id=1` |
| 球迷留言板 | `message/index` |
| 留言提交成功页 | `message/success`（提交后跳转） |
| 我的留言 | `message/mine` |

---

## 二、合并到项目并运行

### 1. 合并代码

```bat
git checkout main
git pull
git merge dev-frontend
```

B 的改动都在 `frontend/`、`console/migrations/m261111_120000_seed_team_demo_data.php`
和 `docs/` 下，不和 A 的 `common/`、C 的 `backend/` 冲突。

### 2. 安装依赖与初始化

> **老师软件包版本**：XAMPP 8.2.12（PHP 8.2.12 + MariaDB 10.4.32 + Apache 2.4.58）
> + Yii advanced 2.0.32。本机 XAMPP 装在 `C:\xampp`，PHP 不在 PATH；
> XAMPP 的 MariaDB 跑在 **3307**（3306 被系统 MySQL 8.0 占用）；
> Composer 在 `..\tools\composer.phar`。核对详情见 `docs/B-软件包核对报告.md`。

```bat
set "PHP=C:\xampp\php\php.exe"
set "COMPOSER=..\tools\composer.phar"

:: XAMPP 的 zip 扩展默认关闭，用 -d 临时加载，Composer 才能解压依赖
"%PHP%" -d extension=zip "%COMPOSER%" install --ignore-platform-reqs
"%PHP%" init
```

`php init` 选 `0 Development`，输入 `yes`。然后在 `common/config/main-local.php` 里确认数据库（本机 XAMPP MariaDB 是 3307）：

```php
'dsn' => 'mysql:host=127.0.0.1;port=3307;dbname=worldcup2026_team;charset=utf8mb4',
'username' => 'root',
'password' => '',
```

> - 仓库的 `composer.lock` 是老师模板 2.0.32 自带的，`doctrine/lexer 1.2.0` 要求 PHP `^7.2`，
>   PHP 8.2 直接安装会报平台错误，加 `--ignore-platform-reqs` 即可。
> - 如果已经在 `C:\xampp\php\php.ini` 里取消 `;extension=zip` 的注释，可以去掉 `-d extension=zip`。
> - 不要用 `tools\php.cmd`（那是作业用的 PHP 8.3.36），本项目用老师包里的 PHP 8.2.12。
> - 第 3、4 节继续使用这里设置的 `%PHP%`；如果新开命令行窗口，先重新执行上面两行 `set`。

### 3. 建库并执行迁移

```bat
:: 建库（XAMPP MariaDB，端口 3307）
"C:\xampp\mysql\bin\mysql.exe" -u root -P 3307 -e "CREATE DATABASE IF NOT EXISTS worldcup2026_team DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

:: 执行 A/C/B 的迁移和演示数据
"%PHP%" yii migrate --interactive=0
```

这条命令会执行 B 新增的数据迁移 `m261111_120000_seed_team_demo_data`：

- 如果 `team_profile` 还是默认名称，更新为“越位三剑客 / 代码不越位，需求必进球！”；
- 如果 `team_member` 为空，插入三名成员和分工（学号留空，可在后台补）；
- 如果后台已经维护过团队资料或成员，迁移自动跳过，不会覆盖、不会重复插入。

**如果数据库是用 `data/install.sql` 恢复的**：先导入，再执行一次 `"%PHP%" yii migrate --interactive=0` 即可，
`install.sql` 的 migration 表里没有 B 这条迁移，所以只会补这一条数据。

```bat
"C:\xampp\mysql\bin\mysql.exe" -u root -P 3307 --default-character-set=utf8mb4 < data\install.sql
```

### 4. 启动前后台

```bat
"%PHP%" -S 127.0.0.1:8081 -t frontend\web
"%PHP%" -S 127.0.0.1:8082 -t backend\web
```

- 前台：`http://127.0.0.1:8081/index.php?r=site/index`
- 后台：`http://127.0.0.1:8082/index.php?r=site/login`（C 的模块，账号 `admin / 123456`）

---

## 三、文件清单

### 新增

```
frontend/controllers/NewsController.php                 新闻列表、详情
frontend/controllers/MessageController.php              留言提交、成功页、我的留言
frontend/models/FanMessageForm.php                      留言表单模型（校验、蜜罐、待审核初始化）
frontend/views/news/index.php                           新闻列表
frontend/views/news/view.php                            新闻详情
frontend/views/news/_card.php                           新闻卡片（列表与相关新闻共用）
frontend/views/message/index.php                        留言板（表单 + 留言墙）
frontend/views/message/success.php                      留言成功页
frontend/views/message/mine.php                         我的留言
frontend/views/message/_item.php                        单条留言卡片
frontend/views/site/_member.php                         成员卡片（首页与团队介绍共用）
console/migrations/m261111_120000_seed_team_demo_data.php  团队资料与三名成员演示数据
docs/B-前台实现文档.md                                   实现文档（范围、结构、功能、自测清单）
docs/B-用户操作手册.md                                   用户手册（普通用户 + 管理员配合）
docs/B-软件包核对报告.md                                 老师软件包（XAMPP 8.2.12）核对与运行命令
data/team/docs/B-前台实现文档.md / .docx                 提交目录里的实现文档（Word 可直接交）
data/team/docs/B-用户操作手册.md / .docx                 提交目录里的用户手册（Word 可直接交）
data/team/docs/B-软件包核对报告.md / .docx               提交目录里的核对报告
data/team/screenshots/B-截图说明.md                   截图时的页面地址与命名对照
README-B.md                                             本文件
```

### 修改

```
frontend/controllers/SiteController.php     首页改为数据库查询；团队介绍页读团队资料/成员
frontend/config/main.php                    前台应用名、中文语言、时区与日期格式
frontend/views/site/index.php               首页改版（原页面为写死的 14/8/4/2 数字）
frontend/views/site/about.php               团队介绍页（原为 Yii 默认 About 占位）
frontend/views/site/error.php               404/500 中文化并给出返回入口
frontend/views/layouts/main.php             导航加入新闻/留言/团队介绍，页脚改课程项目信息
frontend/views/team/view.php                球队详情补一句话简介
frontend/web/css/site.css                   首页、新闻、留言、团队页样式与窄屏导航适配
```

---

## 四、验收对照（计划文档第八章“成员 B”表）

| 工作内容 | 完成效果 | 验收方法 |
|---|---|---|
| 首页和团队介绍 | 首页有项目名称、世界杯主题、三人成员卡和分工；导航到球队、比赛、新闻、留言均有内容 | 从首页逐个点导航，无 404；截完整首页 |
| 球队页面 | 列表 + 详情，显示名称、国家、小组、球员和比赛；无数据有提示 | 后台新增球队后刷新前台可见；访问不存在的 id 显示中文 404 |
| 比赛页面 | 列表可筛选、可看详情；首页有近期比赛摘要 | 用 A 的 14 场演示数据核对 |
| 新闻页面 | 列表/详情只显示 `published`；草稿不出现 | C 后台发布一条 → 前台出现；撤回 → 前台消失 |
| 留言功能 | 填写昵称+内容即可提交；空内容和过短内容有校验提示；成功后进入成功页；数据库新增记录 | 提交一条 → 后台留言管理能看到；空内容提交看提示 |
| 页面质量 | 8 项导航一致、空数据有空状态、404 有中文提示和返回入口 | Chrome 全流程走一遍，截 5 张图 |

---

## 五、B 的 5 张必交截图

统一前缀：`http://127.0.0.1:8081/index.php?r=`，截图 1440×900 或 1920×1080，
存到 `data/team/screenshots/`。文件名加 `B-` 前缀（例如 `B-01-首页.png`），
避免和 C 的 15 张后台截图重名。

| # | 截图 | 地址 | 画面要求 |
|---|---|---|---|
| 01 | 首页 | `site/index` | 主视觉 + 4 个统计数字 + 最新新闻 + 团队成员卡 |
| 02 | 球队列表 | `team/index` | 8 支球队、小组、查看详情按钮 |
| 03 | 比赛列表 | `match/index` | 筛选栏 + 14 场比赛、比分、场馆 |
| 04 | 新闻详情 | `news/view&id=1` | 标题、分类、发布时间、发布人、正文 |
| 05 | 留言成功页 | 先在 `message/index` 提交一条留言 | 绿色对勾 + 留言编号 + 状态“待审核” |

### 建议补拍的 5 张（证明闭环和加分）

| # | 截图 | 操作 |
|---|---|---|
| 06 | 新闻列表 | `news/index`，展示 3 条已发布新闻和分类筛选 |
| 07 | 留言板 | `message/index`，左侧表单 + 右侧已通过留言（含管理员回复） |
| 08 | 我的留言 | `message/mine`，展示刚提交的“待审核”；C 审核后刷新截“已通过” |
| 09 | 团队介绍 | `site/about`，团队名称、口号、三名成员、分工 |
| 10 | 中文 404 | `news/view&id=999`，显示“这篇新闻不存在……”和返回入口 |

> **截图怎么截**：启动前台后按 `Ctrl + F5` 强制刷新；用 `Win + Shift + S` 区域截图；
> 不要截到书签栏和其他标签页。本交付未包含截图文件，需在本地启动后按上面的地址补拍。

---

## 六、和 C 的联调三步（计划文档第九章）

1. **新闻闭环**：C 后台“内容管理 → 新闻管理”新建并发布一条 → B 打开 `news/index` 能看到；
   C 改为草稿 → B 刷新后前台消失。
2. **留言闭环**：B 在 `message/index` 提交留言（状态 `pending`）→ C 在后台“留言管理”通过并回复 →
   B 打开 `message/mine`，状态变成“已通过”，可以看到管理员回复；留言墙也出现这条。
3. **团队资料闭环**：C 在后台“团队设置 → 团队资料/成员管理”修改 → B 刷新 `site/about` 和首页同步变化。

---

## 七、Git 提交

B 的代码在 `dev-frontend` 分支，按模块分成 6 次代码提交；2026-11-22 核查老师软件包后，
补了 1 次文档提交（共 7 次）：

| # | 提交信息 | 内容 |
|---|---|---|
| 1 | `feat(db): 补充团队资料与三名成员的演示数据` | B 的数据迁移 |
| 2 | `feat(frontend): 新闻列表与详情页` | NewsController、news 视图 |
| 3 | `feat(frontend): 留言表单、成功页与我的留言` | FanMessageForm、MessageController、message 视图 |
| 4 | `feat(frontend): 首页与团队介绍改为数据库驱动` | SiteController、site/index、site/about、_member |
| 5 | `style(frontend): 统一导航、页面样式与中文错误页` | layouts/main、site.css、site/error、team/view、config/main |
| 6 | `docs: B 前台实现文档、用户操作手册与交付说明` | README-B、docs |
| 7 | `docs: 老师软件包核对报告与 XAMPP 8.2.12 运行命令` | 核对报告、README-B、实现文档、用户手册 |

合并后执行 `git log --oneline --graph --all` 截图，作为 B 的提交证据。

---

## 八、已知限制

1. **截图待补**：当前工作区还没有安装 Composer 依赖（没有 `vendor`），页面实测和截图需要在
   `composer install`、`php init`、建库启动之后完成（见 `docs/B-软件包核对报告.md`）。
2. “我的留言”按浏览器会话记录，最多保留最近 20 条；换浏览器或清空会话后看不到历史。
3. 留言审核状态只能后台修改，前台不提供改状态的入口（符合分工）。
4. 新闻正文是纯文本，换行用 `nl2br`，没有富文本编辑器（避免 XSS）。
5. 前台没有登录门槛，留言匿名；复杂权限和图形验证码留到后续版本。
6. 球队表没有简介字段，详情页的“简介”是用名称/国家/小组拼出来的，不是数据库字段。



