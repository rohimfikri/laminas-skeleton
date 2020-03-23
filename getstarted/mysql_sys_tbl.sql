-- MariaDB dump 10.17  Distrib 10.4.10-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: lam_sys
-- ------------------------------------------------------
-- Server version	10.4.10-MariaDB

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
-- Table structure for table `_action`
--

DROP TABLE IF EXISTS `_action`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_action` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `controller` int(11) DEFAULT NULL,
  `name` varchar(50) NOT NULL,
  `status` smallint(1) NOT NULL DEFAULT 1,
  `allow_all` smallint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `act_uniq_1` (`controller`,`name`) USING BTREE,
  KEY `act_name` (`name`),
  CONSTRAINT `_action_ibfk_1` FOREIGN KEY (`controller`) REFERENCES `_controller` (`id`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_action`
--

LOCK TABLES `_action` WRITE;
/*!40000 ALTER TABLE `_action` DISABLE KEYS */;
INSERT INTO `_action` VALUES (0,0,'0',1,1),(1,1,'profile',1,1),(2,2,'callmodel',1,0);
/*!40000 ALTER TABLE `_action` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_alert`
--

DROP TABLE IF EXISTS `_alert`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_alert` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `module` varchar(255) DEFAULT NULL,
  `layout` varchar(100) DEFAULT NULL,
  `title` varchar(30) DEFAULT NULL,
  `route` varchar(50) DEFAULT NULL,
  `param` varchar(100) DEFAULT NULL,
  `query` varchar(100) DEFAULT NULL,
  `url` varchar(50) DEFAULT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `status` int(1) DEFAULT 1,
  `content` text DEFAULT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` int(6) DEFAULT 0,
  `class` varchar(100) DEFAULT NULL,
  `img` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `notif_uniq_1` (`module`,`layout`,`title`,`route`,`param`,`query`,`type`) USING BTREE,
  UNIQUE KEY `notif_uniq_2` (`module`,`layout`,`title`,`url`,`type`) USING BTREE,
  UNIQUE KEY `notif_uniq_3` (`module`,`layout`,`title`,`route`,`param`,`query`,`status`) USING BTREE,
  UNIQUE KEY `notif_uniq_4` (`module`,`layout`,`title`,`url`,`status`) USING BTREE,
  UNIQUE KEY `notif_uniq_5` (`module`,`layout`,`title`,`route`,`param`,`query`,`type`,`status`) USING BTREE,
  UNIQUE KEY `notif_uniq_6` (`module`,`layout`,`title`,`url`,`type`,`status`) USING BTREE,
  KEY `notif_module` (`module`),
  KEY `notif_layout` (`layout`),
  KEY `notif_type` (`type`),
  KEY `notif_status` (`status`),
  KEY `notif_createdate` (`created_date`),
  KEY `notif_createby` (`created_by`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_alert`
--

LOCK TABLES `_alert` WRITE;
/*!40000 ALTER TABLE `_alert` DISABLE KEYS */;
INSERT INTO `_alert` VALUES (1,NULL,NULL,'Notif Danger','admin',NULL,NULL,NULL,'fa fa-key',NULL,1,'Fusce eget dolor id justo luctus the commodo vel pharetra nisi. Donec velit of libero.','2020-02-17 22:31:40',0,'bg-danger',NULL),(2,NULL,NULL,'Notif Primary','admin',NULL,NULL,NULL,'fa fa-key',NULL,1,'Fusce eget dolor id justo luctus the commodo vel pharetra nisi. Donec velit of libero.','2020-02-17 22:32:24',0,'bg-primary',NULL);
/*!40000 ALTER TABLE `_alert` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_controller`
--

DROP TABLE IF EXISTS `_controller`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_controller` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module` int(11) DEFAULT NULL,
  `name` varchar(50) NOT NULL,
  `status` smallint(1) NOT NULL DEFAULT 1,
  `allow_all` smallint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `control_uniq_1` (`module`,`name`) USING BTREE,
  KEY `control_name` (`name`),
  CONSTRAINT `_controller_ibfk_1` FOREIGN KEY (`module`) REFERENCES `_module` (`id`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_controller`
--

LOCK TABLES `_controller` WRITE;
/*!40000 ALTER TABLE `_controller` DISABLE KEYS */;
INSERT INTO `_controller` VALUES (0,0,'0',1,1),(1,1,'User',1,0),(2,2,'Ajax',1,0);
/*!40000 ALTER TABLE `_controller` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_inbox`
--

DROP TABLE IF EXISTS `_inbox`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_inbox` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `module` varchar(255) DEFAULT NULL,
  `layout` varchar(100) DEFAULT NULL,
  `title` varchar(30) DEFAULT NULL,
  `route` varchar(50) DEFAULT NULL,
  `param` varchar(100) DEFAULT NULL,
  `query` varchar(100) DEFAULT NULL,
  `url` varchar(50) DEFAULT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `status` int(1) DEFAULT 1,
  `content` text DEFAULT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` int(6) DEFAULT 0,
  `class` varchar(100) DEFAULT NULL,
  `img` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `inbox_uniq_1` (`module`,`layout`,`title`,`route`,`param`,`query`,`type`) USING BTREE,
  UNIQUE KEY `inbox_uniq_2` (`module`,`layout`,`title`,`url`,`type`) USING BTREE,
  UNIQUE KEY `inbox_uniq_3` (`module`,`layout`,`title`,`route`,`param`,`query`,`status`) USING BTREE,
  UNIQUE KEY `inbox_uniq_4` (`module`,`layout`,`title`,`url`,`status`) USING BTREE,
  UNIQUE KEY `inbox_uniq_5` (`module`,`layout`,`title`,`route`,`param`,`query`,`type`,`status`) USING BTREE,
  UNIQUE KEY `inbox_uniq_6` (`module`,`layout`,`title`,`url`,`type`,`status`) USING BTREE,
  KEY `inbox_module` (`module`),
  KEY `inbox_layout` (`layout`),
  KEY `inbox_type` (`type`),
  KEY `inbox_status` (`status`),
  KEY `inbox_createdate` (`created_date`),
  KEY `inbox_createby` (`created_by`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_inbox`
--

LOCK TABLES `_inbox` WRITE;
/*!40000 ALTER TABLE `_inbox` DISABLE KEYS */;
INSERT INTO `_inbox` VALUES (1,NULL,NULL,'Message from facebook',NULL,NULL,NULL,'https://facebook.com','fab fa-facebook','',1,'Fusce eget dolor id justo luctus the commodo vel pharetra nisi. Donec velit of libero.','2020-02-17 22:20:43',0,'bg-info',NULL),(2,NULL,NULL,'Message form google',NULL,NULL,NULL,'https://google.com','fab fa-google','',1,'Fusce eget dolor id justo luctus the commodo vel pharetra nisi. Donec velit of libero.','2020-02-17 22:21:47',0,'bg-info',NULL),(3,NULL,NULL,'Message from system','user/profile',NULL,NULL,NULL,'notika-icon notika-support',NULL,1,'Fusce eget dolor id justo luctus the commodo vel pharetra nisi. Donec velit of libero.','2020-02-17 22:28:53',0,'bg-warning',NULL);
/*!40000 ALTER TABLE `_inbox` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_menu`
--

DROP TABLE IF EXISTS `_menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_menu` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `module` varchar(255) DEFAULT NULL,
  `layout` varchar(100) DEFAULT NULL,
  `title` varchar(30) DEFAULT NULL,
  `route` varchar(50) DEFAULT NULL,
  `param` varchar(100) DEFAULT NULL,
  `query` varchar(100) DEFAULT NULL,
  `url` varchar(50) DEFAULT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `parent` int(4) DEFAULT NULL,
  `status` int(1) DEFAULT 1,
  `desc` varchar(100) DEFAULT NULL,
  `priority` int(2) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `menu_uniq_1` (`module`,`layout`,`title`,`route`,`param`,`query`,`parent`) USING BTREE,
  UNIQUE KEY `menu_uniq_2` (`module`,`layout`,`title`,`url`,`parent`) USING BTREE,
  KEY `menu_module` (`module`),
  KEY `menu_layout` (`layout`),
  KEY `menu_parent` (`parent`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_menu`
--

LOCK TABLES `_menu` WRITE;
/*!40000 ALTER TABLE `_menu` DISABLE KEYS */;
INSERT INTO `_menu` VALUES (1,'App','notika','Administrator',NULL,NULL,NULL,'#','notika-icon notika-app',NULL,1,NULL,100),(2,'App','notika','App Summary Info','admin',NULL,NULL,NULL,'notika-icon notika-bar-chart',1,1,NULL,0),(3,'App','notika','My Profile','user/profile',NULL,NULL,NULL,'notika-icon notika-support',0,1,NULL,0),(4,'App','notika','Manage Menu',NULL,NULL,NULL,'#','fas fa-list',1,1,NULL,3),(5,'App','notika','Manage User',NULL,NULL,NULL,'#','fas fa-users-cog',1,1,NULL,1),(6,'App','notika','Manage Role',NULL,NULL,NULL,'#','fas fa-user-tag',1,1,NULL,2),(7,'App','notika','Manage Access','admin',NULL,NULL,NULL,'fas fa-key',1,1,NULL,4),(8,'App','notika','List User','admin/manage-user',NULL,NULL,NULL,'fas fa-users',5,1,NULL,0),(9,'App','notika','Add User','admin/manage-user/add-user',NULL,NULL,NULL,'notika-icon notika-support',5,1,NULL,1),(10,'App','notika','List Menu','admin',NULL,NULL,NULL,'fas fa-list',4,1,NULL,0),(11,'App','notika','Add Menu','admin',NULL,NULL,NULL,'fas fa-list-alt',4,1,NULL,1),(12,'App','notika','List Role','admin',NULL,NULL,NULL,'fas fa-list',6,1,NULL,0),(13,'App','notika','Add Role','admin',NULL,NULL,NULL,'fas fa-project-diagram',6,1,NULL,1),(14,NULL,NULL,'Google',NULL,NULL,NULL,'https://google.com','fab fa-google',15,1,NULL,0),(15,NULL,NULL,'Other Link',NULL,NULL,NULL,'#','fas fa-link',NULL,1,NULL,101),(16,NULL,NULL,'Facebook',NULL,NULL,NULL,'https://facebook.com','fab fa-facebook-f',15,1,NULL,1),(17,NULL,NULL,'Amerta',NULL,NULL,NULL,'https://amertagroup.id','fas fa-link',0,1,NULL,0);
/*!40000 ALTER TABLE `_menu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_module`
--

DROP TABLE IF EXISTS `_module`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_module` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `status` smallint(1) NOT NULL DEFAULT 1,
  `allow_all` smallint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `modul_uniq_1` (`name`) USING BTREE,
  KEY `module_name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_module`
--

LOCK TABLES `_module` WRITE;
/*!40000 ALTER TABLE `_module` DISABLE KEYS */;
INSERT INTO `_module` VALUES (0,'0',1,1),(1,'App',1,0),(2,'Core',1,0);
/*!40000 ALTER TABLE `_module` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_role`
--

DROP TABLE IF EXISTS `_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_role` (
  `code` varchar(10) NOT NULL,
  `name` varchar(50) NOT NULL,
  `status` smallint(1) NOT NULL DEFAULT 1,
  `redirect_route` varchar(200) DEFAULT NULL,
  `redirect_param` varchar(200) DEFAULT NULL,
  `redirect_query` varchar(200) DEFAULT NULL,
  `redirect_url` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`code`),
  UNIQUE KEY `role_uniq_1` (`name`) USING BTREE,
  KEY `role_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_role`
--

LOCK TABLES `_role` WRITE;
/*!40000 ALTER TABLE `_role` DISABLE KEYS */;
INSERT INTO `_role` VALUES ('GUEST','GUEST',1,'app',NULL,NULL,NULL),('SUPADM','SUPER ADMIN',1,'admin',NULL,NULL,NULL);
/*!40000 ALTER TABLE `_role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_role_access`
--

DROP TABLE IF EXISTS `_role_access`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_role_access` (
  `role` varchar(10) NOT NULL,
  `module` int(11) NOT NULL DEFAULT 0,
  `controller` int(11) NOT NULL DEFAULT 0,
  `action` int(11) NOT NULL DEFAULT 0,
  `status` smallint(6) NOT NULL DEFAULT 1,
  `layout` varchar(100) DEFAULT NULL,
  `route` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`role`,`module`,`controller`,`action`),
  KEY `faccess_role` (`role`),
  KEY `faccess_module` (`module`),
  KEY `faccess_control` (`controller`),
  KEY `faccess_act` (`action`),
  CONSTRAINT `_role_access_ibfk_1` FOREIGN KEY (`role`) REFERENCES `_role` (`code`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `_role_access_ibfk_2` FOREIGN KEY (`module`) REFERENCES `_module` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `_role_access_ibfk_3` FOREIGN KEY (`controller`) REFERENCES `_controller` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `_role_access_ibfk_4` FOREIGN KEY (`action`) REFERENCES `_action` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_role_access`
--

LOCK TABLES `_role_access` WRITE;
/*!40000 ALTER TABLE `_role_access` DISABLE KEYS */;
INSERT INTO `_role_access` VALUES ('SUPADM',0,0,0,1,NULL,NULL),('SUPADM',1,0,0,1,NULL,NULL),('SUPADM',2,0,0,1,NULL,NULL);
/*!40000 ALTER TABLE `_role_access` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_role_alert`
--

DROP TABLE IF EXISTS `_role_alert`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_role_alert` (
  `role` varchar(10) NOT NULL,
  `alert` int(4) NOT NULL,
  `status` int(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`role`,`alert`),
  KEY `rolnotif_role` (`role`),
  KEY `rolnotif_notif` (`alert`),
  CONSTRAINT `_role_alert_ibfk_1` FOREIGN KEY (`alert`) REFERENCES `_alert` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `_role_alert_ibfk_2` FOREIGN KEY (`role`) REFERENCES `_role` (`code`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_role_alert`
--

LOCK TABLES `_role_alert` WRITE;
/*!40000 ALTER TABLE `_role_alert` DISABLE KEYS */;
INSERT INTO `_role_alert` VALUES ('SUPADM',2,1);
/*!40000 ALTER TABLE `_role_alert` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_role_inbox`
--

DROP TABLE IF EXISTS `_role_inbox`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_role_inbox` (
  `role` varchar(10) NOT NULL,
  `inbox` int(4) NOT NULL,
  `status` int(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`role`,`inbox`),
  KEY `rolinbox_role` (`role`),
  KEY `rolinbox_inbox` (`inbox`),
  CONSTRAINT `_role_inbox_ibfk_1` FOREIGN KEY (`inbox`) REFERENCES `_inbox` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `_role_inbox_ibfk_2` FOREIGN KEY (`role`) REFERENCES `_role` (`code`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_role_inbox`
--

LOCK TABLES `_role_inbox` WRITE;
/*!40000 ALTER TABLE `_role_inbox` DISABLE KEYS */;
INSERT INTO `_role_inbox` VALUES ('SUPADM',3,1);
/*!40000 ALTER TABLE `_role_inbox` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_role_menu`
--

DROP TABLE IF EXISTS `_role_menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_role_menu` (
  `role` varchar(10) NOT NULL,
  `menu` int(4) NOT NULL,
  `status` int(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`role`,`menu`),
  KEY `rolmenu_role` (`role`),
  KEY `rolmenu_menu` (`menu`),
  CONSTRAINT `_role_menu_ibfk_1` FOREIGN KEY (`menu`) REFERENCES `_menu` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `_role_menu_ibfk_2` FOREIGN KEY (`role`) REFERENCES `_role` (`code`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_role_menu`
--

LOCK TABLES `_role_menu` WRITE;
/*!40000 ALTER TABLE `_role_menu` DISABLE KEYS */;
INSERT INTO `_role_menu` VALUES ('SUPADM',1,1),('SUPADM',2,1),('SUPADM',3,1),('SUPADM',4,1),('SUPADM',5,1),('SUPADM',6,1),('SUPADM',7,1),('SUPADM',8,1),('SUPADM',9,1),('SUPADM',10,1),('SUPADM',11,1),('SUPADM',12,1),('SUPADM',13,1);
/*!40000 ALTER TABLE `_role_menu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_session`
--

DROP TABLE IF EXISTS `_session`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_session` (
  `id` varchar(32) NOT NULL,
  `name` varchar(32) NOT NULL,
  `modified` int(11) DEFAULT NULL,
  `lifetime` int(11) DEFAULT NULL,
  `data` text DEFAULT NULL,
  `uag` varchar(500) DEFAULT NULL,
  `ip` varchar(20) DEFAULT NULL,
  `uid` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`,`name`),
  UNIQUE KEY `session_uq1` (`id`,`name`,`ip`,`uag`,`uid`),
  KEY `session_ip_uag_uid` (`ip`,`uag`,`uid`),
  KEY `session_ip_uag` (`ip`,`uag`),
  KEY `session_ip_uid` (`ip`,`uid`),
  KEY `session_uag_uid` (`uag`,`uid`),
  KEY `session_uid` (`uid`),
  KEY `session_ip` (`ip`),
  KEY `session_uag` (`uag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_session`
--

LOCK TABLES `_session` WRITE;
/*!40000 ALTER TABLE `_session` DISABLE KEYS */;
INSERT INTO `_session` VALUES ('004m02aec4onqfspfoeqteiljg','Laminas_session',1584004731,2592000,'__Laminas|a:3:{s:20:\"_REQUEST_ACCESS_TIME\";d:1584004731.29078;s:6:\"_VALID\";a:3:{s:28:\"Laminas\\Session\\Validator\\Id\";s:26:\"pjdpm6ileujt0i1aen7vqqaojj\";s:36:\"Laminas\\Session\\Validator\\RemoteAddr\";s:9:\"127.0.0.1\";s:39:\"Laminas\\Session\\Validator\\HttpUserAgent\";s:78:\"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:71.0) Gecko/20100101 Firefox/71.0\";}s:32:\"Laminas_Validator_Csrf_salt_csrf\";a:1:{s:6:\"EXPIRE\";i:1584005331;}}container_init|C:26:\"Laminas\\Stdlib\\ArrayObject\":455:{a:4:{s:7:\"storage\";a:5:{s:4:\"init\";i:1;s:7:\"sess_id\";s:26:\"004m02aec4onqfspfoeqteiljg\";s:10:\"remoteAddr\";s:9:\"127.0.0.1\";s:13:\"httpUserAgent\";s:78:\"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:71.0) Gecko/20100101 Firefox/71.0\";s:11:\"lastRequest\";s:19:\"2020-03-12 16:18:51\";}s:4:\"flag\";i:2;s:13:\"iteratorClass\";s:13:\"ArrayIterator\";s:19:\"protectedProperties\";a:4:{i:0;s:7:\"storage\";i:1;s:4:\"flag\";i:2;s:13:\"iteratorClass\";i:3;s:19:\"protectedProperties\";}}}Laminas_Validator_Csrf_salt_csrf|C:26:\"Laminas\\Stdlib\\ArrayObject\":471:{a:4:{s:7:\"storage\";a:2:{s:9:\"tokenList\";a:2:{s:32:\"1e8a6befcdf14f42332f8944748704dc\";s:32:\"a44cf0fe68eccade435ca55d3e5233c6\";s:32:\"7eef5c7cd3ce7f863d06749d0e6d03f6\";s:32:\"3b4346479e5ddbda2b18d8655bf27b28\";}s:4:\"hash\";s:65:\"3b4346479e5ddbda2b18d8655bf27b28-7eef5c7cd3ce7f863d06749d0e6d03f6\";}s:4:\"flag\";i:2;s:13:\"iteratorClass\";s:13:\"ArrayIterator\";s:19:\"protectedProperties\";a:4:{i:0;s:7:\"storage\";i:1;s:4:\"flag\";i:2;s:13:\"iteratorClass\";i:3;s:19:\"protectedProperties\";}}}container_login|C:26:\"Laminas\\Stdlib\\ArrayObject\":219:{a:4:{s:7:\"storage\";a:1:{s:3:\"try\";i:1;}s:4:\"flag\";i:2;s:13:\"iteratorClass\";s:13:\"ArrayIterator\";s:19:\"protectedProperties\";a:4:{i:0;s:7:\"storage\";i:1;s:4:\"flag\";i:2;s:13:\"iteratorClass\";i:3;s:19:\"protectedProperties\";}}}','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:71.0) Gecko/20100101 Firefox/71.0','127.0.0.1',NULL),('0n4odr0elpq2ghlimd0midkk30','Laminas_session',1584001302,2592000,'__Laminas|a:3:{s:20:\"_REQUEST_ACCESS_TIME\";d:1584001302.247032;s:6:\"_VALID\";a:3:{s:28:\"Laminas\\Session\\Validator\\Id\";s:26:\"b6eff8a5d6nl6hd5qcqvvk6f7l\";s:36:\"Laminas\\Session\\Validator\\RemoteAddr\";s:9:\"127.0.0.1\";s:39:\"Laminas\\Session\\Validator\\HttpUserAgent\";s:78:\"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:71.0) Gecko/20100101 Firefox/71.0\";}s:32:\"Laminas_Validator_Csrf_salt_csrf\";a:1:{s:6:\"EXPIRE\";i:1584001902;}}container_init|C:26:\"Laminas\\Stdlib\\ArrayObject\":455:{a:4:{s:7:\"storage\";a:5:{s:4:\"init\";i:1;s:7:\"sess_id\";s:26:\"0n4odr0elpq2ghlimd0midkk30\";s:10:\"remoteAddr\";s:9:\"127.0.0.1\";s:13:\"httpUserAgent\";s:78:\"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:71.0) Gecko/20100101 Firefox/71.0\";s:11:\"lastRequest\";s:19:\"2020-03-12 15:21:42\";}s:4:\"flag\";i:2;s:13:\"iteratorClass\";s:13:\"ArrayIterator\";s:19:\"protectedProperties\";a:4:{i:0;s:7:\"storage\";i:1;s:4:\"flag\";i:2;s:13:\"iteratorClass\";i:3;s:19:\"protectedProperties\";}}}Laminas_Validator_Csrf_salt_csrf|C:26:\"Laminas\\Stdlib\\ArrayObject\":391:{a:4:{s:7:\"storage\";a:2:{s:9:\"tokenList\";a:1:{s:32:\"b48ebe68307da81ba797fb3fb1d0e92c\";s:32:\"9a04f958e2ebc13781d9914b07cf65dd\";}s:4:\"hash\";s:65:\"9a04f958e2ebc13781d9914b07cf65dd-b48ebe68307da81ba797fb3fb1d0e92c\";}s:4:\"flag\";i:2;s:13:\"iteratorClass\";s:13:\"ArrayIterator\";s:19:\"protectedProperties\";a:4:{i:0;s:7:\"storage\";i:1;s:4:\"flag\";i:2;s:13:\"iteratorClass\";i:3;s:19:\"protectedProperties\";}}}','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:71.0) Gecko/20100101 Firefox/71.0','127.0.0.1',NULL),('32n3hc9r2h5n70bmsg0mq4r51s','Laminas_session',1584004721,2592000,'__Laminas|a:2:{s:20:\"_REQUEST_ACCESS_TIME\";d:1584004721.601296;s:6:\"_VALID\";a:3:{s:28:\"Laminas\\Session\\Validator\\Id\";s:26:\"0nppsevqeolqgtra5ef7ee8vgs\";s:36:\"Laminas\\Session\\Validator\\RemoteAddr\";s:9:\"127.0.0.1\";s:39:\"Laminas\\Session\\Validator\\HttpUserAgent\";s:78:\"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:71.0) Gecko/20100101 Firefox/71.0\";}}container_init|C:26:\"Laminas\\Stdlib\\ArrayObject\":455:{a:4:{s:7:\"storage\";a:5:{s:4:\"init\";i:1;s:7:\"sess_id\";s:26:\"32n3hc9r2h5n70bmsg0mq4r51s\";s:10:\"remoteAddr\";s:9:\"127.0.0.1\";s:13:\"httpUserAgent\";s:78:\"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:71.0) Gecko/20100101 Firefox/71.0\";s:11:\"lastRequest\";s:19:\"2020-03-12 16:18:41\";}s:4:\"flag\";i:2;s:13:\"iteratorClass\";s:13:\"ArrayIterator\";s:19:\"protectedProperties\";a:4:{i:0;s:7:\"storage\";i:1;s:4:\"flag\";i:2;s:13:\"iteratorClass\";i:3;s:19:\"protectedProperties\";}}}','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:71.0) Gecko/20100101 Firefox/71.0','127.0.0.1',NULL),('6cch4hmvqqun0tvdht5351mte2','Laminas_session',1584004651,2592000,'__Laminas|a:3:{s:20:\"_REQUEST_ACCESS_TIME\";d:1584004651.160406;s:6:\"_VALID\";a:3:{s:28:\"Laminas\\Session\\Validator\\Id\";s:26:\"fkm2n9jccd9bfvaljan6qlh1p1\";s:36:\"Laminas\\Session\\Validator\\RemoteAddr\";s:9:\"127.0.0.1\";s:39:\"Laminas\\Session\\Validator\\HttpUserAgent\";s:78:\"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:72.0) Gecko/20100101 Firefox/72.0\";}s:32:\"Laminas_Validator_Csrf_salt_csrf\";a:1:{s:6:\"EXPIRE\";i:1584005188;}}container_init|C:26:\"Laminas\\Stdlib\\ArrayObject\":473:{a:4:{s:7:\"storage\";a:6:{s:4:\"init\";i:1;s:7:\"sess_id\";s:26:\"6cch4hmvqqun0tvdht5351mte2\";s:10:\"remoteAddr\";s:9:\"127.0.0.1\";s:13:\"httpUserAgent\";s:78:\"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:72.0) Gecko/20100101 Firefox/72.0\";s:11:\"lastRequest\";s:19:\"2020-03-12 16:17:31\";s:3:\"uid\";s:1:\"1\";}s:4:\"flag\";i:2;s:13:\"iteratorClass\";s:13:\"ArrayIterator\";s:19:\"protectedProperties\";a:4:{i:0;s:7:\"storage\";i:1;s:4:\"flag\";i:2;s:13:\"iteratorClass\";i:3;s:19:\"protectedProperties\";}}}Laminas_Validator_Csrf_salt_csrf|C:26:\"Laminas\\Stdlib\\ArrayObject\":391:{a:4:{s:7:\"storage\";a:2:{s:9:\"tokenList\";a:1:{s:32:\"29baf9694c900c2ec9c88ff7d1b04961\";s:32:\"afb64504698f65e814b15499de341d91\";}s:4:\"hash\";s:65:\"afb64504698f65e814b15499de341d91-29baf9694c900c2ec9c88ff7d1b04961\";}s:4:\"flag\";i:2;s:13:\"iteratorClass\";s:13:\"ArrayIterator\";s:19:\"protectedProperties\";a:4:{i:0;s:7:\"storage\";i:1;s:4:\"flag\";i:2;s:13:\"iteratorClass\";i:3;s:19:\"protectedProperties\";}}}Laminas_AUTH|C:26:\"Laminas\\Stdlib\\ArrayObject\":1903:{a:4:{s:7:\"storage\";a:1:{s:14:\"authentication\";a:22:{s:2:\"id\";s:1:\"1\";s:8:\"username\";s:5:\"admin\";s:9:\"full_name\";s:11:\"Super Admin\";s:8:\"password\";s:60:\"$2y$10$fXGyPKdxujgtiCJAYVQIgO3Mg75RnsEbYLeUxGIvTL92rdakJVM3m\";s:5:\"email\";s:15:\"admin@localhost\";s:6:\"status\";s:1:\"1\";s:16:\"pass_reset_token\";N;s:12:\"created_date\";s:19:\"2019-12-10 10:19:14\";s:15:\"pass_reset_date\";N;s:14:\"redirect_route\";s:5:\"admin\";s:14:\"redirect_param\";s:2:\"{}\";s:14:\"redirect_query\";s:2:\"{}\";s:12:\"redirect_url\";s:0:\"\";s:9:\"main_role\";s:6:\"SUPADM\";s:9:\"main_ubis\";s:6:\"SUPADM\";s:7:\"is_ldap\";s:1:\"0\";s:13:\"mainrole_data\";a:7:{s:4:\"code\";s:6:\"SUPADM\";s:4:\"name\";s:11:\"SUPER ADMIN\";s:6:\"status\";s:1:\"1\";s:14:\"redirect_route\";s:5:\"admin\";s:14:\"redirect_param\";N;s:14:\"redirect_query\";N;s:12:\"redirect_url\";N;}s:13:\"mainubis_data\";a:9:{s:4:\"code\";s:6:\"SUPADM\";s:4:\"name\";s:11:\"Super Admin\";s:6:\"status\";s:1:\"1\";s:6:\"parent\";N;s:5:\"level\";N;s:14:\"redirect_route\";s:5:\"admin\";s:14:\"redirect_param\";N;s:14:\"redirect_query\";N;s:12:\"redirect_url\";N;}s:5:\"roles\";a:1:{s:6:\"SUPADM\";a:8:{s:4:\"code\";s:6:\"SUPADM\";s:4:\"name\";s:11:\"SUPER ADMIN\";s:6:\"status\";s:1:\"1\";s:14:\"redirect_route\";s:5:\"admin\";s:14:\"redirect_param\";N;s:14:\"redirect_query\";N;s:12:\"redirect_url\";N;s:7:\"is_main\";i:1;}}s:4:\"ubis\";a:1:{s:6:\"SUPADM\";a:10:{s:4:\"code\";s:6:\"SUPADM\";s:4:\"name\";s:11:\"Super Admin\";s:6:\"status\";s:1:\"1\";s:6:\"parent\";N;s:5:\"level\";N;s:14:\"redirect_route\";s:5:\"admin\";s:14:\"redirect_param\";N;s:14:\"redirect_query\";N;s:12:\"redirect_url\";N;s:7:\"is_main\";i:1;}}s:11:\"accessRoute\";a:1:{i:0;s:5:\"admin\";}s:12:\"accessModule\";a:3:{s:1:\"*\";a:1:{s:1:\"*\";a:1:{i:0;s:1:\"*\";}}s:3:\"App\";a:1:{s:4:\"User\";a:1:{i:0;s:7:\"profile\";}}s:4:\"Core\";a:1:{s:4:\"Ajax\";a:1:{i:0;s:9:\"callmodel\";}}}}}s:4:\"flag\";i:2;s:13:\"iteratorClass\";s:13:\"ArrayIterator\";s:19:\"protectedProperties\";a:4:{i:0;s:7:\"storage\";i:1;s:4:\"flag\";i:2;s:13:\"iteratorClass\";i:3;s:19:\"protectedProperties\";}}}FlashMessenger|C:26:\"Laminas\\Stdlib\\ArrayObject\":205:{a:4:{s:7:\"storage\";a:0:{}s:4:\"flag\";i:2;s:13:\"iteratorClass\";s:13:\"ArrayIterator\";s:19:\"protectedProperties\";a:4:{i:0;s:7:\"storage\";i:1;s:4:\"flag\";i:2;s:13:\"iteratorClass\";i:3;s:19:\"protectedProperties\";}}}','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:72.0) Gecko/20100101 Firefox/72.0','127.0.0.1','1'),('9j099ptob372e6fgc25c01711o','Laminas_session',1584001302,2592000,'__Laminas|a:3:{s:20:\"_REQUEST_ACCESS_TIME\";d:1584001302.755982;s:6:\"_VALID\";a:3:{s:28:\"Laminas\\Session\\Validator\\Id\";s:26:\"le0an2u1359n88jnmg8fhnuigd\";s:36:\"Laminas\\Session\\Validator\\RemoteAddr\";s:9:\"127.0.0.1\";s:39:\"Laminas\\Session\\Validator\\HttpUserAgent\";s:78:\"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:71.0) Gecko/20100101 Firefox/71.0\";}s:32:\"Laminas_Validator_Csrf_salt_csrf\";a:1:{s:6:\"EXPIRE\";i:1584001902;}}container_init|C:26:\"Laminas\\Stdlib\\ArrayObject\":455:{a:4:{s:7:\"storage\";a:5:{s:4:\"init\";i:1;s:7:\"sess_id\";s:26:\"9j099ptob372e6fgc25c01711o\";s:10:\"remoteAddr\";s:9:\"127.0.0.1\";s:13:\"httpUserAgent\";s:78:\"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:71.0) Gecko/20100101 Firefox/71.0\";s:11:\"lastRequest\";s:19:\"2020-03-12 15:21:42\";}s:4:\"flag\";i:2;s:13:\"iteratorClass\";s:13:\"ArrayIterator\";s:19:\"protectedProperties\";a:4:{i:0;s:7:\"storage\";i:1;s:4:\"flag\";i:2;s:13:\"iteratorClass\";i:3;s:19:\"protectedProperties\";}}}container_login|C:26:\"Laminas\\Stdlib\\ArrayObject\":219:{a:4:{s:7:\"storage\";a:1:{s:3:\"try\";i:2;}s:4:\"flag\";i:2;s:13:\"iteratorClass\";s:13:\"ArrayIterator\";s:19:\"protectedProperties\";a:4:{i:0;s:7:\"storage\";i:1;s:4:\"flag\";i:2;s:13:\"iteratorClass\";i:3;s:19:\"protectedProperties\";}}}Laminas_Validator_Csrf_salt_csrf|C:26:\"Laminas\\Stdlib\\ArrayObject\":471:{a:4:{s:7:\"storage\";a:2:{s:9:\"tokenList\";a:2:{s:32:\"5c217aff5ccdb77aa4de9ffbae0c9835\";s:32:\"5b62d1d8d26aef489da54b83719d210c\";s:32:\"2706d68d5a722d7997c143490473378a\";s:32:\"181d454b0e16bb5bbebd4ecb8ad03357\";}s:4:\"hash\";s:65:\"181d454b0e16bb5bbebd4ecb8ad03357-2706d68d5a722d7997c143490473378a\";}s:4:\"flag\";i:2;s:13:\"iteratorClass\";s:13:\"ArrayIterator\";s:19:\"protectedProperties\";a:4:{i:0;s:7:\"storage\";i:1;s:4:\"flag\";i:2;s:13:\"iteratorClass\";i:3;s:19:\"protectedProperties\";}}}','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:71.0) Gecko/20100101 Firefox/71.0','127.0.0.1',NULL),('b8likool30kvdq6oou7m9ej11e','Laminas_session',1584001754,2592000,'__Laminas|a:3:{s:20:\"_REQUEST_ACCESS_TIME\";d:1584001754.254972;s:6:\"_VALID\";a:3:{s:28:\"Laminas\\Session\\Validator\\Id\";s:26:\"3ad0j8q8eg3nsuj1k894h3frq1\";s:36:\"Laminas\\Session\\Validator\\RemoteAddr\";s:9:\"127.0.0.1\";s:39:\"Laminas\\Session\\Validator\\HttpUserAgent\";s:78:\"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:72.0) Gecko/20100101 Firefox/72.0\";}s:32:\"Laminas_Validator_Csrf_salt_csrf\";a:1:{s:6:\"EXPIRE\";i:1584002354;}}container_init|C:26:\"Laminas\\Stdlib\\ArrayObject\":455:{a:4:{s:7:\"storage\";a:5:{s:4:\"init\";i:1;s:7:\"sess_id\";s:26:\"b8likool30kvdq6oou7m9ej11e\";s:10:\"remoteAddr\";s:9:\"127.0.0.1\";s:13:\"httpUserAgent\";s:78:\"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:72.0) Gecko/20100101 Firefox/72.0\";s:11:\"lastRequest\";s:19:\"2020-03-12 15:29:14\";}s:4:\"flag\";i:2;s:13:\"iteratorClass\";s:13:\"ArrayIterator\";s:19:\"protectedProperties\";a:4:{i:0;s:7:\"storage\";i:1;s:4:\"flag\";i:2;s:13:\"iteratorClass\";i:3;s:19:\"protectedProperties\";}}}Laminas_Validator_Csrf_salt_csrf|C:26:\"Laminas\\Stdlib\\ArrayObject\":471:{a:4:{s:7:\"storage\";a:2:{s:9:\"tokenList\";a:2:{s:32:\"cb0abd3f03f63acdb13f9b84af6a7b81\";s:32:\"a21d1f1103240355e92608cf7d765d57\";s:32:\"29c0c8f7525ab56857c132a6f9cf3f2a\";s:32:\"c6ef7163f283320e617c305efe7d4004\";}s:4:\"hash\";s:65:\"c6ef7163f283320e617c305efe7d4004-29c0c8f7525ab56857c132a6f9cf3f2a\";}s:4:\"flag\";i:2;s:13:\"iteratorClass\";s:13:\"ArrayIterator\";s:19:\"protectedProperties\";a:4:{i:0;s:7:\"storage\";i:1;s:4:\"flag\";i:2;s:13:\"iteratorClass\";i:3;s:19:\"protectedProperties\";}}}','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:72.0) Gecko/20100101 Firefox/72.0','127.0.0.1',NULL),('bikct3cj5mstajv50st3ir7gc7','Laminas_session',1584004549,2592000,'__Laminas|a:3:{s:20:\"_REQUEST_ACCESS_TIME\";d:1584004549.437459;s:6:\"_VALID\";a:3:{s:28:\"Laminas\\Session\\Validator\\Id\";s:26:\"ag73p441ug6aa243o9vq59dc7p\";s:36:\"Laminas\\Session\\Validator\\RemoteAddr\";s:9:\"127.0.0.1\";s:39:\"Laminas\\Session\\Validator\\HttpUserAgent\";s:78:\"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:72.0) Gecko/20100101 Firefox/72.0\";}s:32:\"Laminas_Validator_Csrf_salt_csrf\";a:1:{s:6:\"EXPIRE\";i:1584005149;}}container_init|C:26:\"Laminas\\Stdlib\\ArrayObject\":455:{a:4:{s:7:\"storage\";a:5:{s:4:\"init\";i:1;s:7:\"sess_id\";s:26:\"bikct3cj5mstajv50st3ir7gc7\";s:10:\"remoteAddr\";s:9:\"127.0.0.1\";s:13:\"httpUserAgent\";s:78:\"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:72.0) Gecko/20100101 Firefox/72.0\";s:11:\"lastRequest\";s:19:\"2020-03-12 16:15:49\";}s:4:\"flag\";i:2;s:13:\"iteratorClass\";s:13:\"ArrayIterator\";s:19:\"protectedProperties\";a:4:{i:0;s:7:\"storage\";i:1;s:4:\"flag\";i:2;s:13:\"iteratorClass\";i:3;s:19:\"protectedProperties\";}}}Laminas_Validator_Csrf_salt_csrf|C:26:\"Laminas\\Stdlib\\ArrayObject\":471:{a:4:{s:7:\"storage\";a:2:{s:9:\"tokenList\";a:2:{s:32:\"8f01064e3f996750d43deee4eafc369e\";s:32:\"e8fb99ff4aa92831a04e7637beb34e1c\";s:32:\"22dec4df0267ac873369d551f952c349\";s:32:\"aca91e8d859963a25cebf6fc696567c9\";}s:4:\"hash\";s:65:\"aca91e8d859963a25cebf6fc696567c9-22dec4df0267ac873369d551f952c349\";}s:4:\"flag\";i:2;s:13:\"iteratorClass\";s:13:\"ArrayIterator\";s:19:\"protectedProperties\";a:4:{i:0;s:7:\"storage\";i:1;s:4:\"flag\";i:2;s:13:\"iteratorClass\";i:3;s:19:\"protectedProperties\";}}}','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:72.0) Gecko/20100101 Firefox/72.0','127.0.0.1',NULL),('c38nblaksmgbosip52rc05r2fp','Laminas_session',1584001240,2592000,'__Laminas|a:2:{s:20:\"_REQUEST_ACCESS_TIME\";d:1584001240.903788;s:6:\"_VALID\";a:3:{s:28:\"Laminas\\Session\\Validator\\Id\";s:26:\"d3r65m9c9iats5q02itjcj9lh0\";s:36:\"Laminas\\Session\\Validator\\RemoteAddr\";s:9:\"127.0.0.1\";s:39:\"Laminas\\Session\\Validator\\HttpUserAgent\";s:78:\"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:71.0) Gecko/20100101 Firefox/71.0\";}}container_init|C:26:\"Laminas\\Stdlib\\ArrayObject\":455:{a:4:{s:7:\"storage\";a:5:{s:4:\"init\";i:1;s:7:\"sess_id\";s:26:\"c38nblaksmgbosip52rc05r2fp\";s:10:\"remoteAddr\";s:9:\"127.0.0.1\";s:13:\"httpUserAgent\";s:78:\"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:71.0) Gecko/20100101 Firefox/71.0\";s:11:\"lastRequest\";s:19:\"2020-03-12 15:20:40\";}s:4:\"flag\";i:2;s:13:\"iteratorClass\";s:13:\"ArrayIterator\";s:19:\"protectedProperties\";a:4:{i:0;s:7:\"storage\";i:1;s:4:\"flag\";i:2;s:13:\"iteratorClass\";i:3;s:19:\"protectedProperties\";}}}','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:71.0) Gecko/20100101 Firefox/71.0','127.0.0.1',NULL),('d7io32ob6vpqvbb3vvat435nh1','Laminas_session',1584001243,2592000,'__Laminas|a:3:{s:20:\"_REQUEST_ACCESS_TIME\";d:1584001243.695172;s:6:\"_VALID\";a:3:{s:28:\"Laminas\\Session\\Validator\\Id\";s:26:\"1nvcfsvldtt2t8tpbem2tv0dh8\";s:36:\"Laminas\\Session\\Validator\\RemoteAddr\";s:9:\"127.0.0.1\";s:39:\"Laminas\\Session\\Validator\\HttpUserAgent\";s:78:\"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:71.0) Gecko/20100101 Firefox/71.0\";}s:32:\"Laminas_Validator_Csrf_salt_csrf\";a:1:{s:6:\"EXPIRE\";i:1584001843;}}container_init|C:26:\"Laminas\\Stdlib\\ArrayObject\":455:{a:4:{s:7:\"storage\";a:5:{s:4:\"init\";i:1;s:7:\"sess_id\";s:26:\"d7io32ob6vpqvbb3vvat435nh1\";s:10:\"remoteAddr\";s:9:\"127.0.0.1\";s:13:\"httpUserAgent\";s:78:\"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:71.0) Gecko/20100101 Firefox/71.0\";s:11:\"lastRequest\";s:19:\"2020-03-12 15:20:43\";}s:4:\"flag\";i:2;s:13:\"iteratorClass\";s:13:\"ArrayIterator\";s:19:\"protectedProperties\";a:4:{i:0;s:7:\"storage\";i:1;s:4:\"flag\";i:2;s:13:\"iteratorClass\";i:3;s:19:\"protectedProperties\";}}}Laminas_Validator_Csrf_salt_csrf|C:26:\"Laminas\\Stdlib\\ArrayObject\":631:{a:4:{s:7:\"storage\";a:2:{s:9:\"tokenList\";a:4:{s:32:\"c5bb033caa83ace74985fe436c122e77\";s:32:\"0d9607f26629e1bda5355ba8e31494c5\";s:32:\"d515129b1379285ad7534b4e6d0a43c6\";s:32:\"ff064415904af40035b7c43e11f27fe2\";s:32:\"4cc9aef0a908c704ea7496587710aeaa\";s:32:\"937682bb86e9dbec77a3b1eed53cf837\";s:32:\"81d4b6177e40b967e31f7993344ab58a\";s:32:\"0a503b83135b2b6687cdda9a828f6f66\";}s:4:\"hash\";s:65:\"0a503b83135b2b6687cdda9a828f6f66-81d4b6177e40b967e31f7993344ab58a\";}s:4:\"flag\";i:2;s:13:\"iteratorClass\";s:13:\"ArrayIterator\";s:19:\"protectedProperties\";a:4:{i:0;s:7:\"storage\";i:1;s:4:\"flag\";i:2;s:13:\"iteratorClass\";i:3;s:19:\"protectedProperties\";}}}container_login|C:26:\"Laminas\\Stdlib\\ArrayObject\":219:{a:4:{s:7:\"storage\";a:1:{s:3:\"try\";i:3;}s:4:\"flag\";i:2;s:13:\"iteratorClass\";s:13:\"ArrayIterator\";s:19:\"protectedProperties\";a:4:{i:0;s:7:\"storage\";i:1;s:4:\"flag\";i:2;s:13:\"iteratorClass\";i:3;s:19:\"protectedProperties\";}}}','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:71.0) Gecko/20100101 Firefox/71.0','127.0.0.1',NULL),('f68064r6iu1pru5sb3shvfanb2','Laminas_session',1584002964,2592000,'__Laminas|a:2:{s:20:\"_REQUEST_ACCESS_TIME\";d:1584002964.902438;s:6:\"_VALID\";a:3:{s:28:\"Laminas\\Session\\Validator\\Id\";s:26:\"ipn4sdvb3octpfu1tqfk77p7gv\";s:36:\"Laminas\\Session\\Validator\\RemoteAddr\";s:9:\"127.0.0.1\";s:39:\"Laminas\\Session\\Validator\\HttpUserAgent\";s:78:\"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:72.0) Gecko/20100101 Firefox/72.0\";}}container_init|C:26:\"Laminas\\Stdlib\\ArrayObject\":455:{a:4:{s:7:\"storage\";a:5:{s:4:\"init\";i:1;s:7:\"sess_id\";s:26:\"f68064r6iu1pru5sb3shvfanb2\";s:10:\"remoteAddr\";s:9:\"127.0.0.1\";s:13:\"httpUserAgent\";s:78:\"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:72.0) Gecko/20100101 Firefox/72.0\";s:11:\"lastRequest\";s:19:\"2020-03-12 15:49:24\";}s:4:\"flag\";i:2;s:13:\"iteratorClass\";s:13:\"ArrayIterator\";s:19:\"protectedProperties\";a:4:{i:0;s:7:\"storage\";i:1;s:4:\"flag\";i:2;s:13:\"iteratorClass\";i:3;s:19:\"protectedProperties\";}}}','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:72.0) Gecko/20100101 Firefox/72.0','127.0.0.1',NULL),('h8nnbig9cbarp7nsp72ihrfa3b','Laminas_session',1584002815,2592000,'__Laminas|a:2:{s:20:\"_REQUEST_ACCESS_TIME\";d:1584002815.771967;s:6:\"_VALID\";a:3:{s:28:\"Laminas\\Session\\Validator\\Id\";s:26:\"c4r6hbka2mitjehd9sq02s65tf\";s:36:\"Laminas\\Session\\Validator\\RemoteAddr\";s:9:\"127.0.0.1\";s:39:\"Laminas\\Session\\Validator\\HttpUserAgent\";s:78:\"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:72.0) Gecko/20100101 Firefox/72.0\";}}container_init|C:26:\"Laminas\\Stdlib\\ArrayObject\":455:{a:4:{s:7:\"storage\";a:5:{s:4:\"init\";i:1;s:7:\"sess_id\";s:26:\"h8nnbig9cbarp7nsp72ihrfa3b\";s:10:\"remoteAddr\";s:9:\"127.0.0.1\";s:13:\"httpUserAgent\";s:78:\"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:72.0) Gecko/20100101 Firefox/72.0\";s:11:\"lastRequest\";s:19:\"2020-03-12 15:46:55\";}s:4:\"flag\";i:2;s:13:\"iteratorClass\";s:13:\"ArrayIterator\";s:19:\"protectedProperties\";a:4:{i:0;s:7:\"storage\";i:1;s:4:\"flag\";i:2;s:13:\"iteratorClass\";i:3;s:19:\"protectedProperties\";}}}','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:72.0) Gecko/20100101 Firefox/72.0','127.0.0.1',NULL),('ifltmu85dv4n6aru87nag40g2h','Laminas_session',1584001435,2592000,'__Laminas|a:2:{s:20:\"_REQUEST_ACCESS_TIME\";d:1584001435.700356;s:6:\"_VALID\";a:3:{s:28:\"Laminas\\Session\\Validator\\Id\";s:26:\"9n8tm35pmu29u7nroltvnjairm\";s:36:\"Laminas\\Session\\Validator\\RemoteAddr\";s:9:\"127.0.0.1\";s:39:\"Laminas\\Session\\Validator\\HttpUserAgent\";s:78:\"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:72.0) Gecko/20100101 Firefox/72.0\";}}container_init|C:26:\"Laminas\\Stdlib\\ArrayObject\":455:{a:4:{s:7:\"storage\";a:5:{s:4:\"init\";i:1;s:7:\"sess_id\";s:26:\"ifltmu85dv4n6aru87nag40g2h\";s:10:\"remoteAddr\";s:9:\"127.0.0.1\";s:13:\"httpUserAgent\";s:78:\"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:72.0) Gecko/20100101 Firefox/72.0\";s:11:\"lastRequest\";s:19:\"2020-03-12 15:23:55\";}s:4:\"flag\";i:2;s:13:\"iteratorClass\";s:13:\"ArrayIterator\";s:19:\"protectedProperties\";a:4:{i:0;s:7:\"storage\";i:1;s:4:\"flag\";i:2;s:13:\"iteratorClass\";i:3;s:19:\"protectedProperties\";}}}','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:72.0) Gecko/20100101 Firefox/72.0','127.0.0.1',NULL),('io100hea4hkggigcr2p36cjn7n','Laminas_session',1583303005,2592000,'__Laminas|a:3:{s:20:\"_REQUEST_ACCESS_TIME\";d:1583303005.718832;s:6:\"_VALID\";a:3:{s:28:\"Laminas\\Session\\Validator\\Id\";s:26:\"mmkoijsult2rvgehilieqmu8pl\";s:36:\"Laminas\\Session\\Validator\\RemoteAddr\";s:3:\"::1\";s:39:\"Laminas\\Session\\Validator\\HttpUserAgent\";s:21:\"PostmanRuntime/7.22.0\";}s:32:\"Laminas_Validator_Csrf_salt_csrf\";a:1:{s:6:\"EXPIRE\";i:1583303605;}}container_init|C:26:\"Laminas\\Stdlib\\ArrayObject\":392:{a:4:{s:7:\"storage\";a:5:{s:4:\"init\";i:1;s:7:\"sess_id\";s:26:\"io100hea4hkggigcr2p36cjn7n\";s:10:\"remoteAddr\";s:3:\"::1\";s:13:\"httpUserAgent\";s:21:\"PostmanRuntime/7.22.0\";s:11:\"lastRequest\";s:19:\"2020-03-04 13:23:25\";}s:4:\"flag\";i:2;s:13:\"iteratorClass\";s:13:\"ArrayIterator\";s:19:\"protectedProperties\";a:4:{i:0;s:7:\"storage\";i:1;s:4:\"flag\";i:2;s:13:\"iteratorClass\";i:3;s:19:\"protectedProperties\";}}}Laminas_Validator_Csrf_salt_csrf|C:26:\"Laminas\\Stdlib\\ArrayObject\":471:{a:4:{s:7:\"storage\";a:2:{s:9:\"tokenList\";a:2:{s:32:\"f695468bf1a1a690a0235d7a08a96f0b\";s:32:\"9502a8882fde9e4cfdc6083e64a8a77b\";s:32:\"fb8bf4a74107b8dfe48c7678e9071003\";s:32:\"bb536ae7bd870521a84d5fc87fab1a8a\";}s:4:\"hash\";s:65:\"bb536ae7bd870521a84d5fc87fab1a8a-fb8bf4a74107b8dfe48c7678e9071003\";}s:4:\"flag\";i:2;s:13:\"iteratorClass\";s:13:\"ArrayIterator\";s:19:\"protectedProperties\";a:4:{i:0;s:7:\"storage\";i:1;s:4:\"flag\";i:2;s:13:\"iteratorClass\";i:3;s:19:\"protectedProperties\";}}}','PostmanRuntime/7.22.0','::1',NULL),('luo5qsq58kk94m7l7nhan5j0ri','Laminas_session',1584003065,2592000,'__Laminas|a:2:{s:20:\"_REQUEST_ACCESS_TIME\";d:1584003065.041073;s:6:\"_VALID\";a:3:{s:28:\"Laminas\\Session\\Validator\\Id\";s:26:\"5qn182le4aunniokovk8cqp4gj\";s:36:\"Laminas\\Session\\Validator\\RemoteAddr\";s:9:\"127.0.0.1\";s:39:\"Laminas\\Session\\Validator\\HttpUserAgent\";s:115:\"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 Safari/537.36\";}}container_init|C:26:\"Laminas\\Stdlib\\ArrayObject\":493:{a:4:{s:7:\"storage\";a:5:{s:4:\"init\";i:1;s:7:\"sess_id\";s:26:\"luo5qsq58kk94m7l7nhan5j0ri\";s:10:\"remoteAddr\";s:9:\"127.0.0.1\";s:13:\"httpUserAgent\";s:115:\"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 Safari/537.36\";s:11:\"lastRequest\";s:19:\"2020-03-12 15:51:05\";}s:4:\"flag\";i:2;s:13:\"iteratorClass\";s:13:\"ArrayIterator\";s:19:\"protectedProperties\";a:4:{i:0;s:7:\"storage\";i:1;s:4:\"flag\";i:2;s:13:\"iteratorClass\";i:3;s:19:\"protectedProperties\";}}}','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 Safari/537.36','127.0.0.1',NULL),('qv9eoec7g0f0tgsstdn6344ank','Laminas_session',1584001302,2592000,'__Laminas|a:2:{s:20:\"_REQUEST_ACCESS_TIME\";d:1584001302.066672;s:6:\"_VALID\";a:3:{s:28:\"Laminas\\Session\\Validator\\Id\";s:26:\"s7lqptm1m07lmtumhnj3c3bcic\";s:36:\"Laminas\\Session\\Validator\\RemoteAddr\";s:9:\"127.0.0.1\";s:39:\"Laminas\\Session\\Validator\\HttpUserAgent\";s:78:\"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:71.0) Gecko/20100101 Firefox/71.0\";}}container_init|C:26:\"Laminas\\Stdlib\\ArrayObject\":455:{a:4:{s:7:\"storage\";a:5:{s:4:\"init\";i:1;s:7:\"sess_id\";s:26:\"qv9eoec7g0f0tgsstdn6344ank\";s:10:\"remoteAddr\";s:9:\"127.0.0.1\";s:13:\"httpUserAgent\";s:78:\"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:71.0) Gecko/20100101 Firefox/71.0\";s:11:\"lastRequest\";s:19:\"2020-03-12 15:21:42\";}s:4:\"flag\";i:2;s:13:\"iteratorClass\";s:13:\"ArrayIterator\";s:19:\"protectedProperties\";a:4:{i:0;s:7:\"storage\";i:1;s:4:\"flag\";i:2;s:13:\"iteratorClass\";i:3;s:19:\"protectedProperties\";}}}','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:71.0) Gecko/20100101 Firefox/71.0','127.0.0.1',NULL),('vq041qkvaq1j9ruia5mu5sfur8','Laminas_session',1584004523,2592000,'__Laminas|a:4:{s:20:\"_REQUEST_ACCESS_TIME\";d:1584004523.222497;s:6:\"_VALID\";a:3:{s:28:\"Laminas\\Session\\Validator\\Id\";s:26:\"ae1fus0o9pvkn1ti3n428grqbh\";s:36:\"Laminas\\Session\\Validator\\RemoteAddr\";s:3:\"::1\";s:39:\"Laminas\\Session\\Validator\\HttpUserAgent\";s:109:\"Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.63 Safari/537.36\";}s:32:\"Laminas_Validator_Csrf_salt_csrf\";a:1:{s:6:\"EXPIRE\";i:1584000603;}s:14:\"FlashMessenger\";a:0:{}}container_init|C:26:\"Laminas\\Stdlib\\ArrayObject\":499:{a:4:{s:7:\"storage\";a:6:{s:4:\"init\";i:1;s:7:\"sess_id\";s:26:\"vq041qkvaq1j9ruia5mu5sfur8\";s:10:\"remoteAddr\";s:3:\"::1\";s:13:\"httpUserAgent\";s:109:\"Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.63 Safari/537.36\";s:11:\"lastRequest\";s:19:\"2020-03-12 16:15:23\";s:3:\"uid\";s:1:\"1\";}s:4:\"flag\";i:2;s:13:\"iteratorClass\";s:13:\"ArrayIterator\";s:19:\"protectedProperties\";a:4:{i:0;s:7:\"storage\";i:1;s:4:\"flag\";i:2;s:13:\"iteratorClass\";i:3;s:19:\"protectedProperties\";}}}Laminas_Validator_Csrf_salt_csrf|C:26:\"Laminas\\Stdlib\\ArrayObject\":471:{a:4:{s:7:\"storage\";a:2:{s:9:\"tokenList\";a:2:{s:32:\"9db5f2f51c923d9b961428fd655638ef\";s:32:\"6dd1d82a8c459b5af5ac8594c974248d\";s:32:\"e9424b3fd683ca96e78f6ba3db696cd0\";s:32:\"b8bd9b2c031e74d0232734e262fabd1c\";}s:4:\"hash\";s:65:\"b8bd9b2c031e74d0232734e262fabd1c-e9424b3fd683ca96e78f6ba3db696cd0\";}s:4:\"flag\";i:2;s:13:\"iteratorClass\";s:13:\"ArrayIterator\";s:19:\"protectedProperties\";a:4:{i:0;s:7:\"storage\";i:1;s:4:\"flag\";i:2;s:13:\"iteratorClass\";i:3;s:19:\"protectedProperties\";}}}Laminas_AUTH|C:26:\"Laminas\\Stdlib\\ArrayObject\":205:{a:4:{s:7:\"storage\";a:0:{}s:4:\"flag\";i:2;s:13:\"iteratorClass\";s:13:\"ArrayIterator\";s:19:\"protectedProperties\";a:4:{i:0;s:7:\"storage\";i:1;s:4:\"flag\";i:2;s:13:\"iteratorClass\";i:3;s:19:\"protectedProperties\";}}}FlashMessenger|C:26:\"Laminas\\Stdlib\\ArrayObject\":205:{a:4:{s:7:\"storage\";a:0:{}s:4:\"flag\";i:2;s:13:\"iteratorClass\";s:13:\"ArrayIterator\";s:19:\"protectedProperties\";a:4:{i:0;s:7:\"storage\";i:1;s:4:\"flag\";i:2;s:13:\"iteratorClass\";i:3;s:19:\"protectedProperties\";}}}container_login|C:26:\"Laminas\\Stdlib\\ArrayObject\":205:{a:4:{s:7:\"storage\";a:0:{}s:4:\"flag\";i:2;s:13:\"iteratorClass\";s:13:\"ArrayIterator\";s:19:\"protectedProperties\";a:4:{i:0;s:7:\"storage\";i:1;s:4:\"flag\";i:2;s:13:\"iteratorClass\";i:3;s:19:\"protectedProperties\";}}}','Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.63 Safari/537.36','::1','1');
/*!40000 ALTER TABLE `_session` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_ubis`
--

DROP TABLE IF EXISTS `_ubis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_ubis` (
  `code` varchar(10) NOT NULL,
  `name` varchar(100) NOT NULL,
  `status` smallint(1) NOT NULL DEFAULT 1,
  `parent` varchar(50) DEFAULT NULL,
  `level` varchar(50) DEFAULT NULL,
  `redirect_route` varchar(200) DEFAULT NULL,
  `redirect_param` varchar(200) DEFAULT NULL,
  `redirect_query` varchar(200) DEFAULT NULL,
  `redirect_url` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`code`),
  UNIQUE KEY `ubis_uniq_1` (`name`,`parent`) USING BTREE,
  KEY `ubis_name` (`name`),
  KEY `ubis_parent` (`parent`),
  KEY `ubis_level` (`level`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_ubis`
--

LOCK TABLES `_ubis` WRITE;
/*!40000 ALTER TABLE `_ubis` DISABLE KEYS */;
INSERT INTO `_ubis` VALUES ('GUEST','Guest',1,NULL,NULL,'app',NULL,NULL,NULL),('SUPADM','Super Admin',1,NULL,NULL,'admin',NULL,NULL,NULL);
/*!40000 ALTER TABLE `_ubis` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_user`
--

DROP TABLE IF EXISTS `_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_user` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `status` smallint(1) NOT NULL DEFAULT 0,
  `pass_reset_token` varchar(100) DEFAULT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  `pass_reset_date` datetime DEFAULT NULL,
  `redirect_route` varchar(200) DEFAULT NULL,
  `redirect_param` varchar(200) DEFAULT NULL,
  `redirect_query` varchar(200) DEFAULT NULL,
  `redirect_url` varchar(200) DEFAULT NULL,
  `main_role` varchar(10) DEFAULT NULL,
  `main_ubis` varchar(10) DEFAULT NULL,
  `is_ldap` smallint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_uniq_1` (`username`) USING BTREE,
  UNIQUE KEY `user_uniq_2` (`email`) USING BTREE,
  KEY `user_name` (`full_name`),
  KEY `user_role` (`main_role`),
  KEY `user_ubis` (`main_ubis`),
  CONSTRAINT `_user_ibfk_1` FOREIGN KEY (`main_role`) REFERENCES `_role` (`code`) ON DELETE SET NULL ON UPDATE SET NULL,
  CONSTRAINT `_user_ibfk_2` FOREIGN KEY (`main_ubis`) REFERENCES `_ubis` (`code`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_user`
--

LOCK TABLES `_user` WRITE;
/*!40000 ALTER TABLE `_user` DISABLE KEYS */;
INSERT INTO `_user` VALUES (1,'admin','Super Admin','$2y$10$fXGyPKdxujgtiCJAYVQIgO3Mg75RnsEbYLeUxGIvTL92rdakJVM3m','admin@localhost',1,NULL,'2019-12-10 10:19:14',NULL,'admin','{}','{}','','SUPADM','SUPADM',0),(11,'aaa','aaa','$2y$10$gYrRIXTmpuOUL1OYwS.N7eNrbg0.Q7OoShGRDGbRDvi0xgsLmLBA.','aaa@aaa.aaa',1,NULL,'2020-03-04 16:18:39',NULL,'admin','{}','{}','','GUEST','GUEST',0);
/*!40000 ALTER TABLE `_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_user_access`
--

DROP TABLE IF EXISTS `_user_access`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_user_access` (
  `user` int(11) NOT NULL,
  `module` int(11) NOT NULL DEFAULT 0,
  `controller` int(11) NOT NULL DEFAULT 0,
  `action` int(11) NOT NULL DEFAULT 0,
  `status` smallint(6) NOT NULL DEFAULT 1,
  `layout` varchar(100) DEFAULT NULL,
  `route` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`user`,`module`,`controller`,`action`),
  KEY `faccess_user` (`user`),
  KEY `faccess_module` (`module`),
  KEY `faccess_control` (`controller`),
  KEY `faccess_act` (`action`),
  CONSTRAINT `_user_access_ibfk_1` FOREIGN KEY (`user`) REFERENCES `_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `_user_access_ibfk_2` FOREIGN KEY (`module`) REFERENCES `_module` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `_user_access_ibfk_3` FOREIGN KEY (`controller`) REFERENCES `_controller` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `_user_access_ibfk_4` FOREIGN KEY (`action`) REFERENCES `_action` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_user_access`
--

LOCK TABLES `_user_access` WRITE;
/*!40000 ALTER TABLE `_user_access` DISABLE KEYS */;
INSERT INTO `_user_access` VALUES (1,0,0,0,1,NULL,'admin');
/*!40000 ALTER TABLE `_user_access` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_user_alert`
--

DROP TABLE IF EXISTS `_user_alert`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_user_alert` (
  `user` int(10) NOT NULL,
  `alert` int(4) NOT NULL,
  `status` int(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`user`,`alert`),
  KEY `unotif_user` (`user`),
  KEY `unotif_notif` (`alert`),
  CONSTRAINT `_user_alert_ibfk_1` FOREIGN KEY (`alert`) REFERENCES `_alert` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `_user_alert_ibfk_2` FOREIGN KEY (`user`) REFERENCES `_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_user_alert`
--

LOCK TABLES `_user_alert` WRITE;
/*!40000 ALTER TABLE `_user_alert` DISABLE KEYS */;
INSERT INTO `_user_alert` VALUES (1,1,1);
/*!40000 ALTER TABLE `_user_alert` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_user_inbox`
--

DROP TABLE IF EXISTS `_user_inbox`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_user_inbox` (
  `user` int(10) NOT NULL,
  `inbox` int(4) NOT NULL,
  `status` int(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`user`,`inbox`),
  KEY `uinbox_user` (`user`),
  KEY `uinbox_inbox` (`inbox`),
  CONSTRAINT `_user_inbox_ibfk_1` FOREIGN KEY (`inbox`) REFERENCES `_inbox` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `_user_inbox_ibfk_2` FOREIGN KEY (`user`) REFERENCES `_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_user_inbox`
--

LOCK TABLES `_user_inbox` WRITE;
/*!40000 ALTER TABLE `_user_inbox` DISABLE KEYS */;
INSERT INTO `_user_inbox` VALUES (1,1,1),(1,2,1);
/*!40000 ALTER TABLE `_user_inbox` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_user_menu`
--

DROP TABLE IF EXISTS `_user_menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_user_menu` (
  `user` int(10) NOT NULL,
  `menu` int(4) NOT NULL,
  `status` int(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`user`,`menu`),
  KEY `umenu_user` (`user`),
  KEY `umenu_menu` (`menu`),
  CONSTRAINT `_user_menu_ibfk_1` FOREIGN KEY (`menu`) REFERENCES `_menu` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `_user_menu_ibfk_2` FOREIGN KEY (`user`) REFERENCES `_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_user_menu`
--

LOCK TABLES `_user_menu` WRITE;
/*!40000 ALTER TABLE `_user_menu` DISABLE KEYS */;
INSERT INTO `_user_menu` VALUES (1,14,1),(1,15,1),(1,16,1),(1,17,1);
/*!40000 ALTER TABLE `_user_menu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_user_role`
--

DROP TABLE IF EXISTS `_user_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_user_role` (
  `user` int(10) NOT NULL,
  `role` varchar(10) NOT NULL,
  `status` smallint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`user`,`role`),
  KEY `urol_user` (`user`),
  KEY `urol_role` (`role`),
  CONSTRAINT `_user_role_ibfk_1` FOREIGN KEY (`role`) REFERENCES `_role` (`code`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `_user_role_ibfk_2` FOREIGN KEY (`user`) REFERENCES `_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_user_role`
--

LOCK TABLES `_user_role` WRITE;
/*!40000 ALTER TABLE `_user_role` DISABLE KEYS */;
INSERT INTO `_user_role` VALUES (1,'SUPADM',1);
/*!40000 ALTER TABLE `_user_role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_user_ubis`
--

DROP TABLE IF EXISTS `_user_ubis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_user_ubis` (
  `user` int(10) NOT NULL,
  `ubis` varchar(10) NOT NULL,
  `status` smallint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`user`,`ubis`),
  KEY `uubis_user` (`user`),
  KEY `uubis_ubis` (`ubis`),
  CONSTRAINT `_user_ubis_ibfk_1` FOREIGN KEY (`ubis`) REFERENCES `_ubis` (`code`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `_user_ubis_ibfk_2` FOREIGN KEY (`user`) REFERENCES `_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_user_ubis`
--

LOCK TABLES `_user_ubis` WRITE;
/*!40000 ALTER TABLE `_user_ubis` DISABLE KEYS */;
INSERT INTO `_user_ubis` VALUES (1,'SUPADM',1);
/*!40000 ALTER TABLE `_user_ubis` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'lam_sys'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2020-03-12 16:44:54
