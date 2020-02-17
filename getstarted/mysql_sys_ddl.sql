use lam_sys;

DROP TABLE IF EXISTS `lam_sys`.`_session`;
CREATE TABLE `lam_sys`.`_session` (
    `id` varchar(32),
    `name` varchar(32),
    `modified` int,
    `lifetime` int,
    `data` text,
    `uag` varchar(500),
    `ip` varchar(20),
    `uid` varchar(50),
    PRIMARY KEY (`id`, `name`),
    CONSTRAINT UNIQUE session_uq1 (`id`, `name`,`ip`, `uag`,`uid`),
    INDEX session_ip_uag_uid(`ip`,`uag`,`uid`),
    INDEX session_ip_uag(`ip`,`uag`),
    INDEX session_ip_uid(`ip`,`uid`),
    INDEX session_uag_uid(`uag`,`uid`),
    INDEX session_uid(`uid`),
    INDEX session_ip(`ip`),
    INDEX session_uag(`uag`)
);

DROP TABLE IF EXISTS `lam_sys`.`_role`;
CREATE TABLE `lam_sys`.`_role` (
    `code` varchar(10) NOT NULL,
    `name` varchar(50) NOT NULL,
    `status` smallint(1) NOT NULL DEFAULT '1',
    `redirect_route` varchar(200) DEFAULT NULL,
    `redirect_param` varchar(200) DEFAULT NULL,
    `redirect_query` varchar(200) DEFAULT NULL,
    `redirect_url` varchar(200) DEFAULT NULL,
    PRIMARY KEY (`code`),
    INDEX role_name (name),
    UNIQUE KEY `role_uniq_1` (`name`) USING BTREE
);

DROP TABLE IF EXISTS `lam_sys`.`_ubis`;
CREATE TABLE `lam_sys`.`_ubis` (
    `code` varchar(10) NOT NULL,
    `name` varchar(100) NOT NULL,
    `status` smallint(1) NOT NULL DEFAULT '1',
    `parent` varchar(50) DEFAULT NULL,
    `level` varchar(50) DEFAULT NULL,
    `redirect_route` varchar(200) DEFAULT NULL,
    `redirect_param` varchar(200) DEFAULT NULL,
    `redirect_query` varchar(200) DEFAULT NULL,
    `redirect_url` varchar(200) DEFAULT NULL,
    PRIMARY KEY (`code`),
    INDEX ubis_name (name),
    INDEX ubis_parent (parent),
    INDEX ubis_level (`level`),
    UNIQUE KEY `ubis_uniq_1` (`name`,parent) USING BTREE
);

DROP TABLE IF EXISTS `lam_sys`.`_menu`;
CREATE TABLE `lam_sys`.`_menu` (
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
    `status` int(1) DEFAULT '1',
    `desc` varchar(100) DEFAULT NULL,
    `priority` int(2) NOT NULL DEFAULT '1',
    PRIMARY KEY (`id`),
    INDEX menu_module (module),
    INDEX menu_layout (layout),
    INDEX menu_parent (parent),
    UNIQUE KEY `menu_uniq_1` (`module`,`layout`,`title`,`route`,`param`,`query`,`parent`) USING BTREE,
    UNIQUE KEY `menu_uniq_2` (`module`,`layout`,`title`,`url`,`parent`) USING BTREE
);

DROP TABLE IF EXISTS `lam_sys`.`_user`;
CREATE TABLE `lam_sys`.`_user` (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `username` varchar(100) NOT NULL,
    `full_name` varchar(100) NOT NULL,
    `password` varchar(100) NOT NULL,
    `email` varchar(100) NOT NULL,
    `status` smallint(1) NOT NULL DEFAULT '0',
    `pass_reset_token` varchar(100) DEFAULT NULL,
    `created_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `pass_reset_date` datetime DEFAULT NULL,
    `redirect_route` varchar(200) DEFAULT NULL,
    `redirect_param` varchar(200) DEFAULT NULL,
    `redirect_query` varchar(200) DEFAULT NULL,
    `redirect_url` varchar(200) DEFAULT NULL,
    `main_role` varchar(10) DEFAULT NULL,
    `main_ubis` varchar(10) DEFAULT NULL,
    `is_ldap` smallint(1) NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`),
    INDEX user_name (full_name),
    INDEX user_role (main_role),
    INDEX user_ubis (main_ubis),
    FOREIGN KEY (main_role) REFERENCES _role(code) ON DELETE SET NULL ON UPDATE SET NULL,
    FOREIGN KEY (main_ubis) REFERENCES _ubis(code) ON DELETE SET NULL ON UPDATE SET NULL,
    UNIQUE KEY `user_uniq_1` (`username`) USING BTREE,
    UNIQUE KEY `user_uniq_2` (`email`) USING BTREE
);

