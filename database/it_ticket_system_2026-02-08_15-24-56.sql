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
) ENGINE=InnoDB AUTO_INCREMENT=76 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attachments`
--

LOCK TABLES `attachments` WRITE;
/*!40000 ALTER TABLE `attachments` DISABLE KEYS */;
INSERT INTO `attachments` VALUES (9,4,'image001.png','uploads/tickets/69847bf8ecd99_image001.png','2026-02-05 15:16:08'),(10,4,'image003.png','uploads/tickets/69847bf934d57_image003.png','2026-02-05 15:16:09'),(11,4,'image004.png','uploads/tickets/69847bf993e17_image004.png','2026-02-05 15:16:09'),(12,4,'image002.png','uploads/tickets/69847bfa2d66f_image002.png','2026-02-05 15:16:10'),(13,4,'image007.png','uploads/tickets/69847bfb77603_image007.png','2026-02-05 15:16:11'),(47,13,'image001.png','uploads/tickets/698825c424a95_image001.png','2026-02-08 09:57:24'),(48,13,'image002.jpg','uploads/tickets/698825c48cc92_image002.jpg','2026-02-08 09:57:24'),(49,19,'image.png','uploads/tickets/698856fc6dd74_image.png','2026-02-08 13:27:24'),(50,20,'image.png','uploads/tickets/698857001bb82_image.png','2026-02-08 13:27:28'),(52,23,'image001.png','uploads/tickets/69885707b7809_image001.png','2026-02-08 13:27:35'),(54,23,'image002.jpg','uploads/tickets/698857081cdbe_image002.jpg','2026-02-08 13:27:36'),(58,27,'image004.png','uploads/tickets/698857114fd28_image004.png','2026-02-08 13:27:45'),(61,31,'image001.png','uploads/tickets/6988571895a26_image001.png','2026-02-08 13:27:52'),(62,31,'image002.jpg','uploads/tickets/69885718da64a_image002.jpg','2026-02-08 13:27:52'),(65,33,'image.png','uploads/tickets/6988571cebd53_image.png','2026-02-08 13:27:56'),(66,33,'image.jpeg','uploads/tickets/6988571d2cd3b_image.jpeg','2026-02-08 13:27:57'),(74,40,'image001.png','uploads/tickets/6988572f196ce_image001.png','2026-02-08 13:28:15'),(75,40,'image002.jpg','uploads/tickets/6988572f52436_image002.jpg','2026-02-08 13:28:15');
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
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `processed_emails`
--

LOCK TABLES `processed_emails` WRITE;
/*!40000 ALTER TABLE `processed_emails` DISABLE KEYS */;
INSERT INTO `processed_emails` VALUES (1,'<002201dc95bd$fb65e9b0$f231bd10$@loopsautomation.com>','2026-02-05 14:11:13'),(2,'<CAPC8sF9aM2ZYxOUu8oZifQ+6_ZyWBE9W=DHf4obtYxn2pMTwrQ@mail.gmail.com>','2026-02-05 15:06:35'),(3,'<CAPC8sF9X0MnXh0fohAbSF1k7bJz93h9BicCk_NCqzR2x-5w3sA@mail.gmail.com>','2026-02-05 15:08:35'),(4,'<006601dc9690$c147fdd0$43d7f970$@loopsautomation.com>','2026-02-05 15:16:14'),(5,'<CAPC8sF9ofNTbwpdWFw_gXH_pUNsOGzQKpy7KF+F-wWxT1OmN4g@mail.gmail.com>','2026-02-05 15:50:11'),(6,'<CAPC8sF_Y1Dd8HpD=W2jZO-D+YvKvLVogmynP93ZtF3RaQ9+1PQ@mail.gmail.com>','2026-02-05 15:51:29'),(7,'<CAPC8sF95e1Ymp7zoWtGd9uM7uP4QLRUUxPPeg1ghvjr3sX2Ogw@mail.gmail.com>','2026-02-05 15:56:38'),(8,'<CAPC8sF8Ty2FyUWQpc_ksp1RPFKdqVah6z1pVc63+hmwWhb9+4A@mail.gmail.com>','2026-02-05 16:01:47'),(9,'<CAPC8sF-wpm7xzkVPYdtAJzku=FBD=Yw55B4MXy12BH-Yrv3wTg@mail.gmail.com>','2026-02-05 16:18:26'),(10,'<CAPC8sF_mdARHV2TtZHcg1GHog_Mp413U3xn0Y7G-6vjF6C6kFw@mail.gmail.com>','2026-02-05 16:25:39'),(11,'<CAPC8sF_=zy+QA=XtsOb0V7jZPfbeOJdJ5t7XYxmfcS79TZMxAQ@mail.gmail.com>','2026-02-05 17:07:34'),(12,'<CAPC8sF-1UH-ssxkiOwjGq794q3feCux47d5Wu8KO-fv98iG2Hg@mail.gmail.com>','2026-02-05 17:32:05'),(13,'<CAPC8sF_bc6eyFimvocg=yB6c+W0oFdbyQ+YS__5g3xBrAvODHQ@mail.gmail.com>','2026-02-05 17:33:39'),(14,'<CAKN8hg+rzgvpo_SjFwsop2_6V3+gR_71JHTWiSi-Dw7Gba3Law@mail.gmail.com>','2026-02-08 08:51:50'),(15,'<CAKN8hg+7BrbQvyLusLqHnf=UKPPKqxNOuYMfk8DJ1ZUXA_Oy8Q@mail.gmail.com>','2026-02-08 08:53:39'),(16,'<CAKN8hgKYFN+8CMqKo4wWi8N5F0_COGWf+88gwg0oQ8Wn2vjk=Q@mail.gmail.com>','2026-02-08 08:55:34'),(17,'<CAKN8hgLAfRVZMVxZPyX-D+10GU433eK6cqEYJ8U0Q90W-grMwg@mail.gmail.com>','2026-02-08 09:18:43'),(18,'<CAKN8hgLZhgpMOaJxJ4240jTifo_9eSurRTp91-w2GCpm_fWg5g@mail.gmail.com>','2026-02-08 09:24:06'),(19,'<006d01dc98bf$a4aae170$ee00a450$@loopsautomation.com>','2026-02-08 09:57:26'),(20,'<003101dc98c4$667b7090$337251b0$@loopsautomation.com>','2026-02-08 10:32:24'),(21,'<CAMKK-wWYxMBAscDBT98qFbBBfZMo+2Cm1PsJvWRVyg0Mif9iUg@mail.gmail.com>','2026-02-08 13:27:27'),(22,'<CAPOCXO2fr0omEkLGS2g5_FX9H0Y9Gw9rnTQff3EP4++MNdvCzg@mail.gmail.com>','2026-02-08 13:27:30'),(23,'<CAPOCXO0U-NPcCj794XaJO4pHNAmPc6ztg+4GiT-2=q-bvak5Zw@mail.gmail.com>','2026-02-08 13:27:34'),(24,'<006701dc95ca$b0139cc0$103ad640$@loopsautomation.com>','2026-02-08 13:27:38'),(26,'<CABJH8KSzMhCaZwJxQKP_m0NYh0DVntrkd5Qm5FNK5XBrYp9ygQ@mail.gmail.com>','2026-02-08 13:27:41'),(28,'<CABJH8KQ=Ysc6HRhcs_mpnM=6Aj7cLhaW8t1gJguV6c=brBMaiw@mail.gmail.com>','2026-02-08 13:27:45'),(30,'<AM0PR10MB267368CDBB2B52F72EF7A06BF89BA@AM0PR10MB2673.EURPRD10.PROD.OUTLOOK.COM>','2026-02-08 13:27:49'),(32,'<102901dc95b4$8504abc0$8f0e0340$@loopsautomation.com>','2026-02-08 13:27:53'),(34,'<CAMKK-wVyvS=VxQeEz4LpqKounFE1G=Qf8cg4mF6Q9Dh+MAQC3w@mail.gmail.com>','2026-02-08 13:27:57'),(36,'<CABJH8KSwQiQQr3tZi5qP3m_Qc6OJvg==+9VgEWeFFS-+hJidEA@mail.gmail.com>','2026-02-08 13:28:02'),(38,'<CABJH8KRVHrM9NmJ0ZZT2nMZFvsf4FVv1A_EySjWpW3i_DeqJxA@mail.gmail.com>','2026-02-08 13:28:06'),(39,'<000001dc941f$590acf90$0b206eb0$@loopsautomation.com>','2026-02-08 13:28:10'),(41,'<001201dc9341$581ee7d0$085cb770$@loopsautomation.com>','2026-02-08 13:28:15'),(43,'<024401dc98e5$30eb9330$92c2b990$@loopsautomation.com>','2026-02-08 14:27:26');
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
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tickets`
--

