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

INSERT INTO `users` VALUES (1,'Nkengafac','joel','joelnkengafac616@gmail.com','$2y$10$A4zceuQCIYEgO2b0FWh3dOSyqRW0ST.ZOIazJSYp6DapguXELkR2S','etudiant','user_68b8589716498.jpg',1,'etu001','2025-09-03 16:02:47','€•‹\0\0\0\0\0\0Œnumpy.core.multiarray”Œ_reconstruct”“”Œnumpy”Œndarray”“”K\0…”Cb”‡”R”(KK€…”hŒdtype”“”Œf8”‰ˆ‡”R”(KŒ<”NNNJÿÿÿÿJÿÿÿÿK\0t”b‰B\0\0\0\0\0\0 WkÈ¿\0\0\0\0Ì”¾?\0\0\0€a»š?\0\0\0àÆ§?\0\0\0à!E}¿\0\0\0@-£¿\0\0\0€!¿?\0\0\0ÀÁQv¿\0\0\0\0bËÄ?\0\0\0€18£¿\0\0\0 ¢óÎ?\0\0\0`½±ˆ?\0\0\0 éÙÅ¿\0\0\0 RÒ¸¿\0\0\0\0\n?§?\0\0\0à|§Á?\0\0\0@šÅ¿\0\0\0 A»º¿\0\0\0À£¾Â¿\0\0\0`åGÃ¿\0\0\0\0™¡¿\0\0\0€?\0\0\0 Ûó‰¿\0\0\0€Åß±?\0\0\0€y¨¼¿\0\0\0À©ìÏ¿\0\0\0€nµ¿\0\0\0àâ¯È¿\0\0\0 }Œ°?\0\0\0\0@‹Å¿\0\0\0À¬’•?\0\0\0 >‘?\0\0\0 C°Ç¿\0\0\0 ~İ²¿\0\0\0 ´l¬¿\0\0\0€Ô?\0\0\0€Dè¥?\0\0\0€ŞÑk¿\0\0\0 öªÀ?\0\0\0À~²?\0\0\0`›`µ¿\0\0\0@[Ùn?\0\0\0Àï\"‰¿\0\0\0@%¿Ò?\0\0\0 XÃ?\0\0\0€£æ—¿\0\0\0\0ğÅC?\0\0\0à£“?\0\0\0\0P ´?\0\0\0\0¬¡Í¿\0\0\0à?ì‚?\0\0\0\0Ë²?\0\0\0@èÙÄ?\0\0\0`’ ±?\0\0\0@‹®„?\0\0\0 ÛRÀ¿\0\0\0À•£¿\0\0\0 \'è£?\0\0\0@ñ…Ã¿\0\0\0 \rİ½?\0\0\0 MR«?\0\0\0€c%¹¿\0\0\0€E½¿\0\0\0 \\,Ÿ¿\0\0\0\0z¹Ë?\0\0\0àÌk»?\0\0\0@Ÿø¸¿\0\0\0€aÈ¹¿\0\0\0`uûÍ?\0\0\0`iÙ»¿\0\0\0À¡Š¿\0\0\0@Š	´?\0\0\0€2¶µ¿\0\0\0`r[Â¿\0\0\0`1ƒË¿\0\0\0\0Æ?\0\0\0 ‡GÖ?\0\0\0`„Â?\0\0\0\0 äÂ¿\0\0\0€Ó×e?\0\0\0`^³Ë¿\0\0\0 ı¼©?\0\0\0€Ò{º¿\0\0\0Àÿ/§?\0\0\0Àö}À¿\0\0\0\0AŸ?\0\0\0 è\Z¹¿\0\0\0€öO±?\0\0\0€•Æ½?\0\0\0\0}L¤?\0\0\0 ¡›¿\0\0\0\0ø¿É?\0\0\0@õ¤¿\0\0\0 ©?\0\0\0\0˜V™?\0\0\0àñoœ¿\0\0\0€ªÀ®¿\0\0\0ÀxŠµ¿\0\0\0 ó¦¿\0\0\0 ŒÍ¤¿\0\0\0€‘P°?\0\0\0@]’Å¿\0\0\0€½a…¿\0\0\0\0Tµ?\0\0\0\0V\rÃ¿\0\0\0€‹Ã?\0\0\0`õÎŸ¿\0\0\0\0 Í?\0\0\0\0ıv•¿\0\0\0@+}»?\0\0\0€íx¡¿\0\0\0€ä”?\0\0\0`ËÄ?\0\0\0 p<È¿\0\0\0 yÍ?\0\0\0 uèÆ?\0\0\0 .N›¿\0\0\0 !Ù½?\0\0\0€™‚˜?\0\0\0\07À?\0\0\0à“­µ¿\0\0\0€†\n„¿\0\0\0@ïWÁ¿\0\0\0ÀÕÊ­¿\0\0\0@p¸©?\0\0\0 oÄ­¿\0\0\0\0%ly?\0\0\0À]€¤?”t”b.');
INSERT INTO `users` VALUES (2,'JOJO','njualem','jojo@gmail.com','$2y$10$lb/r96FxUm9XjKYpdMQOKebKtIQnA/xHrPnbxxtvp5L31Ag3MVDr.','admin','user_68b859d0c0ad3.jpg',2,'ADM001','2025-09-03 16:08:00','€•‹\0\0\0\0\0\0Œnumpy.core.multiarray”Œ_reconstruct”“”Œnumpy”Œndarray”“”K\0…”Cb”‡”R”(KK€…”hŒdtype”“”Œf8”‰ˆ‡”R”(KŒ<”NNNJÿÿÿÿJÿÿÿÿK\0t”b‰B\0\0\0\0\0\0 Ç³Ï¿\0\0\0`òÔ¾?\0\0\0À×›?\0\0\0 ßÑ¨?\0\0\0@)<‡¿\0\0\0`>¿\0\0\0@YÅ¹?\0\0\0@Æ‡?\0\0\0`ÒkÀ?\0\0\0@j=†?\0\0\0àfkÊ?\0\0\0\0Ğì:?\0\0\0Àı/Ê¿\0\0\0 ?»¿\0\0\0 Œ„²?\0\0\0€w½?\0\0\0 7¶Ì¿\0\0\0À†O³¿\0\0\0 7¹¿\0\0\0 ä¾¿\0\0\0@¦Š?\0\0\0`š˜?\0\0\0àÎ!“¿\0\0\0 ˆx´?\0\0\0\0!Â¿\0\0\0Àœ·Ğ¿\0\0\0À%V¿\0\0\0 \rœÌ¿\0\0\0à¾‘¸?\0\0\0 lö½¿\0\0\0`ş„?\0\0\0  ûi?\0\0\0`_æÈ¿\0\0\0ÀÈ°§¿\0\0\0`/¬¯¿\0\0\0@F¿\0\0\0\0¢”?\0\0\0\0!g—¿\0\0\0€Ê¾?\0\0\0 ”€ª?\0\0\0À€‰´¿\0\0\0\0\\™¿\0\0\0\0kZ¿\0\0\0@8ÎÒ?\0\0\0 …ÀÉ?\0\0\0`%£¿\0\0\0€\0P~¿\0\0\0ÀF¨q¿\0\0\0`b%±?\0\0\0@ •Î¿\0\0\0 ¹ª¿\0\0\0À9…´?\0\0\0\0€4Å?\0\0\0À˜‚µ?\0\0\0à*Aš¿\0\0\0€ó-Ã¿\0\0\0\0Xœb?\0\0\0€š‚˜¿\0\0\0 øeÉ¿\0\0\0 *M»?\0\0\0ÀĞZ¡?\0\0\0\0¤`À¿\0\0\0à¸a²¿\0\0\0 ‰c?\0\0\0ÀòğĞ?\0\0\0 bC³?\0\0\0`iÉº¿\0\0\0 ¶1¹¿\0\0\0 ¹Ì?\0\0\0@\Zû¾¿\0\0\0\0üZ?\0\0\0 \rùµ?\0\0\0À`±¿\0\0\0\0™¹Ã¿\0\0\0@cxÌ¿\0\0\0@Á?\0\0\0\0-”Õ?\0\0\0@pVÄ?\0\0\0À™¸Ç¿\0\0\0\0Åy?\0\0\0àåeĞ¿\0\0\0€¹Ÿ£?\0\0\0À\Z¤¿\0\0\0àÊd·?\0\0\0À^¾¿\0\0\0\0œy‘¿\0\0\0@¦¸¿\0\0\0@û‚§?\0\0\0\0i*¾?\0\0\0 Ë‚?\0\0\0`Âß”¿\0\0\0ÀÊ¦Í?\0\0\0@\\î¡?\0\0\0\0gt?\0\0\0`ğ”£?\0\0\0Àğ‡¬¿\0\0\0Àf °¿\0\0\0à‚®²¿\0\0\0 >ğ–¿\0\0\0 ;¡ª¿\0\0\0`„ ?\0\0\0€ÉÄ¿\0\0\0`ìô©?\0\0\0 1Ä¹?\0\0\0`k+Ã¿\0\0\0€ú´Ã?\0\0\0àı˜•?\0\0\0€~X}?\0\0\0 ½ğ?\0\0\0àk°?\0\0\0€¡È¥¿\0\0\0€;»€¿\0\0\0\0Cø¾?\0\0\0 ıÚÍ¿\0\0\0 ·Ì?\0\0\0\0^¥Å?\0\0\0€¨>¢?\0\0\0à¬6Â?\0\0\0\0½Úk?\0\0\0ÀGF¼?\0\0\0`¨Ä·¿\0\0\0@Îœ‘?\0\0\0`4˜»¿\0\0\0@˜¯t¿\0\0\0 ƒ°?\0\0\0@¡ÿ©¿\0\0\0Àµst?\0\0\0`v¡?”t”b.');
INSERT INTO `users` VALUES (3,'nkonlack','gaelle','archangenzouelebo@gmail.com','$2y$10$vMz358zWd01TYJiNuZx1O.17jeUYXHddzEgLtddcSApwLnwIHBWt6','personnel','photo_68ca6fe59381c.jpg',2,'etu002','2025-09-17 09:14:38',NULL);
INSERT INTO `users` VALUES (4,'toukam','erika','toukam@gmail.com','$2y$10$l6AXgqGacFRwZiSzBuR8E.bGWqfeMeoSRTBV7fiy1R3sVDXa/qrcy','personnel','user_68d12abf827b0.jpg',2,'perso001','2025-09-22 11:53:51','€•‹\0\0\0\0\0\0Œnumpy.core.multiarray”Œ_reconstruct”“”Œnumpy”Œndarray”“”K\0…”Cb”‡”R”(KK€…”hŒdtype”“”Œf8”‰ˆ‡”R”(KŒ<”NNNJÿÿÿÿJÿÿÿÿK\0t”b‰B\0\0\0\0\0\0à&Á¿\0\0\0 M°?\0\0\0€¡?³?\0\0\0 »…¼¿\0\0\0`¢¶¼¿\0\0\0@ëbµ¿\0\0\0\0xŞo?\0\0\0à|\'¸¿\0\0\0@{0Æ?\0\0\0`PêÀ¿\0\0\0À‹gÏ?\0\0\0 2{¼¿\0\0\0\07úÉ¿\0\0\0Àxk¤¿\0\0\0`*¥¤¿\0\0\0@æ¥Ì?\0\0\0`lsÅ¿\0\0\0€ŸÅ¿\0\0\0@Aò±¿\0\0\0\0à—°¿\0\0\0 §©“?\0\0\0\0.¶c?\0\0\0`GÏ¢¿\0\0\0 <DÄ?\0\0\0À¢5¿\0\0\0\0§ãÔ¿\0\0\0à´ó½¿\0\0\0ÀÍ¾Á¿\0\0\0À›T?\0\0\0€E_?\0\0\0€:sq¿\0\0\0€øı¼?\0\0\0\0•,È¿\0\0\0 —ê¿\0\0\0€ä ”¿\0\0\0 ÁŸƒ?\0\0\0 >®±?\0\0\0 ¡¿\0\0\0À_!Ã?\0\0\0\0¹,Ÿ¿\0\0\0àªË¿\0\0\0`™´¿\0\0\0 µ+Á?\0\0\0 b¥Ë?\0\0\0ÀÕø¹?\0\0\0ÀU¡˜?\0\0\0€øı?\0\0\0€õöa¿\0\0\0àæ€?\0\0\0`#•Ä¿\0\0\0às©¿\0\0\0€KÛ¹?\0\0\0ÀHÈÅ?\0\0\0@¼È¯?\0\0\0@–@•¿\0\0\0`¨İÌ¿\0\0\0À×%¿\0\0\0ÀK-¶?\0\0\0`; È¿\0\0\0€¢Uª?\0\0\0€5“¿\0\0\0 m1¼¿\0\0\0 µ¤µ¿\0\0\0à=«»¿\0\0\0\0ëK×?\0\0\0 9ÁÁ?\0\0\0\0nÄ¿\0\0\0\0	ÖÄ¿\0\0\0à•MÈ?\0\0\0à!NÅ¿\0\0\0 §Ü°¿\0\0\0\0¤|ª?\0\0\0à+É¿\0\0\0`Ú¢À¿\0\0\0àªíÓ¿\0\0\0\0—X”?\0\0\0€ÄoÓ?\0\0\0 ìÃ·?\0\0\0àáÓÃ¿\0\0\0À’+±?\0\0\0Àsç¿¿\0\0\0à™?\0\0\0àú£®¿\0\0\0@Ä‚º?\0\0\0\0\'$²¿\0\0\0€».¹?\0\0\0àS¬°¿\0\0\0àÉ¿\0\0\0@`\0Å?\0\0\0€¥:µ¿\0\0\0@§£‚?\0\0\0 m·Ë?\0\0\0à7ª¿\0\0\0€Ep?\0\0\0@Ì%³¿\0\0\0ÀûÑ—¿\0\0\0\0Ñ\\¿\0\0\0€*±¦¿\0\0\0€÷Â¿\0\0\0\0‡›?\0\0\0 \n½¯¿\0\0\0 µÆ¿\0\0\0ào™¿\0\0\0€ö?\0\0\0 R×Ê¿\0\0\0àæw¿\0\0\0\0ªÁ‘?\0\0\0\0¢P¦¿\0\0\0àñ¿¿\0\0\0€’¼?\0\0\0@³À¿\0\0\0\06†»¿\0\0\0 ›LÅ?\0\0\0`i›Ë¿\0\0\0`vÉ?\0\0\0\0®áË?\0\0\0`|µ?\0\0\0 õKÄ?\0\0\0\0ºâ¶?\0\0\0 go³?\0\0\0àz±„¿\0\0\0 Ã¯·¿\0\0\0 ¦ò¶¿\0\0\0@niš¿\0\0\0ÀÍÀÆ?\0\0\0€z²¿\0\0\0\0Zß¦?\0\0\0à,¯?”t”b.');
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-09-23  9:38:02
