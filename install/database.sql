-- MySQL dump 10.13  Distrib 5.6.19, for osx10.7 (i386)
--
-- Host: 127.0.0.1    Database: dev
-- ------------------------------------------------------
-- Server version	5.5.38

/*!40101 SET @OLD_CHARACTER_SET_CLIENT = @@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS = @@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION = @@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE = @@TIME_ZONE */;
/*!40103 SET TIME_ZONE = '+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS = @@UNIQUE_CHECKS, UNIQUE_CHECKS = 0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS = @@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS = 0 */;
/*!40101 SET @OLD_SQL_MODE = @@SQL_MODE, SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES = @@SQL_NOTES, SQL_NOTES = 0 */;

--
-- Table structure for table `authors`
--

DROP TABLE IF EXISTS `authors`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8
  COMMENT = 'Stores authors.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `files`
--

DROP TABLE IF EXISTS `files`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `files` (
  `id`             INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `publication_id` INT(11) UNSIGNED NOT NULL,
  `name`           VARCHAR(100)     NOT NULL,
  `extension`      VARCHAR(10)      NOT NULL,
  `size`           INT(11)          NOT NULL,
  `title`          VARCHAR(100)     NOT NULL,
  `full_text`      TINYINT(1)                DEFAULT '0',
  `restricted`     TINYINT(1)                DEFAULT '0',
  `hidden`         TINYINT(1)                DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `file_name` (`name`),
  KEY `publication_id` (`publication_id`),
  CONSTRAINT `files_ibfk_1` FOREIGN KEY (`publication_id`) REFERENCES `publications` (`id`)
)
  ENGINE = InnoDB
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8
  COMMENT = 'Stores files and links to their publications.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `keywords`
--

DROP TABLE IF EXISTS `keywords`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `keywords` (
  `id`   INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100)     NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
)
  ENGINE = InnoDB
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8
  COMMENT = 'Stores keywords.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `oai_tokens`
--

DROP TABLE IF EXISTS `oai_tokens`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8
  COMMENT = 'Stores the tokens of the OAI interface.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permissions` (
  `id`   INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(50)      NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
)
  ENGINE = InnoDB
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8
  COMMENT = 'Stores permissions.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `publications`
--

DROP TABLE IF EXISTS `publications`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  CONSTRAINT `publication_to_study_field` FOREIGN KEY (`study_field_id`) REFERENCES `study_fields` (`id`),
  CONSTRAINT `publication_to_type` FOREIGN KEY (`type_id`) REFERENCES `types` (`id`)
)
  ENGINE = InnoDB
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8
  COMMENT = 'Stores all publications.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `publications_authors`
--

DROP TABLE IF EXISTS `publications_authors`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  CONSTRAINT `publications_authors_ibfk_1` FOREIGN KEY (`publication_id`) REFERENCES `publications` (`id`),
  CONSTRAINT `publications_authors_ibfk_2` FOREIGN KEY (`author_id`) REFERENCES `authors` (`id`)
)
  ENGINE = InnoDB
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8
  COMMENT = 'Links publications to their authors.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `publications_keywords`
--

DROP TABLE IF EXISTS `publications_keywords`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `publications_keywords` (
  `id`             INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `publication_id` INT(11) UNSIGNED NOT NULL,
  `keyword_id`     INT(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_entries` (`publication_id`, `keyword_id`),
  KEY `keyword_id` (`keyword_id`),
  CONSTRAINT `publications_keywords_ibfk_1` FOREIGN KEY (`publication_id`) REFERENCES `publications` (`id`),
  CONSTRAINT `publications_keywords_ibfk_2` FOREIGN KEY (`keyword_id`) REFERENCES `keywords` (`id`)
)
  ENGINE = InnoDB
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8
  COMMENT = 'Links publications to their keywords';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `id`   INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(20)      NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
)
  ENGINE = InnoDB
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8
  COMMENT = 'Stores roles.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `roles_permissions`
--

DROP TABLE IF EXISTS `roles_permissions`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8
  COMMENT = 'Links roles to their permissions.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `study_fields`
--

DROP TABLE IF EXISTS `study_fields`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `study_fields` (
  `id`          INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(100)     NOT NULL DEFAULT '',
  `description` TEXT,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
)
  ENGINE = InnoDB
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8
  COMMENT = 'Stores fields of study.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `types`
--

DROP TABLE IF EXISTS `types`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `types` (
  `id`          INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(20)      NOT NULL,
  `description` TEXT,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
)
  ENGINE = InnoDB
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8
  COMMENT = 'Stores types of publications.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id`              INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`            VARCHAR(40)      NOT NULL,
  `password`        VARCHAR(255)
                    CHARACTER SET utf8
                    COLLATE utf8_bin NOT NULL DEFAULT ''
  COMMENT 'stores passwords. Therefore uses case sensitive collation',
  `mail`            VARCHAR(40)      NOT NULL,
  `date_register`   TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_last_login` TIMESTAMP        NULL     DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `mail` (`mail`)
)
  ENGINE = InnoDB
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8
  COMMENT = 'Stores registered users.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users_roles`
--

DROP TABLE IF EXISTS `users_roles`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  AUTO_INCREMENT = 34
  DEFAULT CHARSET = utf8
  COMMENT = 'Links users to their roles.';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE = @OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE = @OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS = @OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS = @OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT = @OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS = @OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION = @OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES = @OLD_SQL_NOTES */;

-- Dump completed on 2015-04-16 16:46:38
