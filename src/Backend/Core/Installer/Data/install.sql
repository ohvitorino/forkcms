CREATE TABLE IF NOT EXISTS `meta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `keywords` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `keywords_overwrite` tinyint(1) NOT NULL DEFAULT '0',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description_overwrite` tinyint(1) NOT NULL DEFAULT '0',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title_overwrite` tinyint(1) NOT NULL DEFAULT '0',
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url_overwrite` tinyint(1) NOT NULL DEFAULT '0',
  `custom` longtext COLLATE utf8mb4_unicode_ci,
  `data` longtext COLLATE utf8mb4_unicode_ci,
  `seo_follow` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:seo_follow)',
  `seo_index` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:seo_index)',
  PRIMARY KEY (`id`),
  KEY `idx_url` (`url`(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `modules` (
  `name` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT 'unique module name',
  `installed_on` datetime NOT NULL,
  PRIMARY KEY (`name`),
  KEY `idx_name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `modules_extras` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Unique ID for the extra.',
  `module` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT 'The name of the module this extra belongs to.',
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `label` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'The label for this extra. It will be used for displaying purposes.',
  `action` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `data` text COLLATE utf8mb4_unicode_ci COMMENT 'A serialized value with the optional parameters',
  `hidden` tinyint(1) NOT NULL DEFAULT '0',
  `sequence` int(11) NOT NULL COMMENT 'The sequence in the backend.',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='The possible extras' AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `modules_settings` (
  `module` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT 'name of the module',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'name of the setting',
  `value` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'serialized value',
  PRIMARY KEY (`module`(25),`name`(100))
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `modules_tags` (
  `module` varchar(255) CHARACTER SET utf8 NOT NULL,
  `tag_id` int(11) NOT NULL,
  `other_id` int(11) NOT NULL,
  PRIMARY KEY (`module`,`tag_id`,`other_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `groups` (
  `id` INT AUTO_INCREMENT NOT NULL,
  `name` VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `parameters` TEXT COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'serialized array containing default user module/action rights',
  PRIMARY KEY(id)
) ENGINE = InnoDB DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;


CREATE TABLE `groups_rights_actions` (
  `id` INT AUTO_INCREMENT NOT NULL,
  `group_id` INT DEFAULT NULL,
  `module` VARCHAR(255) NOT NULL,
  `action` VARCHAR(255) NOT NULL,
  `level` INT NOT NULL,
  INDEX IDX_15585189FE54D947 (group_id),
  PRIMARY KEY(id)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;


CREATE TABLE `groups_rights_modules` (
  `id` INT AUTO_INCREMENT NOT NULL,
  `group_id` INT DEFAULT NULL,
  `module` VARCHAR(255) NOT NULL,
  INDEX IDX_3EA7E3B1FE54D947 (group_id),
  PRIMARY KEY(id)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;


CREATE TABLE `groups_settings` (
  `name` VARCHAR(255) NOT NULL,
  `group_id` INT NOT NULL,
  `value` LONGTEXT DEFAULT NULL,
  INDEX IDX_5E575A1AFE54D947 (group_id),
  PRIMARY KEY(group_id, name)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;


ALTER TABLE `groups_rights_actions` ADD CONSTRAINT FK_15585189FE54D947 FOREIGN KEY (group_id) REFERENCES groups (id) ON DELETE CASCADE;
ALTER TABLE `groups_rights_modules` ADD CONSTRAINT FK_3EA7E3B1FE54D947 FOREIGN KEY (group_id) REFERENCES groups (id) ON DELETE CASCADE;
ALTER TABLE `groups_settings` ADD CONSTRAINT FK_5E575A1AFE54D947 FOREIGN KEY (group_id) REFERENCES groups (id);


INSERT INTO `groups` (`id`, `name`, `parameters`) VALUES
(1, 'admin', NULL) ON DUPLICATE KEY UPDATE id=1;


CREATE  TABLE IF NOT EXISTS `backend_navigation` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL,
  `label` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `selected_for` text COLLATE utf8mb4_unicode_ci,
  `sequence` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;
