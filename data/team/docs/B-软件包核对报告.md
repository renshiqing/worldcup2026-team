# 老师软件包使用核对报告（B 前台部分）

> 课程：互联网数据库开发　项目：2026 世界杯赛事信息与球迷交流平台
> 核查人：B 张雅程（前台页面与用户交互）　核查日期：2026-11-22
> 老师软件包：`C:\baidunetdiskdownload\互联网数据库开发软件包2025\`
> 项目：`C:\Users\zyc\Desktop\互联网开发\worldcup2026-team`（核查时 `main` / `dev-frontend` 均为 `00c8158`）

---

## 一、结论摘要

| 检查项 | 结论 |
|---|---|
| 软件包选型 | ✅ 正确：项目是老师包里的 **Yii advanced 2.0.32**，不是别的框架 |
| 模板/依赖版本 | ✅ `composer.json`、`composer.lock`、`requirements.php` 与包内模板完全一致 |
| 前端库 | ✅ B 使用 Bootstrap 3 类名，对应包内 `bower-asset/bootstrap 3.4.1`，没有混用 Bootstrap 4/5 |
| B 的 PHP 语法 | ✅ 19 个文件在 XAMPP **PHP 8.2.12** 下 `php -l` 全部通过 |
| B 的扩展依赖 | ✅ 只用 `mbstring`、`pdo_mysql`、`session`、`json`，XAMPP 默认已启用 |
| 数据库 | ✅ `data/install.sql` 是 MariaDB 10.4.32 导出；在 XAMPP MariaDB 10.4.32 上导入 15 张表成功，B 的查询和迁移语义验证通过 |
| 实际运行 | ❌ 尚未完成：项目没有 `vendor`、`main-local.php`、`web/index.php`，也没有建库，所以还没真正跑在老师包上 |
| 运行文档 | ⚠ 原有 `php` / 3306 命令与本机 XAMPP 不符，已按本报告修正 |

**一句话结论**：**版本和代码是对的，但“用老师包把项目跑起来”这一步还没完成。**
现在的状态是“XAMPP 已安装、模板已使用”，还不是“项目已在 XAMPP 上安装并验证”。

---

## 二、老师软件包内容

| 文件/目录 | 内容 | 本项目是否使用 |
|---|---|---|
| `xampp-windows-x64-8.2.12-0-VS16-installer.exe` | XAMPP 8.2.12：PHP 8.2.12、MariaDB 10.4.32、Apache 2.4.58 | ✅ 运行时使用 |
| `yii-advanced-app-2.0.32.tgz` | Yii 2 Advanced 模板 2.0.32 | ✅ 项目模板 |
| `wordpress-6.8.3-zh_CN.zip` | WordPress | 课程其他作业使用，本项目不依赖 |
| `Discuz_X3.5_SC_UTF8_20250901.zip` | Discuz 论坛 | 课程其他作业使用，本项目不依赖 |
| `absolute.zip`、`absolute资源拆解/` | absolute 模板及拆解资源 | 本项目不依赖 |
| `coursemanagement2.zip` | 课程管理示例 | 本项目不依赖 |

> B 的前台只依赖 Yii advanced 模板 + Bootstrap 3 + PHP/MariaDB，不需要 WordPress、Discuz 或 absolute 模板。

---

## 三、版本一致性核查

### 3.1 项目模板与包内 tgz 对比

把 `yii-advanced-app-2.0.32.tgz` 解压后，与项目根目录比较（忽略 CRLF/LF 换行差异）：

| 文件 | 结果 |
|---|---|
| `composer.json` | ✅ 完全一致 |
| `composer.lock` | ✅ 完全一致（`content-hash` 都是 `50114089d7de41b6fbcc4d646295169a`） |
| `requirements.php` | ✅ 完全一致 |

关键版本：

| 包 | 版本 |
|---|---|
| `yiisoft/yii2` | **2.0.32** |
| `yiisoft/yii2-bootstrap` | 2.0.10 |
| `bower-asset/bootstrap` | **3.4.1** |
| `doctrine/lexer` | 1.2.0（要求 PHP `^7.2`，见 7.5） |

### 3.2 XAMPP 实际安装版本

| 组件 | 版本 | 验证 |
|---|---|---|
| XAMPP | 8.2.12 | `C:\xampp\readme_en.txt` 第一行 |
| PHP | 8.2.12 (ZTS, VS16 x64) | `C:\xampp\php\php.exe -v` |
| MariaDB | 10.4.32-MariaDB | `mysqld.exe --version`、`SELECT VERSION()` |
| Apache | 2.4.58 (Win64) | `httpd.exe -v` |

> 与老师包文件名 `xampp-windows-x64-8.2.12-0-VS16-installer.exe` 完全对应；`data/install.sql` 文件头也写着 `MariaDB 10.4.32`，说明 A 的数据库导出环境与老师包一致。

---

## 四、B 的代码兼容性

### 4.1 PHP 8.2 语法

用老师包的 PHP 逐个执行 `php -l`，覆盖 B 新增/修改的 19 个 PHP 文件
（`frontend/config/main.php`、3 个控制器、`FanMessageForm`、布局、site/news/message 视图、`team/view.php`、B 的数据迁移）：

```text
lint failures: 0 / 19
```

B 的代码只使用 PHP 7 级别的语法（`??`、`::class`、短数组、闭包），没有使用 PHP 8.1/8.2 新特性，PHP 8.2.12 可以解析。

### 4.2 PHP 扩展

XAMPP 8.2.12 默认扩展（`php -m`）：

| 扩展 | 状态 | B 是否使用 |
|---|---|---|
| `pdo_mysql` | ✅ 已启用 | ✅ Yii 访问 MariaDB |
| `mbstring` | ✅ 已启用 | ✅ `mb_substr` / `mb_strlen`（截断新闻摘要、取昵称首字） |
| `session`、`json`、`iconv`、`openssl`、`curl`、`fileinfo` | ✅ 已启用 | Yii 会话/表单/CSRF 等 |
| `intl` | ❌ 未启用（`;extension=intl`） | 不需要 |
| `gd` | ❌ 未启用（`;extension=gd`） | 不需要（B 没有图形验证码、没有图片处理） |
| `zip` | ❌ 未启用（`;extension=zip`） | 页面不需要，**Composer 安装依赖需要**（见 7.4） |

> 结论：B 的页面在 XAMPP 默认扩展下就能跑；不要为了 B 去启用 gd/intl，只有 Composer 需要临时加载 zip。

### 4.3 前端库

B 的视图使用 Bootstrap 3 类名：`panel`、`label-*`、`nav nav-pills`、`btn-*`、`glyphicon`、
`table table-hover`、`col-md-*`、`form-group` 等；自定义的 `wc-*` 是项目自己的 CSS，不依赖 Bootstrap 4/5。
这与包内 `bower-asset/bootstrap 3.4.1` 完全对应。

### 4.4 Yii 2.0.32 API

B 使用的都是 Yii 2.0 长期稳定 API：

- `yii\data\Pagination`、`yii\widgets\LinkPager`（新闻分页）
- `yii\bootstrap\ActiveForm`（留言表单）
- `yii\helpers\Html`、`yii\web\Controller`、`yii\filters\VerbFilter`
- `yii\db\Query`（首页比赛/统计）、`yii\web\NotFoundHttpException`
- `yii\bootstrap\Nav`、`NavBar`、`yii\widgets\Breadcrumbs`

这些在 2.0.32 + yii2-bootstrap 2.0.10 中都存在。
**说明**：Yii 2.0.32 是 2021 年版本，PHP 8.2 下可能有弃用提示，但 B 的代码没有使用会被移除的语法/函数；
框架级运行结果需要装好 `vendor` 后实际打开页面确认（见第八节）。

---

## 五、数据库核查（XAMPP MariaDB 10.4.32）

为避免污染现有库，把 `data/install.sql` 的库名临时改成 `worldcup2026_team_check`，
导入本机 XAMPP 的 MariaDB（端口 3307），执行 B 用到的查询，最后删除临时库。

### 5.1 导入结果

```text
表数量：15
球队：8　比赛：14　已发布新闻：3　草稿新闻：0
场馆：4　待开赛/进行中：2
已通过留言：1　待审核留言：1
team_profile：世界杯赛事信息与球迷交流平台 / 数据驱动，服务球迷
team_member：0
```

### 5.2 B 的查询

- 首页“近期比赛”join（比赛 + 阶段 + 双方球队 + 场馆）：返回 2 场待开赛比赛（北星队 vs 红枫队、海岸队 vs 蓝湾队）；
- 新闻列表/详情 `status = published`：3 条，中文标题/分类正常；
- 留言墙 `status = approved`：1 条，带管理员回复；
- 草稿新闻不会出现在 `published` 查询里（验证返回 0）。

### 5.3 B 的数据迁移语义

按 B 迁移的逻辑执行（团队资料仍是默认名称时更新；成员为空时插入 3 人）：

```text
profile_after：越位三剑客 / 代码不越位，需求必进球！
members_after：任诗清、张雅程、钟一成（display_order 1/2/3）
新留言插入：状态 pending 成功
草稿新闻插入后，published 仍为 3，前台查询不到草稿
```

> 数据库层结论：**B 的 SQL/字段/迁移语义在老师包的 MariaDB 10.4.32 上完全兼容**。

---

## 六、本机环境核查（重要）

### 6.1 已经正确的部分

- XAMPP 8.2.12 已安装在 `C:\xampp`，版本与老师包一致；
- XAMPP Apache 正在运行（`httpd.pid` = 25276，80/443 端口监听），日志显示 `/worldcup/` 是 **WordPress 作业站点**，不是本项目；
- XAMPP MariaDB 10.4.32 正在 **3307** 端口运行（`@@port = 3307`），数据目录 `C:\xampp\mysql\data`；
- 系统 MySQL 8.0 在 3306 端口（作业用），与本项目不是同一个数据库软件；
- `tools\composer.phar`（Composer 2.11.2）可用，但它是配合 `tools\php-8.3.36` 的作业环境。

### 6.2 还没有完成的部分

| 检查项 | 当前状态 |
|---|---|
| `vendor/autoload.php` | ❌ 不存在，Yii 框架未安装 |
| `common/config/main-local.php` | ❌ 不存在，`php init` 未执行 |
| `frontend/web/index.php` | ❌ 不存在，前台入口未生成 |
| `worldcup2026_team` 数据库 | ❌ XAMPP MariaDB 里没有（只有 WordPress 的 `worldcup`） |
| 实际页面访问 | ❌ 未启动，无法截图/验收 |
| Composer 网络 | ❌ 核查时代理 `127.0.0.1:17890` 未监听，直连 HTTPS 不通，依赖无法下载 |

> 所以“项目使用老师包”目前只完成了**模板和版本选择**，还没有完成**安装、建库、启动、页面验收**。

### 6.3 作业环境与本项目环境的区别

| 项 | 作业用（`tools` + 系统 MySQL） | 本项目应使用（老师包） |
|---|---|---|
| PHP | 8.3.36（`tools\php-8.3.36`） | **8.2.12**（`C:\xampp\php\php.exe`） |
| 数据库 | MySQL 8.0（3306，`MySQL80`） | **MariaDB 10.4.32**（XAMPP，3307） |
| 用途 | 作业 1/2/3、WordPress | 团队 Yii2 项目 |

> 不要把两套混用；团队项目按老师包版本运行，答辩时也更好解释。

---

## 七、发现的问题与修正

### 7.1 项目尚未初始化（最主要问题）

缺 `vendor`、`main-local.php`、`web/index.php` 和数据库。按第八节命令执行即可；不需要改 B 的代码。

### 7.2 运行命令里的 `php` / `composer` 不在 PATH

原 `README-B.md` 写的是 `php`、`composer`，本机直接执行会提示“找不到命令”。
已改为：

```bat
set "PHP=C:\xampp\php\php.exe"
set "COMPOSER=..\tools\composer.phar"
"%PHP%" -d extension=zip "%COMPOSER%" install --ignore-platform-reqs
"%PHP%" init
```

不要用 `tools\php.cmd` 跑本项目（那是 PHP 8.3.36，不是老师包版本）。

### 7.3 数据库端口

本机 3306 被系统 MySQL 8.0 占用，XAMPP MariaDB 实际在 **3307**。
`common/config/main-local.php` 的 `dsn` 应写：

```php
'dsn' => 'mysql:host=127.0.0.1;port=3307;dbname=worldcup2026_team;charset=utf8mb4',
```

原 README 示例的 3306 已按本机情况修正，并加了说明。

### 7.4 XAMPP 的 zip 扩展默认关闭

Composer 下载的是 zip 包，需要 `ZipArchive`。`C:\xampp\php\php.ini` 里是
`;extension=zip`（注释状态），而 `unzip`/7-Zip 也没有安装。
两种处理：

1. 临时加载（不改 XAMPP 配置，推荐检查用）：
   ```bat
   "%PHP%" -d extension=zip "%COMPOSER%" install --ignore-platform-reqs
   ```
2. 永久启用：把 `C:\xampp\php\php.ini` 里的 `;extension=zip` 改成 `extension=zip`，重新打开命令行。

### 7.5 Composer 平台检查

`composer.lock` 里的 `doctrine/lexer 1.2.0` 要求 PHP `^7.2`，在 PHP 8.2 下直接
`composer install` 会报平台错误；加 `--ignore-platform-reqs` 是老师模板的已知绕法（C 的文档也写了）。
这是模板锁文件的问题，不是 B 的代码问题。

### 7.6 Yii 2.0.32 + PHP 8.2.12

这两个版本就是老师包的原配组合；B 的代码只使用 2.0 稳定 API，语法 lint 通过。
Yii 2.0.32 较旧，PHP 8.2 下可能有弃用提示（deprecation），通常不影响页面；
装好依赖后如果出现**致命错误**，先保留老师包版本排查，不要直接把 Yii 升到最新版（会脱离老师包）。

### 7.7 Apache 用法

老师包的 Apache 正在跑 WordPress 作业站点，本项目没有放进 `htdocs`。
B 的文档用 `php -S`（老师包的 PHP 8.2.12）启动，属于 XAMPP/PHP 的合法用法；
如果老师要求必须用 Apache 访问，可把 `frontend/web` 配成 vhost/别名，或复制到 `htdocs` 再访问，
但要注意 WordPress 已占用 80 端口，不要覆盖 `/worldcup/`。

---

## 八、按老师包跑起来的最短步骤

```bat
cd C:\Users\zyc\Desktop\互联网开发\worldcup2026-team

