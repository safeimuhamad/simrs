-- MariaDB dump 10.19  Distrib 10.4.28-MariaDB, for osx10.10 (x86_64)
--
-- Host: 127.0.0.1    Database: sim-rs
-- ------------------------------------------------------
-- Server version	10.4.28-MariaDB

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
-- Current Database: `sim-rs`
--

/*!40000 DROP DATABASE IF EXISTS `sim-rs`*/;

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `sim-rs` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */;

USE `sim-rs`;

--
-- Table structure for table `activity_logs`
--

DROP TABLE IF EXISTS `activity_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `activity_logs` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `module` varchar(100) DEFAULT NULL,
  `action` varchar(50) DEFAULT NULL,
  `reference_id` bigint(20) DEFAULT NULL,
  `reference_number` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(100) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7987 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ambulance_trips`
--

DROP TABLE IF EXISTS `ambulance_trips`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ambulance_trips` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `record_no` varchar(50) NOT NULL,
  `patient_id` bigint(20) unsigned DEFAULT NULL,
  `visit_id` bigint(20) unsigned DEFAULT NULL,
  `medical_record_id` bigint(20) unsigned DEFAULT NULL,
  `billing_id` bigint(20) unsigned DEFAULT NULL,
  `reference_no` varchar(100) DEFAULT NULL,
  `module_type` varchar(100) DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `record_date` datetime NOT NULL DEFAULT current_timestamp(),
  `status` varchar(50) NOT NULL DEFAULT 'draft',
  `priority` varchar(30) NOT NULL DEFAULT 'normal',
  `amount` decimal(18,2) DEFAULT 0.00,
  `location` varchar(150) DEFAULT NULL,
  `assigned_to` varchar(150) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `payload_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payload_json`)),
  `interoperability_status` varchar(50) NOT NULL DEFAULT 'not_ready',
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_record_no` (`record_no`),
  KEY `idx_patient` (`patient_id`),
  KEY `idx_visit` (`visit_id`),
  KEY `idx_billing` (`billing_id`),
  KEY `idx_status` (`status`),
  KEY `idx_date` (`record_date`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bank_accounts`
--

DROP TABLE IF EXISTS `bank_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bank_accounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account_code` varchar(20) DEFAULT NULL,
  `coa_id` int(11) DEFAULT NULL,
  `account_name` varchar(100) NOT NULL,
  `bank_name` varchar(100) DEFAULT NULL,
  `account_number` varchar(100) DEFAULT NULL,
  `account_holder` varchar(100) DEFAULT NULL,
  `account_type` enum('bank','cash') DEFAULT 'bank',
  `opening_balance` decimal(15,2) DEFAULT 0.00,
  `current_balance` decimal(15,2) DEFAULT 0.00,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bank_transactions`
--

DROP TABLE IF EXISTS `bank_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bank_transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bank_account_id` int(11) NOT NULL,
  `transaction_date` date NOT NULL,
  `transaction_type` enum('in','out') NOT NULL,
  `reference_type` varchar(50) DEFAULT NULL,
  `reference_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bank_transfers`
--

DROP TABLE IF EXISTS `bank_transfers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bank_transfers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `transfer_date` date NOT NULL,
  `from_bank_account_id` int(11) NOT NULL,
  `to_bank_account_id` int(11) NOT NULL,
  `amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `reference_no` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bed_management_details`
--

DROP TABLE IF EXISTS `bed_management_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bed_management_details` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `record_no` varchar(50) NOT NULL,
  `patient_id` bigint(20) unsigned DEFAULT NULL,
  `visit_id` bigint(20) unsigned DEFAULT NULL,
  `medical_record_id` bigint(20) unsigned DEFAULT NULL,
  `billing_id` bigint(20) unsigned DEFAULT NULL,
  `reference_no` varchar(100) DEFAULT NULL,
  `module_type` varchar(100) DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `record_date` datetime NOT NULL DEFAULT current_timestamp(),
  `status` varchar(50) NOT NULL DEFAULT 'draft',
  `priority` varchar(30) NOT NULL DEFAULT 'normal',
  `amount` decimal(18,2) DEFAULT 0.00,
  `location` varchar(150) DEFAULT NULL,
  `assigned_to` varchar(150) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `payload_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payload_json`)),
  `interoperability_status` varchar(50) NOT NULL DEFAULT 'not_ready',
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_record_no` (`record_no`),
  KEY `idx_patient` (`patient_id`),
  KEY `idx_visit` (`visit_id`),
  KEY `idx_billing` (`billing_id`),
  KEY `idx_status` (`status`),
  KEY `idx_date` (`record_date`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bpjs_bridge_logs`
--

DROP TABLE IF EXISTS `bpjs_bridge_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bpjs_bridge_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `record_no` varchar(50) NOT NULL,
  `patient_id` bigint(20) unsigned DEFAULT NULL,
  `visit_id` bigint(20) unsigned DEFAULT NULL,
  `medical_record_id` bigint(20) unsigned DEFAULT NULL,
  `billing_id` bigint(20) unsigned DEFAULT NULL,
  `reference_no` varchar(100) DEFAULT NULL,
  `module_type` varchar(100) DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `record_date` datetime NOT NULL DEFAULT current_timestamp(),
  `status` varchar(50) NOT NULL DEFAULT 'draft',
  `priority` varchar(30) NOT NULL DEFAULT 'normal',
  `amount` decimal(18,2) DEFAULT 0.00,
  `location` varchar(150) DEFAULT NULL,
  `assigned_to` varchar(150) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `payload_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payload_json`)),
  `interoperability_status` varchar(50) NOT NULL DEFAULT 'not_ready',
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_record_no` (`record_no`),
  KEY `idx_patient` (`patient_id`),
  KEY `idx_visit` (`visit_id`),
  KEY `idx_billing` (`billing_id`),
  KEY `idx_status` (`status`),
  KEY `idx_date` (`record_date`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `diagnoses`
--

DROP TABLE IF EXISTS `diagnoses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `diagnoses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `visit_id` int(11) NOT NULL,
  `medical_record_id` int(11) DEFAULT NULL,
  `diagnosis_code` varchar(50) DEFAULT NULL,
  `diagnosis_name` varchar(255) NOT NULL,
  `diagnosis_type` enum('primary','secondary') DEFAULT 'primary',
  `notes` text DEFAULT NULL,
  `fhir_condition_id` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_diag_visit` (`visit_id`),
  KEY `idx_diag_code` (`diagnosis_code`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `doctor_schedules`
--

DROP TABLE IF EXISTS `doctor_schedules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `doctor_schedules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `doctor_id` int(11) NOT NULL,
  `polyclinic_id` int(11) NOT NULL,
  `day_of_week` tinyint(4) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `quota` int(11) DEFAULT 0,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_schedule_doctor_day` (`doctor_id`,`day_of_week`),
  KEY `idx_schedule_poly_day` (`polyclinic_id`,`day_of_week`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `doctors`
--

DROP TABLE IF EXISTS `doctors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `doctors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `doctor_code` varchar(50) NOT NULL,
  `name` varchar(150) NOT NULL,
  `specialist` varchar(150) DEFAULT NULL,
  `sip_no` varchar(100) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `fhir_practitioner_id` varchar(100) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `doctor_code` (`doctor_code`),
  KEY `idx_doctors_user` (`user_id`),
  KEY `idx_doctors_fhir` (`fhir_practitioner_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `emergency_cases`
--

DROP TABLE IF EXISTS `emergency_cases`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `emergency_cases` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `record_no` varchar(50) NOT NULL,
  `patient_id` bigint(20) unsigned DEFAULT NULL,
  `visit_id` bigint(20) unsigned DEFAULT NULL,
  `medical_record_id` bigint(20) unsigned DEFAULT NULL,
  `billing_id` bigint(20) unsigned DEFAULT NULL,
  `reference_no` varchar(100) DEFAULT NULL,
  `module_type` varchar(100) DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `record_date` datetime NOT NULL DEFAULT current_timestamp(),
  `status` varchar(50) NOT NULL DEFAULT 'draft',
  `priority` varchar(30) NOT NULL DEFAULT 'normal',
  `amount` decimal(18,2) DEFAULT 0.00,
  `location` varchar(150) DEFAULT NULL,
  `assigned_to` varchar(150) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `payload_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payload_json`)),
  `interoperability_status` varchar(50) NOT NULL DEFAULT 'not_ready',
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_record_no` (`record_no`),
  KEY `idx_patient` (`patient_id`),
  KEY `idx_visit` (`visit_id`),
  KEY `idx_billing` (`billing_id`),
  KEY `idx_status` (`status`),
  KEY `idx_date` (`record_date`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `employee_shifts`
--

DROP TABLE IF EXISTS `employee_shifts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employee_shifts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `record_no` varchar(50) NOT NULL,
  `patient_id` bigint(20) unsigned DEFAULT NULL,
  `visit_id` bigint(20) unsigned DEFAULT NULL,
  `medical_record_id` bigint(20) unsigned DEFAULT NULL,
  `billing_id` bigint(20) unsigned DEFAULT NULL,
  `reference_no` varchar(100) DEFAULT NULL,
  `module_type` varchar(100) DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `record_date` datetime NOT NULL DEFAULT current_timestamp(),
  `status` varchar(50) NOT NULL DEFAULT 'draft',
  `priority` varchar(30) NOT NULL DEFAULT 'normal',
  `amount` decimal(18,2) DEFAULT 0.00,
  `location` varchar(150) DEFAULT NULL,
  `assigned_to` varchar(150) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `payload_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payload_json`)),
  `interoperability_status` varchar(50) NOT NULL DEFAULT 'not_ready',
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_record_no` (`record_no`),
  KEY `idx_patient` (`patient_id`),
  KEY `idx_visit` (`visit_id`),
  KEY `idx_billing` (`billing_id`),
  KEY `idx_status` (`status`),
  KEY `idx_date` (`record_date`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hospital_assets`
