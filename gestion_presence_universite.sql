-- xampp-lite https://sourceforge.net/projects/xampplite/   
--   
-- MariaDB dump 10.17  Distrib 10.4.8-MariaDB, for Win32 (AMD64)
--
-- Host: localhost    Database: gestion_presence_universite
-- ------------------------------------------------------
-- Server version	10.4.8-MariaDB
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `classes`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `classes` (
  `id` int(11) NOT NULL,
  `nom_classe` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
);
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` VALUES (1,'GL2','genie logiciel');
INSERT INTO `classes` VALUES (2,'finance','administration');
INSERT INTO `classes` VALUES (3,'sr1','systemes et reseaux');
INSERT INTO `classes` VALUES (4,'S I 2','software ingeneering');
INSERT INTO `classes` VALUES (6,'sr2','systemes et reseaux');

--
-- Table structure for table `periode`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `periode` (
  `id_periode` int(11) NOT NULL,
  `nom_periode` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `heure_debut` time DEFAULT NULL,
  `heure_fin` time DEFAULT NULL,
  PRIMARY KEY (`id_periode`)
);
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `periode`
--

INSERT INTO `periode` VALUES (5,'entree','07:00:00','07:45:00');
INSERT INTO `periode` VALUES (6,'premiere periode','07:30:00','09:30:00');
INSERT INTO `periode` VALUES (7,'deuxieme periode','09:30:01','11:30:00');
INSERT INTO `periode` VALUES (8,'troisieme periode','12:45:00','14:45:00');
INSERT INTO `periode` VALUES (10,'quatrieme periode','14:45:01','16:45:00');
INSERT INTO `periode` VALUES (11,'sortie','16:45:01','16:50:00');
INSERT INTO `periode` VALUES (12,'pause','11:31:11','12:44:12');

--
-- Table structure for table `presence`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `presence` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date_heure` datetime NOT NULL,
  `statut` enum('present','absent','retard') COLLATE utf8_unicode_ci NOT NULL,
  `id_periode` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `id_periode` (`id_periode`),
  CONSTRAINT `presence_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `presence_ibfk_2` FOREIGN KEY (`id_periode`) REFERENCES `periode` (`id_periode`) ON DELETE SET NULL
);
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `presence`
--

INSERT INTO `presence` VALUES (1,1,'2025-09-03 16:19:20','present',10);
INSERT INTO `presence` VALUES (2,1,'2025-09-04 08:42:56','present',6);
INSERT INTO `presence` VALUES (3,2,'2025-09-13 13:03:37','present',8);
INSERT INTO `presence` VALUES (4,2,'2025-09-17 09:16:55','present',6);
INSERT INTO `presence` VALUES (5,3,'2025-09-17 09:34:03','present',7);
INSERT INTO `presence` VALUES (6,1,'2025-09-18 10:19:15','present',7);
INSERT INTO `presence` VALUES (7,1,'2025-09-18 12:45:54','present',8);
INSERT INTO `presence` VALUES (8,4,'2025-09-22 11:55:23','present',12);
INSERT INTO `presence` VALUES (9,4,'2025-09-22 14:25:47','present',8);

--
-- Table structure for table `users`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `prenom` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `mot_de_passe` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type` enum('etudiant','personnel','admin') COLLATE utf8_unicode_ci NOT NULL,
  `photo_reference` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `classe_id` int(11) DEFAULT NULL,
  `matricule` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `face_encoding` blob DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `classe_id` (`classe_id`),
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`classe_id`) REFERENCES `classes` (`id`) ON DELETE SET NULL
);
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