DROP TABLE IF EXISTS `lam_sys`.`_user_role`;
CREATE TABLE `lam_sys`.`_user_role` (
    `user` int(10),
    `role` varchar(10),
    `status` smallint(1) NOT NULL DEFAULT '1',
    PRIMARY KEY (`user`,`role`),
    INDEX urol_user (`user`),
    INDEX urol_role (`role`),
    FOREIGN KEY (`role`) REFERENCES _role(code) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (`user`) REFERENCES _user(id) ON DELETE CASCADE ON UPDATE CASCADE
);

DROP TABLE IF EXISTS `lam_sys`.`_user_ubis`;
CREATE TABLE `lam_sys`.`_user_ubis` (
    `user` int(10),
    `ubis` varchar(10),
    `status` smallint(1) NOT NULL DEFAULT '1',
    PRIMARY KEY (`user`,`ubis`),
    INDEX uubis_user (`user`),
    INDEX uubis_ubis (`ubis`),
    FOREIGN KEY (`ubis`) REFERENCES _ubis(code) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (`user`) REFERENCES _user(id) ON DELETE CASCADE ON UPDATE CASCADE
);

DROP TABLE IF EXISTS `lam_sys`.`_user_menu`;
CREATE TABLE `lam_sys`.`_user_menu` (
    `user` int(10),
    `menu` int(4),
    `status` int(1) NOT NULL DEFAULT '1',
    PRIMARY KEY (`user`,`menu`),
    INDEX umenu_user (`user`),
    INDEX umenu_menu (`menu`),
    FOREIGN KEY (`menu`) REFERENCES _menu(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (`user`) REFERENCES _user(id) ON DELETE CASCADE ON UPDATE CASCADE
);

DROP TABLE IF EXISTS `lam_sys`.`_role_menu`;
CREATE TABLE `lam_sys`.`_role_menu` (
    `role` varchar(10),
    `menu` int(4),
    `status` int(1) NOT NULL DEFAULT '1',
    PRIMARY KEY (`role`,`menu`),
    INDEX rolmenu_role (`role`),
    INDEX rolmenu_menu (`menu`),
    FOREIGN KEY (`menu`) REFERENCES _menu(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (`role`) REFERENCES _role(code) ON DELETE CASCADE ON UPDATE CASCADE
);

DROP TABLE IF EXISTS `lam_sys`.`_module`;
CREATE TABLE `lam_sys`.`_module` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(50) NOT NULL,
    `status` smallint(1) NOT NULL DEFAULT '1',
    `allow_all` smallint(1) NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`),
    INDEX module_name(name),
    UNIQUE KEY `modul_uniq_1` (`name`) USING BTREE
);

DROP TABLE IF EXISTS `lam_sys`.`_controller`;
CREATE TABLE `lam_sys`.`_controller` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `module` int(11),
    `name` varchar(50) NOT NULL,
    `status` smallint(1) NOT NULL DEFAULT '1',
    `allow_all` smallint(1) NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`),
    INDEX control_name (name),
    UNIQUE KEY `control_uniq_1` (`module`,`name`) USING BTREE,
    FOREIGN KEY (module) REFERENCES _module(id) ON DELETE SET NULL ON UPDATE SET NULL
);

DROP TABLE IF EXISTS `lam_sys`.`_action`;
CREATE TABLE `lam_sys`.`_action` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `controller` int(11),
    `name` varchar(50) NOT NULL,
    `status` smallint(1) NOT NULL DEFAULT '1',
    `allow_all` smallint(1) NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`),
    INDEX act_name (name),
    UNIQUE KEY `act_uniq_1` (`controller`,`name`) USING BTREE,
    FOREIGN KEY (controller) REFERENCES _controller(id) ON DELETE SET NULL ON UPDATE SET NULL
);

DROP TABLE IF EXISTS `lam_sys`.`_filter_access`;
CREATE TABLE `lam_sys`.`_filter_access` (
    `role` varchar(10),
    `module` int(11) DEFAULT '0',
    `controller` int(11) DEFAULT '0',
    `action` int(11) DEFAULT '0',
    `status` smallint(6) NOT NULL DEFAULT '1',
    `layout` varchar(100) DEFAULT NULL,
    PRIMARY KEY (`role`,`module`,`controller`,`action`),
    INDEX faccess_role (`role`),
    INDEX faccess_module (`module`),
    INDEX faccess_control (`controller`),
    INDEX faccess_act (`action`),
    FOREIGN KEY (`role`) REFERENCES _role(code) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (`module`) REFERENCES _module(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (`controller`) REFERENCES _controller(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (`action`) REFERENCES _action(id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE lam_sys.`_user_access` (
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
  CONSTRAINT `_user_access_ibfk_1` FOREIGN KEY (`user`) REFERENCES lam_sys.`_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `_user_access_ibfk_2` FOREIGN KEY (`module`) REFERENCES lam_sys.`_module` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `_user_access_ibfk_3` FOREIGN KEY (`controller`) REFERENCES lam_sys.`_controller` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `_user_access_ibfk_4` FOREIGN KEY (`action`) REFERENCES lam_sys.`_action` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
);