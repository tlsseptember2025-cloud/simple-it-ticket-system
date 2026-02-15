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
) ENGINE=InnoDB AUTO_INCREMENT=147 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attachments`
--

LOCK TABLES `attachments` WRITE;
/*!40000 ALTER TABLE `attachments` DISABLE KEYS */;
INSERT INTO `attachments` VALUES (9,4,'image001.png','uploads/tickets/69847bf8ecd99_image001.png','2026-02-05 15:16:08'),(10,4,'image003.png','uploads/tickets/69847bf934d57_image003.png','2026-02-05 15:16:09'),(11,4,'image004.png','uploads/tickets/69847bf993e17_image004.png','2026-02-05 15:16:09'),(12,4,'image002.png','uploads/tickets/69847bfa2d66f_image002.png','2026-02-05 15:16:10'),(13,4,'image007.png','uploads/tickets/69847bfb77603_image007.png','2026-02-05 15:16:11'),(47,13,'image001.png','uploads/tickets/698825c424a95_image001.png','2026-02-08 09:57:24'),(48,13,'image002.jpg','uploads/tickets/698825c48cc92_image002.jpg','2026-02-08 09:57:24'),(49,19,'image.png','uploads/tickets/698856fc6dd74_image.png','2026-02-08 13:27:24'),(50,20,'image.png','uploads/tickets/698857001bb82_image.png','2026-02-08 13:27:28'),(52,23,'image001.png','uploads/tickets/69885707b7809_image001.png','2026-02-08 13:27:35'),(54,23,'image002.jpg','uploads/tickets/698857081cdbe_image002.jpg','2026-02-08 13:27:36'),(58,27,'image004.png','uploads/tickets/698857114fd28_image004.png','2026-02-08 13:27:45'),(61,31,'image001.png','uploads/tickets/6988571895a26_image001.png','2026-02-08 13:27:52'),(62,31,'image002.jpg','uploads/tickets/69885718da64a_image002.jpg','2026-02-08 13:27:52'),(65,33,'image.png','uploads/tickets/6988571cebd53_image.png','2026-02-08 13:27:56'),(66,33,'image.jpeg','uploads/tickets/6988571d2cd3b_image.jpeg','2026-02-08 13:27:57'),(74,40,'image001.png','uploads/tickets/6988572f196ce_image001.png','2026-02-08 13:28:15'),(75,40,'image002.jpg','uploads/tickets/6988572f52436_image002.jpg','2026-02-08 13:28:15'),(76,43,'image001.png','uploads/tickets/698996e7f1804_image001.png','2026-02-09 12:12:23'),(77,44,'image001.png','uploads/tickets/698996ec32949_image001.png','2026-02-09 12:12:28'),(78,44,'image002.jpg','uploads/tickets/698996eca4129_image002.jpg','2026-02-09 12:12:28'),(79,45,'image004.png','uploads/tickets/6989abdeae8db_image004.png','2026-02-09 13:41:50'),(80,45,'image005.png','uploads/tickets/6989abdee94f5_image005.png','2026-02-09 13:41:50'),(81,45,'image006.png','uploads/tickets/6989abdf5a748_image006.png','2026-02-09 13:41:51'),(82,45,'image001.png','uploads/tickets/6989abe000891_image001.png','2026-02-09 13:41:52'),(83,45,'image002.png','uploads/tickets/6989abe09aa39_image002.png','2026-02-09 13:41:52'),(84,47,'image001.png','uploads/tickets/6989c81fe5491_image001.png','2026-02-09 15:42:23'),(85,49,'image002.png','uploads/tickets/698ae28ba204e_image002.png','2026-02-10 11:47:23'),(86,49,'image003.jpg','uploads/tickets/698ae28c08dcb_image003.jpg','2026-02-10 11:47:24'),(87,49,'image004.png','uploads/tickets/698ae28c40a9d_image004.png','2026-02-10 11:47:24'),(88,50,'image006.png','uploads/tickets/698aef4ead07d_image006.png','2026-02-10 12:41:50'),(89,50,'image008.png','uploads/tickets/698aef4ee44cf_image008.png','2026-02-10 12:41:50'),(90,50,'image009.png','uploads/tickets/698aef4f4cf11_image009.png','2026-02-10 12:41:51'),(91,50,'image001.png','uploads/tickets/698aef4f9f5da_image001.png','2026-02-10 12:41:51'),(92,50,'image002.png','uploads/tickets/698aef502efb9_image002.png','2026-02-10 12:41:52'),(93,50,'image003.png','uploads/tickets/698aef50b88a8_image003.png','2026-02-10 12:41:52'),(94,51,'image001.png','uploads/tickets/698af09b25fd9_image001.png','2026-02-10 12:47:23'),(95,51,'image002.jpg','uploads/tickets/698af09b7f2a3_image002.jpg','2026-02-10 12:47:23'),(96,52,'image001.png','uploads/tickets/698af09bbc148_image001.png','2026-02-10 12:47:23'),(97,52,'image002.jpg','uploads/tickets/698af09c27671_image002.jpg','2026-02-10 12:47:24'),(98,53,'image001.png','uploads/tickets/698b06e0698ac_image001.png','2026-02-10 14:22:24'),(99,53,'image002.png','uploads/tickets/698b06e0b8c2e_image002.png','2026-02-10 14:22:24'),(100,53,'image003.png','uploads/tickets/698b06e11113a_image003.png','2026-02-10 14:22:25'),(101,53,'image004.png','uploads/tickets/698b06e171eac_image004.png','2026-02-10 14:22:25'),(102,53,'image005.png','uploads/tickets/698b06e1ce7bf_image005.png','2026-02-10 14:22:25'),(103,53,'image006.png','uploads/tickets/698b06e20d3e0_image006.png','2026-02-10 14:22:26'),(104,53,'image007.png','uploads/tickets/698b06e252e15_image007.png','2026-02-10 14:22:26'),(105,53,'image008.png','uploads/tickets/698b06e28b2a4_image008.png','2026-02-10 14:22:26'),(106,53,'image009.png','uploads/tickets/698b06e2ba664_image009.png','2026-02-10 14:22:26'),(107,53,'image004.png','uploads/tickets/698b06e2f02a5_image004.png','2026-02-10 14:22:27'),(108,54,'image004.png','uploads/tickets/698b116d676f9_image004.png','2026-02-10 15:07:25'),(139,68,'Proposal Insurence NAS Al Wataniah.xlsx','uploads/tickets/698c49ba09084_Proposal_Insurence_NAS_Al_Wataniah.xlsx','2026-02-11 13:19:54'),(140,68,'698c49ba09084_Proposal_Insurence_NAS_Al_Wataniah.xlsx','uploads/tickets/698c5218bd611_698c49ba09084_Proposal_Insurence_NAS_Al_Wataniah.xlsx','2026-02-11 13:55:36'),(141,68,'698c49ba09084_Proposal_Insurence_NAS_Al_Wataniah.xlsx','uploads/tickets/698c5218d0748_698c49ba09084_Proposal_Insurence_NAS_Al_Wataniah.xlsx','2026-02-11 13:55:36'),(145,47,'License Request.txt','uploads/tickets/699172ce09073_License_Request.txt','2026-02-15 11:16:30'),(146,47,'License Request.txt','uploads/tickets/699172ce2b97e_License_Request.txt','2026-02-15 11:16:30');
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
) ENGINE=InnoDB AUTO_INCREMENT=80 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `processed_emails`
--

LOCK TABLES `processed_emails` WRITE;
/*!40000 ALTER TABLE `processed_emails` DISABLE KEYS */;
INSERT INTO `processed_emails` VALUES (28,'<CABJH8KQ=Ysc6HRhcs_mpnM=6Aj7cLhaW8t1gJguV6c=brBMaiw@mail.gmail.com>','2026-02-08 13:27:45'),(30,'<AM0PR10MB267368CDBB2B52F72EF7A06BF89BA@AM0PR10MB2673.EURPRD10.PROD.OUTLOOK.COM>','2026-02-08 13:27:49'),(32,'<102901dc95b4$8504abc0$8f0e0340$@loopsautomation.com>','2026-02-08 13:27:53'),(34,'<CAMKK-wVyvS=VxQeEz4LpqKounFE1G=Qf8cg4mF6Q9Dh+MAQC3w@mail.gmail.com>','2026-02-08 13:27:57'),(36,'<CABJH8KSwQiQQr3tZi5qP3m_Qc6OJvg==+9VgEWeFFS-+hJidEA@mail.gmail.com>','2026-02-08 13:28:02'),(38,'<CABJH8KRVHrM9NmJ0ZZT2nMZFvsf4FVv1A_EySjWpW3i_DeqJxA@mail.gmail.com>','2026-02-08 13:28:06'),(39,'<000001dc941f$590acf90$0b206eb0$@loopsautomation.com>','2026-02-08 13:28:10'),(41,'<001201dc9341$581ee7d0$085cb770$@loopsautomation.com>','2026-02-08 13:28:15'),(43,'<024401dc98e5$30eb9330$92c2b990$@loopsautomation.com>','2026-02-08 14:27:26'),(44,'<019801dc999c$20e46860$62ad3920$@loopsautomation.com>','2026-02-09 12:12:27'),(45,'<02b101dc999c$5ed186d0$1c749470$@loopsautomation.com>','2026-02-09 12:12:31'),(46,'<000c01dc99a7$e601d460$b2057d20$@loopsautomation.com>','2026-02-09 13:41:55'),(47,'<CAKN8hgK8hxYu2R-KGfkNPSs4CBCkAfRGpuBRxWPjZgp6phmSqg@mail.gmail.com>','2026-02-09 14:38:43'),(48,'<485001dc99b9$d91f9f40$8b5eddc0$@loopsautomation.com>','2026-02-09 15:42:26'),(49,'<CABsHsJvKTDwJi7rfpg1SKyPOEXCKcegTkwjmz+9zeFs2UvHmBg@mail.gmail.com>','2026-02-10 11:34:12'),(50,'<00c001dc9a61$6b410240$41c306c0$@loopsautomation.com>','2026-02-10 11:47:26'),(51,'<002501dc9a68$9fad6710$df083530$@loopsautomation.com>','2026-02-10 12:41:55'),(52,'<0ff701dc9a69$911d91b0$b358b510$@loopsautomation.com>','2026-02-10 12:47:26'),(54,'<CABJH8KRozBADG+MYgdgJWCtjROneHTwVM1VXHSTW6E=70dWXuA@mail.gmail.com>','2026-02-10 14:22:29'),(55,'<CABJH8KRjnDdSm=fsOr3xV-EvJJb1hwFx0topzLv8yFP1PoV-Fw@mail.gmail.com>','2026-02-10 15:07:28'),(56,'<CAKN8hgK7rbQvM3M1600rB+u_94s5o6EALrqfFWsCmD1T4Gi8LQ@mail.gmail.com>','2026-02-10 15:45:11'),(57,'<CAKN8hgKSgAo3WHJveu+e7G1rHyWhHT3aBK2jOFXofKyVXry0MQ@mail.gmail.com>','2026-02-10 15:49:08'),(58,'<121c01dc9a8c$4231c850$c69558f0$@loopsautomation.com>','2026-02-10 16:56:11'),(59,'<01b401dc9a96$bc12c1b0$34384510$@loopsautomation.com>','2026-02-10 18:02:24'),(60,'<000001dc9a96$8cdb5ab0$a6921010$@loopsautomation.com>','2026-02-10 18:08:19'),(61,'<CAKN8hgLEq5Xfqu9dOX24_dzxqjpK9Kdd0nfT4-DEjKsu1FeeDg@mail.gmail.com>','2026-02-10 18:14:23'),(62,'<002001dc9a99$1350f9e0$39f2eda0$@loopsautomation.com>','2026-02-10 18:27:26'),(63,'<02e701dc9b1d$c66ef6b0$534ce410$@loopsautomation.com>','2026-02-11 10:15:56'),(64,'<CAKN8hgJb+U2OGs=HbNnDF3a6HjSFq8xXonY-Huq_z6-hH2rk4w@mail.gmail.com>','2026-02-11 10:52:48'),(65,'<CAKN8hgLH9Umcr2hVa-tnj3zFG+oUeF79EEZxb=we_9h0SbKcgg@mail.gmail.com>','2026-02-11 10:53:48'),(66,'<CAKN8hgJD4dkwvYNNQNu67Zt0DE+N+=LyAshfZdMuLt9b11SzyQ@mail.gmail.com>','2026-02-11 10:59:26'),(67,'<CAKN8hgKNfug8j2hpqdY1YAJfK+t0PW78ojjG_EbzombjdoE7tQ@mail.gmail.com>','2026-02-11 11:27:29'),(68,'<CAKN8hgKwhYZ1AOdeHWMuJj88p0c+-8G3Xi+PusV9D27EOy8vbw@mail.gmail.com>','2026-02-11 12:00:41'),(69,'<02b501dc9b32$08920690$19b613b0$@loopsautomation.com>','2026-02-11 12:41:26'),(70,'<000001dc9b35$e20320a0$a60961e0$@loopsautomation.com>','2026-02-11 13:08:26'),(71,'<CAKN8hgKUXz50JgbTiQ9CqZd+zCxxhtipn6aDLPQ4BvB+gp_hQg@mail.gmail.com>','2026-02-11 13:13:27'),(72,'<CAPC8sF8RLY1gjZqsHmiZBOWK1tS+BPj-DHptfRSQ9CXskxyL2w@mail.gmail.com>','2026-02-11 13:19:57'),(73,'<013501dc9b39$1493bd60$3dbb3820$@loopsautomation.com>','2026-02-11 13:31:23'),(74,'<03e701dc9b50$c6ac6cb0$54054610$@loopsautomation.com>','2026-02-11 16:21:27'),(75,'<CABsHsJuqLQopdUu0MKVjeC5B-s=Ki5cDRYSyK6Go2VWqA-fsWg@mail.gmail.com>','2026-02-11 17:46:26'),(76,'<CAKN8hgJ8Q+GP=RoWNx1GYj___kU9NH9TtvpOcZ+QwRPBxXxVew@mail.gmail.com>','2026-02-15 09:13:19'),(77,'<CAKN8hg+vKGcvMXK3z-h+OvCALwHuySV7=5cxu-Jp32Uab_zP_Q@mail.gmail.com>','2026-02-15 09:32:01'),(78,'<007301dc9e3c$cf471200$6dd53600$@loopsautomation.com>','2026-02-15 09:35:26'),(79,'<CAKN8hg+_G0GaO29m6cEed-E+9yTV4PRWqfot+XTve40NGDNGWg@mail.gmail.com>','2026-02-15 09:46:06');
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
) ENGINE=InnoDB AUTO_INCREMENT=75 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tickets`
--

