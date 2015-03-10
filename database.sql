# ************************************************************
# Sequel Pro SQL dump
# Version 4135
#
# http://www.sequelpro.com/
# http://code.google.com/p/sequel-pro/
#
# Host: 127.0.0.1 (MySQL 5.5.38)
# Database: dev
# Generation Time: 2015-03-10 11:38:46 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT = @@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS = @@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION = @@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS = @@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS = 0 */;
/*!40101 SET @OLD_SQL_MODE = @@SQL_MODE, SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES = @@SQL_NOTES, SQL_NOTES = 0 */;


# Dump of table list_authors
# ------------------------------------------------------------

DROP TABLE IF EXISTS `list_authors`;

CREATE TABLE `list_authors` (
  `id`             INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `family`         VARCHAR(50)
                   COLLATE utf8_bin NOT NULL DEFAULT '',
  `given`          VARCHAR(50)
                   COLLATE utf8_bin NOT NULL DEFAULT '',
  `academic_title` VARCHAR(20)
                   COLLATE utf8_bin          DEFAULT NULL,
  `website`        TEXT
                   COLLATE utf8_bin,
  `contact`        TEXT
                   COLLATE utf8_bin,
  `text`           TEXT
                   COLLATE utf8_bin,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_name` (`family`, `given`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin
  COMMENT = 'Stores all authors.';


# Dump of table list_journals
# ------------------------------------------------------------

DROP TABLE IF EXISTS `list_journals`;

CREATE TABLE `list_journals` (
  `id`           INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`         VARCHAR(200)
                 COLLATE utf8_bin NOT NULL,
  `date`         DATE                      DEFAULT NULL,
  `publisher_id` INT(11) UNSIGNED          DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;


# Dump of table list_keywords
# ------------------------------------------------------------

DROP TABLE IF EXISTS `list_keywords`;

CREATE TABLE `list_keywords` (
  `id`   INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100)
         COLLATE utf8_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin
  COMMENT = 'Stores all key terms.';


# Dump of table list_permissions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `list_permissions`;

CREATE TABLE `list_permissions` (
  `id`   INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(50)
         COLLATE utf8_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;


# Dump of table list_publications
# ------------------------------------------------------------

DROP TABLE IF EXISTS `list_publications`;

CREATE TABLE `list_publications` (
  `id`             INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type_id`        INT(11) UNSIGNED          DEFAULT NULL
  COMMENT 'Links to the type name.',
  `study_field_id` INT(11) UNSIGNED          DEFAULT NULL
  COMMENT 'Links to the study field name.',
  `title`          VARCHAR(200)
                   COLLATE utf8_bin NOT NULL DEFAULT '',
  `date_added`     TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_published` DATE                      DEFAULT NULL,
  `booktitle`      VARCHAR(200)
                   COLLATE utf8_bin          DEFAULT NULL,
  `journal`        VARCHAR(200)
                   COLLATE utf8_bin          DEFAULT NULL,
  `volume`         INT(5) UNSIGNED           DEFAULT NULL,
  `number`         INT(5) UNSIGNED           DEFAULT NULL,
  `pages_from`     INT(5) UNSIGNED           DEFAULT NULL,
  `pages_to`       INT(5) UNSIGNED           DEFAULT NULL,
  `series`         VARCHAR(50)
                   COLLATE utf8_bin          DEFAULT NULL,
  `edition`        VARCHAR(50)
                   COLLATE utf8_bin          DEFAULT NULL,
  `note`           VARCHAR(200)
                   COLLATE utf8_bin          DEFAULT NULL,
  `location`       VARCHAR(50)
                   COLLATE utf8_bin          DEFAULT NULL,
  `publisher`      VARCHAR(200)
                   COLLATE utf8_bin          DEFAULT NULL,
  `institution`    VARCHAR(200)
                   COLLATE utf8_bin          DEFAULT NULL,
  `school`         VARCHAR(200)
                   COLLATE utf8_bin          DEFAULT NULL,
  `address`        VARCHAR(200)
                   COLLATE utf8_bin          DEFAULT NULL,
  `isbn`           VARCHAR(20)
                   COLLATE utf8_bin          DEFAULT NULL,
  `doi`            VARCHAR(50)
                   COLLATE utf8_bin          DEFAULT NULL,
  `howpublished`   VARCHAR(200)
                   COLLATE utf8_bin          DEFAULT NULL,
  `abstract`       TEXT
                   COLLATE utf8_bin,
  `copyright`      VARCHAR(200)
                   COLLATE utf8_bin          DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `title` (`title`),
  KEY `publication_to_type_idx` (`type_id`),
  KEY `publication_to_study_field_idx` (`study_field_id`),
  CONSTRAINT `publication_to_study_field` FOREIGN KEY (`study_field_id`) REFERENCES `list_study_fields` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `publication_to_type` FOREIGN KEY (`type_id`) REFERENCES `list_types` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin
  COMMENT = 'Stores all publications. Authors and key terms can be found by joining the relation tables.';


# Dump of table list_publishers
# ------------------------------------------------------------

DROP TABLE IF EXISTS `list_publishers`;

CREATE TABLE `list_publishers` (
  `id`   INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(200)
         COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;


# Dump of table list_references
# ------------------------------------------------------------

DROP TABLE IF EXISTS `list_references`;

CREATE TABLE `list_references` (
  `id`                 INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `ref_publication_id` INT(11) UNSIGNED          DEFAULT NULL
  COMMENT 'Links to an existing publication.',
  `text`               TEXT
                       COLLATE utf8_bin,
  `external_url`       TEXT
                       COLLATE utf8_bin,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin
  COMMENT = 'Stores all references, either linking to an existing publication or an external one. This table is still TODO';


# Dump of table list_roles
# ------------------------------------------------------------

DROP TABLE IF EXISTS `list_roles`;

CREATE TABLE `list_roles` (
  `id`   INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(20)
         COLLATE utf8_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;


# Dump of table list_study_fields
# ------------------------------------------------------------

DROP TABLE IF EXISTS `list_study_fields`;

CREATE TABLE `list_study_fields` (
  `id`   INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100)
         COLLATE utf8_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin
  COMMENT = 'Stores all study fields.';


# Dump of table list_types
# ------------------------------------------------------------

DROP TABLE IF EXISTS `list_types`;

CREATE TABLE `list_types` (
  `id`   INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(20)
         COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin
  COMMENT = 'Stores all types of publications. Uses known BibTeX types.';


# Dump of table list_users
# ------------------------------------------------------------

DROP TABLE IF EXISTS `list_users`;

CREATE TABLE `list_users` (
  `id`              INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`            VARCHAR(40)
                    COLLATE utf8_bin NOT NULL,
  `password`        VARCHAR(50)
                    COLLATE utf8_bin NOT NULL,
  `mail`            VARCHAR(40)
                    COLLATE utf8_bin NOT NULL,
  `date_register`   TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_last_login` TIMESTAMP        NULL     DEFAULT NULL,
  `active`          TINYINT(1)       NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin
  COMMENT = 'Stores all registered users.';


# Dump of table rel_publ_to_authors
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rel_publ_to_authors`;

CREATE TABLE `rel_publ_to_authors` (
  `id`             INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `publication_id` INT(11) UNSIGNED NOT NULL,
  `author_id`      INT(11) UNSIGNED NOT NULL,
  `priority`       INT(11) UNSIGNED NOT NULL DEFAULT '1'
  COMMENT 'Used for sorting the authors.',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_entries` (`publication_id`, `author_id`),
  KEY `rel_author_to_author_idx` (`author_id`),
  KEY `rel_author_to_publ_idx` (`publication_id`),
  CONSTRAINT `rel_author_to_publ` FOREIGN KEY (`publication_id`) REFERENCES `list_publications` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin
  COMMENT = 'Links a publication to it’s authors.';


# Dump of table rel_publ_to_references
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rel_publ_to_references`;

CREATE TABLE `rel_publ_to_references` (
  `id`             INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `publication_id` INT(11) UNSIGNED NOT NULL,
  `reference_id`   INT(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `rel_references_to_reference_idx` (`reference_id`),
  KEY `rel_references_to_publ_idx` (`publication_id`),
  CONSTRAINT `rel_references_to_publ` FOREIGN KEY (`publication_id`) REFERENCES `list_publications` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `rel_references_to_reference` FOREIGN KEY (`reference_id`) REFERENCES `list_references` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin
  COMMENT = 'Links a publication to it’s references.';



# Dump of table rel_publication_keywords
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rel_publication_keywords`;

CREATE TABLE `rel_publication_keywords` (
  `id`             INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `publication_id` INT(11) UNSIGNED NOT NULL,
  `keyword_id`     INT(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_entries` (`publication_id`, `keyword_id`),
  KEY `keyword_id` (`keyword_id`),
  CONSTRAINT `rel_publication_keywords_ibfk_1` FOREIGN KEY (`publication_id`) REFERENCES `list_publications` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `rel_publication_keywords_ibfk_2` FOREIGN KEY (`keyword_id`) REFERENCES `list_keywords` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin
  COMMENT = 'Links a publication to it’s key terms.';


# Dump of table rel_roles_permissions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rel_roles_permissions`;

CREATE TABLE `rel_roles_permissions` (
  `id`            INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `role_id`       INT(10) UNSIGNED NOT NULL,
  `permission_id` INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_entries` (`role_id`, `permission_id`),
  KEY `to_permissions` (`permission_id`),
  CONSTRAINT `to_permissions` FOREIGN KEY (`permission_id`) REFERENCES `list_permissions` (`id`),
  CONSTRAINT `to_roles` FOREIGN KEY (`role_id`) REFERENCES `list_roles` (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;


# Dump of table rel_user_roles
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rel_user_roles`;

CREATE TABLE `rel_user_roles` (
  `id`      INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT(10) UNSIGNED NOT NULL,
  `role_id` INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_entry` (`user_id`, `role_id`),
  KEY `to_role` (`role_id`),
  CONSTRAINT `to_role` FOREIGN KEY (`role_id`) REFERENCES `list_roles` (`id`),
  CONSTRAINT `to_user` FOREIGN KEY (`user_id`) REFERENCES `list_users` (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;


/*!40111 SET SQL_NOTES = @OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE = @OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS = @OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT = @OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS = @OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION = @OLD_COLLATION_CONNECTION */;
