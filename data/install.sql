-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: 127.0.0.1    Database: worldcup2026_team
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Current Database: `worldcup2026_team`
--

/*!40000 DROP DATABASE IF EXISTS `worldcup2026_team`*/;

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `worldcup2026_team` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */;

USE `worldcup2026_team`;

--
-- Table structure for table `fan_message`
--

DROP TABLE IF EXISTS `fan_message`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fan_message` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nickname` varchar(80) NOT NULL,
  `email` varchar(120) DEFAULT NULL,
  `subject` varchar(200) DEFAULT NULL,
  `message` text NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `created_at` int(11) NOT NULL DEFAULT 0,
  `updated_at` int(11) NOT NULL DEFAULT 0,
  `admin_reply` text DEFAULT NULL,
  `replied_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx-fan_message-status-created` (`status`,`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fan_message`
--

LOCK TABLES `fan_message` WRITE;
/*!40000 ALTER TABLE `fan_message` DISABLE KEYS */;
INSERT INTO `fan_message` VALUES (1,'演示球迷甲',NULL,'建议增加球员详情','希望球队详情页能够展示球员号码、位置和出生日期，方便查看球队阵容。','approved',1793532920,1793534748,'感谢建议，我们会补充球员详情。',1793533102),(2,'演示球迷乙','demo@example.com','建议增加赛程筛选','希望比赛页面能够支持按比赛日期筛选，方便快速找到感兴趣的比赛。','pending',1793532920,1793532920,NULL,NULL);
/*!40000 ALTER TABLE `fan_message` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `football_match`
--

DROP TABLE IF EXISTS `football_match`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `football_match` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `stage_id` int(11) NOT NULL,
  `group_id` int(11) DEFAULT NULL,
  `home_team_id` int(11) NOT NULL,
  `away_team_id` int(11) NOT NULL,
  `stadium_id` int(11) DEFAULT NULL,
  `kickoff_at` datetime NOT NULL,
  `home_score` smallint(6) DEFAULT NULL,
  `away_score` smallint(6) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'scheduled',
  `created_at` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx-football_match-kickoff_at` (`kickoff_at`),
  KEY `idx-football_match-status` (`status`),
  KEY `idx-football_match-home_team_id` (`home_team_id`),
  KEY `idx-football_match-away_team_id` (`away_team_id`),
  KEY `fk-football_match-stage_id` (`stage_id`),
  KEY `fk-football_match-group_id` (`group_id`),
  KEY `fk-football_match-stadium_id` (`stadium_id`),
  CONSTRAINT `fk-football_match-away_team_id` FOREIGN KEY (`away_team_id`) REFERENCES `football_team` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk-football_match-group_id` FOREIGN KEY (`group_id`) REFERENCES `tournament_group` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk-football_match-home_team_id` FOREIGN KEY (`home_team_id`) REFERENCES `football_team` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk-football_match-stadium_id` FOREIGN KEY (`stadium_id`) REFERENCES `stadium` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk-football_match-stage_id` FOREIGN KEY (`stage_id`) REFERENCES `match_stage` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `football_match`
--

LOCK TABLES `football_match` WRITE;
/*!40000 ALTER TABLE `football_match` DISABLE KEYS */;
INSERT INTO `football_match` VALUES (1,1,1,1,2,1,'2026-06-11 18:00:00',2,1,'finished',1793532920),(2,1,1,3,4,2,'2026-06-12 18:00:00',0,0,'finished',1793532920),(3,1,1,1,3,3,'2026-06-14 18:00:00',1,1,'finished',1793532920),(4,1,1,2,4,4,'2026-06-14 21:00:00',3,0,'finished',1793532920),(5,1,1,1,4,1,'2026-06-17 18:00:00',2,0,'finished',1793532920),(6,1,1,2,3,2,'2026-06-17 21:00:00',1,2,'finished',1793532920),(7,1,2,5,6,3,'2026-06-11 21:00:00',1,0,'finished',1793532920),(8,1,2,7,8,4,'2026-06-12 21:00:00',2,2,'finished',1793532920),(9,1,2,5,7,1,'2026-06-15 18:00:00',0,1,'finished',1793532920),(10,1,2,6,8,2,'2026-06-15 21:00:00',2,1,'finished',1793532920),(11,1,2,5,8,3,'2026-06-18 18:00:00',3,1,'finished',1793532920),(12,1,2,6,7,4,'2026-06-18 21:00:00',0,0,'finished',1793532920),(13,2,NULL,1,5,1,'2026-10-30 19:00:00',NULL,NULL,'scheduled',1793532920),(14,2,NULL,2,6,2,'2026-11-01 19:00:00',NULL,NULL,'scheduled',1793532920);
/*!40000 ALTER TABLE `football_match` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `football_team`
--

DROP TABLE IF EXISTS `football_team`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `football_team` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `country` varchar(80) NOT NULL,
  `group_id` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq-football_team-name` (`name`),
  KEY `idx-football_team-group_id` (`group_id`),
  CONSTRAINT `fk-football_team-group_id` FOREIGN KEY (`group_id`) REFERENCES `tournament_group` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `football_team`
--

LOCK TABLES `football_team` WRITE;
/*!40000 ALTER TABLE `football_team` DISABLE KEYS */;
INSERT INTO `football_team` VALUES (1,'北星队','示例国家A1',1,1793532920),(2,'海岸队','示例国家A2',1,1793532920),(3,'山城队','示例国家A3',1,1793532920),(4,'森林队','示例国家A4',1,1793532920),(5,'红枫队','示例国家B1',2,1793532920),(6,'蓝湾队','示例国家B2',2,1793532920),(7,'银鹰队','示例国家B3',2,1793532920),(8,'金狮队','示例国家B4',2,1793532920);
/*!40000 ALTER TABLE `football_team` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `host_city`
--

DROP TABLE IF EXISTS `host_city`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `host_city` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `country` varchar(60) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq-host_city-name-country` (`name`,`country`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `host_city`
--

LOCK TABLES `host_city` WRITE;
/*!40000 ALTER TABLE `host_city` DISABLE KEYS */;
INSERT INTO `host_city` VALUES (1,'亚特兰大','美国','美国佐治亚州主办城市',1793532920),(2,'波士顿','美国','比赛场馆位于马萨诸塞州福克斯伯勒',1793532920),(3,'达拉斯','美国','比赛场馆位于德克萨斯州阿灵顿',1793532920),(4,'休斯敦','美国','美国德克萨斯州主办城市',1793532920),(5,'堪萨斯城','美国','美国密苏里州主办城市',1793532920),(6,'洛杉矶','美国','比赛场馆位于加利福尼亚州英格尔伍德',1793532920),(7,'迈阿密','美国','美国佛罗里达州主办城市',1793532920),(8,'纽约/新泽西','美国','比赛场馆位于新泽西州东卢瑟福',1793532920),(9,'费城','美国','美国宾夕法尼亚州主办城市',1793532920),(10,'旧金山湾区','美国','比赛场馆位于加利福尼亚州圣克拉拉',1793532920),(11,'西雅图','美国','美国华盛顿州主办城市',1793532920),(12,'墨西哥城','墨西哥','墨西哥主办城市',1793532920),(13,'瓜达拉哈拉','墨西哥','墨西哥主办城市',1793532920),(14,'蒙特雷','墨西哥','墨西哥主办城市',1793532920),(15,'多伦多','加拿大','加拿大主办城市',1793532920),(16,'温哥华','加拿大','加拿大主办城市',1793532920);
/*!40000 ALTER TABLE `host_city` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `match_event`
--

DROP TABLE IF EXISTS `match_event`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `match_event` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `match_id` int(11) NOT NULL,
  `team_id` int(11) DEFAULT NULL,
  `event_type` varchar(30) NOT NULL,
  `minute` smallint(6) unsigned DEFAULT NULL,
  `player_name` varchar(100) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx-match_event-match-minute` (`match_id`,`minute`),
  KEY `fk-match_event-team` (`team_id`),
  CONSTRAINT `fk-match_event-match` FOREIGN KEY (`match_id`) REFERENCES `football_match` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk-match_event-team` FOREIGN KEY (`team_id`) REFERENCES `football_team` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `match_event`
--

LOCK TABLES `match_event` WRITE;
/*!40000 ALTER TABLE `match_event` DISABLE KEYS */;
/*!40000 ALTER TABLE `match_event` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `match_stage`
--

DROP TABLE IF EXISTS `match_stage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `match_stage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `stage_name` varchar(50) NOT NULL,
  `stage_order` smallint(6) NOT NULL DEFAULT 0,
  `created_at` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq-match_stage-stage_name` (`stage_name`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `match_stage`
--

LOCK TABLES `match_stage` WRITE;
/*!40000 ALTER TABLE `match_stage` DISABLE KEYS */;
INSERT INTO `match_stage` VALUES (1,'小组赛',1,1793532920),(2,'32强赛',2,1793532920),(3,'16强赛',3,1793532920),(4,'四分之一决赛',4,1793532920),(5,'半决赛',5,1793532920),(6,'季军赛',6,1793532920),(7,'决赛',7,1793532920);
/*!40000 ALTER TABLE `match_stage` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migration`
--

DROP TABLE IF EXISTS `migration`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migration` (
  `version` varchar(180) NOT NULL,
  `apply_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migration`
--

LOCK TABLES `migration` WRITE;
/*!40000 ALTER TABLE `migration` DISABLE KEYS */;
INSERT INTO `migration` VALUES ('m000000_000000_base',1793532919),('m130524_201442_init',1793532919),('m190124_110200_add_verification_token_column_to_user_table',1793532919),('m261025_023610_create_worldcup_base_tables',1793532919),('m261025_025415_create_worldcup_core_tables',1793532920),('m261025_025908_seed_worldcup_reference_data',1793532920),('m261025_030429_seed_worldcup_demo_data',1793532920),('m261025_063751_add_worldcup_extension_tables',1793532920),('m261025_112207_seed_worldcup_demo_content',1793532920),('m261101_120000_add_admin_flag_and_news_author',1793532920),('m261101_120100_add_reply_to_fan_message',1793532920),('m261101_130000_create_team_profile_tables',1793534445),('m261101_140000_create_operation_log_table',1793683720);
/*!40000 ALTER TABLE `migration` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `news_article`
--

DROP TABLE IF EXISTS `news_article`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `news_article` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `summary` text DEFAULT NULL,
  `content` text NOT NULL,
  `category` varchar(50) NOT NULL DEFAULT '赛事新闻',
  `status` varchar(20) NOT NULL DEFAULT 'draft',
  `published_at` datetime DEFAULT NULL,
  `created_at` int(11) NOT NULL DEFAULT 0,
  `updated_at` int(11) NOT NULL DEFAULT 0,
  `author_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx-news_article-status-published` (`status`,`published_at`),
  KEY `idx-news_article-author_id` (`author_id`),
  CONSTRAINT `fk-news_article-author_id` FOREIGN KEY (`author_id`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `news_article`
--

LOCK TABLES `news_article` WRITE;
/*!40000 ALTER TABLE `news_article` DISABLE KEYS */;
INSERT INTO `news_article` VALUES (1,'世界杯赛事中心上线（演示）','介绍课程项目的赛事查询和数据展示功能。','本网站是基于 Yii2 和 MySQL 开发的世界杯主题课程项目，提供比赛列表、小组积分榜、球队详情及场馆信息查询。所有球队、赛程和比分均为演示数据，不代表官方赛事信息。','赛事新闻','published','2026-11-01 12:35:20',1793532920,1793532920,NULL),(2,'小组赛赛程查询功能说明（演示）','介绍比赛时间、比分和场馆信息的查询方式。','用户可以通过比赛列表查看演示赛程，并根据小组和比赛状态筛选比赛。后续可进一步扩展日期筛选和比赛详情查询功能。','赛事新闻','published','2026-11-01 12:35:20',1793532920,1793532920,NULL),(3,'世界杯小组积分榜规则（演示）','介绍演示积分榜的积分和排名计算方式。','本项目按照已结束且双方比分完整的小组赛计算演示积分榜。胜一场得3分，平局得1分，负场得0分，同时统计进球、失球和净胜球。','赛事新闻','published','2026-11-01 12:35:20',1793532920,1793532920,NULL);
/*!40000 ALTER TABLE `news_article` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `operation_log`
--

DROP TABLE IF EXISTS `operation_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `operation_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `username` varchar(50) NOT NULL DEFAULT '',
  `action` varchar(20) NOT NULL,
  `target_type` varchar(30) NOT NULL DEFAULT '',
  `target_id` int(11) DEFAULT NULL,
  `summary` varchar(255) NOT NULL DEFAULT '',
  `ip` varchar(45) DEFAULT NULL,
  `created_at` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx-operation_log-created_at` (`created_at`),
  KEY `idx-operation_log-action` (`action`),
  KEY `idx-operation_log-target` (`target_type`,`target_id`),
  KEY `fk-operation_log-user_id` (`user_id`),
  CONSTRAINT `fk-operation_log-user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `operation_log`
--

LOCK TABLES `operation_log` WRITE;
/*!40000 ALTER TABLE `operation_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `operation_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stadium`
--

DROP TABLE IF EXISTS `stadium`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stadium` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(120) NOT NULL,
  `city_id` int(11) NOT NULL,
  `capacity` int(11) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `created_at` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq-stadium-name-city` (`name`,`city_id`),
  KEY `fk-stadium-city_id` (`city_id`),
  CONSTRAINT `fk-stadium-city_id` FOREIGN KEY (`city_id`) REFERENCES `host_city` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stadium`
--

LOCK TABLES `stadium` WRITE;
/*!40000 ALTER TABLE `stadium` DISABLE KEYS */;
INSERT INTO `stadium` VALUES (1,'亚特兰大演示球场',1,71000,'亚特兰大，演示地址',1793532920),(2,'达拉斯演示球场',3,80000,'达拉斯，演示地址',1793532920),(3,'多伦多演示球场',15,45000,'多伦多，演示地址',1793532920),(4,'墨西哥城演示球场',12,83000,'墨西哥城，演示地址',1793532920);
/*!40000 ALTER TABLE `stadium` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `team_member`
--

DROP TABLE IF EXISTS `team_member`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `team_member` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `student_id` varchar(30) DEFAULT NULL,
  `role` varchar(50) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `display_order` int(11) NOT NULL DEFAULT 0,
  `created_at` int(11) NOT NULL DEFAULT 0,
  `updated_at` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx-team_member-display_order` (`display_order`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `team_member`
--

LOCK TABLES `team_member` WRITE;
/*!40000 ALTER TABLE `team_member` DISABLE KEYS */;
/*!40000 ALTER TABLE `team_member` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `team_player`
--

DROP TABLE IF EXISTS `team_player`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `team_player` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `team_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `shirt_number` smallint(6) unsigned DEFAULT NULL,
  `position` varchar(30) DEFAULT NULL,
  `nationality` varchar(80) DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `is_captain` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq-team_player-team-shirt` (`team_id`,`shirt_number`),
  CONSTRAINT `fk-team_player-team` FOREIGN KEY (`team_id`) REFERENCES `football_team` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `team_player`
--

LOCK TABLES `team_player` WRITE;
/*!40000 ALTER TABLE `team_player` DISABLE KEYS */;
/*!40000 ALTER TABLE `team_player` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `team_profile`
--

DROP TABLE IF EXISTS `team_profile`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `team_profile` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `team_name` varchar(100) NOT NULL DEFAULT '',
  `slogan` varchar(200) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `contact_email` varchar(120) DEFAULT NULL,
  `created_at` int(11) NOT NULL DEFAULT 0,
  `updated_at` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `team_profile`
--

LOCK TABLES `team_profile` WRITE;
/*!40000 ALTER TABLE `team_profile` DISABLE KEYS */;
INSERT INTO `team_profile` VALUES (1,'世界杯赛事信息与球迷交流平台','数据驱动，服务球迷','基于 Yii2 与 MariaDB 的课程团队项目，提供球队、比赛、新闻和球迷留言功能。','',1793534445,1793534763);
/*!40000 ALTER TABLE `team_profile` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tournament_group`
--

DROP TABLE IF EXISTS `tournament_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tournament_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_name` varchar(20) NOT NULL,
  `created_at` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq-tournament_group-group_name` (`group_name`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tournament_group`
--

LOCK TABLES `tournament_group` WRITE;
/*!40000 ALTER TABLE `tournament_group` DISABLE KEYS */;
INSERT INTO `tournament_group` VALUES (1,'A',1793532920),(2,'B',1793532920),(3,'C',1793532920),(4,'D',1793532920),(5,'E',1793532920),(6,'F',1793532920),(7,'G',1793532920),(8,'H',1793532920),(9,'I',1793532920),(10,'J',1793532920),(11,'K',1793532920),(12,'L',1793532920);
/*!40000 ALTER TABLE `tournament_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `auth_key` varchar(32) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `password_reset_token` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `status` smallint(6) NOT NULL DEFAULT 10,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `verification_token` varchar(255) DEFAULT NULL,
  `is_admin` smallint(6) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `password_reset_token` (`password_reset_token`),
  KEY `idx-user-is_admin` (`is_admin`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,'任诗清','H5otDAypmfCdTiBA_gezQuGcQ29VBCX-','$2y$13$RJZLWYzWGYmHY5QfQ28Th.Atq5n2tLZKKPbH9TLv/J3XrWewbKD2C',NULL,'2671792557@qq.com',10,1792912373,1792912373,'uCUbiG2QH67YYJIy5K3WiPB5kRjYWVMV_1792912373',1);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-11-03 13:29:02