LOCK TABLES `tickets` WRITE;
/*!40000 ALTER TABLE `tickets` DISABLE KEYS */;
INSERT INTO `tickets` VALUES (4,'LA-Support-2026-AAF6E632','mustafa@loopsautomation.com','11072ORD103_CONTROL EXPERT L SINGLE LIC_LICENSE LOST','Dear Rami\n\n \n\nRefer to below mail, we request that installation of Eco structure control expert M580 and validate license to be activate in GEETAC laptop.\n\n \n\nThis is very useful for any further upgradation project.','Closed','2026-02-05 15:16:08','2026-02-08 11:25:43','86efa1ac71f22909e42d0fc609e3d7e9cf3e08829678de9216da44df3de85c20','2026-02-08 10:35:42'),(13,'LA-Support-2026-D2636A68','harris@loopsautomation.com','IT Support','Hi rami\n\nMy company land phone is not working, Need your support and action.','Closed','2026-02-08 09:57:23','2026-02-08 10:03:45','69ef363c4d2aad1d7989369475ba3f2f','2026-02-08 10:03:45'),(19,'LA-Support-2026-71612853','fatemeh@loopsautomation.com','Outlook','Outlook not working I\'m getting this massage','Closed','2026-02-03 13:27:23','2026-02-03 14:05:55','854cebda1431f9a91c825c49081091a8','2026-02-03 13:32:40'),(20,'LA-Support-2026-AE73CB19','john.mathew@loopsautomation.com','Gmail Network Issue','Dear Rami,\nCould you please come and assist me in the below snapshot problem. I am not\naware about how to explain the same. My email is not been send or received.\nKindly do the needful','Closed','2026-02-04 13:27:27','2026-02-04 14:06:38','62ac3de062202ec4204b64faf3ec14e9','2026-02-04 13:33:09'),(23,'LA-Support-2026-1EB87D24','faris.m@loopsautomation.com','APPROVAL REQUIEST FOR CORRECTION PLAN','Dear Rami,\n\n \n\nWe have correction plan regarding Calibration and Hydro test for Field\nInstruments. (31094ORD243)\n\nPlease submit from your side (Contract Manager).','Closed','2026-02-04 13:27:35','2026-02-04 14:07:20','fb0f6c888d97beb7414d2a3ad9e1db89','2026-02-04 13:37:17'),(27,'LA-Support-2026-0BEFD410','john@loopsautomation.com','IT Support','Hi Rami,\n\nOutlook is still not working and is using Gmail. Gmail was automatically\nsigned out. I forgot the password. Could you please provide the same.\nAppreciate your support.\nSending this from my mobile\n\n*','Closed','2026-02-05 13:27:44','2026-02-05 14:07:49','bd5fa4caf7729b925f5700ac7cb7eadd','2026-02-05 13:37:41'),(29,'LA-Support-2026-D44CB761','m.elsiddig@loopsautomation.com','Outlook working','Dear Mr. Rami,\n\nPlease your support on the Outlook its not working.\n\nSiddig.','Closed','2026-02-03 13:27:49','2026-02-03 13:37:58','cc9b2f3c0beda883387d4794205b7188','2026-02-03 13:37:58'),(31,'LA-Support-2026-D32E54B1','saurav@loopsautomation.com','please send email for support','Dear Rami,\n\n \n\nRequired contract manager approval on odoo for 31107ORD203','Closed','2026-02-04 13:27:52','2026-02-04 13:38:12','152353dc28c2bfa0071c97f11a5a2bd9','2026-02-04 13:38:12'),(33,'LA-Support-2026-8268C74A','fatemeh@loopsautomation.com','IT Support','Ethernet in not working\n\nOn Sun, 1 Feb 2026 at 10:12 AM rami wahdan \nwrote:','Closed','2026-02-01 13:27:56','2026-02-01 13:38:34','d685889ba6c0ef67a4b30acf709fa42d','2026-02-01 13:38:34'),(37,'LA-Support-2026-FEA5E0A0','john@loopsautomation.com','Delay in email receipt','Dear Rami,\n\n \n\nThere are some delay in receipt of emails. Trail mail is sent from Kent on\nFriday, however we received it today only. Refer attached snapshot. Please\ncheck and do the needful.','Closed','2026-02-02 13:28:07','2026-02-02 13:38:51','27f88649cf77b04f0a86b0a19d8ca49a','2026-02-02 13:38:51'),(40,'LA-Support-2026-E25AE4A3','saurav@loopsautomation.com','IT Suuport','Dear Rami,\n\n \n\nPlease note that my new email chrome browsing is not available , could you  please help me to restore it and also ODOO credential provide over email.','Closed','2026-02-01 13:28:14','2026-02-01 13:32:07','dc4a20beebfbc2c405d85d5b0381a459','2026-02-01 13:32:07'),(41,'LA-Support-2026-F8EDA4D4','john.mathew@loopsautomation.com','Ticket Created: LA-Support-2026-3AD79652','Dear Rami, \nCould you highlight the reason for ticket. Is there any problem with my\nLaptop?\nYour reply would be very helpful.','Closed','2026-02-08 14:27:23','2026-02-08 14:36:43','452a2560f581bf81c6802b9edc99a4dd','2026-02-08 14:36:43'),(43,'LA-Support-2026-C1F55B2E','m.nassif@loopsautomation.com','KELTON Support: 31894 Follow-on Ticket: Follow-on Ticket: FW: T8073 PO - 31094ORD404','Dear Rami, \n\n \n\nI was facing some issues in KELTON software , as per bellow email they advised to update the software to the latest version . using the link in the bellow email . \n\n \n\nPlease support and download the latest version on the same device by today if possible.','Closed','2026-02-09 12:12:23','2026-02-09 15:01:06','6500dabb97d0ff1f7149601c7230e318','2026-02-09 15:01:06'),(44,'LA-Support-2026-7894F1D7','m.nassif@loopsautomation.com','Professional Camera','Dear Rami, \n\n \n\nIt is with me in office , you can borrow it . \n\n \n\nPlease ensure that it is used properly and handled safely as this is for my personal usage .','Closed','2026-02-09 12:12:27','2026-02-09 12:14:25','a0398cdad3c6b2cb4e97b6571424fb9b','2026-02-09 12:14:25'),(45,'LA-Support-2026-F1EF07FA','mustafa@loopsautomation.com','11072ORD103_CONTROL EXPERT L SINGLE LIC_LICENSE LOST','Dear Rami\n\n \n\nKindly refer below mail for the software download','Closed','2026-02-09 13:41:50','2026-02-10 12:42:26','e0524e40614a794d5cfe27583a312164','2026-02-10 12:42:26'),(46,'LA-Support-2026-61E461FE','saif@loopsautomation.com','From Zahira','Dear Rami,\n\nPlease help Sarah to move her IT related items from her place to new place\n(near harith)\n\n-- \n\n*','Closed','2026-02-09 14:38:41','2026-02-09 15:00:34','68388a0e1079229e8ca7ee9cda848ada','2026-02-09 15:00:34'),(47,'LA-Support-2026-59DB6570','m.nassif@loopsautomation.com','KELTON Support: 31894 Follow-on Ticket: Follow-on Ticket: FW: T8073 PO - 31094ORD404','Dear Brad, \n\n \n\nWe have updated the version to 2.3.1 and tried to open the old calculations files but still the same issue we are facing , nothing happens once we open the files . \n\n \n\nAlso I have tried to re work on new calculation , we have saved the new calculation and tried to open it one more time but the same . we are waiting for more than 15min to open , \n\n \n\nCan we have a meeting to share our screen? Or do you think that if we replaced the device with new device this will work?','Waiting','2026-02-09 15:42:23','2026-02-15 11:16:30','052c8002e62dab726d8dd97de6723ed2','2026-02-15 11:16:30'),(48,'LA-Support-2026-4FF76CD1','jaimon@loopsautomation.com','Meetig Link','Dear Rami,\nplease make meeting link for project 11085-Site\n\nWith','Closed','2026-02-10 11:34:09','2026-02-10 11:39:34','ee4d6df792b34c744550cb59a7b1b1c8','2026-02-10 11:39:34'),(49,'LA-Support-2026-1B330D5E','m.elsiddig@loopsautomation.com','IT Support','Noted. \n\n \n\nHere the charger completed.','Closed','2026-02-10 11:47:23','2026-02-10 11:47:40','501506946da8f61f85bc3468111ec267','2026-02-10 11:47:40'),(50,'LA-Support-2026-54564A6F','mustafa@loopsautomation.com','11072ORD103_CONTROL EXPERT L SINGLE LIC_LICENSE LOST','Dear Rami\n\n \n\nPlease see the below mail for latest download method','Closed','2026-02-10 12:41:50','2026-02-10 16:57:42','aaef8698f598680663b1543331d73d17','2026-02-10 16:57:42'),(51,'LA-Support-2026-43F2AF9D','saurav@loopsautomation.com','IT Support','Dear Rami,\n\n \n\nPlease approve on odoo 31108ORD307','Closed','2026-02-10 12:47:22','2026-02-10 12:48:00','8a28221281ecd869f99ebf0590367cf2','2026-02-10 12:48:00'),(52,'LA-Support-2026-FFCAB15F','saurav@loopsautomation.com','IT Support','Dear Rami,\n\n \n\nPlease approve on odoo 31108ORD307','Closed','2026-02-10 12:47:23','2026-02-10 12:48:49','4ae8c10bf7a371bf5d2397f3a4106f9b','2026-02-10 12:48:49'),(53,'LA-Support-2026-FB5A1B38','john@loopsautomation.com','Issues in delivering the email','Dear Rami,\n\nTrail mail from Kent for your reference and action as required.','Waiting','2026-02-10 14:22:23','2026-02-11 12:46:47','b097a33df7a80d36732e8d445430eeac','2026-02-11 12:46:47'),(54,'LA-Support-2026-0F3EF626','john@loopsautomation.com','Outlook not working','Dear Rami,\n\nMy Oultook is not working. It would be great if you can sorted out the\nissue with my outlook.\n\n-- \n\n*','Closed','2026-02-10 15:07:24','2026-02-10 16:57:04','7eb098f89f5eb44218b1eacf40207d88','2026-02-10 16:57:04'),(57,'LA-Support-2026-01EF98FA','saurav@loopsautomation.com','Add email for scan','Dear Rami,\n\n \n\nPlease add my email for scanning purpose.','Closed','2026-02-10 16:56:08','2026-02-11 09:37:04','3dde837985dd21e50e351a6c1afcbd81','2026-02-11 09:37:04'),(60,'LA-Support-2026-1E1FFCAD','fatemeh@loopsautomation.com','I need access to Ammar Email can you coordinate ?','I need access to Ammar Email can you coordinate ?','Closed','2026-02-10 18:27:23','2026-02-11 14:09:08','e7dbd444e0a29230e7e9234849ec9675','2026-02-11 14:09:08'),(65,'LA-Support-2026-9E1044B0','saurav@loopsautomation.com','APPROVE ON ODDO 31106CO-03ORD001','Dear Rami,\n\n \n\nPlease review and approve on oddo - \n\n31106CO-03ORD001','Closed','2026-02-11 12:41:23','2026-02-11 12:45:52','b92d6203ff0f6b29f011999d248ee3b9','2026-02-11 12:45:52'),(68,'LA-Support-2026-B6E472EA','hr@loopsautomation.com','','---------- Forwarded message ---------\nFrom: Fatima Habib - HR \nDate: Wed, Feb 11, 2026 at 1:07 PM\nSubject:\nTo: rami wahdan \n\n*','Closed','2026-02-11 13:19:53','2026-02-11 14:09:20','7cb98e9ed517a383e372ef1b78f82f26','2026-02-11 14:09:20'),(69,'LA-Support-2026-9BAE56DE','saurav@loopsautomation.com','Add email for scan','Dear Rami ,\n\n \n\nPlease remove sales email , it is still registered under my name.\n\n \n\nKindly add the current email - saurav@loopsautomation.com','Closed','2026-02-11 16:21:23','2026-02-11 16:46:24','5d4947232cd5a9dc88922588c7c216d6','2026-02-11 16:46:24'),(73,'LA-Support-2026-31B61BF5','m.elsiddig@loopsautomation.com','Network threat blocked notification','Dear Rami, \n\n \n\nPlease see the below notification .','Closed','2026-02-15 09:35:23','2026-02-15 09:36:36','2cc3342aa6f797fa8e5e6bed17990ea5','2026-02-15 09:36:36');
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
) ENGINE=InnoDB AUTO_INCREMENT=79 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `updates`
--

