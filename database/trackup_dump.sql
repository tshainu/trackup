-- MySQL dump 10.13  Distrib 8.0.45, for Linux (x86_64)
--
-- Host: localhost    Database: trackup
-- ------------------------------------------------------
-- Server version	8.0.45-0ubuntu0.22.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `admins`
--

DROP TABLE IF EXISTS `admins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `admins` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `admins_user_name_unique` (`user_name`),
  UNIQUE KEY `admins_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admins`
--

LOCK TABLES `admins` WRITE;
/*!40000 ALTER TABLE `admins` DISABLE KEYS */;
INSERT INTO `admins` VALUES (1,'admin','admin@trackup.com','$2y$12$LFBtAW7xN69CuLvIGxJBee7ieigBI/7u/Gy5MKmcvXZZJfB6FS7s.',NULL,'active','2026-05-29 18:26:40','2026-05-29 18:26:40');
/*!40000 ALTER TABLE `admins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customers`
--

DROP TABLE IF EXISTS `customers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `shop_id` bigint unsigned DEFAULT NULL,
  `customer_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nic` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `gps_lat` decimal(10,7) DEFAULT NULL,
  `gps_lng` decimal(10,7) DEFAULT NULL,
  `gps_label` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gps_raw_link` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `customers_shop_customer_id_unique` (`shop_id`,`customer_id`),
  UNIQUE KEY `customers_shop_phone_unique` (`shop_id`,`phone`),
  KEY `customers_shop_id_index` (`shop_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customers`
--

LOCK TABLES `customers` WRITE;
/*!40000 ALTER TABLE `customers` DISABLE KEYS */;
INSERT INTO `customers` VALUES (1,2,'CUS-001','Shainu','0711336666',NULL,NULL,'Kopay',NULL,NULL,NULL,NULL,'2026-05-30 02:39:00','2026-05-30 02:39:00'),(2,2,'CUS-002','Selvan','07445221414',NULL,NULL,'Kopay',NULL,NULL,NULL,NULL,'2026-05-31 13:35:53','2026-05-31 13:35:53'),(4,NULL,'CUS-001','Filix','0724558854',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-06-01 04:17:52','2026-06-01 04:17:52');
/*!40000 ALTER TABLE `customers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `delivered_orders`
--

DROP TABLE IF EXISTS `delivered_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `delivered_orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `shop_id` bigint unsigned DEFAULT NULL,
  `order_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `invoice_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `invoice_date` date DEFAULT NULL,
  `customer_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_nic` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_dob` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `device_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `device_brand` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `serial_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `device_age` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `device_fault` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `issue` text COLLATE utf8mb4_unicode_ci,
  `date` date DEFAULT NULL,
  `rupees` decimal(12,2) NOT NULL DEFAULT '0.00',
  `discount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `paid_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `grand_total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Completed',
  `priority` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estimated_delivery` date DEFAULT NULL,
  `accessories` text COLLATE utf8mb4_unicode_ci,
  `remark` text COLLATE utf8mb4_unicode_ci,
  `need_assistant` tinyint(1) NOT NULL DEFAULT '0',
  `employee_id` bigint unsigned DEFAULT NULL,
  `payment_received` tinyint(1) NOT NULL DEFAULT '1',
  `invoice_items` json DEFAULT NULL,
  `delivered_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_delivered_shop` (`shop_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `delivered_orders`
--