--

DROP TABLE IF EXISTS `hospital_assets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hospital_assets` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `record_no` varchar(50) NOT NULL,
  `patient_id` bigint(20) unsigned DEFAULT NULL,
  `visit_id` bigint(20) unsigned DEFAULT NULL,
  `medical_record_id` bigint(20) unsigned DEFAULT NULL,
  `billing_id` bigint(20) unsigned DEFAULT NULL,
  `reference_no` varchar(100) DEFAULT NULL,
  `module_type` varchar(100) DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `record_date` datetime NOT NULL DEFAULT current_timestamp(),
  `status` varchar(50) NOT NULL DEFAULT 'draft',
  `priority` varchar(30) NOT NULL DEFAULT 'normal',
  `amount` decimal(18,2) DEFAULT 0.00,
  `location` varchar(150) DEFAULT NULL,
  `assigned_to` varchar(150) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `payload_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payload_json`)),
  `interoperability_status` varchar(50) NOT NULL DEFAULT 'not_ready',
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_record_no` (`record_no`),
  KEY `idx_patient` (`patient_id`),
  KEY `idx_visit` (`visit_id`),
  KEY `idx_billing` (`billing_id`),
  KEY `idx_status` (`status`),
  KEY `idx_date` (`record_date`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `inpatient_admissions`
--

DROP TABLE IF EXISTS `inpatient_admissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inpatient_admissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admission_no` varchar(50) NOT NULL,
  `visit_id` int(11) DEFAULT NULL,
  `patient_id` int(11) NOT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `bed_id` int(11) NOT NULL,
  `admission_date` date NOT NULL,
  `discharge_date` date DEFAULT NULL,
  `status` enum('active','discharged','cancelled') NOT NULL DEFAULT 'active',
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `admission_no` (`admission_no`),
  KEY `idx_inpatient_patient` (`patient_id`),
  KEY `idx_inpatient_status` (`status`),
  KEY `idx_inpatient_date` (`admission_date`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `inpatient_beds`
--

DROP TABLE IF EXISTS `inpatient_beds`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inpatient_beds` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bed_no` varchar(30) NOT NULL,
  `room_name` varchar(100) NOT NULL,
  `class_name` varchar(50) NOT NULL,
  `status` enum('available','occupied','maintenance','inactive') NOT NULL DEFAULT 'available',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `bed_no` (`bed_no`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `insurance_claims`
--

DROP TABLE IF EXISTS `insurance_claims`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `insurance_claims` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `record_no` varchar(50) NOT NULL,
  `patient_id` bigint(20) unsigned DEFAULT NULL,
  `visit_id` bigint(20) unsigned DEFAULT NULL,
  `medical_record_id` bigint(20) unsigned DEFAULT NULL,
  `billing_id` bigint(20) unsigned DEFAULT NULL,
  `reference_no` varchar(100) DEFAULT NULL,
  `module_type` varchar(100) DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `record_date` datetime NOT NULL DEFAULT current_timestamp(),
  `status` varchar(50) NOT NULL DEFAULT 'draft',
  `priority` varchar(30) NOT NULL DEFAULT 'normal',
  `amount` decimal(18,2) DEFAULT 0.00,
  `location` varchar(150) DEFAULT NULL,
  `assigned_to` varchar(150) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `payload_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payload_json`)),
  `interoperability_status` varchar(50) NOT NULL DEFAULT 'not_ready',
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_record_no` (`record_no`),
  KEY `idx_patient` (`patient_id`),
  KEY `idx_visit` (`visit_id`),
  KEY `idx_billing` (`billing_id`),
  KEY `idx_status` (`status`),
  KEY `idx_date` (`record_date`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `intensive_care_records`
--

DROP TABLE IF EXISTS `intensive_care_records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `intensive_care_records` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `record_no` varchar(50) NOT NULL,
  `patient_id` bigint(20) unsigned DEFAULT NULL,
  `visit_id` bigint(20) unsigned DEFAULT NULL,
  `medical_record_id` bigint(20) unsigned DEFAULT NULL,
  `billing_id` bigint(20) unsigned DEFAULT NULL,
  `reference_no` varchar(100) DEFAULT NULL,
  `module_type` varchar(100) DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `record_date` datetime NOT NULL DEFAULT current_timestamp(),
  `status` varchar(50) NOT NULL DEFAULT 'draft',
  `priority` varchar(30) NOT NULL DEFAULT 'normal',
  `amount` decimal(18,2) DEFAULT 0.00,
  `location` varchar(150) DEFAULT NULL,
  `assigned_to` varchar(150) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `payload_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payload_json`)),
  `interoperability_status` varchar(50) NOT NULL DEFAULT 'not_ready',
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_record_no` (`record_no`),
  KEY `idx_patient` (`patient_id`),
  KEY `idx_visit` (`visit_id`),
  KEY `idx_billing` (`billing_id`),
  KEY `idx_status` (`status`),
  KEY `idx_date` (`record_date`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lab_orders`
--

DROP TABLE IF EXISTS `lab_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lab_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_no` varchar(50) NOT NULL,
  `visit_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `order_date` date NOT NULL,
  `tests` text DEFAULT NULL,
  `status` enum('ordered','sample_taken','result_ready','cancelled') DEFAULT 'ordered',
  `result_notes` text DEFAULT NULL,
  `fhir_service_request_id` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_no` (`order_no`),
  KEY `idx_lab_visit` (`visit_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lab_result_details`
--

DROP TABLE IF EXISTS `lab_result_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lab_result_details` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `record_no` varchar(50) NOT NULL,
  `patient_id` bigint(20) unsigned DEFAULT NULL,
  `visit_id` bigint(20) unsigned DEFAULT NULL,
  `medical_record_id` bigint(20) unsigned DEFAULT NULL,
  `billing_id` bigint(20) unsigned DEFAULT NULL,
  `reference_no` varchar(100) DEFAULT NULL,
  `module_type` varchar(100) DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `record_date` datetime NOT NULL DEFAULT current_timestamp(),
  `status` varchar(50) NOT NULL DEFAULT 'draft',
  `priority` varchar(30) NOT NULL DEFAULT 'normal',
  `amount` decimal(18,2) DEFAULT 0.00,
  `location` varchar(150) DEFAULT NULL,
  `assigned_to` varchar(150) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `payload_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payload_json`)),
  `interoperability_status` varchar(50) NOT NULL DEFAULT 'not_ready',
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_record_no` (`record_no`),
  KEY `idx_patient` (`patient_id`),
  KEY `idx_visit` (`visit_id`),
  KEY `idx_billing` (`billing_id`),
  KEY `idx_status` (`status`),
  KEY `idx_date` (`record_date`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `medical_documents`
--

DROP TABLE IF EXISTS `medical_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `medical_documents` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `record_no` varchar(50) NOT NULL,
  `patient_id` bigint(20) unsigned DEFAULT NULL,
  `visit_id` bigint(20) unsigned DEFAULT NULL,
  `medical_record_id` bigint(20) unsigned DEFAULT NULL,
  `billing_id` bigint(20) unsigned DEFAULT NULL,
  `reference_no` varchar(100) DEFAULT NULL,
  `module_type` varchar(100) DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `record_date` datetime NOT NULL DEFAULT current_timestamp(),
  `status` varchar(50) NOT NULL DEFAULT 'draft',
  `priority` varchar(30) NOT NULL DEFAULT 'normal',
  `amount` decimal(18,2) DEFAULT 0.00,
  `location` varchar(150) DEFAULT NULL,
  `assigned_to` varchar(150) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `payload_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payload_json`)),
  `interoperability_status` varchar(50) NOT NULL DEFAULT 'not_ready',
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_record_no` (`record_no`),
  KEY `idx_patient` (`patient_id`),
  KEY `idx_visit` (`visit_id`),
  KEY `idx_billing` (`billing_id`),
  KEY `idx_status` (`status`),
  KEY `idx_date` (`record_date`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `medical_items`
--

DROP TABLE IF EXISTS `medical_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `medical_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sku` varchar(80) DEFAULT NULL,
  `name` varchar(180) NOT NULL,
  `category` enum('obat','alkes','bmhp','lainnya') NOT NULL DEFAULT 'obat',
  `item_type` enum('medicine','supply','service') NOT NULL DEFAULT 'medicine',
  `unit_name` varchar(40) NOT NULL DEFAULT 'pcs',
  `current_stock` decimal(14,2) NOT NULL DEFAULT 0.00,
  `minimum_stock` decimal(14,2) NOT NULL DEFAULT 0.00,
  `unit_price` decimal(14,2) NOT NULL DEFAULT 0.00,
  `description` text DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_medical_items_status` (`status`),
  KEY `idx_medical_items_category` (`category`),
  KEY `idx_medical_items_sku` (`sku`)
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `medical_records`
--

DROP TABLE IF EXISTS `medical_records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `medical_records` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `visit_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `subjective` text DEFAULT NULL,
  `objective` text DEFAULT NULL,
  `assessment` text DEFAULT NULL,
  `plan` text DEFAULT NULL,
  `vital_bp` varchar(30) DEFAULT NULL,
  `vital_pulse` varchar(30) DEFAULT NULL,
  `vital_temperature` varchar(30) DEFAULT NULL,
  `vital_respiration` varchar(30) DEFAULT NULL,
  `vital_weight` varchar(30) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `fhir_observation_payload` longtext DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_medical_record_visit` (`visit_id`),
  KEY `idx_mr_patient` (`patient_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `medical_stock_movements`
--

DROP TABLE IF EXISTS `medical_stock_movements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `medical_stock_movements` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `medical_item_id` int(10) unsigned NOT NULL,
  `movement_type` enum('in','out','adjustment') NOT NULL,
  `reference_type` varchar(80) DEFAULT NULL,
  `reference_id` int(10) unsigned DEFAULT NULL,
  `quantity` decimal(14,2) NOT NULL DEFAULT 0.00,
  `stock_before` decimal(14,2) NOT NULL DEFAULT 0.00,
  `stock_after` decimal(14,2) NOT NULL DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_medical_stock_item` (`medical_item_id`),
  KEY `idx_medical_stock_reference` (`reference_type`,`reference_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `notification_reminders`
--

DROP TABLE IF EXISTS `notification_reminders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notification_reminders` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `record_no` varchar(50) NOT NULL,
  `patient_id` bigint(20) unsigned DEFAULT NULL,
  `visit_id` bigint(20) unsigned DEFAULT NULL,
  `medical_record_id` bigint(20) unsigned DEFAULT NULL,
  `billing_id` bigint(20) unsigned DEFAULT NULL,
  `reference_no` varchar(100) DEFAULT NULL,
  `module_type` varchar(100) DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `record_date` datetime NOT NULL DEFAULT current_timestamp(),
  `status` varchar(50) NOT NULL DEFAULT 'draft',
  `priority` varchar(30) NOT NULL DEFAULT 'normal',
  `amount` decimal(18,2) DEFAULT 0.00,
  `location` varchar(150) DEFAULT NULL,
  `assigned_to` varchar(150) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `payload_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payload_json`)),
  `interoperability_status` varchar(50) NOT NULL DEFAULT 'not_ready',
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_record_no` (`record_no`),
  KEY `idx_patient` (`patient_id`),
  KEY `idx_visit` (`visit_id`),
  KEY `idx_billing` (`billing_id`),
  KEY `idx_status` (`status`),
  KEY `idx_date` (`record_date`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nurse_station_tasks`
--

DROP TABLE IF EXISTS `nurse_station_tasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nurse_station_tasks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `record_no` varchar(50) NOT NULL,
  `patient_id` bigint(20) unsigned DEFAULT NULL,
  `visit_id` bigint(20) unsigned DEFAULT NULL,
  `medical_record_id` bigint(20) unsigned DEFAULT NULL,
  `billing_id` bigint(20) unsigned DEFAULT NULL,
  `reference_no` varchar(100) DEFAULT NULL,
  `module_type` varchar(100) DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `record_date` datetime NOT NULL DEFAULT current_timestamp(),
  `status` varchar(50) NOT NULL DEFAULT 'draft',
  `priority` varchar(30) NOT NULL DEFAULT 'normal',
  `amount` decimal(18,2) DEFAULT 0.00,
  `location` varchar(150) DEFAULT NULL,
  `assigned_to` varchar(150) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `payload_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payload_json`)),
  `interoperability_status` varchar(50) NOT NULL DEFAULT 'not_ready',
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_record_no` (`record_no`),
  KEY `idx_patient` (`patient_id`),
  KEY `idx_visit` (`visit_id`),
  KEY `idx_billing` (`billing_id`),
  KEY `idx_status` (`status`),
  KEY `idx_date` (`record_date`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `operating_room_schedules`
--

DROP TABLE IF EXISTS `operating_room_schedules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `operating_room_schedules` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `record_no` varchar(50) NOT NULL,
  `patient_id` bigint(20) unsigned DEFAULT NULL,
  `visit_id` bigint(20) unsigned DEFAULT NULL,
  `medical_record_id` bigint(20) unsigned DEFAULT NULL,
  `billing_id` bigint(20) unsigned DEFAULT NULL,
  `reference_no` varchar(100) DEFAULT NULL,
  `module_type` varchar(100) DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `record_date` datetime NOT NULL DEFAULT current_timestamp(),
  `status` varchar(50) NOT NULL DEFAULT 'draft',
  `priority` varchar(30) NOT NULL DEFAULT 'normal',
  `amount` decimal(18,2) DEFAULT 0.00,
  `location` varchar(150) DEFAULT NULL,
  `assigned_to` varchar(150) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `payload_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payload_json`)),
  `interoperability_status` varchar(50) NOT NULL DEFAULT 'not_ready',
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_record_no` (`record_no`),
  KEY `idx_patient` (`patient_id`),
  KEY `idx_visit` (`visit_id`),
  KEY `idx_billing` (`billing_id`),
  KEY `idx_status` (`status`),
  KEY `idx_date` (`record_date`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `parking_areas`
--

DROP TABLE IF EXISTS `parking_areas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `parking_areas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `area_code` varchar(30) NOT NULL,
  `name` varchar(150) NOT NULL,
  `location` varchar(150) DEFAULT NULL,
  `capacity` int(11) NOT NULL DEFAULT 0,
  `reserved_capacity` int(11) NOT NULL DEFAULT 0,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `area_code` (`area_code`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `parking_gate_logs`
--

DROP TABLE IF EXISTS `parking_gate_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `parking_gate_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket_id` int(11) DEFAULT NULL,
  `gate_id` int(11) NOT NULL,
  `action` enum('open_entry','close_entry','open_exit','close_exit','open_manual','close_manual','deny') NOT NULL,
  `response_status` enum('success','failed','simulated') DEFAULT 'simulated',
  `response_message` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_parking_gate_log_ticket` (`ticket_id`),
  KEY `idx_parking_gate_log_gate` (`gate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=64 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `parking_gates`
--

DROP TABLE IF EXISTS `parking_gates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `parking_gates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gate_code` varchar(30) NOT NULL,
  `name` varchar(150) NOT NULL,
  `gate_type` enum('entry','exit','both') DEFAULT 'both',
  `area_id` int(11) DEFAULT NULL,
  `device_type` enum('manual','http_api','relay','tcp_ip','serial','qr_scanner','lpr_camera') DEFAULT 'manual',
  `device_endpoint` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive','maintenance') DEFAULT 'active',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `gate_code` (`gate_code`),
  KEY `idx_parking_gate_area` (`area_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `parking_members`
--

DROP TABLE IF EXISTS `parking_members`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `parking_members` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_no` varchar(50) NOT NULL,
  `member_type` enum('doctor','employee','vendor','vip','operational') DEFAULT 'employee',
  `user_id` int(11) DEFAULT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `name` varchar(150) NOT NULL,
  `plate_number` varchar(30) NOT NULL,
  `vehicle_type_id` int(11) DEFAULT NULL,
  `valid_from` date DEFAULT NULL,
  `valid_to` date DEFAULT NULL,
  `status` enum('active','inactive','expired') DEFAULT 'active',
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `member_no` (`member_no`),
  KEY `idx_parking_member_plate` (`plate_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `parking_payments`
--

DROP TABLE IF EXISTS `parking_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `parking_payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `payment_no` varchar(50) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `patient_billing_id` int(11) DEFAULT NULL,
  `bank_account_id` int(11) DEFAULT NULL,
  `payment_date` date NOT NULL,
  `payment_method` enum('cash','transfer','debit','qris','member','patient_billing','waived','other') DEFAULT 'cash',
  `amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `status` enum('unpaid','paid','waived','refunded') DEFAULT 'paid',
  `reference_no` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `payment_no` (`payment_no`),
  KEY `idx_parking_payment_ticket` (`ticket_id`),
  KEY `idx_parking_payment_date` (`payment_date`)
) ENGINE=InnoDB AUTO_INCREMENT=277 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `parking_rates`
--

DROP TABLE IF EXISTS `parking_rates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `parking_rates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rate_code` varchar(30) NOT NULL,
  `name` varchar(150) NOT NULL,
  `vehicle_type_id` int(11) NOT NULL,
  `rate_type` enum('flat','hourly','progressive','daily_max') DEFAULT 'hourly',
  `initial_minutes` int(11) NOT NULL DEFAULT 60,
  `initial_rate` decimal(15,2) NOT NULL DEFAULT 0.00,
  `next_hour_rate` decimal(15,2) NOT NULL DEFAULT 0.00,
  `progressive_rate` decimal(15,2) NOT NULL DEFAULT 0.00,
  `max_daily_rate` decimal(15,2) NOT NULL DEFAULT 0.00,
  `grace_minutes` int(11) NOT NULL DEFAULT 15,
  `lost_ticket_fee` decimal(15,2) NOT NULL DEFAULT 0.00,
  `inpatient_special_rate` decimal(15,2) NOT NULL DEFAULT 0.00,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `rate_code` (`rate_code`),
  KEY `idx_parking_rate_vehicle` (`vehicle_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `parking_tickets`
--

DROP TABLE IF EXISTS `parking_tickets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `parking_tickets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket_no` varchar(50) NOT NULL,
  `qr_token` varchar(100) NOT NULL,
  `plate_number` varchar(30) NOT NULL,
  `vehicle_type_id` int(11) NOT NULL,
  `area_id` int(11) DEFAULT NULL,
  `entry_gate_id` int(11) DEFAULT NULL,
  `exit_gate_id` int(11) DEFAULT NULL,
  `patient_visit_id` int(11) DEFAULT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `member_id` int(11) DEFAULT NULL,
  `entry_time` datetime NOT NULL,
  `exit_time` datetime DEFAULT NULL,
  `duration_minutes` int(11) NOT NULL DEFAULT 0,
  `calculated_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `payable_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `status` enum('active','unpaid','paid','lost_ticket','cancelled') DEFAULT 'active',
  `payment_status` enum('unpaid','paid','waived','refunded') DEFAULT 'unpaid',
  `source` enum('manual','lpr','qr','member') DEFAULT 'manual',
  `notes` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `checked_out_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `ticket_no` (`ticket_no`),
  UNIQUE KEY `qr_token` (`qr_token`),
  KEY `idx_parking_ticket_plate` (`plate_number`),
  KEY `idx_parking_ticket_status` (`status`),
  KEY `idx_parking_ticket_entry` (`entry_time`),
  KEY `idx_parking_ticket_visit` (`patient_visit_id`)
) ENGINE=InnoDB AUTO_INCREMENT=871 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `parking_validations`
--

DROP TABLE IF EXISTS `parking_validations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `parking_validations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket_id` int(11) NOT NULL,
  `patient_visit_id` int(11) DEFAULT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `validation_type` enum('outpatient','inpatient','emergency','manual') DEFAULT 'outpatient',
  `discount_type` enum('free','percent','amount','special_rate') DEFAULT 'free',
  `discount_value` decimal(15,2) NOT NULL DEFAULT 0.00,
  `validated_by` int(11) DEFAULT NULL,
  `validated_at` datetime DEFAULT current_timestamp(),
  `notes` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_parking_validation_ticket` (`ticket_id`),
  KEY `idx_parking_validation_visit` (`patient_visit_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `parking_vehicle_types`
--

DROP TABLE IF EXISTS `parking_vehicle_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `parking_vehicle_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type_code` varchar(30) NOT NULL,
  `name` varchar(100) NOT NULL,
  `category` enum('motor','mobil','ambulance','doctor_employee','vendor','vip','operational','other') DEFAULT 'mobil',
  `is_free` tinyint(1) NOT NULL DEFAULT 0,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `type_code` (`type_code`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `patient_billing`
--

DROP TABLE IF EXISTS `patient_billing`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `patient_billing` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `billing_no` varchar(50) NOT NULL,
  `visit_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `billing_date` date NOT NULL,
  `subtotal` decimal(15,2) DEFAULT 0.00,
  `discount_amount` decimal(15,2) DEFAULT 0.00,
  `tax_amount` decimal(15,2) DEFAULT 0.00,
  `grand_total` decimal(15,2) DEFAULT 0.00,
  `paid_amount` decimal(15,2) DEFAULT 0.00,
  `status` enum('draft','unpaid','partial','paid','cancelled') DEFAULT 'draft',
  `notes` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `billing_no` (`billing_no`),
  UNIQUE KEY `uk_billing_visit` (`visit_id`),
  KEY `idx_billing_patient` (`patient_id`),
  KEY `idx_billing_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `patient_billing_items`
--

DROP TABLE IF EXISTS `patient_billing_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `patient_billing_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `billing_id` int(11) NOT NULL,
  `reference_type` varchar(50) DEFAULT NULL,
  `reference_id` int(11) DEFAULT NULL,
  `item_name` varchar(150) NOT NULL,
  `quantity` decimal(12,2) DEFAULT 1.00,
  `unit_price` decimal(15,2) DEFAULT 0.00,
  `total_price` decimal(15,2) DEFAULT 0.00,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_billing_item_billing` (`billing_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `patient_payments`
--

DROP TABLE IF EXISTS `patient_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `patient_payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `payment_no` varchar(50) NOT NULL,
  `billing_id` int(11) NOT NULL,
  `visit_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `bank_account_id` int(11) DEFAULT NULL,
  `payment_date` date NOT NULL,
  `payment_method` enum('cash','transfer','card','insurance','other') DEFAULT 'cash',
  `amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `reference_no` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `payment_no` (`payment_no`),
  KEY `idx_patient_payment_billing` (`billing_id`),
  KEY `idx_patient_payment_date` (`payment_date`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `patient_visits`
--

DROP TABLE IF EXISTS `patient_visits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `patient_visits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `visit_no` varchar(50) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `polyclinic_id` int(11) NOT NULL,
  `schedule_id` int(11) DEFAULT NULL,
  `visit_date` date NOT NULL,
  `visit_time` time DEFAULT NULL,
  `payment_type` enum('umum','asuransi','bpjs') DEFAULT 'umum',
  `chief_complaint` text DEFAULT NULL,
  `status` enum('registered','waiting','in_consultation','pharmacy','billing','paid','completed','cancelled') DEFAULT 'registered',
  `fhir_encounter_id` varchar(100) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `visit_no` (`visit_no`),
  KEY `idx_visit_patient` (`patient_id`),
  KEY `idx_visit_doctor_date` (`doctor_id`,`visit_date`),
  KEY `idx_visit_poly_date` (`polyclinic_id`,`visit_date`),
  KEY `idx_visit_status` (`status`),
  KEY `idx_visit_fhir` (`fhir_encounter_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `patients`
--

DROP TABLE IF EXISTS `patients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `patients` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `medical_record_no` varchar(50) NOT NULL,
  `nik` varchar(30) DEFAULT NULL,
  `name` varchar(150) NOT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `birth_place` varchar(100) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `blood_type` varchar(5) DEFAULT NULL,
  `allergy_notes` text DEFAULT NULL,
  `emergency_contact_name` varchar(150) DEFAULT NULL,
  `emergency_contact_phone` varchar(50) DEFAULT NULL,
  `insurance_type` enum('umum','asuransi','bpjs') DEFAULT 'umum',
  `insurance_no` varchar(100) DEFAULT NULL,
  `fhir_patient_id` varchar(100) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `medical_record_no` (`medical_record_no`),
  KEY `idx_patients_name` (`name`),
  KEY `idx_patients_nik` (`nik`),
  KEY `idx_patients_fhir` (`fhir_patient_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `payment_gateway_logs`
--

DROP TABLE IF EXISTS `payment_gateway_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payment_gateway_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `record_no` varchar(50) NOT NULL,
  `patient_id` bigint(20) unsigned DEFAULT NULL,
  `visit_id` bigint(20) unsigned DEFAULT NULL,
  `medical_record_id` bigint(20) unsigned DEFAULT NULL,
  `billing_id` bigint(20) unsigned DEFAULT NULL,
  `reference_no` varchar(100) DEFAULT NULL,
  `module_type` varchar(100) DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `record_date` datetime NOT NULL DEFAULT current_timestamp(),
  `status` varchar(50) NOT NULL DEFAULT 'draft',
  `priority` varchar(30) NOT NULL DEFAULT 'normal',
  `amount` decimal(18,2) DEFAULT 0.00,
  `location` varchar(150) DEFAULT NULL,
  `assigned_to` varchar(150) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `payload_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payload_json`)),
  `interoperability_status` varchar(50) NOT NULL DEFAULT 'not_ready',
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_record_no` (`record_no`),
  KEY `idx_patient` (`patient_id`),
  KEY `idx_visit` (`visit_id`),
  KEY `idx_billing` (`billing_id`),
  KEY `idx_status` (`status`),
  KEY `idx_date` (`record_date`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module` varchar(100) NOT NULL,
  `action_name` varchar(100) NOT NULL,
  `permission_key` varchar(150) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `permission_key` (`permission_key`)
) ENGINE=InnoDB AUTO_INCREMENT=323 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pharmacy_purchase_requests`
--

DROP TABLE IF EXISTS `pharmacy_purchase_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pharmacy_purchase_requests` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `record_no` varchar(50) NOT NULL,
  `patient_id` bigint(20) unsigned DEFAULT NULL,
  `visit_id` bigint(20) unsigned DEFAULT NULL,
  `medical_record_id` bigint(20) unsigned DEFAULT NULL,
  `billing_id` bigint(20) unsigned DEFAULT NULL,
  `reference_no` varchar(100) DEFAULT NULL,
  `module_type` varchar(100) DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `record_date` datetime NOT NULL DEFAULT current_timestamp(),
  `status` varchar(50) NOT NULL DEFAULT 'draft',
  `priority` varchar(30) NOT NULL DEFAULT 'normal',
  `amount` decimal(18,2) DEFAULT 0.00,
  `location` varchar(150) DEFAULT NULL,
  `assigned_to` varchar(150) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `payload_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payload_json`)),
  `interoperability_status` varchar(50) NOT NULL DEFAULT 'not_ready',
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_record_no` (`record_no`),
  KEY `idx_patient` (`patient_id`),
  KEY `idx_visit` (`visit_id`),
  KEY `idx_billing` (`billing_id`),
  KEY `idx_status` (`status`),
  KEY `idx_date` (`record_date`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pharmacy_transactions`
--

DROP TABLE IF EXISTS `pharmacy_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pharmacy_transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `transaction_no` varchar(50) NOT NULL,
  `prescription_id` int(11) NOT NULL,
  `visit_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `status` enum('verified','prepared','dispensed','cancelled') DEFAULT 'verified',
  `total_amount` decimal(15,2) DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `transaction_no` (`transaction_no`),
  KEY `idx_pharmacy_rx` (`prescription_id`),
  KEY `idx_pharmacy_visit` (`visit_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pharmacy_vendors`
--

DROP TABLE IF EXISTS `pharmacy_vendors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pharmacy_vendors` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `record_no` varchar(50) NOT NULL,
  `patient_id` bigint(20) unsigned DEFAULT NULL,
  `visit_id` bigint(20) unsigned DEFAULT NULL,
  `medical_record_id` bigint(20) unsigned DEFAULT NULL,
  `billing_id` bigint(20) unsigned DEFAULT NULL,
  `reference_no` varchar(100) DEFAULT NULL,
  `module_type` varchar(100) DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `record_date` datetime NOT NULL DEFAULT current_timestamp(),
  `status` varchar(50) NOT NULL DEFAULT 'draft',
  `priority` varchar(30) NOT NULL DEFAULT 'normal',
  `amount` decimal(18,2) DEFAULT 0.00,
  `location` varchar(150) DEFAULT NULL,
  `assigned_to` varchar(150) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `payload_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payload_json`)),
  `interoperability_status` varchar(50) NOT NULL DEFAULT 'not_ready',
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_record_no` (`record_no`),
  KEY `idx_patient` (`patient_id`),
  KEY `idx_visit` (`visit_id`),
  KEY `idx_billing` (`billing_id`),
  KEY `idx_status` (`status`),
  KEY `idx_date` (`record_date`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `polyclinics`
--

DROP TABLE IF EXISTS `polyclinics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `polyclinics` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `clinic_code` varchar(20) NOT NULL,
  `name` varchar(150) NOT NULL,
  `queue_prefix` varchar(5) NOT NULL DEFAULT 'A',
  `location` varchar(150) DEFAULT NULL,
  `fhir_location_id` varchar(100) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `clinic_code` (`clinic_code`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `prescription_items`
--

DROP TABLE IF EXISTS `prescription_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `prescription_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `prescription_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `item_name` varchar(150) NOT NULL,
  `dosage` varchar(100) DEFAULT NULL,
  `frequency` varchar(100) DEFAULT NULL,
  `duration` varchar(100) DEFAULT NULL,
  `quantity` decimal(12,2) DEFAULT 1.00,
  `unit_name` varchar(50) DEFAULT 'unit',
  `price` decimal(15,2) DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_rx_item_prescription` (`prescription_id`),
  KEY `idx_rx_item_product` (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `prescriptions`
--

DROP TABLE IF EXISTS `prescriptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `prescriptions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `prescription_no` varchar(50) NOT NULL,
  `visit_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `status` enum('pending','verified','prepared','dispensed','cancelled') DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `verified_by` int(11) DEFAULT NULL,
  `dispensed_by` int(11) DEFAULT NULL,
  `verified_at` datetime DEFAULT NULL,
  `dispensed_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `prescription_no` (`prescription_no`),
  KEY `idx_prescription_visit` (`visit_id`),
  KEY `idx_prescription_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `radiology_orders`
--

DROP TABLE IF EXISTS `radiology_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `radiology_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_no` varchar(50) NOT NULL,
  `visit_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `order_date` date NOT NULL,
  `examination` text DEFAULT NULL,
  `status` enum('ordered','scheduled','result_ready','cancelled') DEFAULT 'ordered',
  `result_notes` text DEFAULT NULL,
  `fhir_service_request_id` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_no` (`order_no`),
  KEY `idx_rad_visit` (`visit_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `radiology_result_details`
--

DROP TABLE IF EXISTS `radiology_result_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `radiology_result_details` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `record_no` varchar(50) NOT NULL,
  `patient_id` bigint(20) unsigned DEFAULT NULL,
  `visit_id` bigint(20) unsigned DEFAULT NULL,
  `medical_record_id` bigint(20) unsigned DEFAULT NULL,
  `billing_id` bigint(20) unsigned DEFAULT NULL,
  `reference_no` varchar(100) DEFAULT NULL,
  `module_type` varchar(100) DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `record_date` datetime NOT NULL DEFAULT current_timestamp(),
  `status` varchar(50) NOT NULL DEFAULT 'draft',
  `priority` varchar(30) NOT NULL DEFAULT 'normal',
  `amount` decimal(18,2) DEFAULT 0.00,
  `location` varchar(150) DEFAULT NULL,
  `assigned_to` varchar(150) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `payload_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payload_json`)),
  `interoperability_status` varchar(50) NOT NULL DEFAULT 'not_ready',
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_record_no` (`record_no`),
  KEY `idx_patient` (`patient_id`),
  KEY `idx_visit` (`visit_id`),
  KEY `idx_billing` (`billing_id`),
  KEY `idx_status` (`status`),
  KEY `idx_date` (`record_date`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `role_permissions`
--

DROP TABLE IF EXISTS `role_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role_permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_role_permission` (`role_id`,`permission_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3765 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `satusehat_exchange_logs`
--

DROP TABLE IF EXISTS `satusehat_exchange_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `satusehat_exchange_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `record_no` varchar(50) NOT NULL,
  `patient_id` bigint(20) unsigned DEFAULT NULL,
  `visit_id` bigint(20) unsigned DEFAULT NULL,
  `medical_record_id` bigint(20) unsigned DEFAULT NULL,
  `billing_id` bigint(20) unsigned DEFAULT NULL,
  `reference_no` varchar(100) DEFAULT NULL,
  `module_type` varchar(100) DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `record_date` datetime NOT NULL DEFAULT current_timestamp(),
  `status` varchar(50) NOT NULL DEFAULT 'draft',
  `priority` varchar(30) NOT NULL DEFAULT 'normal',
  `amount` decimal(18,2) DEFAULT 0.00,
  `location` varchar(150) DEFAULT NULL,
  `assigned_to` varchar(150) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `payload_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payload_json`)),
  `interoperability_status` varchar(50) NOT NULL DEFAULT 'not_ready',
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_record_no` (`record_no`),
  KEY `idx_patient` (`patient_id`),
  KEY `idx_visit` (`visit_id`),
  KEY `idx_billing` (`billing_id`),
  KEY `idx_status` (`status`),
  KEY `idx_date` (`record_date`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `self_service_queues`
--

DROP TABLE IF EXISTS `self_service_queues`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `self_service_queues` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `queue_no` varchar(20) NOT NULL,
  `queue_date` date NOT NULL,
  `service_type` varchar(40) NOT NULL,
  `polyclinic_id` int(11) DEFAULT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `service_name` varchar(150) NOT NULL,
  `visitor_name` varchar(150) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `counter_no` varchar(20) NOT NULL,
  `status` enum('waiting','called','serving','done','cancelled') DEFAULT 'waiting',
  `printed_at` datetime DEFAULT NULL,
  `called_at` datetime DEFAULT NULL,
  `served_at` datetime DEFAULT NULL,
  `finished_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_self_queue_no_date` (`queue_no`,`queue_date`),
  KEY `idx_self_queue_date_status` (`queue_date`,`status`),
  KEY `idx_self_queue_service` (`service_type`,`polyclinic_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `stock_opnames`
--

DROP TABLE IF EXISTS `stock_opnames`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stock_opnames` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `record_no` varchar(50) NOT NULL,
  `patient_id` bigint(20) unsigned DEFAULT NULL,
  `visit_id` bigint(20) unsigned DEFAULT NULL,
  `medical_record_id` bigint(20) unsigned DEFAULT NULL,
  `billing_id` bigint(20) unsigned DEFAULT NULL,
  `reference_no` varchar(100) DEFAULT NULL,
  `module_type` varchar(100) DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `record_date` datetime NOT NULL DEFAULT current_timestamp(),
  `status` varchar(50) NOT NULL DEFAULT 'draft',
  `priority` varchar(30) NOT NULL DEFAULT 'normal',
  `amount` decimal(18,2) DEFAULT 0.00,
  `location` varchar(150) DEFAULT NULL,
  `assigned_to` varchar(150) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `payload_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payload_json`)),
  `interoperability_status` varchar(50) NOT NULL DEFAULT 'not_ready',
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_record_no` (`record_no`),
  KEY `idx_patient` (`patient_id`),
  KEY `idx_visit` (`visit_id`),
  KEY `idx_billing` (`billing_id`),
  KEY `idx_status` (`status`),
  KEY `idx_date` (`record_date`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `gender` enum('male','female') DEFAULT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `activation_token` varchar(255) DEFAULT NULL,
  `activation_expires_at` datetime DEFAULT NULL,
  `activated_at` datetime DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expires_at` datetime DEFAULT NULL,
  `role_id` int(11) DEFAULT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `data_scope` enum('all','own','department','branch','assigned') DEFAULT 'own',
  `role` varchar(50) DEFAULT NULL,
  `status` enum('pending','active','inactive') DEFAULT 'pending',
  `last_login` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `visit_queue`
--

DROP TABLE IF EXISTS `visit_queue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `visit_queue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `visit_id` int(11) NOT NULL,
  `polyclinic_id` int(11) NOT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `queue_no` varchar(20) NOT NULL,
  `queue_date` date NOT NULL,
  `status` enum('waiting','called','in_service','done','cancelled') DEFAULT 'waiting',
  `called_at` datetime DEFAULT NULL,
  `served_at` datetime DEFAULT NULL,
  `finished_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_queue_visit` (`visit_id`),
  KEY `idx_queue_poly_date` (`polyclinic_id`,`queue_date`),
  KEY `idx_queue_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vital_sign_records`
--

DROP TABLE IF EXISTS `vital_sign_records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vital_sign_records` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `record_no` varchar(50) NOT NULL,
  `patient_id` bigint(20) unsigned DEFAULT NULL,
  `visit_id` bigint(20) unsigned DEFAULT NULL,
  `medical_record_id` bigint(20) unsigned DEFAULT NULL,
  `billing_id` bigint(20) unsigned DEFAULT NULL,
  `reference_no` varchar(100) DEFAULT NULL,
  `module_type` varchar(100) DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `record_date` datetime NOT NULL DEFAULT current_timestamp(),
  `status` varchar(50) NOT NULL DEFAULT 'draft',
  `priority` varchar(30) NOT NULL DEFAULT 'normal',
  `amount` decimal(18,2) DEFAULT 0.00,
  `location` varchar(150) DEFAULT NULL,
  `assigned_to` varchar(150) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `payload_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payload_json`)),
  `interoperability_status` varchar(50) NOT NULL DEFAULT 'not_ready',
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_record_no` (`record_no`),
  KEY `idx_patient` (`patient_id`),
  KEY `idx_visit` (`visit_id`),
  KEY `idx_billing` (`billing_id`),
  KEY `idx_status` (`status`),
  KEY `idx_date` (`record_date`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-05-30 14:20:29
