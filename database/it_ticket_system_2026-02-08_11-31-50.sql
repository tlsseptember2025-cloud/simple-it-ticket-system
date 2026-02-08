-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: it_ticket_system
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `admin`
--

DROP TABLE IF EXISTS `admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin`
--

LOCK TABLES `admin` WRITE;
/*!40000 ALTER TABLE `admin` DISABLE KEYS */;
INSERT INTO `admin` VALUES (1,'admin','$2y$10$4apDqebaZEY60I21HJeuCuiuq.q1ICnQraD3f3qq1e/6kFpX1VqJm','2026-02-05 14:09:26');
/*!40000 ALTER TABLE `admin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `attachments`
--

DROP TABLE IF EXISTS `attachments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `attachments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket_id` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `filepath` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_attachments_ticket` (`ticket_id`),
  CONSTRAINT `fk_attachments_ticket` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attachments`
--

LOCK TABLES `attachments` WRITE;
/*!40000 ALTER TABLE `attachments` DISABLE KEYS */;
INSERT INTO `attachments` VALUES (9,4,'image001.png','uploads/tickets/69847bf8ecd99_image001.png','2026-02-05 15:16:08'),(10,4,'image003.png','uploads/tickets/69847bf934d57_image003.png','2026-02-05 15:16:09'),(11,4,'image004.png','uploads/tickets/69847bf993e17_image004.png','2026-02-05 15:16:09'),(12,4,'image002.png','uploads/tickets/69847bfa2d66f_image002.png','2026-02-05 15:16:10'),(13,4,'image007.png','uploads/tickets/69847bfb77603_image007.png','2026-02-05 15:16:11'),(47,13,'image001.png','uploads/tickets/698825c424a95_image001.png','2026-02-08 09:57:24'),(48,13,'image002.jpg','uploads/tickets/698825c48cc92_image002.jpg','2026-02-08 09:57:24');
/*!40000 ALTER TABLE `attachments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `processed_emails`
--

DROP TABLE IF EXISTS `processed_emails`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `processed_emails` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `message_id` varchar(255) NOT NULL,
  `processed_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `message_id` (`message_id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `processed_emails`
--

LOCK TABLES `processed_emails` WRITE;
/*!40000 ALTER TABLE `processed_emails` DISABLE KEYS */;
INSERT INTO `processed_emails` VALUES (1,'<002201dc95bd$fb65e9b0$f231bd10$@loopsautomation.com>','2026-02-05 14:11:13'),(2,'<CAPC8sF9aM2ZYxOUu8oZifQ+6_ZyWBE9W=DHf4obtYxn2pMTwrQ@mail.gmail.com>','2026-02-05 15:06:35'),(3,'<CAPC8sF9X0MnXh0fohAbSF1k7bJz93h9BicCk_NCqzR2x-5w3sA@mail.gmail.com>','2026-02-05 15:08:35'),(4,'<006601dc9690$c147fdd0$43d7f970$@loopsautomation.com>','2026-02-05 15:16:14'),(5,'<CAPC8sF9ofNTbwpdWFw_gXH_pUNsOGzQKpy7KF+F-wWxT1OmN4g@mail.gmail.com>','2026-02-05 15:50:11'),(6,'<CAPC8sF_Y1Dd8HpD=W2jZO-D+YvKvLVogmynP93ZtF3RaQ9+1PQ@mail.gmail.com>','2026-02-05 15:51:29'),(7,'<CAPC8sF95e1Ymp7zoWtGd9uM7uP4QLRUUxPPeg1ghvjr3sX2Ogw@mail.gmail.com>','2026-02-05 15:56:38'),(8,'<CAPC8sF8Ty2FyUWQpc_ksp1RPFKdqVah6z1pVc63+hmwWhb9+4A@mail.gmail.com>','2026-02-05 16:01:47'),(9,'<CAPC8sF-wpm7xzkVPYdtAJzku=FBD=Yw55B4MXy12BH-Yrv3wTg@mail.gmail.com>','2026-02-05 16:18:26'),(10,'<CAPC8sF_mdARHV2TtZHcg1GHog_Mp413U3xn0Y7G-6vjF6C6kFw@mail.gmail.com>','2026-02-05 16:25:39'),(11,'<CAPC8sF_=zy+QA=XtsOb0V7jZPfbeOJdJ5t7XYxmfcS79TZMxAQ@mail.gmail.com>','2026-02-05 17:07:34'),(12,'<CAPC8sF-1UH-ssxkiOwjGq794q3feCux47d5Wu8KO-fv98iG2Hg@mail.gmail.com>','2026-02-05 17:32:05'),(13,'<CAPC8sF_bc6eyFimvocg=yB6c+W0oFdbyQ+YS__5g3xBrAvODHQ@mail.gmail.com>','2026-02-05 17:33:39'),(14,'<CAKN8hg+rzgvpo_SjFwsop2_6V3+gR_71JHTWiSi-Dw7Gba3Law@mail.gmail.com>','2026-02-08 08:51:50'),(15,'<CAKN8hg+7BrbQvyLusLqHnf=UKPPKqxNOuYMfk8DJ1ZUXA_Oy8Q@mail.gmail.com>','2026-02-08 08:53:39'),(16,'<CAKN8hgKYFN+8CMqKo4wWi8N5F0_COGWf+88gwg0oQ8Wn2vjk=Q@mail.gmail.com>','2026-02-08 08:55:34'),(17,'<CAKN8hgLAfRVZMVxZPyX-D+10GU433eK6cqEYJ8U0Q90W-grMwg@mail.gmail.com>','2026-02-08 09:18:43'),(18,'<CAKN8hgLZhgpMOaJxJ4240jTifo_9eSurRTp91-w2GCpm_fWg5g@mail.gmail.com>','2026-02-08 09:24:06'),(19,'<006d01dc98bf$a4aae170$ee00a450$@loopsautomation.com>','2026-02-08 09:57:26'),(20,'<003101dc98c4$667b7090$337251b0$@loopsautomation.com>','2026-02-08 10:32:24');
/*!40000 ALTER TABLE `processed_emails` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tickets`
--

DROP TABLE IF EXISTS `tickets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tickets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket_number` varchar(100) NOT NULL,
  `sender_email` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` longtext NOT NULL,
  `status` enum('Open','In Progress','Waiting','Closed') DEFAULT 'Open',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status_token` varchar(64) DEFAULT NULL,
  `status_updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ticket_number` (`ticket_number`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tickets`
--

LOCK TABLES `tickets` WRITE;
/*!40000 ALTER TABLE `tickets` DISABLE KEYS */;
INSERT INTO `tickets` VALUES (4,'LA-Support-2026-AAF6E632','mustafa@loopsautomation.com','11072ORD103_CONTROL EXPERT L SINGLE LIC_LICENSE LOST','Dear Rami\n\n \n\nRefer to below mail, we request that installation of Eco structure control expert M580 and validate license to be activate in GEETAC laptop.\n\n \n\nThis is very useful for any further upgradation project.','Closed','2026-02-05 15:16:08','2026-02-08 11:25:43','86efa1ac71f22909e42d0fc609e3d7e9cf3e08829678de9216da44df3de85c20','2026-02-08 10:35:42'),(13,'LA-Support-2026-D2636A68','harris@loopsautomation.com','IT Support','Hi rami\n\nMy company land phone is not working, Need your support and action.','Closed','2026-02-08 09:57:23','2026-02-08 10:03:45','69ef363c4d2aad1d7989369475ba3f2f','2026-02-08 10:03:45');
/*!40000 ALTER TABLE `tickets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `updates`
--

DROP TABLE IF EXISTS `updates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `updates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket_id` int(11) NOT NULL,
  `message` longtext NOT NULL,
  `sent_to_user` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_updates_ticket` (`ticket_id`),
  CONSTRAINT `fk_updates_ticket` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `updates`
--

LOCK TABLES `updates` WRITE;
/*!40000 ALTER TABLE `updates` DISABLE KEYS */;
INSERT INTO `updates` VALUES (3,4,'ok Mustafa we will do that as soon as you come to my office',1,'2026-02-05 15:17:07'),(4,4,'we will wait for download link for V. 14.0',0,'2026-02-05 15:27:17'),(22,13,'ok i will come and fix',1,'2026-02-08 09:58:22'),(23,13,'someone logged in to your phone but now it is restored.',1,'2026-02-08 10:03:45'),(24,4,'Dear Mustafa,\r\nPlease update me on this or else I will consider this as resolved. This ticket will be marked as \"Closed\" by the end of today if no response from your side!',1,'2026-02-08 10:09:33'),(25,4,'Hi Rami\n\nKindly proceed to close the ticket. So far not yet received the support from Schneider.',0,'2026-02-08 10:32:24'),(26,4,'done!',1,'2026-02-08 10:35:42');
/*!40000 ALTER TABLE `updates` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-02-08 11:31:50