set "PHP=C:\xampp\php\php.exe"
set "COMPOSER=..\tools\composer.phar"

:: 1. 安装依赖（老师包 PHP 8.2 + 临时加载 zip）
"%PHP%" -d extension=zip "%COMPOSER%" install --ignore-platform-reqs

:: 2. 生成 main-local.php 和 frontend/web/index.php
"%PHP%" init

:: 3. 改 common/config/main-local.php 的 dsn 为 127.0.0.1:3307/worldcup2026_team

:: 4. 建库（XAMPP MariaDB 3307）
"C:\xampp\mysql\bin\mysql.exe" -u root -P 3307 -e "CREATE DATABASE IF NOT EXISTS worldcup2026_team DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

:: 5. 执行 A/C/B 的迁移和演示数据
"%PHP%" yii migrate --interactive=0

:: 6. 启动前台（8081）和后台（8082）
"%PHP%" -S 127.0.0.1:8081 -t frontend\web
"%PHP%" -S 127.0.0.1:8082 -t backend\web
```

前台：`http://127.0.0.1:8081/index.php?r=site/index`
后台：`http://127.0.0.1:8082/index.php?r=site/login`（`admin / 123456`）

> 如果 `composer install` 报网络错误，先启动代理/VPN，并设置
> `HTTP_PROXY` / `HTTPS_PROXY`（例如 `http://127.0.0.1:17890`）；
> 核查时该代理没有在监听，所以依赖还没装。

