/*
SQLyog Community v13.3.1 (64 bit)
MySQL - 8.4.7 : Database - crimesense
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`crimesense` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;

USE `crimesense`;

/*Table structure for table `activity_logs` */

DROP TABLE IF EXISTS `activity_logs`;

CREATE TABLE `activity_logs` (
  `logID` int NOT NULL AUTO_INCREMENT,
  `userID` int DEFAULT NULL,
  `activity` text COLLATE utf8mb4_unicode_ci,
  `date_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`logID`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `activity_logs` */

insert  into `activity_logs`(`logID`,`userID`,`activity`,`date_created`) values 
(1,1,'Logged in','2026-07-30 12:29:07'),
(2,1,'Logged in','2026-07-30 12:29:47'),
(3,1,'Logged in','2026-07-31 13:52:47'),
(4,1,'Logged in','2026-08-03 10:26:25'),
(5,1,'Logged in','2026-08-03 12:01:27'),
(6,1,'Logged in','2026-08-03 15:46:14'),
(7,1,'Logged in','2026-08-03 16:04:19'),
(8,1,'Logged in','2026-08-03 16:14:37'),
(9,1,'Logged in','2026-08-11 15:56:51');

/*Table structure for table `barangays` */

DROP TABLE IF EXISTS `barangays`;

CREATE TABLE `barangays` (
  `barangayID` int NOT NULL AUTO_INCREMENT,
  `barangay_name` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  PRIMARY KEY (`barangayID`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `barangays` */

insert  into `barangays`(`barangayID`,`barangay_name`,`latitude`,`longitude`) values 
(1,'Ambitacay',16.3133848,120.3934193),
(2,'Balawarte',16.3181779,120.3414863);

/*Table structure for table `crime_reports` */

DROP TABLE IF EXISTS `crime_reports`;

CREATE TABLE `crime_reports` (
  `crimeID` int NOT NULL AUTO_INCREMENT,
  `crimeTypeID` int DEFAULT NULL,
  `barangayID` int DEFAULT NULL,
  `date_committed` date DEFAULT NULL,
  `time_committed` time DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `status` enum('Open','Solved','Closed') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reported_by` int DEFAULT NULL,
  `date_encoded` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `address` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`crimeID`),
  KEY `crimeTypeID` (`crimeTypeID`),
  KEY `barangayID` (`barangayID`),
  KEY `reported_by` (`reported_by`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `crime_reports` */

insert  into `crime_reports`(`crimeID`,`crimeTypeID`,`barangayID`,`date_committed`,`time_committed`,`latitude`,`longitude`,`description`,`status`,`reported_by`,`date_encoded`,`address`) values 
(1,1,1,'2026-07-31','23:47:00',16.3132201,120.3940630,'Carnapping','Open',1,'2026-07-31 20:46:46','Ambitacay, Agoo, La Union, Ilocos Region, 2504, Philippines'),
(2,9,2,'2026-07-22','12:06:00',16.3175035,120.3412342,'Marijuana','Closed',1,'2026-07-31 21:03:43','Cases Boulevard, Agoo Coastline Villas, Balawarte, Agoo, La Union, Ilocos Region, 2504, Philippines'),
(3,5,1,'2026-07-22','12:40:00',16.3121492,120.3857160,'rape','Open',1,'2026-07-31 21:38:59','Santa Maria, Santo Tomas, La Union, Ilocos Region, 2505, Philippines'),
(4,3,1,'2026-07-09','01:46:00',16.3077008,120.3950930,'murder','Solved',1,'2026-07-31 21:41:23','Ambitacay, Agoo, La Union, Ilocos Region, 2504, Philippines'),
(5,4,2,'2026-07-17','23:44:00',16.3198923,120.3968096,'physical injury','Solved',1,'2026-07-31 21:43:14','Ambitacay, Agoo, La Union, Ilocos Region, 2504, Philippines'),
(6,8,2,'2026-07-23','01:39:00',16.3169681,120.3426719,'Child Abuse','Open',1,'2026-08-03 10:36:54','Agoo Coastline Villas, Balawarte, Agoo, La Union, Ilocos Region, 2504, Philippines');

/*Table structure for table `crime_types` */

DROP TABLE IF EXISTS `crime_types`;

CREATE TABLE `crime_types` (
  `crimeTypeID` int NOT NULL AUTO_INCREMENT,
  `crime_name` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `icon` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`crimeTypeID`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `crime_types` */

insert  into `crime_types`(`crimeTypeID`,`crime_name`,`description`,`icon`) values 
(1,'Carnapping','Crime Against Property','car.png'),
(2,'Homicide','Crime Against Person','homicide.png'),
(3,'Murder','Crime Against Person','murder.png'),
(4,'Physical','Crime Against Person','injury.png'),
(5,'Rape','Crime Against Person','rape.png'),
(6,'Robbery','Crime Against Property','robbery.png'),
(7,'Theft','Crime Against Property','theft.png'),
(8,'VAWC','Non-index Crime','violence.png'),
(9,'Drugs','Non-index Crime','drugs.png');

/*Table structure for table `predictions` */

DROP TABLE IF EXISTS `predictions`;

CREATE TABLE `predictions` (
  `predictionID` int NOT NULL AUTO_INCREMENT,
  `barangayID` int DEFAULT NULL,
  `prediction_month` date DEFAULT NULL,
  `predicted_cases` int DEFAULT NULL,
  `risk_level` enum('Low','Medium','High') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`predictionID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `predictions` */

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `userID` int NOT NULL AUTO_INCREMENT,
  `fullname` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `username` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` enum('Administrator','Police') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('Active','Inactive') COLLATE utf8mb4_unicode_ci DEFAULT 'Active',
  PRIMARY KEY (`userID`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `users` */

insert  into `users`(`userID`,`fullname`,`username`,`password`,`role`,`status`) values 
(1,'Joyce M. Villanueva','jmv','$2y$10$bkR4QkSA.t.z9FeNFaRBEeS4UoUOSUDJjY0OrET1oNXEEgwOVLUn6','Administrator','Active');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
