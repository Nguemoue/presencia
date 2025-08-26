-- xampp-lite https://sourceforge.net/projects/xampplite/
--
-- mysqldump-php https://github.com/ifsnop/mysqldump-php
--
-- Host: localhost	Database: gestion_presence_universite
-- ------------------------------------------------------
-- Server version 	5.5.5-10.4.8-MariaDB
-- Date: Mon, 25 Aug 2025 18:55:42 +0200

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom_classe` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`id`, `nom_classe`, `description`) VALUES (1,'GL2','geni logiciel 1');
INSERT INTO `classes` (`id`, `nom_classe`, `description`) VALUES (2,'SR1','systemes et reseaux 1');
INSERT INTO `classes` (`id`, `nom_classe`, `description`) VALUES (3,'DISCIPLINE','RETARDS,PRESENCES ET ABSENCES');
INSERT INTO `classes` (`id`, `nom_classe`, `description`) VALUES (4,'sr4','systeme et reseaux');
INSERT INTO `classes` (`id`, `nom_classe`, `description`) VALUES (5,'sr3','systemes et reseaux');
INSERT INTO `classes` (`id`, `nom_classe`, `description`) VALUES (6,'gl4','genie logiciel');
INSERT INTO `classes` (`id`, `nom_classe`, `description`) VALUES (7,'finance','argent');

-- Dumped table `classes` with 7 row(s)
--

--
-- Table structure for table `historique_connexion`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `historique_connexion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `date_connexion` datetime NOT NULL,
  `ip_adresse` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `historique_connexion_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `historique_connexion`
--


-- Dumped table `historique_connexion` with 0 row(s)
--

--
-- Table structure for table `periode`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `periode` (
  `id_periode` int(11) NOT NULL AUTO_INCREMENT,
  `nom_periode` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `heure_debut` time DEFAULT NULL,
  `heure_fin` time DEFAULT NULL,
  PRIMARY KEY (`id_periode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `periode`
--


-- Dumped table `periode` with 0 row(s)
--

--
-- Table structure for table `presence`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `presence` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `date_heure` int(11) NOT NULL,
  `id_periode` int(11) NOT NULL,
  `statut` enum('present','absent') COLLATE utf8_unicode_ci NOT NULL,
  `image_reference` text COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_presences_user_id` (`user_id`),
  KEY `fk_id_periode` (`id_periode`),
  CONSTRAINT `fk_id_periode` FOREIGN KEY (`id_periode`) REFERENCES `periode` (`id_periode`),
  CONSTRAINT `presence_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `presence_ibfk_2` FOREIGN KEY (`id_periode`) REFERENCES `periode` (`id_periode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `presence`
--


-- Dumped table `presence` with 0 row(s)
--

--
-- Table structure for table `users`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `prenom` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `mot_de_passe` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type` enum('etudiant','personnel','admin') COLLATE utf8_unicode_ci NOT NULL,
  `photo_reference` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  face_encoding blob         not null,
  `classe_id` int(11) DEFAULT NULL,
  `matricule` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `classe_id` (`classe_id`),
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`classe_id`) REFERENCES `classes` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nom`, `prenom`, `email`, `mot_de_passe`, `type`, `photo_reference`, `classe_id`, `matricule`, `created_at`) VALUES (3,'Martin','Alice','alicemartin@gmail.com','$2y$10$V0Fv7T3ZkWq38x61YqU1euNeMVFAcuGhaXssqdcKuhKdofwDsmqr.','admin',NULL,3,'ADM2025001','2025-08-11 23:31:30');
INSERT INTO `users` (`id`, `nom`, `prenom`, `email`, `mot_de_passe`, `type`, `photo_reference`, `classe_id`, `matricule`, `created_at`) VALUES (4,'bonjawo','jaques','jac@gmail.com','$2y$10$sj4i.Pt4k0rGv9N3.53wHOcR6OdqQY.jfsazfmsuGr18LZpqbmFum','etudiant','photo_689f45458b34b.jpg',NULL,'ADM2025002','2025-08-12 17:51:41');
INSERT INTO `users` (`id`, `nom`, `prenom`, `email`, `mot_de_passe`, `type`, `photo_reference`, `classe_id`, `matricule`, `created_at`) VALUES (5,'hankou','raissa','hankou@gmail.com','$2y$10$sCYAxOaOXmrUVTc8rmYeOOyz0gxiK/vdryP3RWan/FKE2z1YqFZiG','admin','photo_689b76be3a5ce.jpg',1,'ADM2025004','2025-08-12 18:15:42');
INSERT INTO `users` (`id`, `nom`, `prenom`, `email`, `mot_de_passe`, `type`, `photo_reference`, `classe_id`, `matricule`, `created_at`) VALUES (6,'KAMEGA ','SIMON','kamega@gmail.com','$2y$10$4hqvDaxrCYLFlUSgWF.gyeqxGp.HFSxRYIIVNjyZWuJSx220.5G5q','personnel','photo_689b7f27c6db6.jpg',1,'MAT202502','2025-08-12 18:51:35');
INSERT INTO `users` (`id`, `nom`, `prenom`, `email`, `mot_de_passe`, `type`, `photo_reference`, `classe_id`, `matricule`, `created_at`) VALUES (7,'Mbakop','rachel','rachel@gmail.com','$2y$10$9ahAPLOP4rcIgFWI93KfxuilpLhmvhUzwF64Me9yeZ6NlXwQoEd9e','admin','photo_68ab2a436375a.jpg',7,'admin125','2025-08-24 16:05:39');
INSERT INTO `users` (`id`, `nom`, `prenom`, `email`, `mot_de_passe`, `type`, `photo_reference`, `classe_id`, `matricule`, `created_at`) VALUES (23,'bonne','nouvelle','bonne@gmail.com','$2y$10$NQA0kpPDCwLQuRi2iYeZWeBAQj1Fltvy9M0txiNoPY6i76FoeXk8u','admin',NULL,7,'bonneADM','2025-08-24 17:05:44');

-- Dumped table `users` with 6 row(s)
--

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on: Mon, 25 Aug 2025 18:55:42 +0200
