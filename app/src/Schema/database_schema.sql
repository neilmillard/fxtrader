-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               5.5.44-MariaDB - mariadb.org binary distribution
-- Server OS:                    Win32
-- HeidiSQL Version:             9.1.0.4867
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

DROP TABLE IF EXISTS `accounts`;
CREATE TABLE `accounts` (
  `id`            INT(11) UNSIGNED NOT NULL  AUTO_INCREMENT,
  `apikey`        VARCHAR(191)
                  COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `accountid`     INT(11) UNSIGNED           DEFAULT NULL,
  `servertype`    VARCHAR(191)
                  COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `users_id`      INT(11) UNSIGNED           DEFAULT NULL,
  `balance`       DOUBLE                     DEFAULT NULL,
  `open_trades`   INT(11) UNSIGNED           DEFAULT NULL,
  `open_orders`   INT(11) UNSIGNED           DEFAULT NULL,
  `unrealized_pl` DOUBLE                     DEFAULT NULL,
  `lasttid`       INT(11) UNSIGNED           DEFAULT NULL,
  `currency`      VARCHAR(191)
                  COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `index_foreignkey_accounts_users` (`users_id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `candle`;
CREATE TABLE `candle` (
  `id`         INT(11) UNSIGNED NOT NULL  AUTO_INCREMENT,
  `date`       DATE                       DEFAULT NULL,
  `instrument` VARCHAR(191)
               COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `candletime` DOUBLE                     DEFAULT NULL,
  `open`       DOUBLE                     DEFAULT NULL,
  `high`       DOUBLE                     DEFAULT NULL,
  `low`        DOUBLE                     DEFAULT NULL,
  `close`      DOUBLE                     DEFAULT NULL,
  `complete`   TINYINT(1) UNSIGNED        DEFAULT NULL,
  `gran`       VARCHAR(191)
               COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `id`          INT(11) UNSIGNED NOT NULL  AUTO_INCREMENT,
  `oandaoid`    INT(11) UNSIGNED           DEFAULT NULL,
  `instrument`  VARCHAR(191)
                COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `units`       INT(11) UNSIGNED           DEFAULT NULL,
  `side`        VARCHAR(191)
                COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type`        TINYINT(1) UNSIGNED        DEFAULT NULL,
  `time`        DOUBLE                     DEFAULT NULL,
  `expiry`      DOUBLE                     DEFAULT NULL,
  `price`       DOUBLE                     DEFAULT NULL,
  `stop_loss`   DOUBLE                     DEFAULT NULL,
  `status`      VARCHAR(191)
                COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `take_profit` TINYINT(1) UNSIGNED        DEFAULT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `strategies`;
CREATE TABLE `strategies` (
  `id`           INT(11) UNSIGNED NOT NULL  AUTO_INCREMENT,
  `name`         VARCHAR(191)
                 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description`  VARCHAR(191)
                 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `strategyexit` INT(11) UNSIGNED           DEFAULT NULL,
  `signal`       VARCHAR(191)
                 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `params`       VARCHAR(191)
                 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `instrument`   VARCHAR(191)
                 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

-- needs to run after strategies has been created
DROP TABLE IF EXISTS `recommendations`;
CREATE TABLE `recommendations` (
  `id`          INT(11) UNSIGNED NOT NULL  AUTO_INCREMENT,
  `instrument`  VARCHAR(191)
                COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `side`        VARCHAR(191)
                COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `entry`       DOUBLE                     DEFAULT NULL,
  `stop_loss`   DOUBLE                     DEFAULT NULL,
  `rr`          INT(11) UNSIGNED           DEFAULT NULL,
  `gran`        VARCHAR(191)
                COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expiry`      INT(11) UNSIGNED           DEFAULT NULL,
  `strategy_id` INT(11) UNSIGNED           DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `index_foreignkey_recommendations_strategy` (`strategy_id`),
  CONSTRAINT `c_fk_recommendations_strategy_id` FOREIGN KEY (`strategy_id`) REFERENCES `strategies` (`id`)
    ON DELETE SET NULL
    ON UPDATE SET NULL
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `trades`;
CREATE TABLE `trades` (
  `id`         INT(11) UNSIGNED NOT NULL  AUTO_INCREMENT,
  `oandaoid`   INT(11) UNSIGNED           DEFAULT NULL,
  `instrument` VARCHAR(191)
               COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `units`      INT(11) UNSIGNED           DEFAULT NULL,
  `price`      DOUBLE                     DEFAULT NULL,
  `side`       VARCHAR(191)
               COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pl`         DOUBLE                     DEFAULT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;


-- Dumping structure for table apps.users
DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `fullname` char(20) NOT NULL,
  `password` char(15) NOT NULL,
  `hash` varchar(255) NOT NULL,
  `role` char(15) NOT NULL DEFAULT 'user',
  PRIMARY KEY (`id`),
  UNIQUE KEY `Email` (`email`)
)
  ENGINE = InnoDB
  AUTO_INCREMENT = 2
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;

INSERT INTO `users` (`id`, `email`, `fullname`, `password`, `hash`, `role`) VALUES
	(1, 'admin@neilmillard.com', 'Administrator_', 'Password1', '$2y$10$/Z3v5y2T/jBWaNcxXzFsA.KyF34yy0Dpbxz/R6Ba09Wn19J2tiSiW', 'ADMIN');

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
