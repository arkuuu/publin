# ************************************************************
# Sequel Pro SQL dump
# Version 4135
#
# http://www.sequelpro.com/
# http://code.google.com/p/sequel-pro/
#
# Host: 127.0.0.1 (MySQL 5.5.38)
# Database: dev
# Generation Time: 2015-02-20 15:38:42 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT = @@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS = @@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION = @@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS = @@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS = 0 */;
/*!40101 SET @OLD_SQL_MODE = @@SQL_MODE, SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES = @@SQL_NOTES, SQL_NOTES = 0 */;


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


/*!40111 SET SQL_NOTES = @OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE = @OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS = @OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT = @OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS = @OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION = @OLD_COLLATION_CONNECTION */;
