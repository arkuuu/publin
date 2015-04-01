# ************************************************************
# Sequel Pro SQL dump
# Version 4135
#
# http://www.sequelpro.com/
# http://code.google.com/p/sequel-pro/
#
# Host: 127.0.0.1 (MySQL 5.5.38)
# Database: dev
# Generation Time: 2015-04-01 17:49:15 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT = @@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS = @@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION = @@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS = @@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS = 0 */;
/*!40101 SET @OLD_SQL_MODE = @@SQL_MODE, SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES = @@SQL_NOTES, SQL_NOTES = 0 */;


# Dump of table authors
# ------------------------------------------------------------

CREATE TABLE `authors` (
  `id`       INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `family`   VARCHAR(50)      NOT NULL DEFAULT '',
  `given`    VARCHAR(50)      NOT NULL DEFAULT '',
  `website`  TEXT,
  `contact`  TEXT,
  `about`    TEXT,
  `modified` TIMESTAMP        NULL     DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_name` (`family`, `given`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COMMENT = 'Stores authors.';



# Dump of table files
# ------------------------------------------------------------

CREATE TABLE `files` (
  `id`             INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `publication_id` INT(11) UNSIGNED          DEFAULT NULL,
  `name`           VARCHAR(100)              DEFAULT NULL,
  `title`          VARCHAR(100)              DEFAULT NULL,
  `full_text`      TINYINT(1)                DEFAULT '0',
  `restricted`     TINYINT(1)                DEFAULT '0',
  `hidden`         TINYINT(1)                DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `file_name` (`name`),
  KEY `publication_id` (`publication_id`),
  CONSTRAINT `files_ibfk_1` FOREIGN KEY (`publication_id`) REFERENCES `publications` (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COMMENT = 'Stores files and links to their publications.';


# Dump of table keywords
# ------------------------------------------------------------

CREATE TABLE `keywords` (
  `id`   INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100)     NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COMMENT = 'Stores keywords.';


# Dump of table oai_tokens
# ------------------------------------------------------------

CREATE TABLE `oai_tokens` (
  `id`              INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `created`         TIMESTAMP        NULL     DEFAULT CURRENT_TIMESTAMP,
  `metadata_prefix` VARCHAR(10)               DEFAULT NULL,
  `from`            VARCHAR(50)               DEFAULT NULL,
  `until`           VARCHAR(50)               DEFAULT NULL,
  `set`             VARCHAR(100)              DEFAULT NULL,
  `cursor`          INT(11) UNSIGNED          DEFAULT NULL,
  `list_size`       INT(11) UNSIGNED          DEFAULT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COMMENT = 'Stores the tokens of the OAI interface.';


# Dump of table permissions
# ------------------------------------------------------------

CREATE TABLE `permissions` (
  `id`   INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(50)      NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COMMENT = 'Stores permissions.';


# Dump of table publications
# ------------------------------------------------------------

CREATE TABLE `publications` (
  `id`             INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type_id`        INT(11) UNSIGNED          DEFAULT NULL
  COMMENT 'Links to the type name.',
  `study_field_id` INT(11) UNSIGNED          DEFAULT NULL
  COMMENT 'Links to the study field name.',
  `title`          VARCHAR(200)     NOT NULL DEFAULT '',
  `date_added`     TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_published` DATE                      DEFAULT NULL,
  `booktitle`      VARCHAR(200)              DEFAULT NULL,
  `journal`        VARCHAR(200)              DEFAULT NULL,
  `volume`         INT(5) UNSIGNED           DEFAULT NULL,
  `number`         INT(5) UNSIGNED           DEFAULT NULL,
  `pages_from`     INT(5) UNSIGNED           DEFAULT NULL,
  `pages_to`       INT(5) UNSIGNED           DEFAULT NULL,
  `series`         VARCHAR(50)               DEFAULT NULL,
  `edition`        VARCHAR(50)               DEFAULT NULL,
  `note`           VARCHAR(200)              DEFAULT NULL,
  `location`       VARCHAR(50)               DEFAULT NULL,
  `publisher`      VARCHAR(200)              DEFAULT NULL,
  `institution`    VARCHAR(200)              DEFAULT NULL,
  `school`         VARCHAR(200)              DEFAULT NULL,
  `address`        VARCHAR(200)              DEFAULT NULL,
  `isbn`           VARCHAR(20)               DEFAULT NULL,
  `doi`            VARCHAR(50)               DEFAULT NULL,
  `howpublished`   VARCHAR(200)              DEFAULT NULL,
  `abstract`       TEXT,
  `copyright`      VARCHAR(200)              DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `title` (`title`),
  KEY `publication_to_type_idx` (`type_id`),
  KEY `publication_to_study_field_idx` (`study_field_id`),
  CONSTRAINT `publication_to_study_field` FOREIGN KEY (`study_field_id`) REFERENCES `study_fields` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `publication_to_type` FOREIGN KEY (`type_id`) REFERENCES `types` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COMMENT = 'Stores all publications.';


# Dump of table publications_authors
# ------------------------------------------------------------

CREATE TABLE `publications_authors` (
  `id`             INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `publication_id` INT(11) UNSIGNED NOT NULL,
  `author_id`      INT(11) UNSIGNED NOT NULL,
  `priority`       INT(11) UNSIGNED NOT NULL DEFAULT '1'
  COMMENT 'Used for sorting the authors.',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_entries` (`publication_id`, `author_id`),
  KEY `rel_author_to_author_idx` (`author_id`),
  KEY `rel_author_to_publ_idx` (`publication_id`),
  CONSTRAINT `rel_author_to_publ` FOREIGN KEY (`publication_id`) REFERENCES `publications` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COMMENT = 'Links publications to their authors.';


# Dump of table publications_keywords
# ------------------------------------------------------------

CREATE TABLE `publications_keywords` (
  `id`             INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `publication_id` INT(11) UNSIGNED NOT NULL,
  `keyword_id`     INT(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_entries` (`publication_id`, `keyword_id`),
  KEY `keyword_id` (`keyword_id`),
  CONSTRAINT `publications_keywords_ibfk_1` FOREIGN KEY (`publication_id`) REFERENCES `publications` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `publications_keywords_ibfk_2` FOREIGN KEY (`keyword_id`) REFERENCES `keywords` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COMMENT = 'Links publications to their keywords';


# Dump of table roles
# ------------------------------------------------------------

CREATE TABLE `roles` (
  `id`   INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(20)      NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COMMENT = 'Stores roles.';


# Dump of table roles_permissions
# ------------------------------------------------------------

CREATE TABLE `roles_permissions` (
  `id`            INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `role_id`       INT(11) UNSIGNED NOT NULL,
  `permission_id` INT(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_entries` (`role_id`, `permission_id`),
  KEY `to_permissions` (`permission_id`),
  CONSTRAINT `to_permissions` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`),
  CONSTRAINT `to_roles` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COMMENT = 'Links roles to their permissions.';


# Dump of table study_fields
# ------------------------------------------------------------

CREATE TABLE `study_fields` (
  `id`          INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(100)     NOT NULL DEFAULT '',
  `description` TEXT,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COMMENT = 'Stores fields of study.';


# Dump of table types
# ------------------------------------------------------------

CREATE TABLE `types` (
  `id`          INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(20)      NOT NULL,
  `description` TEXT,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COMMENT = 'Stores types of publications.';


# Dump of table users
# ------------------------------------------------------------

CREATE TABLE `users` (
  `id`              INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`            VARCHAR(40)      NOT NULL,
  `password`        VARCHAR(50)
                    CHARACTER SET utf8
                    COLLATE utf8_bin NOT NULL
  COMMENT 'stores passwords. Therefore uses case sensitive collation',
  `mail`            VARCHAR(40)      NOT NULL,
  `date_register`   TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_last_login` TIMESTAMP        NULL     DEFAULT NULL,
  `active`          TINYINT(1)       NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `mail` (`mail`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COMMENT = 'Stores registered users.';


# Dump of table users_roles
# ------------------------------------------------------------

CREATE TABLE `users_roles` (
  `id`      INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) UNSIGNED NOT NULL,
  `role_id` INT(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_entry` (`user_id`, `role_id`),
  KEY `to_role` (`role_id`),
  CONSTRAINT `to_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`),
  CONSTRAINT `to_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COMMENT = 'Links users to their roles.';


/*!40111 SET SQL_NOTES = @OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE = @OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS = @OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT = @OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS = @OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION = @OLD_COLLATION_CONNECTION */;
