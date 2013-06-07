# ************************************************************
# Sequel Pro SQL dump
# Version 3408
#
# http://www.sequelpro.com/
# http://code.google.com/p/sequel-pro/
#
# Host: 127.0.0.1 (MySQL 5.5.24)
# Database: brovine
# Generation Time: 2013-06-05 02:23:01 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table apriori_staging
# ------------------------------------------------------------

DROP TABLE IF EXISTS `apriori_staging`;

CREATE TABLE `apriori_staging` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `geneid` int(11) NOT NULL,
  `tf_cart` text CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



# Dump of table comparison_types
# ------------------------------------------------------------

DROP TABLE IF EXISTS `comparison_types`;

CREATE TABLE `comparison_types` (
  `comparisontypeid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `species` varchar(64) COLLATE utf8_bin NOT NULL,
  `celltype` varchar(255) COLLATE utf8_bin NOT NULL,
  `hidden` tinyint(1) NOT NULL,
  `date_edited` int(11) NOT NULL,
  PRIMARY KEY (`comparisontypeid`),
  UNIQUE KEY `species` (`species`,`celltype`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



# Dump of table experiments
# ------------------------------------------------------------

DROP TABLE IF EXISTS `experiments`;

CREATE TABLE `experiments` (
  `experimentid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `label` varchar(255) COLLATE utf8_bin NOT NULL,
  `tessjob` varchar(255) COLLATE utf8_bin NOT NULL,
  `comparisontypeid` int(11) unsigned NOT NULL,
  `experimenter_email` varchar(255) COLLATE utf8_bin NOT NULL,
  `storage_time` text COLLATE utf8_bin,
  `search_transfac_strings` tinyint(1) DEFAULT NULL,
  `search_my_site_strings` tinyint(1) DEFAULT NULL,
  `selected` tinyint(1) DEFAULT NULL,
  `search_transfac_matrices` tinyint(1) DEFAULT NULL,
  `search_imd_matrices` tinyint(1) DEFAULT NULL,
  `search_cbil_matrices` tinyint(1) DEFAULT NULL,
  `search_jaspar_matrices` tinyint(1) DEFAULT NULL,
  `search_my_weight_matrices` tinyint(1) DEFAULT NULL,
  `combine_with` text COLLATE utf8_bin,
  `factor_attr_1` text COLLATE utf8_bin,
  `matches` text COLLATE utf8_bin,
  `use_core_positions` tinyint(1) DEFAULT NULL,
  `max_mismatch` int(11) DEFAULT NULL,
  `min_log_likelihood` int(11) DEFAULT NULL,
  `min_strlen` int(11) DEFAULT NULL,
  `min_lg` double DEFAULT NULL,
  `group_selection` text COLLATE utf8_bin,
  `max_lg` int(11) DEFAULT NULL,
  `min_core` double DEFAULT NULL,
  `min_matrix` double DEFAULT NULL,
  `secondary_lg` int(11) DEFAULT NULL,
  `count_significance` double DEFAULT NULL,
  `pseudocounts` double DEFAULT NULL,
  `use_at` double DEFAULT NULL,
  `explicit_acgt` text COLLATE utf8_bin,
  `handle_ambig` text COLLATE utf8_bin,
  `hidden` tinyint(1) NOT NULL,
  `date_edited` int(11) NOT NULL,
  PRIMARY KEY (`experimentid`),
  UNIQUE KEY `label` (`label`),
  KEY `comparisontypeid` (`comparisontypeid`),
  CONSTRAINT `experiments_ibfk_1` FOREIGN KEY (`comparisontypeid`) REFERENCES `comparison_types` (`comparisontypeid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



# Dump of table factor_matches
# ------------------------------------------------------------

DROP TABLE IF EXISTS `factor_matches`;

CREATE TABLE `factor_matches` (
  `matchid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `seqid` int(11) unsigned NOT NULL,
  `study` varchar(255) COLLATE utf8_bin NOT NULL,
  `transfac` varchar(32) COLLATE utf8_bin NOT NULL,
  `la` double NOT NULL,
  `la_slash` double NOT NULL,
  `lq` double NOT NULL,
  `ld` double NOT NULL,
  `lpv` double NOT NULL,
  `sc` double NOT NULL,
  `sm` double NOT NULL,
  `spv` double NOT NULL,
  `ppv` double NOT NULL,
  `hidden` tinyint(1) NOT NULL,
  `date_edited` int(11) NOT NULL,
  PRIMARY KEY (`matchid`),
  UNIQUE KEY `seqid` (`seqid`,`study`,`transfac`),
  KEY `tfKey` (`transfac`,`study`,`seqid`),
  CONSTRAINT `factor_matches_ibfk_1` FOREIGN KEY (`seqid`) REFERENCES `regulatory_sequences` (`seqid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



# Dump of table genes
# ------------------------------------------------------------

DROP TABLE IF EXISTS `genes`;

CREATE TABLE `genes` (
  `geneid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `genename` varchar(255) COLLATE utf8_bin NOT NULL,
  `chromosome` smallint(2) NOT NULL,
  `start` int(11) NOT NULL,
  `end` int(11) NOT NULL,
  `experimentid` int(11) unsigned NOT NULL,
  `geneabbrev` varchar(32) COLLATE utf8_bin NOT NULL,
  `regulation` varchar(20) COLLATE utf8_bin NOT NULL,
  `hidden` tinyint(1) NOT NULL,
  `date_edited` int(11) NOT NULL,
  PRIMARY KEY (`geneid`),
  UNIQUE KEY `experimentid` (`experimentid`,`genename`),
  CONSTRAINT `genes_ibfk_1` FOREIGN KEY (`experimentid`) REFERENCES `experiments` (`experimentid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



# Dump of table promoter_sequences
# ------------------------------------------------------------

DROP TABLE IF EXISTS `promoter_sequences`;

CREATE TABLE `promoter_sequences` (
  `geneid` int(11) unsigned NOT NULL,
  `sequence` text COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`geneid`),
  CONSTRAINT `promoter_sequences_ibfk_1` FOREIGN KEY (`geneid`) REFERENCES `genes` (`geneid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



# Dump of table regulatory_sequences
# ------------------------------------------------------------

DROP TABLE IF EXISTS `regulatory_sequences`;

CREATE TABLE `regulatory_sequences` (
  `seqid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `beginning` int(11) NOT NULL,
  `length` int(11) NOT NULL,
  `sense` char(1) COLLATE utf8_bin NOT NULL,
  `geneid` int(11) unsigned NOT NULL,
  `hidden` tinyint(1) NOT NULL,
  `date_edited` int(11) NOT NULL,
  PRIMARY KEY (`seqid`),
  UNIQUE KEY `geneid` (`geneid`,`beginning`,`length`,`sense`),
  CONSTRAINT `regulatory_sequences_ibfk_1` FOREIGN KEY (`geneid`) REFERENCES `genes` (`geneid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



# Dump of table study_pages
# ------------------------------------------------------------

DROP TABLE IF EXISTS `study_pages`;

CREATE TABLE `study_pages` (
  `pageno` char(7) COLLATE utf8_bin NOT NULL,
  `seqid` int(11) unsigned NOT NULL,
  PRIMARY KEY (`seqid`,`pageno`),
  UNIQUE KEY `pageno` (`pageno`,`seqid`),
  CONSTRAINT `study_pages_ibfk_1` FOREIGN KEY (`seqid`) REFERENCES `regulatory_sequences` (`seqid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



# Dump of table users
# ------------------------------------------------------------

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(20) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `password` varchar(128) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `privileges` tinyint(4) NOT NULL DEFAULT '0',
  `display_name` varchar(60) CHARACTER SET utf8 NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
