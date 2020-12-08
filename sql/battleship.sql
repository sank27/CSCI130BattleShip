/*
SQLyog Ultimate v13.1.5  (64 bit)
MySQL - 10.4.7-MariaDB : Database - battleship
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
USE `battleship`;

/*Table structure for table `attacks` */

DROP TABLE IF EXISTS `attacks`;

CREATE TABLE `attacks` (
                           `id` int(20) NOT NULL AUTO_INCREMENT,
                           `game_id` int(11) DEFAULT NULL,
                           `player_id` int(11) DEFAULT NULL,
                           `attack` varchar(20) DEFAULT NULL,
                           `created` datetime DEFAULT current_timestamp(),
                           PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `attacks` */

/*Table structure for table `games` */

DROP TABLE IF EXISTS `games`;

CREATE TABLE `games` (
                         `id` int(11) NOT NULL AUTO_INCREMENT,
                         `turn` int(11) DEFAULT 0,
                         `started` tinyint(1) DEFAULT 0,
                         `created` datetime DEFAULT current_timestamp(),
                         `finished` tinyint(1) DEFAULT 0,
                         PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

/*Data for the table `games` */

/*Table structure for table `players` */

DROP TABLE IF EXISTS `players`;

CREATE TABLE `players` (
                           `id` int(20) NOT NULL AUTO_INCREMENT,
                           `game_id` int(20) DEFAULT NULL,
                           `player_id` int(20) DEFAULT NULL,
                           `created` datetime DEFAULT current_timestamp(),
                           PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

/*Data for the table `players` */

/*Table structure for table `pulse` */

DROP TABLE IF EXISTS `pulse`;

CREATE TABLE `pulse` (
                         `id` int(30) NOT NULL AUTO_INCREMENT,
                         `user_id` int(20) DEFAULT NULL,
                         `player` varchar(128) DEFAULT NULL,
                         `created` datetime DEFAULT NULL,
                         PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

/*Data for the table `pulse` */

/*Table structure for table `request` */

DROP TABLE IF EXISTS `request`;

CREATE TABLE `request` (
                           `id` int(20) NOT NULL AUTO_INCREMENT,
                           `requester` int(10) DEFAULT NULL,
                           `requestee` int(10) DEFAULT NULL,
                           `status` int(2) DEFAULT NULL,
                           `created` datetime DEFAULT current_timestamp(),
                           `modified` datetime DEFAULT NULL,
                           PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

/*Data for the table `request` */

/*Table structure for table `ships` */

DROP TABLE IF EXISTS `ships`;

CREATE TABLE `ships` (
                         `id` int(20) NOT NULL AUTO_INCREMENT,
                         `game_id` int(11) DEFAULT NULL,
                         `player_id` int(11) DEFAULT NULL,
                         `ships` text DEFAULT NULL,
                         PRIMARY KEY (`id`),
                         KEY `pk_game_player` (`game_id`,`player_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

/*Data for the table `ships` */
/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
                         `id` int(10) NOT NULL AUTO_INCREMENT,
                         `username` varchar(128) DEFAULT NULL,
                         `hashedPassword` varchar(256) DEFAULT NULL,
                         `created` datetime DEFAULT NULL,
                         `modified` datetime DEFAULT NULL,
                         PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

/*Data for the table `users` */
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