LOCK TABLES `tickets` WRITE;
/*!40000 ALTER TABLE `tickets` DISABLE KEYS */;
INSERT INTO `tickets` VALUES (4,'LA-Support-2026-AAF6E632','mustafa@loopsautomation.com','11072ORD103_CONTROL EXPERT L SINGLE LIC_LICENSE LOST','Dear Rami\n\n \n\nRefer to below mail, we request that installation of Eco structure control expert M580 and validate license to be activate in GEETAC laptop.\n\n \n\nThis is very useful for any further upgradation project.','Closed','2026-02-05 15:16:08','2026-02-08 11:25:43','86efa1ac71f22909e42d0fc609e3d7e9cf3e08829678de9216da44df3de85c20','2026-02-08 10:35:42'),(13,'LA-Support-2026-D2636A68','harris@loopsautomation.com','IT Support','Hi rami\n\nMy company land phone is not working, Need your support and action.','Closed','2026-02-08 09:57:23','2026-02-08 10:03:45','69ef363c4d2aad1d7989369475ba3f2f','2026-02-08 10:03:45'),(19,'LA-Support-2026-71612853','fatemeh@loopsautomation.com','Outlook','Outlook not working I\'m getting this massage','Closed','2026-02-03 13:27:23','2026-02-03 14:05:55','854cebda1431f9a91c825c49081091a8','2026-02-03 13:32:40'),(20,'LA-Support-2026-AE73CB19','john.mathew@loopsautomation.com','Gmail Network Issue','Dear Rami,\nCould you please come and assist me in the below snapshot problem. I am not\naware about how to explain the same. My email is not been send or received.\nKindly do the needful','Closed','2026-02-04 13:27:27','2026-02-04 14:06:38','62ac3de062202ec4204b64faf3ec14e9','2026-02-04 13:33:09'),(23,'LA-Support-2026-1EB87D24','faris.m@loopsautomation.com','APPROVAL REQUIEST FOR CORRECTION PLAN','Dear Rami,\n\n \n\nWe have correction plan regarding Calibration and Hydro test for Field\nInstruments. (31094ORD243)\n\nPlease submit from your side (Contract Manager).','Closed','2026-02-04 13:27:35','2026-02-04 14:07:20','fb0f6c888d97beb7414d2a3ad9e1db89','2026-02-04 13:37:17'),(27,'LA-Support-2026-0BEFD410','john@loopsautomation.com','IT Support','Hi Rami,\n\nOutlook is still not working and is using Gmail. Gmail was automatically\nsigned out. I forgot the password. Could you please provide the same.\nAppreciate your support.\nSending this from my mobile\n\n*','Closed','2026-02-05 13:27:44','2026-02-05 14:07:49','bd5fa4caf7729b925f5700ac7cb7eadd','2026-02-05 13:37:41'),(29,'LA-Support-2026-D44CB761','m.elsiddig@loopsautomation.com','Outlook working','Dear Mr. Rami,\n\nPlease your support on the Outlook its not working.\n\nSiddig.','Closed','2026-02-03 13:27:49','2026-02-03 13:37:58','cc9b2f3c0beda883387d4794205b7188','2026-02-03 13:37:58'),(31,'LA-Support-2026-D32E54B1','saurav@loopsautomation.com','please send email for support','Dear Rami,\n\n \n\nRequired contract manager approval on odoo for 31107ORD203','Closed','2026-02-04 13:27:52','2026-02-04 13:38:12','152353dc28c2bfa0071c97f11a5a2bd9','2026-02-04 13:38:12'),(33,'LA-Support-2026-8268C74A','fatemeh@loopsautomation.com','IT Support','Ethernet in not working\n\nOn Sun, 1 Feb 2026 at 10:12 AM rami wahdan \nwrote:','Closed','2026-02-01 13:27:56','2026-02-01 13:38:34','d685889ba6c0ef67a4b30acf709fa42d','2026-02-01 13:38:34'),(37,'LA-Support-2026-FEA5E0A0','john@loopsautomation.com','Delay in email receipt','Dear Rami,\n\n \n\nThere are some delay in receipt of emails. Trail mail is sent from Kent on\nFriday, however we received it today only. Refer attached snapshot. Please\ncheck and do the needful.','Closed','2026-02-02 13:28:07','2026-02-02 13:38:51','27f88649cf77b04f0a86b0a19d8ca49a','2026-02-02 13:38:51'),(40,'LA-Support-2026-E25AE4A3','saurav@loopsautomation.com','IT Suuport','Dear Rami,\n\n \n\nPlease note that my new email chrome browsing is not available , could you  please help me to restore it and also ODOO credential provide over email.','Closed','2026-02-01 13:28:14','2026-02-01 13:32:07','dc4a20beebfbc2c405d85d5b0381a459','2026-02-01 13:32:07'),(41,'LA-Support-2026-F8EDA4D4','john.mathew@loopsautomation.com','Ticket Created: LA-Support-2026-3AD79652','Dear Rami, \nCould you highlight the reason for ticket. Is there any problem with my\nLaptop?\nYour reply would be very helpful.','Closed','2026-02-08 14:27:23','2026-02-08 14:36:43','452a2560f581bf81c6802b9edc99a4dd','2026-02-08 14:36:43');
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
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `updates`
--