LOCK TABLES `updates` WRITE;
/*!40000 ALTER TABLE `updates` DISABLE KEYS */;
INSERT INTO `updates` VALUES (3,4,'ok Mustafa we will do that as soon as you come to my office',1,'2026-02-05 15:17:07'),(4,4,'we will wait for download link for V. 14.0',0,'2026-02-05 15:27:17'),(22,13,'ok i will come and fix',1,'2026-02-08 09:58:22'),(23,13,'someone logged in to your phone but now it is restored.',1,'2026-02-08 10:03:45'),(24,4,'Dear Mustafa,\r\nPlease update me on this or else I will consider this as resolved. This ticket will be marked as \"Closed\" by the end of today if no response from your side!',1,'2026-02-08 10:09:33'),(25,4,'Hi Rami\n\nKindly proceed to close the ticket. So far not yet received the support from Schneider.',0,'2026-02-08 10:32:24'),(26,4,'done!',1,'2026-02-08 10:35:42'),(27,40,'It is ok now',1,'2026-02-08 13:32:07'),(28,19,'App password created and now it is ok',1,'2026-02-08 13:32:40'),(29,20,'gmail app password created and now ok',1,'2026-02-08 13:33:09'),(30,23,'done!',1,'2026-02-08 13:37:17'),(31,27,'issue with gmail servers',1,'2026-02-08 13:37:41'),(32,29,'created new profile now ok',1,'2026-02-08 13:37:58'),(33,31,'done!',1,'2026-02-08 13:38:12'),(34,33,'fixed!',1,'2026-02-08 13:38:34'),(35,37,'issue with gmail server!',1,'2026-02-08 13:38:51'),(36,41,'no it was for the support i provided in the previous days. It won\'t happened again',1,'2026-02-08 14:36:43'),(37,43,'ok will do that inshalla',1,'2026-02-09 12:12:40'),(38,43,'https://we.tl/t-UEyV2hnUYI',1,'2026-02-09 12:45:26'),(39,43,'I opened the link and nothing to download so please ask them for the correct link!',1,'2026-02-09 12:49:32'),(40,45,'ok come and we will do the installation',1,'2026-02-09 13:42:37'),(41,45,'I will need to close it again as no resolution yet. Please advise?',1,'2026-02-09 14:35:50'),(42,46,'ok',1,'2026-02-09 14:38:59'),(43,46,'done',1,'2026-02-09 15:00:34'),(44,43,'Done, I installed the new version as requested.',1,'2026-02-09 15:01:06'),(45,47,'waiting for client reply',1,'2026-02-09 18:02:14'),(46,45,'Gentle reminder...',1,'2026-02-10 11:03:18'),(47,48,'here is the meeting link Mr. Jaimon.\r\n\r\nhttps://teams.live.com/meet/932033598389?p=23ZTtvmvfO8xjBXUJ2',1,'2026-02-10 11:39:34'),(48,49,'thanks',1,'2026-02-10 11:47:40'),(49,51,'duplicated',1,'2026-02-10 12:48:00'),(50,52,'done',1,'2026-02-10 12:48:49'),(51,54,'outlook issue is on-going as it is our version 2013 is not supported. I will try to find a solution!',1,'2026-02-10 15:11:25'),(52,53,'ok I will look into it',1,'2026-02-10 15:13:19'),(53,54,'Dear John,\r\n\r\nIssue resolved and now you are getting the emails.',1,'2026-02-10 16:57:04'),(54,50,'Dear Mustafa,\r\n\r\nWe installed the software and put the serial number. now it is working fine',1,'2026-02-10 16:57:42'),(55,47,'Dear Nassif,\r\n\r\nPlease update on the status!',1,'2026-02-10 16:58:08'),(56,53,'I will investigate the issue, the link should be www.loopsautomation.com',1,'2026-02-10 16:59:14'),(57,57,'Dear Saurav,\r\nPlease check now and let me know. You should see your name there in the (second printer on the left).',1,'2026-02-10 17:04:04'),(58,57,'Please confirm so i can close this ticket',1,'2026-02-10 17:37:57'),(59,47,'I cant update on the status , the link bellow is not working when I press on\nit . \n\n-----Original Message-----\nFrom: IT Support [mailto:rami.wahdan@loopsautomation.com] \nSent: Tuesday, February 10, 2026 4:58 PM\nTo: m.nassif@loopsautomation.com\nSubject: Update on Ticket LA-Support-2026-59DB6570\n\nThere is an update on your IT Support ticket.\n\nTicket Number: LA-Support-2026-59DB6570\nStatus: Waiting\n\nMessage:\nDear Nassif,\n\nPlease update on the status!\n\nCheck ticket status:\nhttp://localhost/simple-it-ticket-system/public/ticket_status.php?token=052c\n8002e62dab726d8dd97de6723ed2',1,'2026-02-10 18:02:24'),(60,57,'since no answer the ticket is closed',1,'2026-02-11 09:37:04'),(61,60,'Please tell me if you know ammar gmail password?',1,'2026-02-11 09:54:20'),(62,47,'that is what i meant to click the link in the email. I am asking any news regarding the issue? you sent them an email, did you get a response?',1,'2026-02-11 10:13:48'),(66,65,'Done',1,'2026-02-11 12:45:52'),(67,53,'Dear John,\r\nI will resolve this issue next week on Sunday',1,'2026-02-11 12:46:46'),(68,60,'Please reply to me if you have his password!',1,'2026-02-11 12:58:58'),(70,68,'ok',1,'2026-02-11 13:20:24'),(71,60,'NO',1,'2026-02-11 13:31:23'),(72,68,'done check and let me know before I close the ticket!',1,'2026-02-11 13:55:36'),(73,60,'done it is ok and downloading emails.',1,'2026-02-11 14:09:07'),(74,68,'done',1,'2026-02-11 14:09:20'),(75,69,'done',1,'2026-02-11 16:46:24'),(76,73,'just ignore it, thanks',1,'2026-02-15 09:36:36'),(77,47,'ok i will come upstairs at 11AM',1,'2026-02-15 10:39:03'),(78,47,'Dear Nassif it is done. Attached is the file requested so please send them the file and when they reply message me back again here so we can install the license.',1,'2026-02-15 11:16:30');
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

-- Dump completed on 2026-02-15 11:17:32