LOCK TABLES `delivered_orders` WRITE;
/*!40000 ALTER TABLE `delivered_orders` DISABLE KEYS */;
/*!40000 ALTER TABLE `delivered_orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `device_accessories`
--

DROP TABLE IF EXISTS `device_accessories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `device_accessories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `device_list_id` bigint unsigned DEFAULT NULL,
  `accessory_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `device_accessories_device_list_id_foreign` (`device_list_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `device_accessories`
--

LOCK TABLES `device_accessories` WRITE;
/*!40000 ALTER TABLE `device_accessories` DISABLE KEYS */;
INSERT INTO `device_accessories` VALUES (1,NULL,'Case','2026-06-01 04:13:21','2026-06-01 04:13:21'),(2,NULL,'Remot control','2026-06-01 04:13:32','2026-06-01 04:13:32'),(3,NULL,'Wire','2026-06-01 04:13:39','2026-06-01 04:13:39');
/*!40000 ALTER TABLE `device_accessories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `device_brands`
--

DROP TABLE IF EXISTS `device_brands`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `device_brands` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `device_list_id` bigint unsigned NOT NULL,
  `device_brand` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `device_brands_device_list_id_foreign` (`device_list_id`),
  CONSTRAINT `device_brands_device_list_id_foreign` FOREIGN KEY (`device_list_id`) REFERENCES `device_lists` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `device_brands`
--

LOCK TABLES `device_brands` WRITE;
/*!40000 ALTER TABLE `device_brands` DISABLE KEYS */;
INSERT INTO `device_brands` VALUES (1,1,'Sony','2026-05-29 18:26:41','2026-05-29 18:26:41'),(2,1,'LG','2026-05-29 18:26:41','2026-05-29 18:26:41'),(3,1,'Samsung','2026-05-29 18:26:41','2026-05-29 18:26:41'),(4,1,'Toshiba','2026-05-29 18:26:41','2026-05-29 18:26:41'),(5,1,'Panasonic','2026-05-29 18:26:41','2026-05-29 18:26:41'),(6,2,'Orient','2026-05-29 18:26:41','2026-05-29 18:26:41'),(7,2,'Havells','2026-05-29 18:26:41','2026-05-29 18:26:41'),(8,2,'Voltas','2026-05-29 18:26:41','2026-05-29 18:26:41'),(9,2,'Crompton','2026-05-29 18:26:41','2026-05-29 18:26:41'),(10,3,'LG','2026-05-29 18:26:41','2026-05-29 18:26:41'),(11,3,'Samsung','2026-05-29 18:26:41','2026-05-29 18:26:41'),(12,3,'Toshiba','2026-05-29 18:26:41','2026-05-29 18:26:41'),(13,3,'Sony','2026-05-29 18:26:41','2026-05-29 18:26:41'),(14,3,'Carrier','2026-05-29 18:26:41','2026-05-29 18:26:41'),(15,3,'Haier','2026-05-29 18:26:41','2026-05-29 18:26:41'),(16,4,'LG','2026-05-29 18:26:41','2026-05-29 18:26:41'),(17,4,'Samsung','2026-05-29 18:26:41','2026-05-29 18:26:41'),(18,4,'Whirlpool','2026-05-29 18:26:41','2026-05-29 18:26:41'),(19,4,'IFB','2026-05-29 18:26:41','2026-05-29 18:26:41'),(20,5,'Acer','2026-05-29 18:26:41','2026-05-29 18:26:41'),(21,5,'Dell','2026-05-29 18:26:41','2026-05-29 18:26:41'),(22,5,'HP','2026-05-29 18:26:41','2026-05-29 18:26:41'),(23,5,'Lenovo','2026-05-29 18:26:41','2026-05-29 18:26:41'),(24,5,'Asus','2026-05-29 18:26:41','2026-05-29 18:26:41'),(25,6,'Preethi','2026-05-29 18:26:41','2026-05-29 18:26:41'),(26,6,'Panasonic','2026-05-29 18:26:41','2026-05-29 18:26:41'),(27,6,'Philips','2026-05-29 18:26:41','2026-05-29 18:26:41'),(28,7,'LG','2026-05-29 18:26:41','2026-05-29 18:26:41'),(29,7,'Samsung','2026-05-29 18:26:41','2026-05-29 18:26:41'),(30,7,'Whirlpool','2026-05-29 18:26:41','2026-05-29 18:26:41');
/*!40000 ALTER TABLE `device_brands` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `device_faults`
--

DROP TABLE IF EXISTS `device_faults`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `device_faults` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `device_list_id` bigint unsigned NOT NULL,
  `device_fault` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `device_faults_device_list_id_foreign` (`device_list_id`),
  CONSTRAINT `device_faults_device_list_id_foreign` FOREIGN KEY (`device_list_id`) REFERENCES `device_lists` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `device_faults`
--

LOCK TABLES `device_faults` WRITE;
/*!40000 ALTER TABLE `device_faults` DISABLE KEYS */;
INSERT INTO `device_faults` VALUES (1,1,'No power','2026-05-29 18:26:41','2026-05-29 18:26:41'),(2,1,'Video not matching audio','2026-05-29 18:26:41','2026-05-29 18:26:41'),(3,1,'Screen flickering','2026-05-29 18:26:41','2026-05-29 18:26:41'),(4,1,'Remote not working','2026-05-29 18:26:41','2026-05-29 18:26:41'),(5,1,'No picture','2026-05-29 18:26:41','2026-05-29 18:26:41'),(6,2,'Reduced speed','2026-05-29 18:26:41','2026-05-29 18:26:41'),(7,2,'Not rotating','2026-05-29 18:26:41','2026-05-29 18:26:41'),(8,2,'Noisy operation','2026-05-29 18:26:41','2026-05-29 18:26:41'),(9,2,'Wobbly fan','2026-05-29 18:26:41','2026-05-29 18:26:41'),(10,2,'No power','2026-05-29 18:26:41','2026-05-29 18:26:41'),(11,3,'No cooling','2026-05-29 18:26:41','2026-05-29 18:26:41'),(12,3,'Refrigerant leak','2026-05-29 18:26:41','2026-05-29 18:26:41'),(13,3,'Dirty air filter','2026-05-29 18:26:41','2026-05-29 18:26:41'),(14,3,'AC making noises','2026-05-29 18:26:41','2026-05-29 18:26:41'),(15,3,'Remote not working','2026-05-29 18:26:41','2026-05-29 18:26:41'),(16,4,'Not spinning','2026-05-29 18:26:41','2026-05-29 18:26:41'),(17,4,'Water leaking','2026-05-29 18:26:41','2026-05-29 18:26:41'),(18,4,'Not draining','2026-05-29 18:26:41','2026-05-29 18:26:41'),(19,4,'Vibrating excessively','2026-05-29 18:26:41','2026-05-29 18:26:41'),(20,5,'Not booting','2026-05-29 18:26:41','2026-05-29 18:26:41'),(21,5,'Screen broken','2026-05-29 18:26:41','2026-05-29 18:26:41'),(22,5,'Battery not charging','2026-05-29 18:26:41','2026-05-29 18:26:41'),(23,5,'Keyboard not working','2026-05-29 18:26:41','2026-05-29 18:26:41'),(24,5,'Overheating','2026-05-29 18:26:41','2026-05-29 18:26:41'),(25,6,'Thermal fuse issue','2026-05-29 18:26:41','2026-05-29 18:26:41'),(26,6,'Not heating','2026-05-29 18:26:41','2026-05-29 18:26:41'),(27,6,'Not switching to warm','2026-05-29 18:26:41','2026-05-29 18:26:41'),(28,7,'Not cooling','2026-05-29 18:26:41','2026-05-29 18:26:41'),(29,7,'Making noise','2026-05-29 18:26:41','2026-05-29 18:26:41'),(30,7,'Water leaking','2026-05-29 18:26:41','2026-05-29 18:26:41'),(31,7,'Ice maker broken','2026-05-29 18:26:41','2026-05-29 18:26:41');
/*!40000 ALTER TABLE `device_faults` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `device_lists`
--

DROP TABLE IF EXISTS `device_lists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `device_lists` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `device_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `device_lists`
--

LOCK TABLES `device_lists` WRITE;
/*!40000 ALTER TABLE `device_lists` DISABLE KEYS */;
INSERT INTO `device_lists` VALUES (1,'Television','2026-05-29 18:26:41','2026-05-29 18:26:41'),(2,'Fan','2026-05-29 18:26:41','2026-05-29 18:26:41'),(3,'AC','2026-05-29 18:26:41','2026-05-29 18:26:41'),(4,'Washing Machine','2026-05-29 18:26:41','2026-05-29 18:26:41'),(5,'Laptop','2026-05-29 18:26:41','2026-05-29 18:26:41'),(6,'Rice Cooker','2026-05-29 18:26:41','2026-05-29 18:26:41'),(7,'Refrigerator','2026-05-29 18:26:41','2026-05-29 18:26:41');
/*!40000 ALTER TABLE `device_lists` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employees`
--

DROP TABLE IF EXISTS `employees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `employees` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `shop_id` bigint unsigned DEFAULT NULL,
  `user_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `employee_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `registration_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `employee_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nic` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_no_1` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_no_2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'employee',
  `type` enum('inbound','outbound') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'inbound',
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `api_token` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `employees_user_id_unique` (`user_id`),
  UNIQUE KEY `employees_user_name_unique` (`user_name`),
  UNIQUE KEY `employees_api_token_unique` (`api_token`),
  KEY `employees_shop_id_index` (`shop_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employees`
--

LOCK TABLES `employees` WRITE;
/*!40000 ALTER TABLE `employees` DISABLE KEYS */;
INSERT INTO `employees` VALUES (8,3,'EMP001','Shainu','REG001',NULL,NULL,'0713335555',NULL,NULL,'shainu','technician','inbound','$2y$12$afBkNOpYYI7zdcphYf1YB.0EZMjaPFi8yKv.a0.TLnGZEB4uEexBi','5Jr2wMnUsaXKi5NBLHMC5zXcA0LnV6IegnSrCmOEwNuaAIbRMfPF8DE8l6UG',NULL,'active',NULL,'2026-06-01 03:56:02','2026-06-01 04:07:16');
/*!40000 ALTER TABLE `employees` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `field_complaint_items`
--

DROP TABLE IF EXISTS `field_complaint_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `field_complaint_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `field_complaint_id` bigint unsigned NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `qty` int NOT NULL DEFAULT '1',
  `unit_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total` decimal(10,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `field_complaint_items_field_complaint_id_foreign` (`field_complaint_id`),
  CONSTRAINT `field_complaint_items_field_complaint_id_foreign` FOREIGN KEY (`field_complaint_id`) REFERENCES `field_complaints` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `field_complaint_items`
--

LOCK TABLES `field_complaint_items` WRITE;
/*!40000 ALTER TABLE `field_complaint_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `field_complaint_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `field_complaints`
--

DROP TABLE IF EXISTS `field_complaints`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `field_complaints` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `shop_id` bigint unsigned DEFAULT NULL,
  `complaint_no` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_db_id` bigint unsigned DEFAULT NULL,
  `customer_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone_no` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `location_notes` text COLLATE utf8mb4_unicode_ci,
  `gps_lat` decimal(10,7) DEFAULT NULL,
  `gps_lng` decimal(10,7) DEFAULT NULL,
  `gps_label` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `service_type_id` bigint unsigned DEFAULT NULL,
  `service_type_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `priority` enum('Low','Normal','High','Urgent') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Normal',
  `status` enum('Pending','Assigned','In Progress','Completed','Billed','Cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pending',
  `assigned_to` bigint unsigned DEFAULT NULL,
  `assigned_at` timestamp NULL DEFAULT NULL,
  `scheduled_date` date DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `completion_notes` text COLLATE utf8mb4_unicode_ci,
  `photos` json DEFAULT NULL,
  `service_charge` decimal(10,2) NOT NULL DEFAULT '0.00',
  `discount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `paid_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `advance_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `payment_status` enum('unpaid','partial','paid') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unpaid',
  `payment_received` tinyint(1) NOT NULL DEFAULT '0',
  `invoice_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `invoice_date` date DEFAULT NULL,
  `remark` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `field_complaints_complaint_no_unique` (`complaint_no`),
  KEY `field_complaints_service_type_id_foreign` (`service_type_id`),
  KEY `field_complaints_assigned_to_foreign` (`assigned_to`),
  KEY `field_complaints_customer_db_id_foreign` (`customer_db_id`),
  KEY `field_complaints_shop_id_index` (`shop_id`),
  CONSTRAINT `field_complaints_assigned_to_foreign` FOREIGN KEY (`assigned_to`) REFERENCES `employees` (`id`) ON DELETE SET NULL,
  CONSTRAINT `field_complaints_customer_db_id_foreign` FOREIGN KEY (`customer_db_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `field_complaints_service_type_id_foreign` FOREIGN KEY (`service_type_id`) REFERENCES `service_types` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `field_complaints`
--

LOCK TABLES `field_complaints` WRITE;
/*!40000 ALTER TABLE `field_complaints` DISABLE KEYS */;
INSERT INTO `field_complaints` VALUES (1,2,'FC-2605001',1,'Shainu','0711336666','Kopay',NULL,NULL,NULL,NULL,1,'AC Service',NULL,'Normal','Pending',NULL,NULL,'2026-05-30',NULL,NULL,NULL,2500.00,0.00,0.00,0.00,'unpaid',0,NULL,NULL,NULL,NULL,'2026-05-30 02:39:00','2026-05-30 02:39:00'),(2,2,'FC-2605002',2,'Selvan','07445221414','Kopay',NULL,50.0465780,21.1003964,'Customer Location',1,'AC Service',NULL,'Normal','Completed',NULL,'2026-05-31 13:35:53','2026-06-01','2026-05-31 14:58:27',NULL,NULL,2500.00,0.00,0.00,0.00,'unpaid',0,'FI-2026-0001','2026-05-31',NULL,NULL,'2026-05-31 13:35:53','2026-05-31 14:58:27'),(3,3,'FC-2606001',2,'Selvan','07445221414','Kopay',NULL,NULL,NULL,NULL,3,'Service',NULL,'Normal','Assigned',8,'2026-06-01 04:18:26',NULL,NULL,NULL,NULL,2000.00,0.00,0.00,0.00,'unpaid',0,NULL,NULL,NULL,NULL,'2026-06-01 04:18:26','2026-06-01 04:18:26');
/*!40000 ALTER TABLE `field_complaints` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `field_payment_logs`
--

DROP TABLE IF EXISTS `field_payment_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `field_payment_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `field_complaint_id` bigint unsigned NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `note` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `paid_at` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `field_payment_logs_field_complaint_id_foreign` (`field_complaint_id`),
  CONSTRAINT `field_payment_logs_field_complaint_id_foreign` FOREIGN KEY (`field_complaint_id`) REFERENCES `field_complaints` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `field_payment_logs`
--

LOCK TABLES `field_payment_logs` WRITE;
/*!40000 ALTER TABLE `field_payment_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `field_payment_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `invoice_items`
--

DROP TABLE IF EXISTS `invoice_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `invoice_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `job_card_id` bigint unsigned NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `unit_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `qty` int NOT NULL DEFAULT '1',
  `total` decimal(10,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `invoice_items_job_card_id_foreign` (`job_card_id`),
  CONSTRAINT `invoice_items_job_card_id_foreign` FOREIGN KEY (`job_card_id`) REFERENCES `job_cards` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoice_items`
--

LOCK TABLES `invoice_items` WRITE;
/*!40000 ALTER TABLE `invoice_items` DISABLE KEYS */;
INSERT INTO `invoice_items` VALUES (1,11,'Change gascut',1200.00,1,1200.00,'2026-05-31 15:01:32','2026-05-31 15:01:32');
/*!40000 ALTER TABLE `invoice_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_cards`
--

DROP TABLE IF EXISTS `job_cards`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_cards` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `shop_id` bigint unsigned DEFAULT NULL,
  `order_no` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `invoice_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `invoice_date` date DEFAULT NULL,
  `customer_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_nic` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_dob` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_no` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `device_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `device_brand` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `serial_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `device_age` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `device_fault` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `item_description` text COLLATE utf8mb4_unicode_ci,
  `issue` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date` date NOT NULL,
  `rupees` decimal(10,2) NOT NULL DEFAULT '0.00',
  `advance_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `discount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `paid_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `payment_status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_received` tinyint(1) NOT NULL DEFAULT '0',
  `status` enum('Pending','In Progress','Completed','Not Completed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pending',
  `priority` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Normal',
  `estimated_delivery` date DEFAULT NULL,
  `accessories` text COLLATE utf8mb4_unicode_ci,
  `remark` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cancelled_reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `need_assistant` tinyint(1) NOT NULL DEFAULT '0',
  `employee_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `job_cards_order_no_unique` (`order_no`),
  KEY `job_cards_employee_id_foreign` (`employee_id`),
  KEY `job_cards_shop_id_index` (`shop_id`),
  CONSTRAINT `job_cards_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_cards`
--

LOCK TABLES `job_cards` WRITE;
/*!40000 ALTER TABLE `job_cards` DISABLE KEYS */;
INSERT INTO `job_cards` VALUES (1,1,'ORD-2024-001',NULL,NULL,'CUS-001','Arjun Krishnan','Jaffna, Sri Lanka','customer@example.com','901234567V','1990-01-01','0771234501','Television','Sony','SN001ABC','5','No power',NULL,'TV not turning on','2024-01-10',1500.00,0.00,0.00,0.00,NULL,0,'Completed','Normal',NULL,NULL,'Replaced fuse',NULL,NULL,0,NULL,'2026-05-29 18:26:41','2026-05-29 18:26:41'),(2,1,'ORD-2024-002',NULL,NULL,'CUS-002','Meena Lakshmi','Jaffna, Sri Lanka','customer@example.com','901234567V','1990-01-01','0772234502','AC','LG','SN002DEF','3','No cooling',NULL,'Room not cooling','2024-01-15',3500.00,0.00,0.00,0.00,NULL,0,'In Progress','Normal',NULL,NULL,'Gas refill needed',NULL,NULL,0,NULL,'2026-05-29 18:26:41','2026-05-29 18:26:41'),(3,1,'ORD-2024-003',NULL,NULL,'CUS-003','Suthan Pillai','Jaffna, Sri Lanka','customer@example.com','901234567V','1990-01-01','0773334503','Fan','Orient','SN003GHI','7','Reduced speed',NULL,'Fan running slow','2024-02-01',800.00,0.00,0.00,0.00,NULL,0,'Completed','Normal',NULL,NULL,'Capacitor replaced',NULL,NULL,0,NULL,'2026-05-29 18:26:41','2026-05-29 18:26:41'),(4,1,'ORD-2024-004',NULL,NULL,'CUS-004','Viji Rajan','Jaffna, Sri Lanka','customer@example.com','901234567V','1990-01-01','0774434504','Washing Machine','Samsung','SN004JKL','4','Not spinning',NULL,'Clothes not drying','2024-02-10',2000.00,0.00,0.00,0.00,NULL,0,'Pending','Normal',NULL,NULL,'',NULL,NULL,1,NULL,'2026-05-29 18:26:41','2026-05-29 18:26:41'),(5,1,'ORD-2024-005',NULL,NULL,'CUS-005','Karthik Sundaram','Jaffna, Sri Lanka','customer@example.com','901234567V','1990-01-01','0775534505','Laptop','Acer','SN005MNO','2','Not booting',NULL,'Laptop freezes on startup','2024-03-05',5000.00,0.00,0.00,0.00,NULL,0,'In Progress','Normal',NULL,NULL,'Checking motherboard',NULL,NULL,0,NULL,'2026-05-29 18:26:41','2026-05-29 18:26:41'),(6,1,'ORD-2024-006',NULL,NULL,'CUS-006','Radha Balakrishnan','Jaffna, Sri Lanka','customer@example.com','901234567V','1990-01-01','0776634506','Rice Cooker','Preethi','SN006PQR','9','Thermal fuse issue',NULL,'Not heating at all','2024-03-20',600.00,0.00,0.00,0.00,NULL,0,'Completed','Normal',NULL,NULL,'Thermal fuse replaced',NULL,NULL,0,NULL,'2026-05-29 18:26:41','2026-05-29 18:26:41'),(7,1,'ORD-2024-007',NULL,NULL,'CUS-007','Nirmala Tharmaraj','Jaffna, Sri Lanka','customer@example.com','901234567V','1990-01-01','0777734507','Television','Samsung','SN007STU','6','Screen flickering',NULL,'Screen keeps flickering','2024-04-02',2500.00,0.00,0.00,0.00,NULL,0,'Not Completed','Normal',NULL,NULL,'Parts not available',NULL,NULL,0,NULL,'2026-05-29 18:26:41','2026-05-29 18:26:41'),(8,1,'ORD-2024-008',NULL,NULL,'CUS-008','Ganesh Kumar','Jaffna, Sri Lanka','customer@example.com','901234567V','1990-01-01','0778834508','Refrigerator','LG','SN008VWX','8','Not cooling',NULL,'Fridge not cold enough','2024-04-15',4000.00,0.00,0.00,0.00,NULL,0,'Pending','Normal',NULL,NULL,'',NULL,NULL,1,NULL,'2026-05-29 18:26:41','2026-05-29 18:26:41'),(9,1,'ORD-2024-009',NULL,NULL,'CUS-001','Arjun Krishnan','Jaffna, Sri Lanka','customer@example.com','901234567V','1990-01-01','0771234501','AC','Toshiba','SN009YZA','4','AC making noises',NULL,'Loud noise from unit','2024-05-01',1800.00,0.00,0.00,0.00,NULL,0,'Completed','Normal',NULL,NULL,'Fan blade cleaned',NULL,NULL,0,NULL,'2026-05-29 18:26:41','2026-05-29 18:26:41'),(10,1,'ORD-2024-010',NULL,NULL,'CUS-009','Thilaga Devi','Jaffna, Sri Lanka','customer@example.com','901234567V','1990-01-01','0779934509','Fan','Havells','SN010BCD','10','Noisy operation',NULL,'Grinding sound','2024-05-10',900.00,0.00,0.00,0.00,NULL,0,'In Progress','Normal',NULL,NULL,'Bearing replacement ordered',NULL,NULL,0,NULL,'2026-05-29 18:26:41','2026-05-29 18:26:41'),(11,2,'2605001','INV-2026-0002','2026-05-31','CUS-001','Shainu','Kopay',NULL,NULL,NULL,'0711336666','Television','Sony','8524222','2','No power','Button damaged',NULL,'2026-05-30',4500.00,0.00,0.00,5700.00,'paid',1,'Completed','Normal',NULL,'Remort',NULL,NULL,NULL,0,NULL,'2026-05-30 02:41:20','2026-05-31 15:01:45'),(12,2,'2605002','INV-2026-0001','2026-05-31','CUS-002','Selvan','Kopay',NULL,NULL,NULL,'07445221414','Refrigerator','LG',NULL,'3','Water leaking',NULL,NULL,'2026-05-31',7500.00,0.00,0.00,0.00,NULL,0,'Completed','Normal','2026-06-03',NULL,NULL,NULL,NULL,0,NULL,'2026-05-31 13:23:14','2026-05-31 14:59:01'),(13,3,'2606001',NULL,NULL,'CUS-001','Ravi','Manipay',NULL,NULL,NULL,'0781122354','Television','Panasonic',NULL,'5','Screen flickering',NULL,NULL,'2026-06-01',5000.00,1000.00,0.00,1000.00,'partial',0,'Pending','Normal','2026-06-03','Remote',NULL,NULL,NULL,0,8,'2026-06-01 04:10:03','2026-06-01 04:10:03'),(14,3,'2606002',NULL,NULL,'CUS-002','Filix',NULL,NULL,NULL,NULL,'0724558854','Fan','Havells',NULL,'2','Noisy operation',NULL,NULL,'2026-06-01',5000.00,0.00,0.00,0.00,NULL,0,'Pending','Normal','2026-06-11','Wire',NULL,NULL,NULL,0,8,'2026-06-01 04:14:28','2026-06-01 04:14:28');
/*!40000 ALTER TABLE `job_cards` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `label_settings`
--

DROP TABLE IF EXISTS `label_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `label_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `shop_id` bigint unsigned NOT NULL,
  `width_mm` double NOT NULL DEFAULT '62',
  `height_mm` double NOT NULL DEFAULT '29',
  `font_size` int NOT NULL DEFAULT '10',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `label_settings_shop_id_unique` (`shop_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `label_settings`
--

LOCK TABLES `label_settings` WRITE;
/*!40000 ALTER TABLE `label_settings` DISABLE KEYS */;
INSERT INTO `label_settings` VALUES (1,2,38,25,7,'2026-05-30 03:46:11','2026-05-30 03:46:49'),(2,3,62,29,10,'2026-06-01 04:10:04','2026-06-01 04:10:04');
/*!40000 ALTER TABLE `label_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2024_01_01_000001_create_trackup_tables',1),(5,'2026_05_23_000001_add_priority_fields_to_job_cards',1),(6,'2026_05_23_093812_add_payment_received_to_job_cards',1),(7,'2026_05_23_120726_add_photo_to_employees_table',1),(8,'2026_05_23_141828_create_invoices_migration',1),(9,'2026_05_23_162242_create_delivered_orders_table',1),(10,'2026_05_23_162819_add_payment_status_and_broken_to_job_cards',1),(11,'2026_05_23_165914_add_advance_amount_to_job_cards',1),(12,'2026_05_24_024758_create_payment_logs_table',1),(13,'2026_05_24_062241_add_cancellation_fields_to_job_cards',1),(14,'2026_05_24_062648_create_device_accessories_table',1),(15,'2026_05_24_062649_add_item_description_to_job_cards',1),(16,'2026_05_24_064156_make_device_accessories_global',1),(17,'2026_05_24_105548_create_service_types_table',1),(18,'2026_05_24_105549_create_field_complaints_table',1),(19,'2026_05_24_105550_add_type_to_employees_table',1),(20,'2026_05_24_105551_create_field_complaint_items_table',1),(21,'2026_05_24_105644_create_field_payment_logs_table',1),(22,'2026_05_24_112114_create_customers_table',1),(23,'2026_05_24_112115_add_customer_id_to_field_complaints_table',1),(24,'2026_05_24_112838_add_description_to_service_types_table',1),(25,'2026_05_24_143244_add_api_token_to_employees_table',1),(26,'2026_05_24_200000_create_sms_tables',1),(27,'2026_05_24_210000_add_cancelled_to_job_cards_status',1),(28,'2026_05_29_000001_create_super_admin_tables',2),(29,'2026_05_30_000001_add_shop_id_to_all_tables',3),(30,'2026_05_30_000001_create_label_settings_table',4),(31,'2026_05_30_100000_add_modules_to_shops_table',5),(32,'2026_05_31_000001_add_admin_api_token_to_shops',6),(33,'2026_05_31_100000_create_whatsapp_tables',7),(34,'2026_06_01_000001_fix_customers_unique_per_shop',8);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment_logs`
--

DROP TABLE IF EXISTS `payment_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `job_card_id` bigint unsigned NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `note` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `paid_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payment_logs_job_card_id_foreign` (`job_card_id`),
  CONSTRAINT `payment_logs_job_card_id_foreign` FOREIGN KEY (`job_card_id`) REFERENCES `job_cards` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_logs`
--

LOCK TABLES `payment_logs` WRITE;
/*!40000 ALTER TABLE `payment_logs` DISABLE KEYS */;
INSERT INTO `payment_logs` VALUES (1,11,5700.00,'Payment','2026-05-31 15:01:45','2026-05-31 15:01:45','2026-05-31 15:01:45');
/*!40000 ALTER TABLE `payment_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `service_types`
--

DROP TABLE IF EXISTS `service_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `service_types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `shop_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `icon` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'bx-wrench',
  `base_charge` decimal(10,2) NOT NULL DEFAULT '0.00',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `service_types_shop_id_index` (`shop_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `service_types`
--

LOCK TABLES `service_types` WRITE;
/*!40000 ALTER TABLE `service_types` DISABLE KEYS */;
INSERT INTO `service_types` VALUES (1,2,'AC Service',NULL,'bx-wrench',2500.00,1,'2026-05-30 02:37:03','2026-05-30 02:37:03'),(2,3,'AC Cleaning',NULL,'bx-wrench',2500.00,1,'2026-06-01 04:15:00','2026-06-01 04:15:00'),(3,3,'Service',NULL,'bx-wrench',2000.00,1,'2026-06-01 04:15:20','2026-06-01 04:15:20');
/*!40000 ALTER TABLE `service_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('DugrFmQtr9Ctd7Dzwm9da2GIt8C330ZaRXbmeh06',NULL,'18.216.166.10','visionheight.com/scan Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) Chrome/126.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoibk5za290cGxGRGdFcnJpRm1PTkxDZTFYSDRMTWttMXRaN0p2eDdTYiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjU6Imh0dHA6Ly82OS4xNjkuOTcuMTk1OjgwODAiO3M6NToicm91dGUiO047fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1780284509),('ezQNuNLP6eKOjZMLUIq7l3O9xoIgFHClJPCJrFkX',NULL,'112.135.30.33','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','YToxNDp7czo2OiJfdG9rZW4iO3M6NDA6InZqYmUxWmFyMmxmZkY2MGhNZU94VGtqcUNSREN0UVZWSnVnNzdSTkUiO3M6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjQ4OiJodHRwOi8vNjkuMTY5Ljk3LjE5NTo4MDgwL2FkbWluL2ZpZWxkLWNvbXBsYWludHMiO3M6NToicm91dGUiO3M6Mjg6ImFkbWluLmZpZWxkLWNvbXBsYWludHMuaW5kZXgiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjIxOiJzdXBlcl9hZG1pbl9sb2dnZWRfaW4iO2I6MTtzOjE0OiJzdXBlcl9hZG1pbl9pZCI7aToxO3M6MTY6InN1cGVyX2FkbWluX25hbWUiO3M6MTE6IlN1cGVyIEFkbWluIjtzOjE3OiJzdXBlcl9hZG1pbl9lbWFpbCI7czoyMjoic3VwZXJhZG1pbkB0cmFja3VwLmNvbSI7czoxNToiYWRtaW5fbG9nZ2VkX2luIjtiOjE7czo4OiJhZG1pbl9pZCI7aToxO3M6MTA6ImFkbWluX25hbWUiO3M6NToiYWRtaW4iO3M6Nzoic2hvcF9pZCI7aTozO3M6OToic2hvcF9jb2RlIjtzOjQ6Ik84NjAiO3M6OToic2hvcF9uYW1lIjtzOjExOiJTdW4gU2VydmljZSI7czoxMjoic2hvcF9tb2R1bGVzIjthOjI6e2k6MDtzOjEwOiJqb2Jfb3JkZXJzIjtpOjE7czoxNDoiZmllbGRfc2VydmljZXMiO319',1780288539),('FUlZTU127tCrxANHqJ4Nd23FxhD8jH6C6orLagHL',NULL,'74.51.221.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoia3kzSGY5ZGs3MDRwY1lpanA2SXRMZVVTREN4T1Jxd2ZtSzNaRDFTRiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly82OS4xNjkuOTcuMTk1OjgwODAvYWRtaW4vbG9naW4iO3M6NToicm91dGUiO3M6MTE6ImFkbWluLmxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1780287224),('ipQXGLWquw3ZSYzj9jX4iUhtVRSFKlBfq8AhRByu',NULL,'18.216.166.10','visionheight.com/scan Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) Chrome/126.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiQTBXVTdxeURvMmtXODRKa1RlbldNUUp1MGladm5UaGhYcW5Nb05FRiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly82OS4xNjkuOTcuMTk1OjgwODAvYWRtaW4vbG9naW4iO3M6NToicm91dGUiO3M6MTE6ImFkbWluLmxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1780284434),('OXiRrJbO589yVsLH3pILZTw0aeOWQIjjYyzoKEt3',NULL,'20.163.2.53','Mozilla/5.0 zgrab/0.x','YTozOntzOjY6Il90b2tlbiI7czo0MDoiT1RadUhtdjRSM3hSRThTY1Q3cWlqOFlxZjJYcERZbkh5TjVBWXMxeSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly82OS4xNjkuOTcuMTk1OjgwODAvYWRtaW4vbG9naW4iO3M6NToicm91dGUiO3M6MTE6ImFkbWluLmxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1780280582),('SNOWu6lCHoBV6c7psekaVU4cyXxSe0UHvcwcUegj',NULL,'18.216.166.10','visionheight.com/scan Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) Chrome/126.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiVFR5N3RSYUFWNVRzQVBKbFJ6SURFemlYeFVNa1J3VVdZaWIxTU9nSyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjU6Imh0dHA6Ly82OS4xNjkuOTcuMTk1OjgwODAiO3M6NToicm91dGUiO047fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1780284434),('uLJufupz0siOhlqn978eANp3jND60wCQB9xBE4c9',NULL,'127.0.0.1','curl/7.81.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoieFgxbkZja1BRZDNlZnVMaERmZU1SUU5Xd0k0RXNxeUtWb3Zjd1RYRiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzM6Imh0dHA6Ly9sb2NhbGhvc3Q6ODA4MC9hZG1pbi9sb2dpbiI7czo1OiJyb3V0ZSI7czoxMToiYWRtaW4ubG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1780286231),('VQo4zJWG9NBXQJp5hb6oV1mxq0vdPwh0jyXwwMNr',NULL,'20.163.2.53','Mozilla/5.0 zgrab/0.x','YTozOntzOjY6Il90b2tlbiI7czo0MDoiTFR5NG4wTWZ0OUFEME5VTjFZYmh0dVpsOGIzNG1uR1ZFRHptYTJ4ViI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjU6Imh0dHA6Ly82OS4xNjkuOTcuMTk1OjgwODAiO3M6NToicm91dGUiO047fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1780280582),('yxONBPRuVwbNiQm71zM29nwazk8GA11iDtTMl4aL',NULL,'127.0.0.1','curl/7.81.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoic0VZWHVURHBTYlA2aUp4UjJ1VUtoS1lnZFhqZEZ6SHlqSVZNbjN6TyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDQ6Imh0dHA6Ly9sb2NhbGhvc3Q6ODA4MC9hZG1pbi9maWVsZC1jb21wbGFpbnRzIjtzOjU6InJvdXRlIjtzOjI4OiJhZG1pbi5maWVsZC1jb21wbGFpbnRzLmluZGV4Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1780288256);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shop_activity_logs`
--

DROP TABLE IF EXISTS `shop_activity_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `shop_activity_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `shop_id` bigint unsigned NOT NULL,
  `action` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `performed_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `shop_activity_logs_shop_id_foreign` (`shop_id`),
  KEY `shop_activity_logs_performed_by_foreign` (`performed_by`),
  CONSTRAINT `shop_activity_logs_performed_by_foreign` FOREIGN KEY (`performed_by`) REFERENCES `super_admins` (`id`) ON DELETE SET NULL,
  CONSTRAINT `shop_activity_logs_shop_id_foreign` FOREIGN KEY (`shop_id`) REFERENCES `shops` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_activity_logs`
--

LOCK TABLES `shop_activity_logs` WRITE;
/*!40000 ALTER TABLE `shop_activity_logs` DISABLE KEYS */;
INSERT INTO `shop_activity_logs` VALUES (1,1,'created','Shop created by super admin',1,'2026-05-30 02:09:08','2026-05-30 02:09:08'),(2,2,'created','Shop created by super admin',1,'2026-05-30 02:23:35','2026-05-30 02:23:35'),(3,3,'created','Shop created by super admin',1,'2026-06-01 03:49:55','2026-06-01 03:49:55');
/*!40000 ALTER TABLE `shop_activity_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shops`
--

DROP TABLE IF EXISTS `shops`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `shops` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `shop_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `shop_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Sri Lanka',
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `admin_username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `admin_password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `admin_plain_password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('active','suspended','pending') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `last_active_at` timestamp NULL DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `modules` json DEFAULT NULL,
  `admin_api_token` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `shops_shop_code_unique` (`shop_code`),
  UNIQUE KEY `shops_email_unique` (`email`),
  UNIQUE KEY `shops_admin_api_token_unique` (`admin_api_token`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shops`
--

LOCK TABLES `shops` WRITE;
/*!40000 ALTER TABLE `shops` DISABLE KEYS */;
INSERT INTO `shops` VALUES (1,'SOS Shop','G115','Shainu','axisxnor@gmail.com','0711234567','39 Hospital Rd','Mannar','Sri Lanka',NULL,'admin','$2y$12$5Lsg3zUk//4Bv0YvZp6YU.jpONQ/m4maT8cbUVxyjgdtHVl4TNmQ.','date048','active',NULL,NULL,'[\"job_orders\", \"field_services\"]','2DxBxu5GDUgiCjUDjJJQZAn5LNKztaJZGVI8veBUoKKVU0ZgHV5UCtdfZR18','2026-05-30 02:09:08','2026-05-31 13:11:26'),(2,'Bahu Reapire','T355','Rajan Selvakumar','axisxnor1@gmail.com',NULL,'39 Hospital Rd','Mannar','Sri Lanka',NULL,'admin','$2y$12$RJov8DWEci0SOqDiFcWme.BchUN444wBAlZk/hyWc7S5LhSs9XkIe','grap724','active','2026-06-01 03:41:07',NULL,'[\"job_orders\", \"field_services\"]','dtrZGLN9IdtT3CJYH8NCvpbLAnCwAkweScWonPcMXgZVrblAdOPN3ImsfuUd','2026-05-30 02:23:35','2026-06-01 04:02:07'),(3,'Sun Service','O860','Siva','siv@gmil.com','0713335555',NULL,'Jaffna','Sri Lanka',NULL,'admin','$2y$12$NRIr101yFl2oD190s04J3ORGjbKiDRXAT3RcWkQ0vx5hfp/jVj3AO','mang815','active','2026-06-01 03:50:18',NULL,'[\"job_orders\", \"field_services\"]',NULL,'2026-06-01 03:49:55','2026-06-01 03:50:18');
/*!40000 ALTER TABLE `shops` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sms_settings`
--

DROP TABLE IF EXISTS `sms_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sms_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `shop_id` bigint unsigned DEFAULT NULL,
  `api_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `api_key` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sender_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sms_settings_shop_id_index` (`shop_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sms_settings`
--

LOCK TABLES `sms_settings` WRITE;
/*!40000 ALTER TABLE `sms_settings` DISABLE KEYS */;
INSERT INTO `sms_settings` VALUES (1,1,'','','',0,'2026-05-29 18:14:18','2026-05-29 18:14:18'),(2,2,'','','',0,'2026-05-30 02:39:00','2026-05-30 02:39:00'),(3,3,'','','',0,'2026-06-01 04:10:03','2026-06-01 04:10:03');
/*!40000 ALTER TABLE `sms_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sms_templates`
--

DROP TABLE IF EXISTS `sms_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sms_templates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `label` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sms_templates_key_unique` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sms_templates`
--

LOCK TABLES `sms_templates` WRITE;
/*!40000 ALTER TABLE `sms_templates` DISABLE KEYS */;
INSERT INTO `sms_templates` VALUES (1,'job_created','New Job Order Created','Dear {customer_name}, your job order {order_no} has been received. We will contact you shortly. - {store_name}',1,'2026-05-29 18:14:18','2026-05-29 18:14:18'),(2,'job_status_changed','Job Order Status Updated','Dear {customer_name}, your order {order_no} status has been updated to: {status}. For queries call us. - {store_name}',1,'2026-05-29 18:14:18','2026-05-29 18:14:18'),(3,'field_complaint_created','Field Service Request Logged','Dear {customer_name}, your service request {complaint_no} has been logged. Our team will reach you soon. - {store_name}',1,'2026-05-29 18:14:18','2026-05-29 18:14:18'),(4,'field_service_completed','Field Service Completed','Dear {customer_name}, field service {complaint_no} has been completed by {technician}. Thank you! - {store_name}',1,'2026-05-29 18:14:18','2026-05-29 18:14:18');
/*!40000 ALTER TABLE `sms_templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `store_info`
--

DROP TABLE IF EXISTS `store_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `store_info` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `shop_id` bigint unsigned DEFAULT NULL,
  `store_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `registration_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `store_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_no1` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_no2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `owner_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `owner_phoneno` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `owner_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `store_info_shop_id_index` (`shop_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `store_info`
--

LOCK TABLES `store_info` WRITE;
/*!40000 ALTER TABLE `store_info` DISABLE KEYS */;
INSERT INTO `store_info` VALUES (1,1,'TrackUp Repair Center','BRN-2023-001','45 Main Street, Jaffna, Sri Lanka','021-1234567','077-1234567','Rajan Selvakumar','077-9876543','12 Temple Road, Jaffna',NULL,'2026-05-29 18:26:41','2026-05-29 18:26:41'),(2,2,'Bahu Reapire',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-06-01 03:45:01','2026-06-01 03:45:01');
/*!40000 ALTER TABLE `store_info` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `super_admins`
--

DROP TABLE IF EXISTS `super_admins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `super_admins` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `last_login_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `super_admins_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `super_admins`
--

LOCK TABLES `super_admins` WRITE;
/*!40000 ALTER TABLE `super_admins` DISABLE KEYS */;
INSERT INTO `super_admins` VALUES (1,'Super Admin','superadmin@trackup.com','$2y$12$O./0B3bmS5Hzw2owActcouaeJO6DJxbkPxEopvEEgvOOZUIbepYDi','active','2026-06-01 03:43:58','2026-05-30 01:38:57','2026-06-01 03:43:58');
/*!40000 ALTER TABLE `super_admins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `whatsapp_settings`
--

DROP TABLE IF EXISTS `whatsapp_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `whatsapp_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `api_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `api_key` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `instance_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_number_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `whatsapp_settings`
--

LOCK TABLES `whatsapp_settings` WRITE;
/*!40000 ALTER TABLE `whatsapp_settings` DISABLE KEYS */;
INSERT INTO `whatsapp_settings` VALUES (1,'','','','',0,'2026-05-31 15:17:54','2026-05-31 15:17:54');
/*!40000 ALTER TABLE `whatsapp_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `whatsapp_templates`
--

DROP TABLE IF EXISTS `whatsapp_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `whatsapp_templates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `label` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `whatsapp_templates_key_unique` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `whatsapp_templates`
--

LOCK TABLES `whatsapp_templates` WRITE;
/*!40000 ALTER TABLE `whatsapp_templates` DISABLE KEYS */;
INSERT INTO `whatsapp_templates` VALUES (1,'invoice_sent','Invoice Sent to Customer','Dear {customer_name}, your invoice #{invoice_no} for {device} is ready.\nTotal: Rs. {total} | Balance: Rs. {balance}\nThank you for choosing {store_name}.',1,'2026-05-31 15:17:54','2026-05-31 15:17:54'),(2,'job_alert','Job Order Alert','Hi {customer_name}, your device ({device}) status: *{status}*.\nOrder: {order_no} — {store_name}',1,'2026-05-31 15:17:54','2026-05-31 15:17:54'),(3,'field_alert','Field Service Alert','Hi {customer_name}, your field service request {complaint_no} has been updated: *{status}*.\nTech: {technician} — {store_name}',1,'2026-05-31 15:17:54','2026-05-31 15:17:54'),(4,'payment_reminder','Payment Reminder','Dear {customer_name}, a payment reminder for order {order_no}.\nBalance due: Rs. {balance}\nPlease contact us at {store_name}.',1,'2026-05-31 15:17:54','2026-05-31 15:17:54');
/*!40000 ALTER TABLE `whatsapp_templates` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-06-01  7:44:11