LOCK TABLES `updates` WRITE;
/*!40000 ALTER TABLE `updates` DISABLE KEYS */;
INSERT INTO `updates` VALUES (3,4,'ok Mustafa we will do that as soon as you come to my office',1,'2026-02-05 15:17:07'),(4,4,'we will wait for download link for V. 14.0',0,'2026-02-05 15:27:17'),(22,13,'ok i will come and fix',1,'2026-02-08 09:58:22'),(23,13,'someone logged in to your phone but now it is restored.',1,'2026-02-08 10:03:45'),(24,4,'Dear Mustafa,\r\nPlease update me on this or else I will consider this as resolved. This ticket will be marked as \"Closed\" by the end of today if no response from your side!',1,'2026-02-08 10:09:33'),(25,4,'Hi Rami\n\nKindly proceed to close the ticket. So far not yet received the support from Schneider.',0,'2026-02-08 10:32:24'),(26,4,'done!',1,'2026-02-08 10:35:42'),(27,40,'It is ok now',1,'2026-02-08 13:32:07'),(28,19,'App password created and now it is ok',1,'2026-02-08 13:32:40'),(29,20,'gmail app password created and now ok',1,'2026-02-08 13:33:09'),(30,23,'done!',1,'2026-02-08 13:37:17'),(31,27,'issue with gmail servers',1,'2026-02-08 13:37:41'),(32,29,'created new profile now ok',1,'2026-02-08 13:37:58'),(33,31,'done!',1,'2026-02-08 13:38:12'),(34,33,'fixed!',1,'2026-02-08 13:38:34'),(35,37,'issue with gmail server!',1,'2026-02-08 13:38:51'),(36,41,'no it was for the support i provided in the previous days. It won\'t happened again',1,'2026-02-08 14:36:43');
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

-- Dump completed on 2026-02-08 15:24:56