### 用 `data/install.sql` 的替代路径

```bat
"C:\xampp\mysql\bin\mysql.exe" -u root -P 3307 --default-character-set=utf8mb4 < data\install.sql
"%PHP%" yii migrate --interactive=0
```

先导入 A 的 15 张表和演示数据，再补 B 的数据迁移（只会插入三名成员/团队资料，不会重复）。

---

## 九、复现记录

| 检查 | 命令/方法 | 结果 |
|---|---|---|
| XAMPP 版本 | `C:\xampp\readme_en.txt` | XAMPP 8.2.12 |
| PHP 版本 | `C:\xampp\php\php.exe -v` | PHP 8.2.12 |
| MariaDB 版本/端口 | `mysql -P 3307 -e "SELECT VERSION(), @@port"` | 10.4.32-MariaDB / 3307 |
| Apache | `httpd -v` | Apache 2.4.58 |
| B 语法 | 19 个文件 `php -l` | 0 失败 |
| B 扩展 | `php -m` | pdo_mysql、mbstring、session、json 等已启用 |
| 模板一致性 | tgz 内文件与 repo 对比（忽略换行） | composer.json/lock/requirements.php 一致 |
| 数据库导入 | 临时库 `worldcup2026_team_check` | 15 张表导入成功 |
| B 查询 | 首页/新闻/留言 join | 数据与 `install.sql` 一致 |
| B 迁移语义 | 更新 profile + 插入 3 名成员 | 成功，草稿新闻前台不可见 |

---

## 十、结论与待办

**正确的部分**：老师包版本选型、Yii advanced 2.0.32 模板、Bootstrap 3.4.1、B 的 PHP 8.2 语法和扩展依赖、数据库字段/SQL 兼容性。

**待完成的部分**：

1. 连接网络/代理，执行 `composer install --ignore-platform-reqs`（带 zip）；
2. `php init` 生成 `main-local.php` 和 `frontend/web/index.php`；
3. 在 XAMPP MariaDB 3307 建库并执行迁移；
4. 用 `C:\xampp\php\php.exe -S` 启动前台，打开 B 的 7 个页面验收；
5. 按 `data/team/screenshots/B-截图说明.md` 补 5 张前台截图；
6. 把 `dev-frontend` 的新文档提交推送给 A。

> 完成以上 1～4 后，才算“项目正确运行在老师要求的软件包上”；目前完成的是版本和代码层面的核查。