INSERT INTO `users` VALUES (1,'Nkengafac','joel','joelnkengafac616@gmail.com','$2y$10$A4zceuQCIYEgO2b0FWh3dOSyqRW0ST.ZOIazJSYp6DapguXELkR2S','etudiant','user_68b8589716498.jpg',1,'etu001','2025-09-03 16:02:47','���\0\0\0\0\0\0�numpy.core.multiarray��_reconstruct����numpy��ndarray���K\0��Cb���R�(KK���h�dtype����f8�����R�(K�<�NNNJ����J����K\0t�b�B\0\0\0\0\0\0�Wkȿ\0\0\0\0̔�?\0\0\0�a��?\0\0\0�Ƨ�?\0\0\0�!E}�\0\0\0@-���\0\0\0��!�?\0\0\0��Qv�\0\0\0\0b��?\0\0\0�18��\0\0\0 ���?\0\0\0`���?\0\0\0���ſ\0\0\0 RҸ�\0\0\0\0\n?�?\0\0\0�|��?\0\0\0@�ſ\0\0\0�A���\0\0\0���¿\0\0\0`�Gÿ\0\0\0\0���\0\0\0��?\0\0\0���\0\0\0��߱?\0\0\0�y���\0\0\0���Ͽ\0\0\0�n��\0\0\0��ȿ\0\0\0�}��?\0\0\0\0@�ſ\0\0\0����?\0\0\0 >�?\0\0\0�C�ǿ\0\0\0 ~ݲ�\0\0\0��l��\0\0\0�Ԑ?\0\0\0�D�?\0\0\0���k�\0\0\0����?\0\0\0�~�?\0\0\0`�`��\0\0\0@[�n?\0\0\0��\"��\0\0\0@%��?\0\0\0 X�?\0\0\0��旿\0\0\0\0��C?\0\0\0࣓�?\0\0\0\0P �?\0\0\0\0��Ϳ\0\0\0�?�?\0\0\0\0��?\0\0\0@���?\0\0\0`���?\0\0\0@���?\0\0\0 �R��\0\0\0����\0\0\0�\'�?\0\0\0@�ÿ\0\0\0 \rݽ?\0\0\0 MR�?\0\0\0�c%��\0\0\0�E���\0\0\0�\\,��\0\0\0\0z��?\0\0\0��k�?\0\0\0@����\0\0\0�aȹ�\0\0\0`u��?\0\0\0`iٻ�\0\0\0����\0\0\0@�	�?\0\0\0�2���\0\0\0`r[¿\0\0\0`1�˿\0\0\0\0�?\0\0\0 �G�?\0\0\0`��?\0\0\0\0 �¿\0\0\0���e?\0\0\0`^�˿\0\0\0����?\0\0\0��{��\0\0\0��/�?\0\0\0��}��\0\0\0\0A�?\0\0\0��\Z��\0\0\0��O�?\0\0\0��ƽ?\0\0\0\0}L�?\0\0\0����\0\0\0\0���?\0\0\0@���\0\0\0 ��?\0\0\0\0�V�?\0\0\0��o��\0\0\0�����\0\0\0�x���\0\0\0��\0\0\0 �ͤ�\0\0\0��P�?\0\0\0@]�ſ\0\0\0��a��\0\0\0\0T�?\0\0\0\0V\rÿ\0\0\0���?\0\0\0`�Ο�\0\0\0\0 �?\0\0\0\0�v��\0\0\0@+}�?\0\0\0��x��\0\0\0���?\0\0\0`��?\0\0\0�p<ȿ\0\0\0�y�?\0\0\0�u��?\0\0\0 .N��\0\0\0�!ٽ?\0\0\0����?\0\0\0\07�?\0\0\0�����\0\0\0��\n��\0\0\0@�W��\0\0\0��ʭ�\0\0\0@p��?\0\0\0 oĭ�\0\0\0\0%ly?\0\0\0�]��?�t�b.');
INSERT INTO `users` VALUES (2,'JOJO','njualem','jojo@gmail.com','$2y$10$lb/r96FxUm9XjKYpdMQOKebKtIQnA/xHrPnbxxtvp5L31Ag3MVDr.','admin','user_68b859d0c0ad3.jpg',2,'ADM001','2025-09-03 16:08:00','���\0\0\0\0\0\0�numpy.core.multiarray��_reconstruct����numpy��ndarray���K\0��Cb���R�(KK���h�dtype����f8�����R�(K�<�NNNJ����J����K\0t�b�B\0\0\0\0\0\0�ǳϿ\0\0\0`�Ծ?\0\0\0�כ?\0\0\0 �Ѩ?\0\0\0@)<��\0\0\0`>��\0\0\0@YŹ?\0\0\0@Ƈ?\0\0\0`�k�?\0\0\0@j=�?\0\0\0�fk�?\0\0\0\0��:?\0\0\0��/ʿ\0\0\0 ?��\0\0\0����?\0\0\0�w��?\0\0\0�7�̿\0\0\0��O��\0\0\0 �7��\0\0\0�便\0\0\0@��?\0\0\0`���?\0\0\0��!��\0\0\0 �x�?\0\0\0\0!¿\0\0\0���п\0\0\0�%V��\0\0\0�\r�̿\0\0\0ྑ�?\0\0\0�l���\0\0\0`��?\0\0\0���i?\0\0\0`_�ȿ\0\0\0�Ȱ��\0\0\0`/���\0\0\0@F��\0\0\0\0���?\0\0\0\0!g��\0\0\0�ʏ�?\0\0\0 ���?\0\0\0�����\0\0\0\0\\��\0\0\0\0kZ��\0\0\0@8��?\0\0\0����?\0\0\0`%��\0\0\0�\0P~�\0\0\0�F�q�\0\0\0`b%�?\0\0\0@��ο\0\0\0 ����\0\0\0�9��?\0\0\0\0�4�?\0\0\0����?\0\0\0�*A��\0\0\0��-ÿ\0\0\0\0X�b?\0\0\0�����\0\0\0��eɿ\0\0\0 *M�?\0\0\0��Z�?\0\0\0\0�`��\0\0\0�a��\0\0\0 �c?\0\0\0����?\0\0\0�bC�?\0\0\0`iɺ�\0\0\0 �1��\0\0\0���?\0\0\0@\Z���\0\0\0\0�Z?\0\0\0�\r��?\0\0\0��`��\0\0\0\0��ÿ\0\0\0@cx̿\0\0\0@�?\0\0\0\0-��?\0\0\0@pV�?\0\0\0���ǿ\0\0\0\0��y?\0\0\0��eп\0\0\0����?\0\0\0�\Z��\0\0\0��d�?\0\0\0�^��\0\0\0\0�y��\0\0\0@����\0\0\0@���?\0\0\0\0i*�?\0\0\0���?\0\0\0`�ߔ�\0\0\0�ʦ�?\0\0\0@\\�?\0\0\0\0gt?\0\0\0`�?\0\0\0�����\0\0\0�f���\0\0\0�����\0\0\0 >�\0\0\0�;���\0\0\0`��?\0\0\0��Ŀ\0\0\0`���?\0\0\0 1Ĺ?\0\0\0`k+ÿ\0\0\0����?\0\0\0����?\0\0\0�~X}?\0\0\0 ���?\0\0\0�k�?\0\0\0��ȥ�\0\0\0�;���\0\0\0\0C��?\0\0\0 ��Ϳ\0\0\0 ��?\0\0\0\0^��?\0\0\0��>�?\0\0\0�6�?\0\0\0\0��k?\0\0\0�GF�?\0\0\0`�ķ�\0\0\0@Μ�?\0\0\0`4���\0\0\0@��t�\0\0\0 ��?\0\0\0@����\0\0\0��st?\0\0\0`v�?�t�b.');
INSERT INTO `users` VALUES (3,'nkonlack','gaelle','archangenzouelebo@gmail.com','$2y$10$vMz358zWd01TYJiNuZx1O.17jeUYXHddzEgLtddcSApwLnwIHBWt6','personnel','photo_68ca6fe59381c.jpg',2,'etu002','2025-09-17 09:14:38',NULL);
INSERT INTO `users` VALUES (4,'toukam','erika','toukam@gmail.com','$2y$10$l6AXgqGacFRwZiSzBuR8E.bGWqfeMeoSRTBV7fiy1R3sVDXa/qrcy','personnel','user_68d12abf827b0.jpg',2,'perso001','2025-09-22 11:53:51','���\0\0\0\0\0\0�numpy.core.multiarray��_reconstruct����numpy��ndarray���K\0��Cb���R�(KK���h�dtype����f8�����R�(K�<�NNNJ����J����K\0t�b�B\0\0\0\0\0\0�&��\0\0\0 M�?\0\0\0��?�?\0\0\0�����\0\0\0`����\0\0\0@�b��\0\0\0\0x�o?\0\0\0�|\'��\0\0\0@{0�?\0\0\0`P���\0\0\0��g�?\0\0\0 2{��\0\0\0\07�ɿ\0\0\0�xk��\0\0\0`*���\0\0\0@��?\0\0\0`lsſ\0\0\0���ſ\0\0\0@A�\0\0\0\0����\0\0\0����?\0\0\0\0.�c?\0\0\0`GϢ�\0\0\0 <D�?\0\0\0��5��\0\0\0\0��Կ\0\0\0��\0\0\0�;��\0\0\0��T?\0\0\0�E_?\0\0\0�:sq�\0\0\0����?\0\0\0\0�,ȿ\0\0\0 �ꐿ\0\0\0�� ��\0\0\0 ���?\0\0\0 >��?\0\0\0 ���\0\0\0�_!�?\0\0\0\0�,��\0\0\0��˿\0\0\0`����\0\0\0 �+�?\0\0\0 b��?\0\0\0����?\0\0\0�U��?\0\0\0����?\0\0\0���a�\0\0\0�怐?\0\0\0`#�Ŀ\0\0\0�s��\0\0\0�K۹?\0\0\0�H��?\0\0\0@�ȯ?\0\0\0@�@��\0\0\0`��̿\0\0\0��%��\0\0\0�K-�?\0\0\0`; ȿ\0\0\0��U�?\0\0\0��5��\0\0\0 m1��\0\0\0�����\0\0\0�=���\0\0\0\0�K�?\0\0\0 9��?\0\0\0\0nĿ\0\0\0\0	�Ŀ\0\0\0��M�?\0\0\0�!Nſ\0\0\0��ܰ�\0\0\0\0�|�?\0\0\0�+ɿ\0\0\0`ڢ��\0\0\0��ӿ\0\0\0\0�X�?\0\0\0��o�?\0\0\0 �÷?\0\0\0���ÿ\0\0\0��+�?\0\0\0�s翿\0\0\0���?\0\0\0�����\0\0\0@Ă�?\0\0\0\0\'$��\0\0\0��.�?\0\0\0�S���\0\0\0����\0\0\0@`\0�?\0\0\0��:��\0\0\0@���?\0\0\0�m��?\0\0\0�7���\0\0\0�Ep�?\0\0\0@�%��\0\0\0��ї�\0\0\0\0�\\�\0\0\0�*���\0\0\0��¿\0\0\0\0��?\0\0\0 \n���\0\0\0��Ɲ�\0\0\0�o��\0\0\0����?\0\0\0�R�ʿ\0\0\0��w�\0\0\0\0���?\0\0\0\0�P��\0\0\0��\0\0\0����?\0\0\0@���\0\0\0\06���\0\0\0��L�?\0\0\0`i�˿\0\0\0`v�?\0\0\0\0���?\0\0\0`|�?\0\0\0��K�?\0\0\0\0��?\0\0\0�go�?\0\0\0�z���\0\0\0�ï��\0\0\0���\0\0\0@ni��\0\0\0����?\0\0\0�z���\0\0\0\0Zߦ?\0\0\0�,�?�t�b.');
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-09-23  9:38:02
