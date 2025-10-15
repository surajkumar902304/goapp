-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Oct 08, 2025 at 09:45 AM
-- Server version: 8.0.43
-- PHP Version: 8.4.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `goappdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `name`, `email`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@gmail.com', '$2y$10$eiGFTQ/e6nOntn10y/x/W.sHw/F7x64JiCdBFs8s.KQs8n38wtda2', NULL, '2025-01-30 11:26:30', '2025-01-30 11:26:30');

-- --------------------------------------------------------

--
-- Table structure for table `bank_details`
--

CREATE TABLE `bank_details` (
  `bank_detail_id` bigint UNSIGNED NOT NULL,
  `company_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bank_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `note` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bank_details`
--

INSERT INTO `bank_details` (`bank_detail_id`, `company_name`, `bank_name`, `account_number`, `sort_code`, `note`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'TEST', 'TEST', '123456789', '123456', 'Testing', 1, '2025-08-05 04:52:23', '2025-08-05 04:52:23');

-- --------------------------------------------------------

--
-- Table structure for table `browsebanners`
--

CREATE TABLE `browsebanners` (
  `browsebanner_id` bigint UNSIGNED NOT NULL,
  `browsebanner_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `browsebanner_image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `browsebanner_position` int NOT NULL DEFAULT '0',
  `main_mcat_id` bigint UNSIGNED DEFAULT NULL,
  `mcat_id` bigint UNSIGNED DEFAULT NULL,
  `msubcat_id` bigint UNSIGNED DEFAULT NULL,
  `mproduct_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `browsebanners`
--

INSERT INTO `browsebanners` (`browsebanner_id`, `browsebanner_name`, `browsebanner_image`, `browsebanner_position`, `main_mcat_id`, `mcat_id`, `msubcat_id`, `mproduct_id`, `created_at`, `updated_at`) VALUES
(14, 'Chocolate', 'goapp/images/browsebanner/browsebanner_68aff055ea3a7.png', 2, NULL, NULL, NULL, NULL, '2025-08-28 05:59:51', '2025-08-28 06:00:21'),
(17, 'Takis', 'goapp/images/browsebanner/browsebanner_68aff0fe31f8f.png', 5, NULL, NULL, NULL, NULL, '2025-08-28 06:02:39', '2025-08-28 06:02:39'),
(18, 'KitKat', 'goapp/images/browsebanner/browsebanner_68aff10a6553a.png', 6, NULL, NULL, NULL, NULL, '2025-08-28 06:02:52', '2025-08-28 06:02:52');

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `cart_item_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `mvariant_id` bigint UNSIGNED DEFAULT NULL,
  `quantity` bigint DEFAULT NULL,
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cart_items`
--

INSERT INTO `cart_items` (`cart_item_id`, `user_id`, `mvariant_id`, `quantity`, `status`, `created_at`, `updated_at`) VALUES
(649, 11, 476, 1, 'active', '2025-09-09 06:31:16', '2025-09-09 06:31:16'),
(674, 27, 476, 1, 'active', '2025-10-01 04:59:34', '2025-10-01 04:59:34'),
(675, 27, 477, 1, 'active', '2025-10-01 04:59:34', '2025-10-01 04:59:34'),
(676, 10, 476, 1, 'active', '2025-10-03 05:44:26', '2025-10-06 13:48:50');

-- --------------------------------------------------------

--
-- Table structure for table `coupons`
--

CREATE TABLE `coupons` (
  `coupon_id` bigint UNSIGNED NOT NULL,
  `main_mcat_id` bigint UNSIGNED DEFAULT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `discount_type` enum('fixed','percent') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'fixed',
  `discount_value` decimal(10,2) NOT NULL,
  `expires_at` date DEFAULT NULL,
  `usage_limit` int UNSIGNED DEFAULT NULL,
  `per_user_limit` int UNSIGNED DEFAULT NULL,
  `min_cart_value` decimal(10,2) NOT NULL DEFAULT '0.00',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `coupons`
--

INSERT INTO `coupons` (`coupon_id`, `main_mcat_id`, `code`, `discount_type`, `discount_value`, `expires_at`, `usage_limit`, `per_user_limit`, `min_cart_value`, `is_active`, `created_at`, `updated_at`) VALUES
(2, NULL, 'MAX6000', 'fixed', 100.00, '2025-07-01', 98, 1, 1000.00, 1, '2025-07-11 13:45:14', '2025-08-25 00:13:57'),
(3, 1, 'TEST1', 'percent', 10.00, '2025-09-02', 50, 1, 0.00, 1, '2025-07-11 13:45:14', '2025-08-30 11:37:49'),
(5, 1, 'COUP001', 'percent', 10.00, '2025-08-20', 5, 2, 100.00, 1, '2025-08-30 11:40:39', '2025-09-09 08:09:19');

-- --------------------------------------------------------

--
-- Table structure for table `coupon_usages`
--

CREATE TABLE `coupon_usages` (
  `coupon_usage_id` bigint UNSIGNED NOT NULL,
  `coupon_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `used_count` int UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `coupon_usages`
--

INSERT INTO `coupon_usages` (`coupon_usage_id`, `coupon_id`, `user_id`, `used_count`, `created_at`, `updated_at`) VALUES
(2, 3, 10, 1, '2025-07-22 05:18:50', '2025-07-22 05:18:50'),
(4, 3, 11, 1, '2025-07-24 12:32:42', '2025-07-24 12:32:42');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `rep_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mobile` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rep_code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `commission_percent` decimal(5,2) NOT NULL DEFAULT '5.00',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`rep_id`, `name`, `email`, `password`, `mobile`, `rep_code`, `commission_percent`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Rep', 'rep@example.com', '$2y$10$q8JilizK3Hvdu/eZMacuC.3ep7mwwaKETT/uYw9Oh5ohd82lUilG2', '5465414545', 'TEST', 5.00, NULL, '2025-07-31 12:33:37', '2025-07-31 12:33:40'),
(2, 'Rony Singh', 'info@truewebpro.co.uk', '$2y$10$PXFkNeYBAw//LXkXBLJPNupwrpVK6rmrN1F7dAudmJNr/DhSj67KG', '0744747437071', 'TR001', 5.00, NULL, '2025-07-07 19:09:34', '2025-08-24 16:28:15'),
(3, 'Preet', 'parmlongia@hotmail.com', '$2y$10$Wb8qSKjLxs5.y.7fIAb.SuUuUTJloptmDIrHgMz4KpbbHjdRY7ebm', '9090807050', 'TR002', 10.00, NULL, '2025-07-26 14:45:54', '2025-08-30 11:12:13'),
(5, 'John', 'john@yopmail.com', '$2y$10$f9gO5d7IuOZ/D38RBxgpROQv6bF.601cbQoS3wO8kdx2MIfoDmvB6', '99887766', 'TR100', 6.00, NULL, '2025-08-30 11:22:45', '2025-08-30 11:22:45');

-- --------------------------------------------------------

--
-- Table structure for table `customer_commissions`
--

CREATE TABLE `customer_commissions` (
  `customer_commission_id` bigint UNSIGNED NOT NULL,
  `rep_id` bigint UNSIGNED NOT NULL,
  `total_commission` decimal(10,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customer_commissions`
--

INSERT INTO `customer_commissions` (`customer_commission_id`, `rep_id`, `total_commission`, `created_at`, `updated_at`) VALUES
(1, 2, 416.86, '2025-08-02 07:46:57', '2025-09-10 17:25:34'),
(2, 3, 229.62, '2025-08-05 05:33:31', '2025-08-27 05:29:14');

-- --------------------------------------------------------

--
-- Table structure for table `delivery_methods`
--

CREATE TABLE `delivery_methods` (
  `delivery_method_id` bigint UNSIGNED NOT NULL,
  `delivery_method_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `delivery_method_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `is_active` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `delivery_methods`
--

INSERT INTO `delivery_methods` (`delivery_method_id`, `delivery_method_name`, `delivery_method_amount`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'SATURDAY DELIVERY (1-2 DAYS) - £14.99', 14.99, 1, '2025-06-02 18:40:54', '2025-09-07 13:12:29'),
(2, 'NEXT DAY DELIVERY (1-2 DAYS) - £7.99', 7.99, 1, '2025-06-02 18:40:54', '2025-09-07 13:12:07');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `failed_jobs`
--

INSERT INTO `failed_jobs` (`id`, `uuid`, `connection`, `queue`, `payload`, `exception`, `failed_at`) VALUES
(1, '866cb143-624f-402d-b1f2-095e2cc29781', 'database', 'default', '{\"uuid\":\"866cb143-624f-402d-b1f2-095e2cc29781\",\"displayName\":\"App\\\\Mail\\\\UserApprovalMail\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Mail\\\\SendQueuedMailable\",\"command\":\"O:34:\\\"Illuminate\\\\Mail\\\\SendQueuedMailable\\\":13:{s:8:\\\"mailable\\\";O:25:\\\"App\\\\Mail\\\\UserApprovalMail\\\":29:{s:4:\\\"user\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":4:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";i:14;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";}s:6:\\\"locale\\\";N;s:4:\\\"from\\\";a:0:{}s:2:\\\"to\\\";a:1:{i:0;a:2:{s:4:\\\"name\\\";N;s:7:\\\"address\\\";s:23:\\\"kplsharma8185@gmail.com\\\";}}s:2:\\\"cc\\\";a:0:{}s:3:\\\"bcc\\\";a:0:{}s:7:\\\"replyTo\\\";a:0:{}s:7:\\\"subject\\\";N;s:8:\\\"markdown\\\";N;s:7:\\\"\\u0000*\\u0000html\\\";N;s:4:\\\"view\\\";N;s:8:\\\"textView\\\";N;s:8:\\\"viewData\\\";a:0:{}s:11:\\\"attachments\\\";a:0:{}s:14:\\\"rawAttachments\\\";a:0:{}s:15:\\\"diskAttachments\\\";a:0:{}s:9:\\\"callbacks\\\";a:0:{}s:5:\\\"theme\\\";N;s:6:\\\"mailer\\\";s:4:\\\"smtp\\\";s:29:\\\"\\u0000*\\u0000assertionableRenderStrings\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:17:\\\"shouldBeEncrypted\\\";b:0;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}}\"}}', 'Swift_TransportException: Failed to authenticate on SMTP server with username \"9b1444797acddc\" using 3 possible authenticators. Authenticator CRAM-MD5 returned Expected response code 250 but got an empty response. Authenticator LOGIN returned Expected response code 250 but got an empty response. Authenticator PLAIN returned Expected response code 250 but got an empty response. in /var/www/html/goapp/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Transport/Esmtp/AuthHandler.php:191\nStack trace:\n#0 /var/www/html/goapp/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Transport/EsmtpTransport.php(371): Swift_Transport_Esmtp_AuthHandler->afterEhlo()\n#1 /var/www/html/goapp/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Transport/AbstractSmtpTransport.php(148): Swift_Transport_EsmtpTransport->doHeloCommand()\n#2 /var/www/html/goapp/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Mailer.php(65): Swift_Transport_AbstractSmtpTransport->start()\n#3 /var/www/html/goapp/vendor/laravel/framework/src/Illuminate/Mail/Mailer.php(521): Swift_Mailer->send()\n#4 /var/www/html/goapp/vendor/laravel/framework/src/Illuminate/Mail/Mailer.php(288): Illuminate\\Mail\\Mailer->sendSwiftMessage()\n#5 /var/www/html/goapp/vendor/laravel/framework/src/Illuminate/Mail/Mailable.php(181): Illuminate\\Mail\\Mailer->send()\n#6 /var/www/html/goapp/vendor/laravel/framework/src/Illuminate/Support/Traits/Localizable.php(19): Illuminate\\Mail\\Mailable->{closure:Illuminate\\Mail\\Mailable::send():174}()\n#7 /var/www/html/goapp/vendor/laravel/framework/src/Illuminate/Mail/Mailable.php(174): Illuminate\\Mail\\Mailable->withLocale()\n#8 /var/www/html/goapp/vendor/laravel/framework/src/Illuminate/Mail/SendQueuedMailable.php(65): Illuminate\\Mail\\Mailable->send()\n#9 /var/www/html/goapp/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(36): Illuminate\\Mail\\SendQueuedMailable->handle()\n#10 /var/www/html/goapp/vendor/laravel/framework/src/Illuminate/Container/Util.php(40): Illuminate\\Container\\BoundMethod::{closure:Illuminate\\Container\\BoundMethod::call():35}()\n#11 /var/www/html/goapp/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(93): Illuminate\\Container\\Util::unwrapIfClosure()\n#12 /var/www/html/goapp/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod()\n#13 /var/www/html/goapp/vendor/laravel/framework/src/Illuminate/Container/Container.php(653): Illuminate\\Container\\BoundMethod::call()\n#14 /var/www/html/goapp/vendor/laravel/framework/src/Illuminate/Bus/Dispatcher.php(128): Illuminate\\Container\\Container->call()\n#15 /var/www/html/goapp/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(128): Illuminate\\Bus\\Dispatcher->{closure:Illuminate\\Bus\\Dispatcher::dispatchNow():125}()\n#16 /var/www/html/goapp/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(103): Illuminate\\Pipeline\\Pipeline->{closure:Illuminate\\Pipeline\\Pipeline::prepareDestination():126}()\n#17 /var/www/html/goapp/vendor/laravel/framework/src/Illuminate/Bus/Dispatcher.php(132): Illuminate\\Pipeline\\Pipeline->then()\n#18 /var/www/html/goapp/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(119): Illuminate\\Bus\\Dispatcher->dispatchNow()\n#19 /var/www/html/goapp/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(128): Illuminate\\Queue\\CallQueuedHandler->{closure:Illuminate\\Queue\\CallQueuedHandler::dispatchThroughMiddleware():118}()\n#20 /var/www/html/goapp/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(103): Illuminate\\Pipeline\\Pipeline->{closure:Illuminate\\Pipeline\\Pipeline::prepareDestination():126}()\n#21 /var/www/html/goapp/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(118): Illuminate\\Pipeline\\Pipeline->then()\n#22 /var/www/html/goapp/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(70): Illuminate\\Queue\\CallQueuedHandler->dispatchThroughMiddleware()\n#23 /var/www/html/goapp/vendor/laravel/framework/src/Illuminate/Queue/Jobs/Job.php(98): Illuminate\\Queue\\CallQueuedHandler->call()\n#24 /var/www/html/goapp/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(428): Illuminate\\Queue\\Jobs\\Job->fire()\n#25 /var/www/html/goapp/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(378): Illuminate\\Queue\\Worker->process()\n#26 /var/www/html/goapp/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(172): Illuminate\\Queue\\Worker->runJob()\n#27 /var/www/html/goapp/vendor/laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php(117): Illuminate\\Queue\\Worker->daemon()\n#28 /var/www/html/goapp/vendor/laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php(100): Illuminate\\Queue\\Console\\WorkCommand->runWorker()\n#29 /var/www/html/goapp/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(36): Illuminate\\Queue\\Console\\WorkCommand->handle()\n#30 /var/www/html/goapp/vendor/laravel/framework/src/Illuminate/Container/Util.php(40): Illuminate\\Container\\BoundMethod::{closure:Illuminate\\Container\\BoundMethod::call():35}()\n#31 /var/www/html/goapp/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(93): Illuminate\\Container\\Util::unwrapIfClosure()\n#32 /var/www/html/goapp/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod()\n#33 /var/www/html/goapp/vendor/laravel/framework/src/Illuminate/Container/Container.php(653): Illuminate\\Container\\BoundMethod::call()\n#34 /var/www/html/goapp/vendor/laravel/framework/src/Illuminate/Console/Command.php(136): Illuminate\\Container\\Container->call()\n#35 /var/www/html/goapp/vendor/symfony/console/Command/Command.php(298): Illuminate\\Console\\Command->execute()\n#36 /var/www/html/goapp/vendor/laravel/framework/src/Illuminate/Console/Command.php(120): Symfony\\Component\\Console\\Command\\Command->run()\n#37 /var/www/html/goapp/vendor/symfony/console/Application.php(1040): Illuminate\\Console\\Command->run()\n#38 /var/www/html/goapp/vendor/symfony/console/Application.php(301): Symfony\\Component\\Console\\Application->doRunCommand()\n#39 /var/www/html/goapp/vendor/symfony/console/Application.php(171): Symfony\\Component\\Console\\Application->doRun()\n#40 /var/www/html/goapp/vendor/laravel/framework/src/Illuminate/Console/Application.php(94): Symfony\\Component\\Console\\Application->run()\n#41 /var/www/html/goapp/vendor/laravel/framework/src/Illuminate/Foundation/Console/Kernel.php(129): Illuminate\\Console\\Application->run()\n#42 /var/www/html/goapp/artisan(35): Illuminate\\Foundation\\Console\\Kernel->handle()\n#43 {main}', '2025-08-29 07:59:49');

-- --------------------------------------------------------

--
-- Table structure for table `fields`
--

CREATE TABLE `fields` (
  `field_id` bigint UNSIGNED NOT NULL,
  `field_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_field_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `fields`
--

INSERT INTO `fields` (`field_id`, `field_name`, `product_field_name`, `created_at`, `updated_at`) VALUES
(1, 'Title', 'product_title', NULL, NULL),
(2, 'Type', 'product_type_name', NULL, NULL),
(3, 'Brand', 'brand_name', NULL, NULL),
(4, 'Tag', 'mtag_name', NULL, NULL),
(5, 'Price', 'price', NULL, NULL),
(6, 'Inventory stock', 'qty', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `field_query_relations`
--

CREATE TABLE `field_query_relations` (
  `field_id` bigint UNSIGNED NOT NULL,
  `query_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `field_query_relations`
--

INSERT INTO `field_query_relations` (`field_id`, `query_id`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, NULL),
(1, 2, NULL, NULL),
(1, 3, NULL, NULL),
(1, 4, NULL, NULL),
(1, 5, NULL, NULL),
(1, 6, NULL, NULL),
(2, 1, NULL, NULL),
(2, 2, NULL, NULL),
(2, 3, NULL, NULL),
(2, 4, NULL, NULL),
(2, 5, NULL, NULL),
(2, 6, NULL, NULL),
(3, 1, NULL, NULL),
(3, 2, NULL, NULL),
(3, 3, NULL, NULL),
(3, 4, NULL, NULL),
(3, 5, NULL, NULL),
(3, 6, NULL, NULL),
(5, 1, NULL, NULL),
(5, 2, NULL, NULL),
(5, 7, NULL, NULL),
(5, 8, NULL, NULL),
(6, 1, NULL, NULL),
(6, 7, NULL, NULL),
(6, 8, NULL, NULL),
(4, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `home_explore_deal_banners`
--

CREATE TABLE `home_explore_deal_banners` (
  `home_explore_deal_banner_id` bigint UNSIGNED NOT NULL,
  `home_explore_deal_banner_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `home_explore_deal_banner_image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `home_explore_deal_banner_position` int NOT NULL DEFAULT '0',
  `main_mcat_id` bigint UNSIGNED DEFAULT NULL,
  `mcat_id` bigint UNSIGNED DEFAULT NULL,
  `msubcat_id` bigint UNSIGNED DEFAULT NULL,
  `mproduct_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `home_explore_deal_banners`
--

INSERT INTO `home_explore_deal_banners` (`home_explore_deal_banner_id`, `home_explore_deal_banner_name`, `home_explore_deal_banner_image`, `home_explore_deal_banner_position`, `main_mcat_id`, `mcat_id`, `msubcat_id`, `mproduct_id`, `created_at`, `updated_at`) VALUES
(1, 'DEALS DEALS AND MORE DEALS', 'goapp/images/home_explore_deal_banner/home_explore_deal_banner_68526bc691bdd.png', 2, 2, 23, 36, 68, '2025-05-22 06:51:22', '2025-06-18 02:03:27'),
(2, 'SNACK DEALS', 'goapp/images/home_explore_deal_banner/home_explore_deal_banner_68526b8949967.png', 1, 2, 23, 36, 68, '2025-05-22 06:51:42', '2025-06-18 02:02:26'),
(4, 'SWEET DEALS', 'goapp/images/home_explore_deal_banner/home_explore_deal_banner_68526bd421ed4.png', 3, 2, 23, 36, 68, '2025-06-18 02:03:42', '2025-06-18 02:03:42'),
(5, 'MEMBER PRICES', 'goapp/images/home_explore_deal_banner/home_explore_deal_banner_68526c3f344e6.png', 4, 2, 23, 36, 68, '2025-06-18 02:05:28', '2025-06-18 02:05:28'),
(6, 'THE WEEKLY DEAL DROP', 'goapp/images/home_explore_deal_banner/home_explore_deal_banner_68526c716bbd6.png', 5, 2, 23, 36, 68, '2025-06-18 02:06:18', '2025-06-18 02:06:18'),
(7, 'STOCK UP & SAVE', 'goapp/images/home_explore_deal_banner/home_explore_deal_banner_68526c81ba347.png', 6, 2, 23, 36, 68, '2025-06-18 02:06:34', '2025-06-18 02:06:34');

-- --------------------------------------------------------

--
-- Table structure for table `home_fruit_banners`
--

CREATE TABLE `home_fruit_banners` (
  `home_fruit_banner_id` bigint UNSIGNED NOT NULL,
  `home_fruit_banner_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `home_fruit_banner_image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `home_fruit_banner_position` int NOT NULL DEFAULT '0',
  `main_mcat_id` bigint UNSIGNED DEFAULT NULL,
  `mcat_id` bigint UNSIGNED DEFAULT NULL,
  `msubcat_id` bigint UNSIGNED DEFAULT NULL,
  `mproduct_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `home_fruit_banners`
--

INSERT INTO `home_fruit_banners` (`home_fruit_banner_id`, `home_fruit_banner_name`, `home_fruit_banner_image`, `home_fruit_banner_position`, `main_mcat_id`, `mcat_id`, `msubcat_id`, `mproduct_id`, `created_at`, `updated_at`) VALUES
(9, 'Apple', 'goapp/images/home_fruit_banner/home_fruit_banner_68aff0976e4ac.png', 1, NULL, NULL, NULL, NULL, '2025-08-28 06:00:56', '2025-08-28 06:00:56'),
(10, 'Banana', 'goapp/images/home_fruit_banner/home_fruit_banner_68aff0a2bf16b.png', 2, NULL, NULL, NULL, NULL, '2025-08-28 06:01:07', '2025-08-28 06:01:07'),
(11, 'Berries', 'goapp/images/home_fruit_banner/home_fruit_banner_68aff0aca6d20.png', 3, NULL, NULL, NULL, NULL, '2025-08-28 06:01:17', '2025-08-28 06:01:17'),
(12, 'Citrus', 'goapp/images/home_fruit_banner/home_fruit_banner_68aff0b67a4b8.png', 4, NULL, NULL, NULL, NULL, '2025-08-28 06:01:27', '2025-08-28 06:01:27'),
(13, 'Extotic', 'goapp/images/home_fruit_banner/home_fruit_banner_68aff0c0d81fc.png', 5, NULL, NULL, NULL, NULL, '2025-08-28 06:01:37', '2025-08-28 06:01:37'),
(14, 'Fresh Fruit', 'goapp/images/home_fruit_banner/home_fruit_banner_68aff0cb993f6.png', 6, NULL, NULL, NULL, NULL, '2025-08-28 06:01:48', '2025-08-28 06:01:48');

-- --------------------------------------------------------

--
-- Table structure for table `home_large_banners`
--

CREATE TABLE `home_large_banners` (
  `home_large_banner_id` bigint UNSIGNED NOT NULL,
  `home_large_banner_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `home_large_banner_image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `home_large_banner_position` int NOT NULL DEFAULT '0',
  `main_mcat_id` bigint UNSIGNED DEFAULT NULL,
  `mcat_id` bigint UNSIGNED DEFAULT NULL,
  `msubcat_id` bigint UNSIGNED DEFAULT NULL,
  `mproduct_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `home_large_banners`
--

INSERT INTO `home_large_banners` (`home_large_banner_id`, `home_large_banner_name`, `home_large_banner_image`, `home_large_banner_position`, `main_mcat_id`, `mcat_id`, `msubcat_id`, `mproduct_id`, `created_at`, `updated_at`) VALUES
(1, 'DEW', 'goapp/images/home_large_banner/home_large_banner_685267fbc4e96.png', 3, 2, 23, 36, 68, '2025-05-22 07:05:21', '2025-08-30 12:47:23'),
(6, 'MTN DEW', 'goapp/images/home_large_banner/home_large_banner_6852683f8e672.png', 2, 2, 23, 36, 68, '2025-06-18 01:48:33', '2025-08-30 12:47:23'),
(8, 'ENJOY HAYATI', 'goapp/images/home_large_banner/home_large_banner_6852688301157.png', 4, 2, 23, 36, 68, '2025-06-18 01:49:33', '2025-08-30 12:47:23'),
(9, 'DXVA GO', 'goapp/images/home_large_banner/home_large_banner_685268969025c.png', 1, 2, 23, 37, 92, '2025-06-18 01:49:52', '2025-09-26 12:01:20'),
(10, 'HAYATI RUBIK', 'goapp/images/home_large_banner/home_large_banner_685268a9eb674.png', 5, 2, 23, 36, 68, '2025-06-18 01:50:14', '2025-08-30 12:47:23'),
(11, 'HAYATI PRO ULTRA +', 'goapp/images/home_large_banner/home_large_banner_685268d13e525.png', 6, 2, 23, 36, 68, '2025-06-18 01:50:52', '2025-08-30 12:47:23');

-- --------------------------------------------------------

--
-- Table structure for table `home_round_banners`
--

CREATE TABLE `home_round_banners` (
  `home_round_banner_id` bigint UNSIGNED NOT NULL,
  `home_round_banner_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `home_round_banner_image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `home_round_banner_position` int NOT NULL DEFAULT '0',
  `main_mcat_id` bigint UNSIGNED DEFAULT NULL,
  `mcat_id` bigint UNSIGNED DEFAULT NULL,
  `msubcat_id` bigint UNSIGNED DEFAULT NULL,
  `mproduct_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `home_round_banners`
--

INSERT INTO `home_round_banners` (`home_round_banner_id`, `home_round_banner_name`, `home_round_banner_image`, `home_round_banner_position`, `main_mcat_id`, `mcat_id`, `msubcat_id`, `mproduct_id`, `created_at`, `updated_at`) VALUES
(15, 'KitKat', 'goapp/images/home_round_banner/home_round_banner_68aff06ca272a.png', 6, NULL, NULL, NULL, NULL, '2025-08-28 06:00:13', '2025-08-30 10:02:34'),
(16, 'Takis', 'goapp/images/home_round_banner/home_round_banner_68aff08256d05.png', 4, NULL, NULL, NULL, NULL, '2025-08-28 06:00:34', '2025-08-30 10:02:34'),
(17, 'Sour Patch', 'goapp/images/home_round_banner/home_round_banner_68aff121983de.png', 1, NULL, NULL, NULL, NULL, '2025-08-28 06:03:14', '2025-08-30 10:02:34'),
(18, 'Chupa Chups', 'goapp/images/home_round_banner/home_round_banner_68aff12da86d7.png', 2, NULL, NULL, NULL, NULL, '2025-08-28 06:03:26', '2025-08-30 10:02:34'),
(19, 'Jolly Rancher', 'goapp/images/home_round_banner/home_round_banner_68aff139da1a7.png', 3, NULL, NULL, NULL, NULL, '2025-08-28 06:03:38', '2025-08-30 10:02:34'),
(20, 'Nerds', 'goapp/images/home_round_banner/home_round_banner_68aff142431c1.png', 5, NULL, NULL, NULL, NULL, '2025-08-28 06:03:46', '2025-08-30 10:02:34'),
(22, 'Hayat 6k', 'goapp/images/home_round_banner/home_round_banner_68c165d5e95e5.png', 7, 2, 29, 41, 92, '2025-09-10 11:49:43', '2025-09-10 11:49:43'),
(23, 'Lost Mary Nera', 'goapp/images/home_round_banner/home_round_banner_68c165f6e2201.png', 8, 2, 29, 43, 96, '2025-09-10 11:50:15', '2025-09-10 11:50:15'),
(24, 'Hayati 25k', 'goapp/images/home_round_banner/home_round_banner_68c1661fc3faa.png', 9, 2, 29, 41, 94, '2025-09-10 11:50:57', '2025-09-10 11:50:57');

-- --------------------------------------------------------

--
-- Table structure for table `home_small_banners`
--

CREATE TABLE `home_small_banners` (
  `home_small_banner_id` bigint UNSIGNED NOT NULL,
  `home_small_banner_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `home_small_banner_image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `home_small_banner_position` int NOT NULL DEFAULT '0',
  `main_mcat_id` bigint UNSIGNED DEFAULT NULL,
  `mcat_id` bigint UNSIGNED DEFAULT NULL,
  `msubcat_id` bigint UNSIGNED DEFAULT NULL,
  `mproduct_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `home_small_banners`
--

INSERT INTO `home_small_banners` (`home_small_banner_id`, `home_small_banner_name`, `home_small_banner_image`, `home_small_banner_position`, `main_mcat_id`, `mcat_id`, `msubcat_id`, `mproduct_id`, `created_at`, `updated_at`) VALUES
(5, 'HOP INTO SWEETNESS', 'goapp/images/home_small_banner/home_small_banner_6852693fa0aea.png', 1, 2, 23, 36, 68, '2025-06-18 01:52:42', '2025-09-04 14:06:44'),
(6, 'RIDE THE BAJA WAVE', 'goapp/images/home_small_banner/home_small_banner_6852695d2d895.png', 2, 2, 23, 36, 68, '2025-06-18 01:53:12', '2025-09-04 14:06:44'),
(7, 'TAKIS', 'goapp/images/home_small_banner/home_small_banner_68526972e4860.png', 3, 2, 23, 36, 68, '2025-06-18 01:53:33', '2025-09-04 14:06:44'),
(8, 'KIT KAT', 'goapp/images/home_small_banner/home_small_banner_68526981e20f0.png', 4, 2, 23, 36, 68, '2025-06-18 01:53:48', '2025-09-04 14:06:44');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`id`, `queue`, `payload`, `attempts`, `reserved_at`, `available_at`, `created_at`) VALUES
(2, 'default', '{\"uuid\":\"3937e164-81a5-4f26-b436-66e9462972a9\",\"displayName\":\"App\\\\Mail\\\\UserApprovalMail\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Mail\\\\SendQueuedMailable\",\"command\":\"O:34:\\\"Illuminate\\\\Mail\\\\SendQueuedMailable\\\":13:{s:8:\\\"mailable\\\";O:25:\\\"App\\\\Mail\\\\UserApprovalMail\\\":29:{s:4:\\\"user\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":4:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";i:14;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";}s:6:\\\"locale\\\";N;s:4:\\\"from\\\";a:0:{}s:2:\\\"to\\\";a:1:{i:0;a:2:{s:4:\\\"name\\\";N;s:7:\\\"address\\\";s:23:\\\"kplsharma8185@gmail.com\\\";}}s:2:\\\"cc\\\";a:0:{}s:3:\\\"bcc\\\";a:0:{}s:7:\\\"replyTo\\\";a:0:{}s:7:\\\"subject\\\";N;s:8:\\\"markdown\\\";N;s:7:\\\"\\u0000*\\u0000html\\\";N;s:4:\\\"view\\\";N;s:8:\\\"textView\\\";N;s:8:\\\"viewData\\\";a:0:{}s:11:\\\"attachments\\\";a:0:{}s:14:\\\"rawAttachments\\\";a:0:{}s:15:\\\"diskAttachments\\\";a:0:{}s:9:\\\"callbacks\\\";a:0:{}s:5:\\\"theme\\\";N;s:6:\\\"mailer\\\";s:4:\\\"smtp\\\";s:29:\\\"\\u0000*\\u0000assertionableRenderStrings\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:17:\\\"shouldBeEncrypted\\\";b:0;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}}\"}}', 0, NULL, 1756454991, 1756454991),
(3, 'default', '{\"uuid\":\"ae8e7f0f-1c9d-459c-bf46-98bc713d5730\",\"displayName\":\"App\\\\Mail\\\\UserApprovalMail\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Mail\\\\SendQueuedMailable\",\"command\":\"O:34:\\\"Illuminate\\\\Mail\\\\SendQueuedMailable\\\":13:{s:8:\\\"mailable\\\";O:25:\\\"App\\\\Mail\\\\UserApprovalMail\\\":29:{s:4:\\\"user\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":4:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";i:14;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";}s:6:\\\"locale\\\";N;s:4:\\\"from\\\";a:0:{}s:2:\\\"to\\\";a:1:{i:0;a:2:{s:4:\\\"name\\\";N;s:7:\\\"address\\\";s:23:\\\"kplsharma8185@gmail.com\\\";}}s:2:\\\"cc\\\";a:0:{}s:3:\\\"bcc\\\";a:0:{}s:7:\\\"replyTo\\\";a:0:{}s:7:\\\"subject\\\";N;s:8:\\\"markdown\\\";N;s:7:\\\"\\u0000*\\u0000html\\\";N;s:4:\\\"view\\\";N;s:8:\\\"textView\\\";N;s:8:\\\"viewData\\\";a:0:{}s:11:\\\"attachments\\\";a:0:{}s:14:\\\"rawAttachments\\\";a:0:{}s:15:\\\"diskAttachments\\\";a:0:{}s:9:\\\"callbacks\\\";a:0:{}s:5:\\\"theme\\\";N;s:6:\\\"mailer\\\";s:4:\\\"smtp\\\";s:29:\\\"\\u0000*\\u0000assertionableRenderStrings\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:17:\\\"shouldBeEncrypted\\\";b:0;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}}\"}}', 0, NULL, 1756461846, 1756461846),
(4, 'default', '{\"uuid\":\"c5b4946d-d0cd-4067-976e-532cae73e5ce\",\"displayName\":\"App\\\\Mail\\\\OrderPlacedMail\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Mail\\\\SendQueuedMailable\",\"command\":\"O:34:\\\"Illuminate\\\\Mail\\\\SendQueuedMailable\\\":13:{s:8:\\\"mailable\\\";O:24:\\\"App\\\\Mail\\\\OrderPlacedMail\\\":29:{s:5:\\\"order\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":4:{s:5:\\\"class\\\";s:16:\\\"App\\\\Models\\\\Order\\\";s:2:\\\"id\\\";i:140;s:9:\\\"relations\\\";a:2:{i:0;s:5:\\\"items\\\";i:1;s:4:\\\"user\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";}s:6:\\\"locale\\\";N;s:4:\\\"from\\\";a:0:{}s:2:\\\"to\\\";a:1:{i:0;a:2:{s:4:\\\"name\\\";N;s:7:\\\"address\\\";s:16:\\\"john@example.com\\\";}}s:2:\\\"cc\\\";a:0:{}s:3:\\\"bcc\\\";a:0:{}s:7:\\\"replyTo\\\";a:0:{}s:7:\\\"subject\\\";N;s:8:\\\"markdown\\\";N;s:7:\\\"\\u0000*\\u0000html\\\";N;s:4:\\\"view\\\";N;s:8:\\\"textView\\\";N;s:8:\\\"viewData\\\";a:0:{}s:11:\\\"attachments\\\";a:0:{}s:14:\\\"rawAttachments\\\";a:0:{}s:15:\\\"diskAttachments\\\";a:0:{}s:9:\\\"callbacks\\\";a:0:{}s:5:\\\"theme\\\";N;s:6:\\\"mailer\\\";s:4:\\\"smtp\\\";s:29:\\\"\\u0000*\\u0000assertionableRenderStrings\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:17:\\\"shouldBeEncrypted\\\";b:0;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}}\"}}', 0, NULL, 1756538685, 1756538685),
(5, 'default', '{\"uuid\":\"204b5bc5-d623-4c0d-b31c-48628db231a7\",\"displayName\":\"App\\\\Mail\\\\OrderPlacedMail\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Mail\\\\SendQueuedMailable\",\"command\":\"O:34:\\\"Illuminate\\\\Mail\\\\SendQueuedMailable\\\":13:{s:8:\\\"mailable\\\";O:24:\\\"App\\\\Mail\\\\OrderPlacedMail\\\":29:{s:5:\\\"order\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":4:{s:5:\\\"class\\\";s:16:\\\"App\\\\Models\\\\Order\\\";s:2:\\\"id\\\";i:141;s:9:\\\"relations\\\";a:2:{i:0;s:5:\\\"items\\\";i:1;s:4:\\\"user\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";}s:6:\\\"locale\\\";N;s:4:\\\"from\\\";a:0:{}s:2:\\\"to\\\";a:1:{i:0;a:2:{s:4:\\\"name\\\";N;s:7:\\\"address\\\";s:16:\\\"john@example.com\\\";}}s:2:\\\"cc\\\";a:0:{}s:3:\\\"bcc\\\";a:0:{}s:7:\\\"replyTo\\\";a:0:{}s:7:\\\"subject\\\";N;s:8:\\\"markdown\\\";N;s:7:\\\"\\u0000*\\u0000html\\\";N;s:4:\\\"view\\\";N;s:8:\\\"textView\\\";N;s:8:\\\"viewData\\\";a:0:{}s:11:\\\"attachments\\\";a:0:{}s:14:\\\"rawAttachments\\\";a:0:{}s:15:\\\"diskAttachments\\\";a:0:{}s:9:\\\"callbacks\\\";a:0:{}s:5:\\\"theme\\\";N;s:6:\\\"mailer\\\";s:4:\\\"smtp\\\";s:29:\\\"\\u0000*\\u0000assertionableRenderStrings\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:17:\\\"shouldBeEncrypted\\\";b:0;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}}\"}}', 0, NULL, 1756541943, 1756541943),
(6, 'default', '{\"uuid\":\"488d3dc3-a149-41bd-90a6-e5fe7ddb4d29\",\"displayName\":\"App\\\\Mail\\\\OrderPlacedMail\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Mail\\\\SendQueuedMailable\",\"command\":\"O:34:\\\"Illuminate\\\\Mail\\\\SendQueuedMailable\\\":13:{s:8:\\\"mailable\\\";O:24:\\\"App\\\\Mail\\\\OrderPlacedMail\\\":29:{s:5:\\\"order\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":4:{s:5:\\\"class\\\";s:16:\\\"App\\\\Models\\\\Order\\\";s:2:\\\"id\\\";i:142;s:9:\\\"relations\\\";a:2:{i:0;s:5:\\\"items\\\";i:1;s:4:\\\"user\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";}s:6:\\\"locale\\\";N;s:4:\\\"from\\\";a:0:{}s:2:\\\"to\\\";a:1:{i:0;a:2:{s:4:\\\"name\\\";N;s:7:\\\"address\\\";s:16:\\\"john@example.com\\\";}}s:2:\\\"cc\\\";a:0:{}s:3:\\\"bcc\\\";a:0:{}s:7:\\\"replyTo\\\";a:0:{}s:7:\\\"subject\\\";N;s:8:\\\"markdown\\\";N;s:7:\\\"\\u0000*\\u0000html\\\";N;s:4:\\\"view\\\";N;s:8:\\\"textView\\\";N;s:8:\\\"viewData\\\";a:0:{}s:11:\\\"attachments\\\";a:0:{}s:14:\\\"rawAttachments\\\";a:0:{}s:15:\\\"diskAttachments\\\";a:0:{}s:9:\\\"callbacks\\\";a:0:{}s:5:\\\"theme\\\";N;s:6:\\\"mailer\\\";s:4:\\\"smtp\\\";s:29:\\\"\\u0000*\\u0000assertionableRenderStrings\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:17:\\\"shouldBeEncrypted\\\";b:0;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}}\"}}', 0, NULL, 1756884967, 1756884967),
(7, 'default', '{\"uuid\":\"b0818f22-db5a-4b7e-9f96-77a4faec3fa6\",\"displayName\":\"App\\\\Mail\\\\OrderPlacedMail\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Mail\\\\SendQueuedMailable\",\"command\":\"O:34:\\\"Illuminate\\\\Mail\\\\SendQueuedMailable\\\":13:{s:8:\\\"mailable\\\";O:24:\\\"App\\\\Mail\\\\OrderPlacedMail\\\":29:{s:5:\\\"order\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":4:{s:5:\\\"class\\\";s:16:\\\"App\\\\Models\\\\Order\\\";s:2:\\\"id\\\";i:143;s:9:\\\"relations\\\";a:2:{i:0;s:5:\\\"items\\\";i:1;s:4:\\\"user\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";}s:6:\\\"locale\\\";N;s:4:\\\"from\\\";a:0:{}s:2:\\\"to\\\";a:1:{i:0;a:2:{s:4:\\\"name\\\";N;s:7:\\\"address\\\";s:17:\\\"john1@example.com\\\";}}s:2:\\\"cc\\\";a:0:{}s:3:\\\"bcc\\\";a:0:{}s:7:\\\"replyTo\\\";a:0:{}s:7:\\\"subject\\\";N;s:8:\\\"markdown\\\";N;s:7:\\\"\\u0000*\\u0000html\\\";N;s:4:\\\"view\\\";N;s:8:\\\"textView\\\";N;s:8:\\\"viewData\\\";a:0:{}s:11:\\\"attachments\\\";a:0:{}s:14:\\\"rawAttachments\\\";a:0:{}s:15:\\\"diskAttachments\\\";a:0:{}s:9:\\\"callbacks\\\";a:0:{}s:5:\\\"theme\\\";N;s:6:\\\"mailer\\\";s:4:\\\"smtp\\\";s:29:\\\"\\u0000*\\u0000assertionableRenderStrings\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:17:\\\"shouldBeEncrypted\\\";b:0;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}}\"}}', 0, NULL, 1756903855, 1756903855),
(8, 'default', '{\"uuid\":\"0b05c354-a579-4b64-b21f-c10da27f3355\",\"displayName\":\"App\\\\Mail\\\\OrderPlacedMail\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Mail\\\\SendQueuedMailable\",\"command\":\"O:34:\\\"Illuminate\\\\Mail\\\\SendQueuedMailable\\\":13:{s:8:\\\"mailable\\\";O:24:\\\"App\\\\Mail\\\\OrderPlacedMail\\\":29:{s:5:\\\"order\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":4:{s:5:\\\"class\\\";s:16:\\\"App\\\\Models\\\\Order\\\";s:2:\\\"id\\\";i:144;s:9:\\\"relations\\\";a:2:{i:0;s:5:\\\"items\\\";i:1;s:4:\\\"user\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";}s:6:\\\"locale\\\";N;s:4:\\\"from\\\";a:0:{}s:2:\\\"to\\\";a:1:{i:0;a:2:{s:4:\\\"name\\\";N;s:7:\\\"address\\\";s:16:\\\"john@example.com\\\";}}s:2:\\\"cc\\\";a:0:{}s:3:\\\"bcc\\\";a:0:{}s:7:\\\"replyTo\\\";a:0:{}s:7:\\\"subject\\\";N;s:8:\\\"markdown\\\";N;s:7:\\\"\\u0000*\\u0000html\\\";N;s:4:\\\"view\\\";N;s:8:\\\"textView\\\";N;s:8:\\\"viewData\\\";a:0:{}s:11:\\\"attachments\\\";a:0:{}s:14:\\\"rawAttachments\\\";a:0:{}s:15:\\\"diskAttachments\\\";a:0:{}s:9:\\\"callbacks\\\";a:0:{}s:5:\\\"theme\\\";N;s:6:\\\"mailer\\\";s:4:\\\"smtp\\\";s:29:\\\"\\u0000*\\u0000assertionableRenderStrings\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:17:\\\"shouldBeEncrypted\\\";b:0;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}}\"}}', 0, NULL, 1756992566, 1756992566),
(9, 'default', '{\"uuid\":\"81026faa-18d3-4dab-a34b-a827e96b8389\",\"displayName\":\"App\\\\Mail\\\\OrderPlacedMail\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Mail\\\\SendQueuedMailable\",\"command\":\"O:34:\\\"Illuminate\\\\Mail\\\\SendQueuedMailable\\\":13:{s:8:\\\"mailable\\\";O:24:\\\"App\\\\Mail\\\\OrderPlacedMail\\\":29:{s:5:\\\"order\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":4:{s:5:\\\"class\\\";s:16:\\\"App\\\\Models\\\\Order\\\";s:2:\\\"id\\\";i:145;s:9:\\\"relations\\\";a:2:{i:0;s:5:\\\"items\\\";i:1;s:4:\\\"user\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";}s:6:\\\"locale\\\";N;s:4:\\\"from\\\";a:0:{}s:2:\\\"to\\\";a:1:{i:0;a:2:{s:4:\\\"name\\\";N;s:7:\\\"address\\\";s:17:\\\"john1@example.com\\\";}}s:2:\\\"cc\\\";a:0:{}s:3:\\\"bcc\\\";a:0:{}s:7:\\\"replyTo\\\";a:0:{}s:7:\\\"subject\\\";N;s:8:\\\"markdown\\\";N;s:7:\\\"\\u0000*\\u0000html\\\";N;s:4:\\\"view\\\";N;s:8:\\\"textView\\\";N;s:8:\\\"viewData\\\";a:0:{}s:11:\\\"attachments\\\";a:0:{}s:14:\\\"rawAttachments\\\";a:0:{}s:15:\\\"diskAttachments\\\";a:0:{}s:9:\\\"callbacks\\\";a:0:{}s:5:\\\"theme\\\";N;s:6:\\\"mailer\\\";s:4:\\\"smtp\\\";s:29:\\\"\\u0000*\\u0000assertionableRenderStrings\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:17:\\\"shouldBeEncrypted\\\";b:0;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}}\"}}', 0, NULL, 1757075834, 1757075834),
(10, 'default', '{\"uuid\":\"4af2e1d4-edf2-47f9-83fa-653c606699af\",\"displayName\":\"App\\\\Mail\\\\OrderPlacedMail\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Mail\\\\SendQueuedMailable\",\"command\":\"O:34:\\\"Illuminate\\\\Mail\\\\SendQueuedMailable\\\":13:{s:8:\\\"mailable\\\";O:24:\\\"App\\\\Mail\\\\OrderPlacedMail\\\":29:{s:5:\\\"order\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":4:{s:5:\\\"class\\\";s:16:\\\"App\\\\Models\\\\Order\\\";s:2:\\\"id\\\";i:146;s:9:\\\"relations\\\";a:2:{i:0;s:5:\\\"items\\\";i:1;s:4:\\\"user\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";}s:6:\\\"locale\\\";N;s:4:\\\"from\\\";a:0:{}s:2:\\\"to\\\";a:1:{i:0;a:2:{s:4:\\\"name\\\";N;s:7:\\\"address\\\";s:16:\\\"john@example.com\\\";}}s:2:\\\"cc\\\";a:0:{}s:3:\\\"bcc\\\";a:0:{}s:7:\\\"replyTo\\\";a:0:{}s:7:\\\"subject\\\";N;s:8:\\\"markdown\\\";N;s:7:\\\"\\u0000*\\u0000html\\\";N;s:4:\\\"view\\\";N;s:8:\\\"textView\\\";N;s:8:\\\"viewData\\\";a:0:{}s:11:\\\"attachments\\\";a:0:{}s:14:\\\"rawAttachments\\\";a:0:{}s:15:\\\"diskAttachments\\\";a:0:{}s:9:\\\"callbacks\\\";a:0:{}s:5:\\\"theme\\\";N;s:6:\\\"mailer\\\";s:4:\\\"smtp\\\";s:29:\\\"\\u0000*\\u0000assertionableRenderStrings\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:17:\\\"shouldBeEncrypted\\\";b:0;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}}\"}}', 0, NULL, 1757251158, 1757251158),
(11, 'default', '{\"uuid\":\"8c426452-d38f-4dfc-bac4-b76e32f4cb9c\",\"displayName\":\"App\\\\Mail\\\\OrderPlacedMail\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Mail\\\\SendQueuedMailable\",\"command\":\"O:34:\\\"Illuminate\\\\Mail\\\\SendQueuedMailable\\\":13:{s:8:\\\"mailable\\\";O:24:\\\"App\\\\Mail\\\\OrderPlacedMail\\\":29:{s:5:\\\"order\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":4:{s:5:\\\"class\\\";s:16:\\\"App\\\\Models\\\\Order\\\";s:2:\\\"id\\\";i:147;s:9:\\\"relations\\\";a:2:{i:0;s:5:\\\"items\\\";i:1;s:4:\\\"user\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";}s:6:\\\"locale\\\";N;s:4:\\\"from\\\";a:0:{}s:2:\\\"to\\\";a:1:{i:0;a:2:{s:4:\\\"name\\\";N;s:7:\\\"address\\\";s:16:\\\"john@example.com\\\";}}s:2:\\\"cc\\\";a:0:{}s:3:\\\"bcc\\\";a:0:{}s:7:\\\"replyTo\\\";a:0:{}s:7:\\\"subject\\\";N;s:8:\\\"markdown\\\";N;s:7:\\\"\\u0000*\\u0000html\\\";N;s:4:\\\"view\\\";N;s:8:\\\"textView\\\";N;s:8:\\\"viewData\\\";a:0:{}s:11:\\\"attachments\\\";a:0:{}s:14:\\\"rawAttachments\\\";a:0:{}s:15:\\\"diskAttachments\\\";a:0:{}s:9:\\\"callbacks\\\";a:0:{}s:5:\\\"theme\\\";N;s:6:\\\"mailer\\\";s:4:\\\"smtp\\\";s:29:\\\"\\u0000*\\u0000assertionableRenderStrings\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:17:\\\"shouldBeEncrypted\\\";b:0;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}}\"}}', 0, NULL, 1757402043, 1757402043),
(12, 'default', '{\"uuid\":\"d80000d7-f9f4-4b6e-a3cd-57b3738c7c44\",\"displayName\":\"App\\\\Mail\\\\OrderPlacedMail\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Mail\\\\SendQueuedMailable\",\"command\":\"O:34:\\\"Illuminate\\\\Mail\\\\SendQueuedMailable\\\":13:{s:8:\\\"mailable\\\";O:24:\\\"App\\\\Mail\\\\OrderPlacedMail\\\":29:{s:5:\\\"order\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":4:{s:5:\\\"class\\\";s:16:\\\"App\\\\Models\\\\Order\\\";s:2:\\\"id\\\";i:148;s:9:\\\"relations\\\";a:2:{i:0;s:5:\\\"items\\\";i:1;s:4:\\\"user\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";}s:6:\\\"locale\\\";N;s:4:\\\"from\\\";a:0:{}s:2:\\\"to\\\";a:1:{i:0;a:2:{s:4:\\\"name\\\";N;s:7:\\\"address\\\";s:16:\\\"john@example.com\\\";}}s:2:\\\"cc\\\";a:0:{}s:3:\\\"bcc\\\";a:0:{}s:7:\\\"replyTo\\\";a:0:{}s:7:\\\"subject\\\";N;s:8:\\\"markdown\\\";N;s:7:\\\"\\u0000*\\u0000html\\\";N;s:4:\\\"view\\\";N;s:8:\\\"textView\\\";N;s:8:\\\"viewData\\\";a:0:{}s:11:\\\"attachments\\\";a:0:{}s:14:\\\"rawAttachments\\\";a:0:{}s:15:\\\"diskAttachments\\\";a:0:{}s:9:\\\"callbacks\\\";a:0:{}s:5:\\\"theme\\\";N;s:6:\\\"mailer\\\";s:4:\\\"smtp\\\";s:29:\\\"\\u0000*\\u0000assertionableRenderStrings\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:17:\\\"shouldBeEncrypted\\\";b:0;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}}\"}}', 0, NULL, 1757402191, 1757402191),
(13, 'default', '{\"uuid\":\"9e5e319f-cd4b-41f3-85a7-fe6f04970100\",\"displayName\":\"App\\\\Mail\\\\OrderPlacedMail\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Mail\\\\SendQueuedMailable\",\"command\":\"O:34:\\\"Illuminate\\\\Mail\\\\SendQueuedMailable\\\":13:{s:8:\\\"mailable\\\";O:24:\\\"App\\\\Mail\\\\OrderPlacedMail\\\":29:{s:5:\\\"order\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":4:{s:5:\\\"class\\\";s:16:\\\"App\\\\Models\\\\Order\\\";s:2:\\\"id\\\";i:149;s:9:\\\"relations\\\";a:2:{i:0;s:5:\\\"items\\\";i:1;s:4:\\\"user\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";}s:6:\\\"locale\\\";N;s:4:\\\"from\\\";a:0:{}s:2:\\\"to\\\";a:1:{i:0;a:2:{s:4:\\\"name\\\";N;s:7:\\\"address\\\";s:27:\\\"aalam.impactmindz@gmail.com\\\";}}s:2:\\\"cc\\\";a:0:{}s:3:\\\"bcc\\\";a:0:{}s:7:\\\"replyTo\\\";a:0:{}s:7:\\\"subject\\\";N;s:8:\\\"markdown\\\";N;s:7:\\\"\\u0000*\\u0000html\\\";N;s:4:\\\"view\\\";N;s:8:\\\"textView\\\";N;s:8:\\\"viewData\\\";a:0:{}s:11:\\\"attachments\\\";a:0:{}s:14:\\\"rawAttachments\\\";a:0:{}s:15:\\\"diskAttachments\\\";a:0:{}s:9:\\\"callbacks\\\";a:0:{}s:5:\\\"theme\\\";N;s:6:\\\"mailer\\\";s:4:\\\"smtp\\\";s:29:\\\"\\u0000*\\u0000assertionableRenderStrings\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:17:\\\"shouldBeEncrypted\\\";b:0;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}}\"}}', 0, NULL, 1757422903, 1757422903);

-- --------------------------------------------------------

--
-- Table structure for table `loyalty_reward_banners`
--

CREATE TABLE `loyalty_reward_banners` (
  `loyalty_reward_banner_id` bigint UNSIGNED NOT NULL,
  `loyalty_reward_banner_image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `loyalty_reward_banners`
--

INSERT INTO `loyalty_reward_banners` (`loyalty_reward_banner_id`, `loyalty_reward_banner_image`, `created_at`, `updated_at`) VALUES
(1, 'goapp/images/loyalty_reward/loyalty_reward_686cfccabbda7.png', '2025-07-08 11:11:07', '2025-07-08 11:11:07');

-- --------------------------------------------------------

--
-- Table structure for table `main_categories`
--

CREATE TABLE `main_categories` (
  `main_mcat_id` bigint UNSIGNED NOT NULL,
  `main_mcat_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `main_mcat_position` int NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `main_categories`
--

INSERT INTO `main_categories` (`main_mcat_id`, `main_mcat_name`, `main_mcat_position`, `status`, `created_at`, `updated_at`) VALUES
(1, 'DEALS & OFFERS', 1, 1, '2025-05-19 10:14:06', '2025-09-10 10:40:22'),
(2, 'VAPING PRODUCTS', 2, 1, '2025-05-19 10:14:14', '2025-09-10 10:40:22'),
(3, 'SODA & DRINKS', 3, 1, '2025-05-19 10:14:21', '2025-09-10 10:40:23'),
(4, 'SWEETS & CANDY', 4, 1, '2025-05-19 14:31:53', '2025-09-10 10:40:23'),
(5, 'SNACKS & CHIPS', 7, 1, '2025-05-19 19:10:55', '2025-09-10 10:40:23'),
(6, 'FOOD & GROCERIES', 6, 1, '2025-05-19 19:11:17', '2025-09-10 10:40:23'),
(7, 'Smoking PRODUCTS', 8, 1, '2025-05-19 19:11:46', '2025-09-10 10:40:23'),
(8, 'HOME & KITCHENWARE', 9, 1, '2025-05-19 19:14:05', '2025-09-10 10:40:23'),
(9, 'PET CARE PRODUCTS', 10, 1, '2025-05-19 19:14:27', '2025-09-10 10:40:23'),
(10, 'ELECTRONICS', 11, 1, '2025-05-19 19:15:48', '2025-09-10 10:40:23'),
(11, 'HEALTH & BEAUTY', 12, 1, '2025-05-19 19:16:04', '2025-09-10 10:40:23'),
(12, 'FASHION STORE', 13, 1, '2025-05-19 19:16:46', '2025-09-10 10:40:23'),
(13, 'Medical Equipments', 5, 1, '2025-08-30 11:57:57', '2025-09-10 10:40:23');

-- --------------------------------------------------------

--
-- Table structure for table `mbrands`
--

CREATE TABLE `mbrands` (
  `mbrand_id` bigint UNSIGNED NOT NULL,
  `mbrand_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mbrand_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mbrands`
--

INSERT INTO `mbrands` (`mbrand_id`, `mbrand_name`, `mbrand_image`, `created_at`, `updated_at`) VALUES
(27, 'Hayati', 'goapp/images/mbrands/mbrand_682bb47cec72a.png', '2025-05-12 13:46:58', '2025-05-19 22:45:17'),
(35, 'Crystal Prime', 'goapp/images/mbrands/mbrand_68abc2e938616.png', '2025-08-25 01:56:57', '2025-08-25 01:56:57'),
(36, 'IVG', 'goapp/images/mbrands/mbrand_68abc53b0a47e.png', '2025-08-25 02:06:52', '2025-08-25 02:06:52'),
(37, 'Honda', 'goapp/images/mbrands/mbrand_68b2ef81d2b6c.png', '2025-08-30 12:33:07', '2025-08-30 12:33:07');

-- --------------------------------------------------------

--
-- Table structure for table `mcategories`
--

CREATE TABLE `mcategories` (
  `mcat_id` bigint UNSIGNED NOT NULL,
  `mcat_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `main_mcat_id` bigint UNSIGNED DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mcategories`
--

INSERT INTO `mcategories` (`mcat_id`, `mcat_name`, `main_mcat_id`, `status`, `created_at`, `updated_at`) VALUES
(23, 'Pre-filled Pod Kits', 2, 1, '2025-05-19 22:21:05', '2025-05-19 22:21:05'),
(24, 'Nic Salts', 2, 1, '2025-05-19 22:22:01', '2025-05-19 22:22:01'),
(25, 'Vape Kits', 2, 1, '2025-05-19 22:22:26', '2025-05-19 22:22:26'),
(26, 'E-liquids', 2, 0, '2025-05-19 22:22:46', '2025-08-25 01:21:35'),
(27, 'Refill Pods', 2, 1, '2025-05-19 22:23:08', '2025-07-18 14:59:10'),
(28, 'Vape Coils', 2, 0, '2025-05-19 22:23:22', '2025-08-25 01:21:32'),
(29, 'Pre-filled Pods', 2, 1, '2025-08-25 00:55:19', '2025-08-25 00:55:19'),
(30, 'Nicotine Pouches', 2, 0, '2025-08-25 00:57:22', '2025-08-25 01:21:44'),
(31, 'Parle', 4, 1, '2025-08-30 09:53:53', '2025-08-30 11:59:37');

-- --------------------------------------------------------

--
-- Table structure for table `mcollection_autos`
--

CREATE TABLE `mcollection_autos` (
  `collection_auto_id` bigint UNSIGNED NOT NULL,
  `msubcat_id` bigint UNSIGNED NOT NULL,
  `field_id` bigint UNSIGNED NOT NULL,
  `query_id` bigint UNSIGNED NOT NULL,
  `value` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `logical_operator` enum('all','any') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'all',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mcollection_autos`
--

INSERT INTO `mcollection_autos` (`collection_auto_id`, `msubcat_id`, `field_id`, `query_id`, `value`, `logical_operator`, `created_at`, `updated_at`) VALUES
(50, 36, 1, 1, 'hayati pro max + 6000', 'all', '2025-08-25 01:31:55', '2025-08-25 01:31:55'),
(60, 37, 4, 1, '14', 'all', '2025-08-25 01:39:16', '2025-08-25 01:39:16'),
(61, 39, 1, 5, 'ivg max 10k', 'all', '2025-08-25 02:07:42', '2025-08-25 02:07:42'),
(69, 46, 4, 1, '5', 'all', '2025-08-30 13:32:04', '2025-08-30 13:32:04');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(28, '2014_10_12_000000_create_users_table', 11),
(29, '2014_10_12_100000_create_password_resets_table', 11),
(30, '2019_08_19_000000_create_failed_jobs_table', 11),
(31, '2019_12_14_000001_create_personal_access_tokens_table', 11),
(32, '2025_01_02_095736_create_admins_table', 11),
(45, '2025_01_29_121411_create_fields_table', 11),
(46, '2025_01_29_121428_create_queries_table', 11),
(47, '2025_01_29_121518_create_field_query_relations_table', 11),
(50, '2025_03_04_060142_create_mproduct_types_table', 12),
(51, '2025_03_04_060324_create_mbrands_table', 12),
(52, '2025_03_04_060418_create_mtags_table', 12),
(58, '2025_03_04_074450_create_moptions_table', 13),
(62, '2025_03_13_125605_create_mlocations_table', 14),
(67, '2025_03_13_125530_create_mproducts_table', 15),
(68, '2025_03_13_125543_create_mvariants_table', 15),
(69, '2025_03_13_125555_create_mvariant_details_table', 15),
(70, '2025_03_13_125612_create_mstocks_table', 16),
(74, '2025_04_21_104308_add_column_to_users_table', 18),
(75, '2025_04_22_063558_add_column_to_mbrands_table', 19),
(76, '2025_04_23_054512_create_mcategories_table', 20),
(85, '2025_04_23_054839_create_msubcategories_table', 21),
(86, '2025_04_25_064449_create_mcollection_autos_table', 21),
(88, '2025_04_29_124451_add_column_to_msubcategories_table', 22),
(93, '2025_05_02_095621_create_browsebanners_table', 25),
(94, '2025_05_05_071628_add_columns_to_msubcategories_table', 26),
(97, '2025_04_02_080546_add_columns_to_mproducts_table', 27),
(100, '2025_05_08_063654_create_product__offers_table', 28),
(101, '2025_05_12_060048_add_columns_to_users_table', 29),
(102, '2025_05_13_063330_create_user_company_addresses_table', 30),
(103, '2025_05_14_093414_add_logical_operator_to_msubcategories_table', 30),
(104, '2025_05_05_134256_create_wishlists_table', 31),
(105, '2025_05_19_060210_create_main_categories_table', 32),
(106, '2025_05_19_061431_add_main_mcat_id_to_mcategories_table', 32),
(107, '2025_05_19_080353_add_main_mcat_id_to_browsebanners_table', 32),
(108, '2025_05_20_071327_add_main_mcat_position_to_main_categories_table', 33),
(109, '2025_05_12_061644_create_home_large_banners_table', 34),
(110, '2025_05_12_061858_create_home_small_banners_table', 34),
(111, '2025_05_12_061932_create_home_explore_deal_banners_table', 34),
(112, '2025_05_12_061956_create_home_fruit_banners_table', 34),
(114, '2025_05_23_103508_create_home_round_banners_table', 36),
(115, '2025_05_07_063700_create_cart_items_table', 39),
(116, '2025_06_02_105859_create_delivery_methods_table', 38),
(118, '2025_06_03_055636_create_orders_table', 39),
(119, '2025_06_03_055652_create_order_items_table', 39),
(120, '2025_06_05_054644_create_coupons_table', 40),
(121, '2025_06_05_054720_create_coupon_usages_table', 40),
(122, '2025_06_05_070106_add_wallet_discount_to_orders_table', 40),
(123, '2025_06_06_072959_create_service_solutions_table', 41),
(124, '2025_06_23_054000_create_new_products_table', 42),
(125, '2025_06_23_054038_create_top_sellers_table', 42),
(126, '2025_06_24_061111_create_slider_headers_table', 42),
(129, '2025_06_25_131630_add_status_to_main_categories_table', 42),
(130, '2025_06_25_131733_add_status_to_mcategories_table', 42),
(131, '2025_06_25_131810_add_status_to_msubcategories_table', 42),
(133, '2025_07_03_074008_create_customers_table', 43),
(135, '2025_07_04_113636_update_users_table_replace_rep_code_with_rep_id', 44),
(136, '2025_07_08_071726_create_loyalty_reward_banners_table', 45),
(137, '2025_07_10_130528_create_settings_table', 46),
(138, '2025_07_14_112459_create_wallets_table', 47),
(139, '2025_07_14_112508_create_wallet_transactions_table', 47),
(141, '2025_07_21_104934_add_coupon_id_to_orders_table', 49),
(143, '2025_07_24_105050_add_main_mcat_id_to_coupons_table', 51),
(144, '2025_07_25_074149_add_is_active__to_settings_table', 51),
(145, '2025_07_30_095208_add_total_paid_to_orders_table', 52),
(146, '2025_07_17_062101_create_bank_details_table', 53),
(147, '2025_07_18_095912_create_customer_commissions_table', 53),
(148, '2025_07_18_100247_create_order_commissions_table', 53),
(149, '2025_08_02_061049_add_fulfilled_quantity_to_order_items', 53),
(150, '2025_08_29_064631_create_jobs_table', 54),
(151, '2025_09_02_133710_create_order_fulfillments_table', 55),
(152, '2025_09_02_133731_create_order_fulfillment_items_table', 55),
(153, '2025_09_09_074601_add_pay_by_bank_to_orders_table', 56),
(154, '2025_09_09_095757_create_product_vats_table', 56),
(155, '2025_09_13_055648_create_referrals_table', 57),
(156, '2025_09_13_055931_add_referred_by_to_users_table', 57),
(157, '2025_09_13_072939_create_referral_invites_table', 57),
(158, '2025_09_18_071241_add_clickdrop_columns_to_orders_table', 57),
(159, '2025_08_21_124920_create_user_tags_table', 58),
(160, '2025_09_25_131842_create_user_tag_prices_table', 58),
(161, '2025_08_22_110324_add_user_tag_id_to_users_table', 59);

-- --------------------------------------------------------

--
-- Table structure for table `mlocations`
--

CREATE TABLE `mlocations` (
  `mlocation_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'default',
  `adresss` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'default location',
  `is_default` enum('true','false') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'false',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mlocations`
--

INSERT INTO `mlocations` (`mlocation_id`, `name`, `adresss`, `is_default`, `created_at`, `updated_at`) VALUES
(1, 'default', 'default location', 'true', '2025-03-13 07:48:05', '2025-03-13 07:48:05');

-- --------------------------------------------------------

--
-- Table structure for table `moptions`
--

CREATE TABLE `moptions` (
  `moption_id` bigint UNSIGNED NOT NULL,
  `moption_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `moptions`
--

INSERT INTO `moptions` (`moption_id`, `moption_name`, `created_at`, `updated_at`) VALUES
(1, 'Colour', NULL, '2025-05-12 08:37:27'),
(3, 'Flavour', NULL, NULL),
(4, 'Weight', '2025-04-02 05:39:11', '2025-04-02 05:39:11'),
(7, 'Strength', '2025-05-20 11:53:52', '2025-05-20 11:53:52'),
(8, 'Size', '2025-08-30 12:31:16', '2025-08-30 12:31:16');

-- --------------------------------------------------------

--
-- Table structure for table `mproducts`
--

CREATE TABLE `mproducts` (
  `mproduct_id` bigint UNSIGNED NOT NULL,
  `mproduct_title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mproduct_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mproduct_slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mproduct_desc` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('Draft','Active') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Draft',
  `mproduct_type_id` bigint UNSIGNED DEFAULT NULL,
  `mbrand_id` bigint UNSIGNED DEFAULT NULL,
  `mtags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `saleschannel` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ;

--
-- Dumping data for table `mproducts`
--

INSERT INTO `mproducts` (`mproduct_id`, `mproduct_title`, `mproduct_image`, `mproduct_slug`, `mproduct_desc`, `status`, `mproduct_type_id`, `mbrand_id`, `mtags`, `saleschannel`, `created_at`, `updated_at`) VALUES
(92, 'Hayati Pro Ultra + 25k', 'goapp/images/mproduct/mproduct_68abb930d2ee3.png', 'hayati-pro-ultra-25k-68b838a6bf178', '', 'Active', 7, 27, '[11,12,14,16]', '[\"Online Store\"]', '2025-08-25 01:13:14', '2025-10-06 21:07:42'),
(93, 'Hayati Rubik 7000', 'goapp/images/mproduct/mproduct_68abba778bfa2.png', 'hayati-rubik-7000-68b2d96cb325f', '', 'Draft', 7, 27, '[11,12]', '[\"Online Store\"]', '2025-08-25 01:20:56', '2025-08-30 10:58:52'),
(94, 'Hayati Pro Max + 6k Pods', 'goapp/images/mproduct/mproduct_68abbbc4ecbbb.png', 'hayati-pro-max-6k-pods-68ac152fcc742', '', 'Active', 7, 27, '[11,13,15]', '[\"Online Store\"]', '2025-08-25 01:25:17', '2025-09-09 08:35:54'),
(95, 'Hayati Pro Utra + 25k Pods', 'goapp/images/mproduct/mproduct_68abbca5243a4.png', 'hayati-pro-utra-25k-pods-68ac153466f9d', '', 'Active', 7, 27, '[11,13]', '[\"Online Store\"]', '2025-08-25 01:30:13', '2025-08-25 07:48:04'),
(96, 'Crystal Prime Aura Bar 10k', 'goapp/images/mproduct/mproduct_68abc13bcdd67.png', 'crystal-prime-aura-bar-10k-68b2b54abdf5e', '', 'Active', 5, 35, '[11,12]', '[\"Online Store\"]', '2025-08-25 01:49:49', '2025-08-30 08:24:42'),
(97, 'IVG Max 10k', 'goapp/images/mproduct/mproduct_68abc524bd545.png', 'ivg-max-10k-68b2d31be5f02', 'this is very nice product', 'Draft', 6, 36, '[8,12]', '[\"Online Store\"]', '2025-08-25 02:06:29', '2025-08-30 10:31:55');

-- --------------------------------------------------------

--
-- Table structure for table `mproduct_types`
--

CREATE TABLE `mproduct_types` (
  `mproduct_type_id` bigint UNSIGNED NOT NULL,
  `mproduct_type_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mproduct_types`
--

INSERT INTO `mproduct_types` (`mproduct_type_id`, `mproduct_type_name`, `created_at`, `updated_at`) VALUES
(1, 'truewebapp', '2025-05-01 09:09:09', '2025-05-01 09:09:09'),
(2, 'reg', '2025-05-06 06:03:40', '2025-05-06 06:03:40'),
(3, 'Others', '2025-05-12 13:45:59', '2025-05-12 13:45:59'),
(4, 'Batteries', '2025-05-12 16:19:30', '2025-05-12 16:19:30'),
(5, 'Deals & Offers', '2025-05-13 10:34:05', '2025-05-13 10:34:05'),
(6, 'American Candy', '2025-05-13 10:38:31', '2025-05-13 10:38:31'),
(7, 'Vaping', '2025-05-16 16:36:14', '2025-05-16 16:36:14');

-- --------------------------------------------------------

--
-- Table structure for table `mstocks`
--

CREATE TABLE `mstocks` (
  `mstock_id` bigint UNSIGNED NOT NULL,
  `quantity` bigint DEFAULT NULL,
  `mlocation_id` bigint UNSIGNED NOT NULL,
  `mvariant_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mstocks`
--

INSERT INTO `mstocks` (`mstock_id`, `quantity`, `mlocation_id`, `mvariant_id`, `created_at`, `updated_at`) VALUES
(476, 52, 1, 476, '2025-08-25 01:13:14', '2025-09-26 12:20:59'),
(477, 65, 1, 477, '2025-08-25 01:13:14', '2025-09-10 13:14:47'),
(478, 83, 1, 478, '2025-08-25 01:13:14', '2025-09-10 13:14:48'),
(479, 87, 1, 479, '2025-08-25 01:13:14', '2025-09-10 13:14:48'),
(480, 96, 1, 480, '2025-08-25 01:13:14', '2025-09-05 12:37:13'),
(481, 95, 1, 481, '2025-08-25 01:13:15', '2025-09-05 12:37:13'),
(482, 89, 1, 482, '2025-08-25 01:13:15', '2025-09-10 17:25:34'),
(483, 100, 1, 483, '2025-08-25 01:20:56', '2025-08-25 01:20:56'),
(484, 100, 1, 484, '2025-08-25 01:20:56', '2025-08-25 01:20:56'),
(485, 100, 1, 485, '2025-08-25 01:20:56', '2025-08-25 01:20:56'),
(486, 100, 1, 486, '2025-08-25 01:20:57', '2025-08-25 01:20:57'),
(487, 100, 1, 487, '2025-08-25 01:20:57', '2025-08-25 01:20:57'),
(488, 100, 1, 488, '2025-08-25 01:20:57', '2025-08-25 01:20:57'),
(489, 97, 1, 489, '2025-08-25 01:25:17', '2025-08-25 02:20:15'),
(490, 97, 1, 490, '2025-08-25 01:25:17', '2025-08-25 02:20:15'),
(491, 98, 1, 491, '2025-08-25 01:25:17', '2025-09-07 13:19:18'),
(492, 98, 1, 492, '2025-08-25 01:25:17', '2025-09-07 13:19:18'),
(493, 96, 1, 493, '2025-08-25 01:30:14', '2025-09-10 13:14:48'),
(494, 97, 1, 494, '2025-08-25 01:30:14', '2025-09-04 13:29:26'),
(495, 96, 1, 495, '2025-08-25 01:30:14', '2025-09-07 13:19:18'),
(496, 95, 1, 496, '2025-08-25 01:30:14', '2025-09-07 13:19:18'),
(497, 100, 1, 497, '2025-08-25 01:49:49', '2025-08-25 01:49:49'),
(498, 99, 1, 498, '2025-08-25 01:49:50', '2025-08-25 02:20:15'),
(499, 99, 1, 499, '2025-08-25 01:49:51', '2025-08-25 02:20:15'),
(500, 98, 1, 500, '2025-08-25 01:49:51', '2025-09-10 13:14:48'),
(554, 2, 1, 554, '2025-08-30 10:28:09', '2025-08-30 10:28:09'),
(555, 2, 1, 555, '2025-08-30 10:28:09', '2025-08-30 10:28:09'),
(556, 2, 1, 556, '2025-08-30 10:28:09', '2025-08-30 10:28:09'),
(557, 2, 1, 557, '2025-08-30 10:28:09', '2025-08-30 10:28:09');

-- --------------------------------------------------------

--
-- Table structure for table `msubcategories`
--

CREATE TABLE `msubcategories` (
  `msubcat_id` bigint UNSIGNED NOT NULL,
  `mcat_id` bigint UNSIGNED NOT NULL,
  `msubcat_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `msubcat_slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `msubcat_tag` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `msubcat_image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `msubcat_publish` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `offer_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `msubcat_type` enum('manual','smart') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'manual',
  `logical_operator` enum('all','any') COLLATE utf8mb4_unicode_ci DEFAULT 'all',
  `product_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ;

--
-- Dumping data for table `msubcategories`
--

INSERT INTO `msubcategories` (`msubcat_id`, `mcat_id`, `msubcat_name`, `msubcat_slug`, `msubcat_tag`, `msubcat_image`, `status`, `msubcat_publish`, `offer_name`, `start_time`, `end_time`, `msubcat_type`, `logical_operator`, `product_ids`, `created_at`, `updated_at`) VALUES
(36, 23, 'Hayati Pro Max + 6000', 'hayati-pro-max-6000-682bb03e63619', 'TPD, Hot', 'goapp/images/msub-categories/msubcat_682bb03da4fd6.png', 0, '[\"Online Store\",\"Other\"]', NULL, NULL, NULL, 'smart', 'all', '[]', '2025-05-19 22:27:10', '2025-09-04 04:54:39'),
(37, 23, 'Hayati Pro Ultra + 25000', 'hayati-pro-ultra-25000-682bb06d021cb', NULL, 'goapp/images/msub-categories/msubcat_682bb06c4b8d0.png', 1, '[\"Online Store\",\"Other\"]', NULL, NULL, NULL, 'smart', 'all', '[]', '2025-05-19 22:27:57', '2025-08-25 01:39:03'),
(38, 23, 'Crystal Prime 10000', 'crystal-prime-10000-682bb0a07114d', NULL, 'goapp/images/msub-categories/msubcat_68abc31d4fe0b.png', 1, '[\"Online Store\",\"Other\"]', NULL, NULL, NULL, 'smart', 'all', '[]', '2025-05-19 22:28:48', '2025-08-25 01:57:49'),
(39, 23, 'IVG Max 10k', 'ivg-max-10k-682bb0ce9f7d3', NULL, 'goapp/images/msub-categories/msubcat_682bb0cdca387.png', 1, '[\"Online Store\",\"Other\"]', NULL, NULL, NULL, 'smart', 'all', '[]', '2025-05-19 22:29:34', '2025-05-19 22:29:34'),
(41, 29, 'Hayati Pro Max + 6k Pods', 'hayati-pro-max-6000-pods-68abc6037ad33', 'Hot', 'goapp/images/msub-categories/msubcat_68abc602bcfb3.png', 1, '[\"Online Store\"]', NULL, NULL, NULL, 'manual', NULL, '[94]', '2025-08-25 02:10:11', '2025-09-11 08:36:22'),
(42, 29, 'Hayati Pro Ultra + 25k Pods', 'hayati-pro-ultra-25k-pods-68abc645d4f17', 'Hot', 'goapp/images/msub-categories/msubcat_68abc64525c71.png', 1, '[\"Online Store\"]', NULL, NULL, NULL, 'manual', NULL, '[95]', '2025-08-25 02:11:17', '2025-08-25 02:11:17'),
(43, 29, 'Lost Mary Nera 30k', 'lost-mary-nera-30k-68b2d26e5adc4', NULL, 'goapp/images/msub-categories/msubcat_68b2d26da0775.png', 1, '[]', NULL, NULL, NULL, 'manual', NULL, '[92,93,94,95]', '2025-08-30 10:29:02', '2025-08-30 10:29:02'),
(46, 27, 'Bone saw', 'bone-saw-68b2fd2ce11b3', 'prefilled kit', 'goapp/images/msub-categories/msubcat_68b2fd2b3b1ea.png', 1, '[\"Online Store\"]', NULL, NULL, NULL, 'smart', 'all', '[]', '2025-08-30 13:31:24', '2025-08-30 13:31:24');

-- --------------------------------------------------------

--
-- Table structure for table `mtags`
--

CREATE TABLE `mtags` (
  `mtag_id` bigint UNSIGNED NOT NULL,
  `mtag_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mtags`
--

INSERT INTO `mtags` (`mtag_id`, `mtag_name`, `created_at`, `updated_at`) VALUES
(1, 'truewebapp', NULL, NULL),
(2, 'battery', '2025-05-12 16:19:47', '2025-05-12 16:19:47'),
(3, '20', '2025-05-13 10:34:18', '2025-05-13 10:34:18'),
(4, 'american candy', '2025-05-13 10:53:16', '2025-05-13 10:53:16'),
(5, 'Hayati Pro Max + 6000', '2025-05-13 10:59:22', '2025-05-13 10:59:22'),
(6, 'Nic Salt', '2025-05-16 16:35:57', '2025-05-16 16:35:57'),
(7, 'Pod Kit', '2025-05-16 17:02:58', '2025-05-16 17:02:58'),
(8, 'rony', '2025-05-16 17:05:23', '2025-05-16 17:05:23'),
(9, 'div', '2025-05-16 17:05:42', '2025-05-16 17:05:42'),
(10, 'test now', '2025-05-19 14:31:36', '2025-05-19 14:31:36'),
(11, 'new', '2025-05-24 19:30:00', '2025-05-24 19:30:00'),
(12, 'prefilled kit', '2025-07-24 00:58:21', '2025-07-24 00:58:21'),
(13, 'prefilled pods', '2025-08-25 01:23:12', '2025-08-25 01:23:12'),
(14, 'hayati pro ultra + 25k', '2025-08-25 01:36:34', '2025-08-25 01:36:34'),
(15, 'Clearance', '2025-09-09 08:35:51', '2025-09-09 08:35:51'),
(16, 'MCRVAPDISTRO5', '2025-10-06 21:07:39', '2025-10-06 21:07:39');

-- --------------------------------------------------------

--
-- Table structure for table `mvariants`
--

CREATE TABLE `mvariants` (
  `mvariant_id` bigint UNSIGNED NOT NULL,
  `sku` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mvariant_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` double(8,2) DEFAULT NULL,
  `compare_price` double(8,2) DEFAULT NULL,
  `cost_price` double(8,2) DEFAULT NULL,
  `taxable` tinyint NOT NULL DEFAULT '0',
  `barcode` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `weight` double(8,2) DEFAULT NULL,
  `weightunit` enum('kg','g') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'kg',
  `isvalidatedetails` tinyint NOT NULL DEFAULT '0',
  `mproduct_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mvariants`
--

INSERT INTO `mvariants` (`mvariant_id`, `sku`, `mvariant_image`, `price`, `compare_price`, `cost_price`, `taxable`, `barcode`, `weight`, `weightunit`, `isvalidatedetails`, `mproduct_id`, `created_at`, `updated_at`) VALUES
(476, 'E84OT', 'goapp/images/mvproduct/mvproduct_68abb93180345.png', 39.99, 0.00, 0.00, 1, '', 0.00, 'kg', 1, 92, '2025-08-25 01:13:14', '2025-08-25 01:15:29'),
(477, 'E84OT-1', 'goapp/images/mvproduct/mvproduct_68abb931bcb59.png', 39.99, 0.00, 0.00, 1, '', 0.00, 'kg', 1, 92, '2025-08-25 01:13:14', '2025-08-25 01:15:30'),
(478, 'E84OT-2', 'goapp/images/mvproduct/mvproduct_68abb93204e50.png', 39.99, 0.00, 0.00, 1, '', 0.00, 'kg', 1, 92, '2025-08-25 01:13:14', '2025-08-25 01:15:30'),
(479, 'E84OT-3', 'goapp/images/mvproduct/mvproduct_68abb932426ef.png', 39.99, 0.00, 0.00, 1, '', 0.00, 'kg', 1, 92, '2025-08-25 01:13:14', '2025-08-25 01:15:30'),
(480, 'E84OT-4', 'goapp/images/mvproduct/mvproduct_68abb9327fe4d.png', 39.99, 0.00, 0.00, 1, '', 0.00, 'kg', 1, 92, '2025-08-25 01:13:14', '2025-08-25 01:15:30'),
(481, 'E84OT-5', 'goapp/images/mvproduct/mvproduct_68abb932bbbbc.png', 39.99, 0.00, 0.00, 1, '', 0.00, 'kg', 1, 92, '2025-08-25 01:13:14', '2025-08-25 01:15:31'),
(482, 'E84OT-6', 'goapp/images/mvproduct/mvproduct_68abb933035fb.png', 39.99, 0.00, 0.00, 1, '', 0.00, 'kg', 1, 92, '2025-08-25 01:13:15', '2025-08-25 01:15:31'),
(483, '30U99', 'goapp/images/mvproduct/mvproduct_68abba782fb88.png', 24.99, 0.00, 0.00, 1, '', 0.00, 'kg', 1, 93, '2025-08-25 01:20:56', '2025-08-25 01:20:56'),
(484, '30U99-1', 'goapp/images/mvproduct/mvproduct_68abba786b89c.png', 24.99, 0.00, 0.00, 1, '', 0.00, 'kg', 1, 93, '2025-08-25 01:20:56', '2025-08-25 01:20:56'),
(485, '30U99-2', 'goapp/images/mvproduct/mvproduct_68abba78a5ee4.png', 24.99, 0.00, 0.00, 1, '', 0.00, 'kg', 1, 93, '2025-08-25 01:20:56', '2025-08-25 01:20:56'),
(486, '30U99-3', 'goapp/images/mvproduct/mvproduct_68abba78e16e4.png', 24.99, 0.00, 0.00, 1, '', 0.00, 'kg', 1, 93, '2025-08-25 01:20:57', '2025-08-25 01:20:57'),
(487, '30U99-4', 'goapp/images/mvproduct/mvproduct_68abba7927912.png', 24.99, 0.00, 0.00, 1, '', 0.00, 'kg', 1, 93, '2025-08-25 01:20:57', '2025-08-25 01:20:57'),
(488, '30U99-5', 'goapp/images/mvproduct/mvproduct_68abba7962a41.png', 24.99, 0.00, 0.00, 1, '', 0.00, 'kg', 1, 93, '2025-08-25 01:20:57', '2025-08-25 01:20:57'),
(489, 'M724D', 'goapp/images/mvproduct/mvproduct_68abbbc5a8eaa.png', 19.99, 0.00, 0.00, 1, '', 0.00, 'kg', 1, 94, '2025-08-25 01:25:17', '2025-08-25 01:26:29'),
(490, 'M724D-1', 'goapp/images/mvproduct/mvproduct_68abbbc5e6a29.png', 19.99, 0.00, 0.00, 1, '', 0.00, 'kg', 1, 94, '2025-08-25 01:25:17', '2025-08-25 01:26:30'),
(491, 'M724D-2', 'goapp/images/mvproduct/mvproduct_68abbbc634836.png', 19.99, 0.00, 0.00, 1, '', 0.00, 'kg', 1, 94, '2025-08-25 01:25:17', '2025-08-25 01:26:30'),
(492, 'M724D-3', 'goapp/images/mvproduct/mvproduct_68abbbc678bb4.png', 19.99, 0.00, 0.00, 1, '', 0.00, 'kg', 1, 94, '2025-08-25 01:25:17', '2025-08-25 01:26:30'),
(493, 'QFKH5', 'goapp/images/mvproduct/mvproduct_68abbca5c8eeb.png', 23.99, 0.00, 0.00, 1, '', 0.00, 'kg', 1, 95, '2025-08-25 01:30:14', '2025-08-25 01:30:14'),
(494, 'QFKH5-1', 'goapp/images/mvproduct/mvproduct_68abbca61e9d5.png', 23.99, 0.00, 0.00, 1, '', 0.00, 'kg', 1, 95, '2025-08-25 01:30:14', '2025-08-25 01:30:14'),
(495, 'QFKH5-2', 'goapp/images/mvproduct/mvproduct_68abbca6654a8.png', 23.99, 0.00, 0.00, 1, '', 0.00, 'kg', 1, 95, '2025-08-25 01:30:14', '2025-08-25 01:30:14'),
(496, 'QFKH5-3', 'goapp/images/mvproduct/mvproduct_68abbca6ac0aa.png', 23.99, 0.00, 0.00, 1, '', 0.00, 'kg', 1, 95, '2025-08-25 01:30:14', '2025-08-25 01:30:14'),
(497, 'B87J4', 'goapp/images/mvproduct/mvproduct_68abc13d162fa.png', 22.99, 0.00, 0.00, 1, '', 0.00, 'kg', 1, 96, '2025-08-25 01:49:49', '2025-08-25 01:49:49'),
(498, 'B87J4-1', 'goapp/images/mvproduct/mvproduct_68abc13dbde55.png', 22.99, 0.00, 0.00, 1, '', 0.00, 'kg', 1, 96, '2025-08-25 01:49:50', '2025-08-25 01:49:50'),
(499, 'B87J4-2', 'goapp/images/mvproduct/mvproduct_68abc13e76c47.png', 22.99, 0.00, 0.00, 1, '', 0.00, 'kg', 1, 96, '2025-08-25 01:49:51', '2025-08-25 01:49:51'),
(500, 'B87J4-3', 'goapp/images/mvproduct/mvproduct_68abc13f333b1.png', 22.99, 0.00, 0.00, 1, '', 0.00, 'kg', 1, 96, '2025-08-25 01:49:51', '2025-08-25 01:49:51'),
(554, 'JVPPA-50', NULL, 25.00, 30.00, 0.00, 1, '123', 0.00, 'kg', 1, 97, '2025-08-30 10:28:09', '2025-08-30 10:28:09'),
(555, 'JVPPA-59', NULL, 20.00, 25.00, 0.00, 1, '123', 0.00, 'kg', 1, 97, '2025-08-30 10:28:09', '2025-08-30 10:28:09'),
(556, 'JVPPA-60', NULL, 30.00, 35.00, 0.00, 1, '1234', 0.00, 'kg', 1, 97, '2025-08-30 10:28:09', '2025-08-30 10:28:09'),
(557, 'JVPPA-61', NULL, 25.00, 30.00, 0.00, 1, '12345', 0.00, 'kg', 1, 97, '2025-08-30 10:28:09', '2025-08-30 10:28:09');

-- --------------------------------------------------------

--
-- Table structure for table `mvariant_details`
--

CREATE TABLE `mvariant_details` (
  `mvariant_detail_id` bigint UNSIGNED NOT NULL,
  `options` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `option_value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `mvariant_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ;

--
-- Dumping data for table `mvariant_details`
--

INSERT INTO `mvariant_details` (`mvariant_detail_id`, `options`, `option_value`, `mvariant_id`, `created_at`, `updated_at`) VALUES
(476, '[\"Flavour\"]', '{\"Flavour\":\"Blue Razz Cherry\"}', 476, '2025-08-25 01:13:14', '2025-09-03 12:46:30'),
(477, '[\"Flavour\"]', '{\"Flavour\":\"Blue Razz Gb\"}', 477, '2025-08-25 01:13:14', '2025-09-03 12:46:30'),
(478, '[\"Flavour\"]', '{\"Flavour\":\"Blue Razz Pineapple\\/strawberry Ice\"}', 478, '2025-08-25 01:13:14', '2025-09-03 12:46:30'),
(479, '[\"Flavour\"]', '{\"Flavour\":\"Blueberry Cotton K\\/raspberry Cotton K\"}', 479, '2025-08-25 01:13:14', '2025-09-03 12:46:30'),
(480, '[\"Flavour\"]', '{\"Flavour\":\"Blueberry Hubba Bubba\\/watermelon Hubba Bubba\"}', 480, '2025-08-25 01:13:14', '2025-09-03 12:46:30'),
(481, '[\"Flavour\"]', '{\"Flavour\":\"Blueberry Raspberry\"}', 481, '2025-08-25 01:13:15', '2025-09-03 12:46:30'),
(482, '[\"Flavour\"]', '{\"Flavour\":\"Blueberry Straw Menthol\\/blueberry Raspberry Menthol\"}', 482, '2025-08-25 01:13:15', '2025-09-03 12:46:30'),
(483, '[\"Flavour\"]', '{\"Flavour\":\"Banana Ice\"}', 483, '2025-08-25 01:20:56', '2025-08-30 10:58:52'),
(484, '[\"Flavour\"]', '{\"Flavour\":\"Blue Razz Cherry\"}', 484, '2025-08-25 01:20:56', '2025-08-30 10:58:52'),
(485, '[\"Flavour\"]', '{\"Flavour\":\"Blue Razz Gummy Bear\"}', 485, '2025-08-25 01:20:56', '2025-08-30 10:58:52'),
(486, '[\"Flavour\"]', '{\"Flavour\":\"Blue Sour Raspberry\"}', 486, '2025-08-25 01:20:57', '2025-08-30 10:58:52'),
(487, '[\"Flavour\"]', '{\"Flavour\":\"Blueberry Cherry Cranberry\"}', 487, '2025-08-25 01:20:57', '2025-08-30 10:58:52'),
(488, '[\"Flavour\"]', '{\"Flavour\":\"Blueberry Raspberry\"}', 488, '2025-08-25 01:20:57', '2025-08-30 10:58:52'),
(489, '[\"Flavour\"]', '{\"Flavour\":\"Blueberry Raspberry\"}', 489, '2025-08-25 01:25:17', '2025-08-25 01:25:17'),
(490, '[\"Flavour\"]', '{\"Flavour\":\"Strawberry Raspberry Blueberry\"}', 490, '2025-08-25 01:25:17', '2025-08-25 01:25:17'),
(491, '[\"Flavour\"]', '{\"Flavour\":\"Strawberry Raspberry Ice\"}', 491, '2025-08-25 01:25:17', '2025-08-25 01:25:17'),
(492, '[\"Flavour\"]', '{\"Flavour\":\"Watermelon Ice\"}', 492, '2025-08-25 01:25:17', '2025-08-25 01:25:17'),
(493, '[\"Flavour\"]', '{\"Flavour\":\"Strawberry Watermelon\"}', 493, '2025-08-25 01:30:14', '2025-08-25 01:30:14'),
(494, '[\"Flavour\"]', '{\"Flavour\":\"Mr Blue\"}', 494, '2025-08-25 01:30:14', '2025-08-25 01:30:14'),
(495, '[\"Flavour\"]', '{\"Flavour\":\"Summer Dream\"}', 495, '2025-08-25 01:30:14', '2025-08-25 01:30:14'),
(496, '[\"Flavour\"]', '{\"Flavour\":\"Lemon & Lime\"}', 496, '2025-08-25 01:30:14', '2025-08-25 01:30:14'),
(497, '[\"Flavour\"]', '{\"Flavour\":\"Blue Razz Cherry\"}', 497, '2025-08-25 01:49:49', '2025-08-30 08:24:42'),
(498, '[\"Flavour\"]', '{\"Flavour\":\"Blue Razz Gummy Bear\"}', 498, '2025-08-25 01:49:50', '2025-08-30 08:24:42'),
(499, '[\"Flavour\"]', '{\"Flavour\":\"Blueberry Raspberry\"}', 499, '2025-08-25 01:49:51', '2025-08-30 08:24:42'),
(500, '[\"Flavour\"]', '{\"Flavour\":\"Blue Sour Raspberry\"}', 500, '2025-08-25 01:49:51', '2025-08-30 08:24:42'),
(554, '[\"Flavour\",\"Colour\",\"Weight\"]', '{\"Flavour\":\"Blueberry Raspberry\",\"Colour\":\"Red\",\"Weight\":\"20kg\"}', 554, '2025-08-30 10:28:09', '2025-08-30 10:28:09'),
(555, '[\"Flavour\",\"Colour\",\"Weight\"]', '{\"Flavour\":\"Blue Sour Raspberry\",\"Colour\":\"Red\",\"Weight\":\"20kg\"}', 555, '2025-08-30 10:28:09', '2025-08-30 10:28:09'),
(556, '[\"Flavour\",\"Colour\",\"Weight\"]', '{\"Flavour\":\"Classic Menthol\",\"Colour\":\"Red\",\"Weight\":\"20kg\"}', 556, '2025-08-30 10:28:09', '2025-08-30 10:28:09'),
(557, '[\"Flavour\",\"Colour\",\"Weight\"]', '{\"Flavour\":\"Chocolate Flavour\",\"Colour\":\"Red\",\"Weight\":\"20kg\"}', 557, '2025-08-30 10:28:09', '2025-08-30 10:28:09');

-- --------------------------------------------------------

--
-- Table structure for table `new_products`
--

CREATE TABLE `new_products` (
  `new_product_id` bigint UNSIGNED NOT NULL,
  `mvariant_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `new_products`
--

INSERT INTO `new_products` (`new_product_id`, `mvariant_id`, `created_at`, `updated_at`) VALUES
(1, 476, '2025-09-03 12:50:31', '2025-09-03 12:50:31'),
(2, 477, '2025-09-03 12:50:31', '2025-09-03 12:50:31');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `total_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `wallet_discount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `coupon_discount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `coupon_id` bigint UNSIGNED DEFAULT NULL,
  `status` enum('pending','paid','shipped','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `fulfillment_status` enum('fulfilled','unfulfilled','partiallyfulfilled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unfulfilled',
  `user_company_address_id` bigint UNSIGNED NOT NULL,
  `delivery_method_id` bigint UNSIGNED NOT NULL,
  `vat` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_paid` decimal(10,2) NOT NULL DEFAULT '0.00',
  `product_total_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `delivery_instructions` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pay_by_bank` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `royalmail_order_identifier` bigint UNSIGNED DEFAULT NULL,
  `pushed_to_cnd_at` timestamp NULL DEFAULT NULL,
  `cnd_status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cnd_last_error` json DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `total_amount`, `wallet_discount`, `coupon_discount`, `coupon_id`, `status`, `fulfillment_status`, `user_company_address_id`, `delivery_method_id`, `vat`, `total_paid`, `product_total_amount`, `delivery_instructions`, `pay_by_bank`, `created_at`, `updated_at`, `royalmail_order_identifier`, `pushed_to_cnd_at`, `cnd_status`, `cnd_last_error`) VALUES
(149, 27, 110.97, 0.00, 0.00, NULL, 'pending', 'unfulfilled', 30, 1, 16.00, 110.97, 79.98, NULL, 1, '2025-09-09 13:01:43', '2025-09-09 13:01:43', NULL, NULL, NULL, NULL),
(150, 27, 110.97, 0.00, 0.00, NULL, 'cancelled', 'unfulfilled', 30, 1, 16.00, 110.97, 79.98, NULL, 1, '2025-09-09 13:04:14', '2025-09-09 13:04:48', NULL, NULL, NULL, NULL),
(151, 10, 103.97, 0.00, 0.00, NULL, 'paid', 'fulfilled', 29, 2, 16.00, 103.97, 79.98, NULL, 1, '2025-09-09 13:43:08', '2025-10-06 21:13:59', NULL, NULL, NULL, NULL),
(152, 27, 254.93, 254.93, 0.00, NULL, 'paid', 'fulfilled', 30, 1, 39.99, 0.00, 199.95, NULL, 0, '2025-09-09 13:56:01', '2025-10-06 21:13:59', NULL, NULL, NULL, NULL),
(153, 27, 302.92, 0.00, 0.00, NULL, 'paid', 'partiallyfulfilled', 30, 1, 47.99, 302.92, 239.94, NULL, 1, '2025-09-09 14:07:32', '2025-10-06 21:13:40', NULL, NULL, NULL, NULL),
(154, 27, 302.92, 302.92, 0.00, NULL, 'paid', 'fulfilled', 30, 1, 47.99, 0.00, 239.94, NULL, 0, '2025-09-09 14:08:18', '2025-10-06 21:13:59', NULL, NULL, NULL, NULL),
(155, 27, 302.92, 302.92, 0.00, NULL, 'paid', 'fulfilled', 30, 1, 47.99, 0.00, 239.94, NULL, 0, '2025-09-09 14:27:44', '2025-10-06 21:13:59', NULL, NULL, NULL, NULL),
(156, 10, 446.88, 0.00, 0.00, NULL, 'paid', 'fulfilled', 28, 1, 71.98, 446.88, 359.91, NULL, 1, '2025-09-10 05:46:31', '2025-10-06 21:13:28', NULL, NULL, NULL, NULL),
(157, 10, 1024.52, 0.00, 0.00, NULL, 'paid', 'fulfilled', 28, 1, 170.75, 1024.52, 853.77, NULL, 1, '2025-09-10 13:14:47', '2025-10-06 21:13:28', NULL, NULL, NULL, NULL),
(158, 10, 287.93, 0.00, 0.00, NULL, 'paid', 'fulfilled', 28, 1, 47.99, 287.93, 239.94, NULL, 1, '2025-09-10 17:25:34', '2025-10-06 21:13:28', NULL, NULL, NULL, NULL),
(159, 27, 91.77, 0.00, 0.00, NULL, 'paid', 'fulfilled', 30, 1, 12.80, 91.77, 63.98, NULL, 1, '2025-09-26 12:20:59', '2025-10-06 21:12:06', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order_commissions`
--

CREATE TABLE `order_commissions` (
  `order_commission_id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED NOT NULL,
  `rep_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `product_total` decimal(10,2) NOT NULL,
  `commission_percent` decimal(5,2) NOT NULL,
  `commission_amount` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_commissions`
--

INSERT INTO `order_commissions` (`order_commission_id`, `order_id`, `rep_id`, `user_id`, `product_total`, `commission_percent`, `commission_amount`, `created_at`, `updated_at`) VALUES
(30, 151, 2, 10, 79.98, 5.00, 4.00, '2025-09-09 13:43:08', '2025-09-09 13:43:08'),
(31, 156, 2, 10, 359.91, 5.00, 18.00, '2025-09-10 05:46:31', '2025-09-10 05:46:31'),
(32, 157, 2, 10, 853.77, 5.00, 42.69, '2025-09-10 13:14:48', '2025-09-10 13:14:48'),
(33, 158, 2, 10, 239.94, 5.00, 12.00, '2025-09-10 17:25:34', '2025-09-10 17:25:34');

-- --------------------------------------------------------

--
-- Table structure for table `order_fulfillments`
--

CREATE TABLE `order_fulfillments` (
  `order_fulfillment_id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED NOT NULL,
  `tracking_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_courier` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fulfilled_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_fulfillments`
--

INSERT INTO `order_fulfillments` (`order_fulfillment_id`, `order_id`, `tracking_id`, `shipping_courier`, `fulfilled_at`, `created_at`, `updated_at`) VALUES
(8, 153, NULL, NULL, '2025-09-27 06:03:50', '2025-09-27 06:03:50', '2025-09-27 06:03:50'),
(9, 159, NULL, NULL, '2025-10-06 21:12:06', '2025-10-06 21:12:06', '2025-10-06 21:12:06'),
(10, 156, NULL, NULL, '2025-10-06 21:13:18', '2025-10-06 21:13:18', '2025-10-06 21:13:18'),
(11, 157, NULL, NULL, '2025-10-06 21:13:18', '2025-10-06 21:13:18', '2025-10-06 21:13:18'),
(12, 158, NULL, NULL, '2025-10-06 21:13:18', '2025-10-06 21:13:18', '2025-10-06 21:13:18'),
(13, 151, NULL, NULL, '2025-10-06 21:13:59', '2025-10-06 21:13:59', '2025-10-06 21:13:59'),
(14, 152, NULL, NULL, '2025-10-06 21:13:59', '2025-10-06 21:13:59', '2025-10-06 21:13:59'),
(15, 154, NULL, NULL, '2025-10-06 21:13:59', '2025-10-06 21:13:59', '2025-10-06 21:13:59'),
(16, 155, NULL, NULL, '2025-10-06 21:13:59', '2025-10-06 21:13:59', '2025-10-06 21:13:59');

-- --------------------------------------------------------

--
-- Table structure for table `order_fulfillment_items`
--

CREATE TABLE `order_fulfillment_items` (
  `order_fulfillment_item_id` bigint UNSIGNED NOT NULL,
  `order_fulfillment_id` bigint UNSIGNED NOT NULL,
  `order_item_id` bigint UNSIGNED NOT NULL,
  `quantity` int UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_fulfillment_items`
--

INSERT INTO `order_fulfillment_items` (`order_fulfillment_item_id`, `order_fulfillment_id`, `order_item_id`, `quantity`, `created_at`, `updated_at`) VALUES
(18, 8, 519, 3, '2025-09-27 06:03:50', '2025-09-27 06:03:50'),
(19, 9, 533, 2, '2025-10-06 21:12:06', '2025-10-06 21:12:06'),
(20, 10, 524, 5, '2025-10-06 21:13:18', '2025-10-06 21:13:18'),
(21, 10, 525, 4, '2025-10-06 21:13:18', '2025-10-06 21:13:18'),
(22, 11, 526, 4, '2025-10-06 21:13:18', '2025-10-06 21:13:18'),
(23, 11, 527, 7, '2025-10-06 21:13:18', '2025-10-06 21:13:18'),
(24, 11, 528, 4, '2025-10-06 21:13:18', '2025-10-06 21:13:18'),
(25, 11, 529, 4, '2025-10-06 21:13:18', '2025-10-06 21:13:18'),
(26, 11, 530, 2, '2025-10-06 21:13:18', '2025-10-06 21:13:18'),
(27, 11, 531, 2, '2025-10-06 21:13:18', '2025-10-06 21:13:18'),
(28, 12, 532, 6, '2025-10-06 21:13:18', '2025-10-06 21:13:18'),
(29, 13, 515, 1, '2025-10-06 21:13:59', '2025-10-06 21:13:59'),
(30, 13, 516, 1, '2025-10-06 21:13:59', '2025-10-06 21:13:59'),
(31, 14, 517, 5, '2025-10-06 21:13:59', '2025-10-06 21:13:59'),
(32, 15, 520, 3, '2025-10-06 21:13:59', '2025-10-06 21:13:59'),
(33, 15, 521, 3, '2025-10-06 21:13:59', '2025-10-06 21:13:59'),
(34, 16, 522, 3, '2025-10-06 21:13:59', '2025-10-06 21:13:59'),
(35, 16, 523, 3, '2025-10-06 21:13:59', '2025-10-06 21:13:59');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED NOT NULL,
  `mvariant_id` bigint UNSIGNED NOT NULL,
  `quantity` int UNSIGNED NOT NULL DEFAULT '1',
  `fulfilled_quantity` int UNSIGNED NOT NULL DEFAULT '0',
  `unit_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`order_item_id`, `order_id`, `mvariant_id`, `quantity`, `fulfilled_quantity`, `unit_price`, `created_at`, `updated_at`) VALUES
(512, 149, 476, 1, 0, 47.99, '2025-09-09 13:01:43', '2025-09-09 13:01:43'),
(513, 149, 477, 1, 0, 47.99, '2025-09-09 13:01:43', '2025-09-09 13:01:43'),
(514, 150, 476, 2, 0, 47.99, '2025-09-09 13:04:14', '2025-09-09 13:04:14'),
(515, 151, 476, 1, 1, 47.99, '2025-09-09 13:43:08', '2025-10-06 21:13:59'),
(516, 151, 477, 1, 1, 47.99, '2025-09-09 13:43:08', '2025-10-06 21:13:59'),
(517, 152, 476, 5, 5, 47.99, '2025-09-09 13:56:01', '2025-10-06 21:13:59'),
(518, 153, 476, 3, 0, 47.99, '2025-09-09 14:07:32', '2025-09-09 14:07:32'),
(519, 153, 477, 3, 3, 47.99, '2025-09-09 14:07:32', '2025-09-27 06:03:50'),
(520, 154, 476, 3, 3, 47.99, '2025-09-09 14:08:18', '2025-10-06 21:13:59'),
(521, 154, 477, 3, 3, 47.99, '2025-09-09 14:08:18', '2025-10-06 21:13:59'),
(522, 155, 476, 3, 3, 47.99, '2025-09-09 14:27:44', '2025-10-06 21:13:59'),
(523, 155, 477, 3, 3, 47.99, '2025-09-09 14:27:44', '2025-10-06 21:13:59'),
(524, 156, 476, 5, 5, 47.99, '2025-09-10 05:46:31', '2025-10-06 21:13:18'),
(525, 156, 477, 4, 4, 47.99, '2025-09-10 05:46:31', '2025-10-06 21:13:18'),
(526, 157, 476, 4, 4, 47.99, '2025-09-10 13:14:47', '2025-10-06 21:13:18'),
(527, 157, 477, 7, 7, 47.99, '2025-09-10 13:14:47', '2025-10-06 21:13:18'),
(528, 157, 478, 4, 4, 47.99, '2025-09-10 13:14:48', '2025-10-06 21:13:18'),
(529, 157, 479, 4, 4, 47.99, '2025-09-10 13:14:48', '2025-10-06 21:13:18'),
(530, 157, 493, 2, 2, 28.79, '2025-09-10 13:14:48', '2025-10-06 21:13:18'),
(531, 157, 500, 2, 2, 27.59, '2025-09-10 13:14:48', '2025-10-06 21:13:18'),
(532, 158, 482, 6, 6, 47.99, '2025-09-10 17:25:34', '2025-10-06 21:13:18'),
(533, 159, 476, 2, 2, 31.99, '2025-09-26 12:20:59', '2025-10-06 21:12:06');

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE `pages` (
  `page_id` bigint UNSIGNED NOT NULL,
  `page_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `page_slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `page_content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `page_status` enum('Active','Inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pages`
--

INSERT INTO `pages` (`page_id`, `page_name`, `page_slug`, `page_content`, `page_status`, `created_at`, `updated_at`) VALUES
(1, 'Terms Conditions', 'terms-conditions', '<p><strong>Terms and Conditions</strong></p><p><strong>Effective Date:</strong> 18/04/2025</p><p>Welcome to <strong>Truewebapp Smart Solutions</strong>, a mobile application (“App”) developed and managed by <strong>Truewebpro Ltd</strong> (“we”, “our”, or “us”).</p><p>By accessing or using this App, you (“Retailer”, “User”, or “you”) agree to be bound by these Terms and Conditions. If you do not agree with any part of these terms, please do not use the App.</p><p><strong>1. App Access and Eligibility</strong></p><ul><li>The App is available <strong>exclusively to approved retail partners</strong> for the purpose of discovering, ordering, and reordering products from authorized suppliers.</li><li>Our platform is <strong>not open to the general public</strong>, and access is restricted to <strong>mobile app use only</strong>. Our website, <a href=\"http://www.truewebapp.com\" rel=\"noopener noreferrer\" target=\"_blank\">www.truewebapp.com</a>, is for informational purposes and does not support direct purchases.</li></ul><p><br></p><p><strong>2. Account Registration</strong></p><ul><li>To use the App, you must register and be verified as a retailer.</li><li>You agree to provide accurate and complete information during registration and to update your details as necessary.</li><li>You are responsible for maintaining the confidentiality of your login credentials.</li></ul><p class=\"ql-align-center\"><br></p><p><strong>3. Ordering Products</strong></p><ul><li>Retailers can browse supplier products, add them to their eCommerce cart, and place orders directly via the App.</li><li>Orders are subject to supplier availability and confirmation.</li><li>We reserve the right to cancel or modify orders due to product availability or errors.</li></ul><p class=\"ql-align-center\"><br></p><p><strong>4. Reorder and Favourites</strong></p><ul><li>The App offers features like <strong>Reorder</strong>, allowing retailers to quickly place repeat orders.</li><li>You can <strong>mark brands or products as Favourites</strong> for quicker access and improved ordering experience.</li></ul><p class=\"ql-align-center\"><br></p><p><strong>5. Reward Points</strong></p><ul><li>Retailers may earn reward points through qualifying purchases and activities within the App.</li><li>Points are non-transferable, have no cash value, and may only be redeemed within the App per applicable guidelines.</li><li>We reserve the right to modify or discontinue the rewards program at any time.</li></ul><p class=\"ql-align-center\"><br></p><p><strong>6. Payments</strong></p><ul><li>Payments can be made using the <strong>Bank Payment Option</strong> as specified at checkout.</li><li>You are responsible for ensuring timely and correct payments. Orders may be cancelled for non-payment or incorrect information.</li></ul><p class=\"ql-align-center\"><br></p><p><strong>7. License and Use</strong></p><ul><li>We grant you a limited, non-transferable, revocable license to use the App solely for your internal business purposes as a retailer.</li><li>You may not reverse engineer, modify, or exploit the App in any unauthorized way.</li></ul><p class=\"ql-align-center\"><br></p><p><strong>8. Prohibited Activities</strong></p><p>You agree not to:</p><ul><li>Use the App for any illegal or unauthorized purpose.</li><li>Attempt to gain unauthorized access to our systems or data.</li><li>Share or misuse reward points or user accounts.</li></ul><p class=\"ql-align-center\"><br></p><p><strong>9. Intellectual Property</strong></p><p>All content, branding, and features within the App are the property of <strong>Truewebpro UK Private Ltd</strong> and are protected by intellectual property laws.</p><p><strong>10. Termination</strong></p><p>We may suspend or terminate your access to the App at our sole discretion, without notice, if we believe you are in violation of these terms or are misusing the platform.</p><p><strong>11. Limitation of Liability</strong></p><p>We are not liable for any indirect, incidental, or consequential damages resulting from your use of the App. All purchases and transactions are between the retailer and supplier; we facilitate but do not guarantee supplier performance.</p><p><strong>12. Modifications</strong></p><p>We may update these Terms and Conditions at any time. Continued use of the App after changes means you accept the revised terms.</p><p><strong>13. Governing Law</strong></p><p>These terms are governed by the laws of the United Kingdom. Any disputes will be subject to the exclusive jurisdiction of the courts of England and Wales.</p><p><strong>14. Contact Us</strong></p><p>For any questions about these Terms and Conditions, please contact:</p><p><strong>Truewebpro UK Private Ltd</strong></p><p>Email: info@truewebapp.com</p><p>Website: <a href=\"http://www.truewebapp.com\" rel=\"noopener noreferrer\" target=\"_blank\">www.truewebapp.com</a></p>', 'Active', '2025-06-10 10:04:20', '2025-06-10 10:04:20'),
(2, 'Privacy Policy', 'privacy-policy', '<p><strong>Privacy Policy</strong></p><p><strong>Effective Date:</strong> 18/04/2025</p><p>Welcome to <strong>Truewebapp Smart Solutions</strong> (the \"App\"), operated by <strong>Truewebpro Ltd</strong> (\"we\", \"our\", or \"us\"). This Privacy Policy outlines how we collect, use, disclose, and protect your personal data when you use our mobile application.</p><p>By accessing or using the App, you agree to the collection and use of information in accordance with this Privacy Policy. If you do not agree, please do not use the App.</p><p><strong>1. Information We Collect</strong></p><p>We collect the following types of information:</p><p><strong>a. Personal Information:</strong></p><ul><li>Name, email address, phone number</li><li>Business details (e.g., shop name, GST, address)</li></ul><p><strong>b. Usage Information:</strong></p><ul><li>Products browsed and ordered</li><li>Favourite brands</li><li>Reward points and reorder history</li></ul><p><strong>c. Device and App Information:</strong></p><ul><li>Device ID, operating system, and app version</li><li>Location (if enabled by you)</li></ul><p><strong>2. How We Use Your Information</strong></p><p>We use your data to:</p><ul><li>Facilitate order placement between retailers and suppliers</li><li>Manage your reward points and reorders</li><li>Customize your user experience (e.g., save favourite brands)</li><li>Provide customer support and service notifications</li><li>Improve app performance and add new features</li><li>Process payments and maintain transaction history</li></ul><p><strong>3. Data Sharing and Disclosure</strong></p><p>We <strong>do not sell</strong> your personal data. We may share your information:</p><ul><li>With suppliers to fulfil your orders</li><li>With payment gateways and banks (for payment in bank option)</li><li>With third-party service providers (e.g., cloud hosting, analytics)</li><li>When required by law or to protect our legal rights</li></ul><p><strong>4. Data Security</strong></p><p>We implement appropriate technical and organizational measures to protect your data from unauthorized access, alteration, disclosure, or destruction.</p><p><strong>5. Data Retention</strong></p><p>We retain your data only as long as necessary for the purposes mentioned in this policy, unless a longer retention period is required by law.</p><p><strong>6. Your Rights</strong></p><p>You have the right to:</p><ul><li>Access and update your personal data</li><li>Delete your account and associated data (subject to legal obligations)</li><li>Withdraw consent for marketing communications</li></ul><p>You can exercise these rights by contacting our support team.</p><p><strong>7. Cookies and Tracking</strong></p><p>The App may use cookies or similar technologies to enhance user experience, track behaviour, and collect analytics data.</p><p><strong>8. Children\'s Privacy</strong></p><p>The App is not intended for children under the age of 18. We do not knowingly collect personal data from children.</p><p><strong>9. Changes to this Privacy Policy</strong></p><p>We may update this policy from time to time. We will notify you of any material changes through the App or via email.</p><p><strong>10. Contact Us</strong></p><p>If you have questions or concerns regarding this Privacy Policy, you can contact us at:</p><p><strong>Truewebpro UK Private Ltd</strong></p><p> Email: info@truewebapp.com</p><p> Website: <a href=\"https://truewebapp.com\" rel=\"noopener noreferrer\" target=\"_blank\">https://truewebapp.com</a></p>', 'Active', '2025-06-10 10:04:20', '2025-06-10 10:04:20');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`email`, `token`, `created_at`) VALUES
('info@truewebsync.com', '$2y$10$HeTxBKFhH6McKsln2yBfxu4Qu7e3dTOD1uXfXamNV9zyHkNx8pB4.', '2025-07-03 11:11:26'),
('john1@example.com', '$2y$10$mIhAoDd9FJcHDL5Jl0FYwOaxaAkwp9kShzTpFR1YC0EHAjFBqcSu.', '2025-08-01 05:40:59'),
('info@truewebpro.com', '$2y$10$EEvGQcEYNOzpGsWszjyl9eS4blBSed0mC./YlGOJ.X41OrVgCzo2u', '2025-08-13 10:20:35'),
('ssukhraj12@gmail.com', '$2y$10$gc5jJMbBPFX0WinZ7EyerOCbZAsLEy4tQXHMnsDNlnkyLYWLyZdZ.', '2025-08-13 10:25:15'),
('john345@example.com', '$2y$10$29N9PJsMXjsLlhstWBhZCeYM2LZZP8UmRCa.fU/LGArynS/0lE/di', '2025-08-24 18:14:58'),
('john@example.com', '$2y$10$0hcFroXpeMTvKcPzdA7fS.j1IlosergZG7KLyHif2KDBXZks5ibIK', '2025-08-26 15:34:41'),
('info@truewebpro.co.uk', '$2y$10$jDr7V9ZBk7Iyj05HZfouDezNAEYL4rE6kLZVB2im2n7zT3GiJMFlK', '2025-08-28 12:43:22'),
('gshubham81@gmail.com', '$2y$10$kTFsm9H1OJG4ecfPzyAuJucLEjgU9hz44WFAcB0Gb3u7iXqwWNlOC', '2025-08-28 12:45:33');

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_vats`
--

CREATE TABLE `product_vats` (
  `product_vat_id` bigint UNSIGNED NOT NULL,
  `product_vat` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_vats`
--

INSERT INTO `product_vats` (`product_vat_id`, `product_vat`, `created_at`, `updated_at`) VALUES
(1, 20, '2025-09-09 12:45:17', '2025-09-09 12:45:17');

-- --------------------------------------------------------

--
-- Table structure for table `product__offers`
--

CREATE TABLE `product__offers` (
  `product_offer_id` bigint UNSIGNED NOT NULL,
  `mvariant_id` bigint UNSIGNED NOT NULL,
  `product_deal_tag` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_offer` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product__offers`
--

INSERT INTO `product__offers` (`product_offer_id`, `mvariant_id`, `product_deal_tag`, `product_offer`, `created_at`, `updated_at`) VALUES
(11, 476, NULL, NULL, '2025-09-07 13:06:18', '2025-10-07 12:35:21'),
(12, 477, NULL, NULL, '2025-09-07 13:06:18', '2025-10-07 12:35:21'),
(13, 479, 'Pack of 5', 'Pack of 5', '2025-09-07 13:06:18', '2025-09-07 13:06:18'),
(14, 480, 'Pack of 5', 'Pack of 5', '2025-09-07 13:06:18', '2025-09-07 13:06:18'),
(15, 481, 'Pack of 5', 'Pack of 5', '2025-09-07 13:06:18', '2025-09-07 13:06:18'),
(16, 482, NULL, NULL, '2025-09-07 13:06:18', '2025-10-07 12:35:21'),
(17, 478, NULL, NULL, '2025-09-07 13:06:18', '2025-10-07 12:35:21'),
(18, 489, 'Clearance', NULL, '2025-09-09 08:35:22', '2025-09-09 08:35:22'),
(19, 490, 'Clearance', NULL, '2025-09-09 08:35:22', '2025-09-09 08:35:22'),
(20, 491, 'Clearance', NULL, '2025-09-09 08:35:22', '2025-09-09 08:35:22'),
(21, 492, 'Clearance', NULL, '2025-09-09 08:35:22', '2025-09-09 08:35:22'),
(22, 497, 'Flash Deal', 'any 2 for £30', '2025-10-07 12:37:14', '2025-10-07 12:37:14'),
(23, 498, 'Flash Deal', 'any 2 for £30', '2025-10-07 12:37:14', '2025-10-07 12:37:14'),
(24, 499, 'Flash Deal', 'any 2 for £30', '2025-10-07 12:37:14', '2025-10-07 12:37:14'),
(25, 500, 'Flash Deal', 'any 2 for £30', '2025-10-07 12:37:14', '2025-10-07 12:37:14');

-- --------------------------------------------------------

--
-- Table structure for table `queries`
--

CREATE TABLE `queries` (
  `query_id` bigint UNSIGNED NOT NULL,
  `query_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `queries`
--

INSERT INTO `queries` (`query_id`, `query_name`, `created_at`, `updated_at`) VALUES
(1, 'is equal to', NULL, NULL),
(2, 'is not equal to', NULL, NULL),
(3, 'starts with', NULL, NULL),
(4, 'ends with', NULL, NULL),
(5, 'contains', NULL, NULL),
(6, 'does not contains', NULL, NULL),
(7, 'greater than', NULL, NULL),
(8, 'less than', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `referrals`
--

CREATE TABLE `referrals` (
  `ref_id` bigint UNSIGNED NOT NULL,
  `referrer_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `has_received_bonus` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `referral_invites`
--

CREATE TABLE `referral_invites` (
  `referral_invite_id` bigint UNSIGNED NOT NULL,
  `sender_user_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `referral_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `service_solutions`
--

CREATE TABLE `service_solutions` (
  `service_solution_id` bigint UNSIGNED NOT NULL,
  `service_solution_title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `service_solution_sub_title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `service_solution_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `service_solution_desc` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `service_solutions`
--

INSERT INTO `service_solutions` (`service_solution_id`, `service_solution_title`, `service_solution_sub_title`, `service_solution_image`, `service_solution_desc`, `created_at`, `updated_at`) VALUES
(1, 'Basic POS', 'Free with min app spend £2000 + VAT pcm', 'goapp/images/mbrands/mbrand_68429a9800f64.png', 'Compare and get real time business analytics.\nFast & Easy Billing.\nDiscount & Promotions Management.\nPrice management from Head office.\nRack Management.\nRetail Customer Relationship Management.', NULL, NULL),
(2, 'Advanced POS', 'Free with min app spend £2000 + VAT pcm', 'goapp/images/mbrands/mbrand_68429a9800f64.png', 'Compare and get real time business analytics.\nFast & Easy Billing.\nDiscount & Promotions Management.\nPrice management from Head office.\nRack Management.\nRetail Customer Relationship Management.', NULL, NULL),
(3, 'Premium POS', 'Free with min app spend £2000 + VAT pcm', 'goapp/images/mbrands/mbrand_68429a9800f64.png', 'Compare and get real time business analytics.\nFast & Easy Billing.\nDiscount & Promotions Management.\nPrice management from Head office.\nRack Management.\nRetail Customer Relationship Management.', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `setting_id` bigint UNSIGNED NOT NULL,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`setting_id`, `key`, `value`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'min_order_free_delivery', '500', 1, '2025-07-11 05:51:48', '2025-10-01 06:53:40'),
(2, 'min_order_place', '200', 1, '2025-10-01 06:53:41', '2025-10-01 06:53:41');

-- --------------------------------------------------------

--
-- Table structure for table `slider_headers`
--

CREATE TABLE `slider_headers` (
  `slider_header_id` bigint UNSIGNED NOT NULL,
  `header_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `header_value` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `slider_headers`
--

INSERT INTO `slider_headers` (`slider_header_id`, `header_name`, `header_value`, `created_at`, `updated_at`) VALUES
(1, 'first banner slider', 'EXPLORE THE DEALS CENTRE', '2025-06-27 08:05:36', '2025-06-27 08:05:36'),
(2, 'first product slider', 'NEW PRODUCTS', '2025-06-27 08:05:36', '2025-06-27 08:05:36'),
(3, 'second banner slider', 'WEEKY DEALS', '2025-06-27 08:05:36', '2025-09-07 13:28:46'),
(4, 'second product slider', 'TOP SELLER', '2025-06-27 08:05:36', '2025-06-27 08:05:36');

-- --------------------------------------------------------

--
-- Table structure for table `top_sellers`
--

CREATE TABLE `top_sellers` (
  `top_seller_id` bigint UNSIGNED NOT NULL,
  `mvariant_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `top_sellers`
--

INSERT INTO `top_sellers` (`top_seller_id`, `mvariant_id`, `created_at`, `updated_at`) VALUES
(1, 478, '2025-08-25 04:38:40', '2025-08-25 04:38:40'),
(2, 479, '2025-08-25 04:38:40', '2025-08-25 04:38:40'),
(3, 480, '2025-08-25 04:38:40', '2025-08-25 04:38:40'),
(4, 481, '2025-08-25 04:38:40', '2025-08-25 04:38:40'),
(5, 482, '2025-08-25 04:38:40', '2025-08-25 04:38:40'),
(6, 483, '2025-08-25 04:38:40', '2025-08-25 04:38:40'),
(7, 484, '2025-08-25 04:38:40', '2025-08-25 04:38:40'),
(8, 485, '2025-08-25 04:38:40', '2025-08-25 04:38:40'),
(9, 486, '2025-08-25 04:38:40', '2025-08-25 04:38:40'),
(10, 487, '2025-08-25 04:38:40', '2025-08-25 04:38:40');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `referred_by` bigint UNSIGNED DEFAULT NULL,
  `referral_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rep_id` bigint UNSIGNED DEFAULT NULL,
  `user_tag_id` bigint UNSIGNED DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mobile` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `company_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address1` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `country` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `postcode` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `admin_approval` enum('Pending','Approved','Declined') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pending',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `referred_by`, `referral_code`, `rep_id`, `user_tag_id`, `name`, `email`, `email_verified_at`, `password`, `mobile`, `company_name`, `address1`, `address2`, `city`, `country`, `postcode`, `admin_approval`, `remember_token`, `created_at`, `updated_at`) VALUES
(10, NULL, NULL, 2, NULL, 'John Doefish', 'john@example.com', NULL, '$2y$10$jNYsBAUCMA8h8y/8L4azbe5/4X6tr2H2zXw0jH7NeEwmkQswR2z2u', '9876541111', 'test34343456', 'test1ywyw', 'test33', 'test', 'testing', 'test', 'Approved', 'SuZW1EeCabIJ93OWKgwyEReTceE7EvL8U11ARaxnpXQbI36egDLwRQV2gbhG', '2025-04-21 05:46:15', '2025-09-04 13:35:50'),
(11, NULL, NULL, NULL, NULL, 'John1 Doe1', 'john1@example.com', NULL, '$2y$10$q8JilizK3Hvdu/eZMacuC.3ep7mwwaKETT/uYw9Oh5ohd82lUilG2', '9876543210', 'test', 'test', NULL, 'test', 'test', 'test', 'Declined', NULL, '2025-04-21 05:46:53', '2025-08-30 08:15:15'),
(12, NULL, NULL, NULL, NULL, 'John Doe', 'john3@example.com', NULL, '$2y$10$h/yKWk5cAwlil09O1meBguZJVmQmD1VA467aUnXxXFSmubf74tvAm', '919876543210', 'Test', 'Test', 'Test', 'Test', 'Test', 'Test', 'Approved', NULL, '2025-05-05 06:50:20', '2025-09-03 07:57:31'),
(13, NULL, NULL, NULL, NULL, 'John Doe', 'john34@example.com', NULL, '$2y$10$/ZMEkn2d2Rp2SW8zvgnbMO2yWpPwE8W4WYqKO8Lk5K9o4Lh.fJjPG', '9876543211', 'test', 'test', 'test', 'test', 'test', 'test', 'Declined', NULL, '2025-05-06 07:39:15', '2025-05-12 13:54:00'),
(14, NULL, NULL, 1, NULL, 'kapil sharma', 'kplsharma8185@gmail.com', NULL, '$2y$10$ko65DLpBcZWPjvq9sUeba.PSsYyodKedUpId/MbIfsXbWTMVgDphe', '9814364509', 'impactmindz', 'gagshhs', NULL, 'Dera Bassi', 'India', '140507', 'Declined', NULL, '2025-05-07 07:25:15', '2025-08-30 08:15:19'),
(16, NULL, NULL, 2, NULL, 'Divtej', 'divtej04@gmail.com', NULL, '$2y$10$YiLA.uKcv8uuWsiOo6eVZOLNrL77Jn0E/1aI4jRCrdz1Mkt9DRhgi', '73553344', 'abcd', 'House no.12', NULL, 'chandigarh', 'India', '160081', 'Approved', NULL, '2025-05-07 07:59:57', '2025-08-30 11:10:49'),
(17, NULL, NULL, 2, NULL, 'Shubham Garg', 'gshubham81@gmail.com', NULL, '$2y$10$msB6CVykNzfiwj58.yJXpO4RontPdPOQvMJHH4G4kTwxiUQKt6qP2', '9888899880', 'Sg traders', 'Sector 22', NULL, 'Manchester', 'United Kongdom', 'MLB065', 'Approved', NULL, '2025-05-08 11:25:53', '2025-08-30 08:26:03'),
(18, NULL, NULL, NULL, NULL, 'kapil sharma', 'kplsharma81851@gmail.com', NULL, '$2y$10$GswgjIzPSXeYI9a/R7UQeeJ/rgwPEq1LkCBjvxtl3N8OcG9jWhaxi', '9814364508', 'Impactmindz', '83-84, Jan Marg', NULL, 'Chandigarh', 'India', '160017', 'Approved', NULL, '2025-05-08 11:30:45', '2025-08-29 07:38:19'),
(19, NULL, NULL, NULL, 6, 'Rony Singh', 'info@truewebpro.co.uk', NULL, '$2y$10$NSL09HaU8IsbnSslqG11purMjEYad.OrED/8YMIPxOUUanNf4axJi', '7447437071', 'Trueweb Pro Ltd', '6 Park Lane', 'whitefiled', 'Manchester', 'United Kingdom', 'M457PB', 'Approved', NULL, '2025-08-24 12:35:54', '2025-10-06 21:05:58'),
(20, NULL, NULL, NULL, NULL, 'divtej singh', 'divtej003@gmail.com', NULL, '$2y$10$pIaVVZijHq2jdqu5juuE1.va6wwKZ75pK34j5YQBOEbdp2W5l8frC', '7814006938', 'dave', '123', '18', 'chandigarh', 'india', '160018', 'Approved', NULL, '2025-08-24 14:11:18', '2025-08-24 14:14:50'),
(21, NULL, NULL, NULL, NULL, 'John Doe', 'john345@example.com', NULL, '$2y$10$nPmBdv9H2S84hDyINYCPNuprk7Xl5Q0zE8uhiIu0JIyOJu2TVcZ4y', '+919876543211', 'test', 'test', 'test', 'test', 'test', 'test', 'Approved', NULL, '2025-08-24 17:12:08', '2025-08-24 17:58:15'),
(22, NULL, NULL, NULL, NULL, 'shubham garg', 'shubhas@gmail.com', NULL, '$2y$10$tNZt9fH2jC8LN/aetkrlU.qr1sZEKqyRAnrRSAwXZs1IMVM3YAE06', '4545469999', 'shubh pvt ltd', 'sector 22 c', 'Chandigarh', 'Chandigarh', 'india', '164001', 'Approved', NULL, '2025-08-24 18:14:11', '2025-08-25 04:34:51'),
(23, NULL, NULL, 3, NULL, 'Sanjay bora', 'sanjay.impactmindz@gmail.com', NULL, '$2y$10$1YHDqMH5n6Rrkz3YVd3TPe0Q508ndCSPNtS9CMveI2TaBH/T4IFBG', '1346464646', 'test', 'test', 'test', 'test', 'test', '157001', 'Approved', NULL, '2025-08-24 18:18:32', '2025-09-03 13:17:06'),
(24, NULL, NULL, NULL, NULL, 'test test', 'testt@gmail.com', NULL, '$2y$10$g0TUCWgpxZsdlQ3z.Gd5V.vW5wN9i9acG.21.GFhdg87DMphIFCZe', '1234569852', 'test', 'test', 'test', 'test', 'test', '147001', 'Approved', NULL, '2025-08-24 18:19:41', '2025-09-10 13:25:48'),
(25, NULL, NULL, 2, NULL, 'info truewebpro', 'info@truewebpro.com', NULL, '$2y$10$9XGGba9SPxekd9thJo1vMe3fHHZTjcunwQsG8r5nZB/5mGY5k2Dw6', '7710303298', 'true web pro com', '28 Kelvin Grove', NULL, 'Manchester', 'United Kingdom', 'M8 0SX', 'Approved', NULL, '2025-08-25 12:19:59', '2025-09-10 13:19:50'),
(26, NULL, NULL, 3, NULL, 'shubh garg', 'shubha@gmail.com', NULL, '$2y$10$DUCI6g2nXt7gTNtGO/JVYe9j8BpQs2jhR1xOdYE31/PVi/bHPy06G', '9512357465', 'shubha pvt ltd', 'sector 22', NULL, 'Chandigarh', 'India', '147001', 'Approved', NULL, '2025-08-27 05:22:26', '2025-08-27 05:26:00'),
(27, NULL, NULL, NULL, NULL, 'Aalam sir', 'aalam.impactmindz@gmail.com', NULL, '$2y$10$7kP14I0LWYMul.4CNLtPjOG6LZeNsJDQmmArdijkFvbs9zN7GMBGi', '1234567890', 'Trueweb Pro Ltd', '6 Park Lane', 'whitefiled', 'Manchester', 'United Kingdom', 'M457PB', 'Approved', NULL, '2025-09-09 12:58:39', '2025-09-26 11:51:53');

-- --------------------------------------------------------

--
-- Table structure for table `user_company_addresses`
--

CREATE TABLE `user_company_addresses` (
  `user_company_address_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `user_company_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `company_address1` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `company_address2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company_city` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `company_country` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `company_postcode` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_company_addresses`
--

INSERT INTO `user_company_addresses` (`user_company_address_id`, `user_id`, `user_company_name`, `company_address1`, `company_address2`, `company_city`, `company_country`, `company_postcode`, `created_at`, `updated_at`) VALUES
(7, 11, 'Nnn', 'Lll', 'Ooo', 'Eee', 'Aaa', 'Xxx', '2025-06-30 06:11:50', '2025-06-30 06:11:50'),
(17, 11, 'Itx', '2', NULL, 'r', 'India', '121212', '2025-07-11 11:11:06', '2025-07-11 11:11:06'),
(27, 26, 'shubha pvt ltd', 'sco 3345', 'sector 15', 'Chandigarh', 'India', '147001', '2025-08-27 05:28:32', '2025-08-27 05:28:43'),
(28, 10, 'test', 'teststagay', 'test', 'testing', 'test', '123456', '2025-09-03 07:41:46', '2025-09-04 13:35:27'),
(29, 10, 'Alaya', 'Shhshs', 'india', 'chandigafh', 'india', '16162662', '2025-09-04 13:35:13', '2025-09-04 13:35:13'),
(30, 27, 'xyz', '111', NULL, 'Chandigarh', 'india', '16002', '2025-09-09 13:01:35', '2025-09-09 13:01:35');

-- --------------------------------------------------------

--
-- Table structure for table `user_tags`
--

CREATE TABLE `user_tags` (
  `user_tag_id` bigint UNSIGNED NOT NULL,
  `user_tag_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('custom','percentage') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'percentage',
  `discount` int DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_tags`
--

INSERT INTO `user_tags` (`user_tag_id`, `user_tag_name`, `type`, `discount`, `is_active`, `created_at`, `updated_at`) VALUES
(5, 'THENO1PLUG10', 'percentage', 10, 1, '2025-10-06 21:04:48', '2025-10-06 21:04:48'),
(6, 'MCRVAPDISTRO5', 'percentage', 5, 1, '2025-10-06 21:05:06', '2025-10-06 21:05:06'),
(7, 'AURAVAPES', 'custom', NULL, 1, '2025-10-06 21:08:45', '2025-10-06 21:08:45');

-- --------------------------------------------------------

--
-- Table structure for table `user_tag_prices`
--

CREATE TABLE `user_tag_prices` (
  `user_tag_price_id` bigint UNSIGNED NOT NULL,
  `user_tag_id` bigint UNSIGNED NOT NULL,
  `mvariant_id` bigint UNSIGNED NOT NULL,
  `tag_price` double(8,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_tag_prices`
--

INSERT INTO `user_tag_prices` (`user_tag_price_id`, `user_tag_id`, `mvariant_id`, `tag_price`, `created_at`, `updated_at`) VALUES
(3, 7, 556, 29.50, '2025-10-06 21:09:30', '2025-10-06 21:09:30');

-- --------------------------------------------------------

--
-- Table structure for table `wallets`
--

CREATE TABLE `wallets` (
  `wallet_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `balance` decimal(10,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `wallets`
--

INSERT INTO `wallets` (`wallet_id`, `user_id`, `balance`, `created_at`, `updated_at`) VALUES
(1, 11, 5000.00, '2025-07-14 12:53:00', '2025-08-02 11:43:22'),
(2, 10, 1004.00, '2025-07-14 13:11:18', '2025-10-06 21:13:40'),
(3, 16, 600.00, '2025-07-30 06:45:37', '2025-09-10 13:29:18'),
(5, 20, 0.00, '2025-08-24 14:38:00', '2025-08-24 14:38:00'),
(6, 21, 0.00, '2025-08-24 18:05:18', '2025-08-24 18:05:18'),
(7, 22, 0.00, '2025-08-25 04:35:43', '2025-08-25 04:35:43'),
(8, 26, 752.60, '2025-08-27 05:24:57', '2025-08-27 05:29:14'),
(9, 23, 0.00, '2025-08-28 06:24:40', '2025-08-28 06:24:40'),
(10, 27, 4145.23, '2025-09-09 13:00:39', '2025-10-06 21:13:40');

-- --------------------------------------------------------

--
-- Table structure for table `wallet_transactions`
--

CREATE TABLE `wallet_transactions` (
  `wallet_transaction_id` bigint UNSIGNED NOT NULL,
  `wallet_id` bigint UNSIGNED NOT NULL,
  `type` enum('credit','debit') COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `wallet_transactions`
--

INSERT INTO `wallet_transactions` (`wallet_transaction_id`, `wallet_id`, `type`, `amount`, `reference`, `description`, `created_at`, `updated_at`) VALUES
(2, 2, 'debit', 978.43, 'ORDER-6889adc0092b7', 'Wallet used during checkout', '2025-07-30 05:29:36', '2025-07-30 05:29:36'),
(3, 2, 'debit', 21.57, 'ORDER-6889af4573369', 'Wallet used during checkout', '2025-07-30 05:36:05', '2025-07-30 05:36:05'),
(4, 2, 'debit', 1040.82, 'ORDER-6889b7ae04b51', 'Wallet used during checkout', '2025-07-30 06:11:58', '2025-07-30 06:11:58'),
(5, 1, 'debit', 1216.33, 'ORDER-6889c088b5bfa', 'Wallet used during checkout', '2025-07-30 06:49:44', '2025-07-30 06:49:44'),
(6, 1, 'debit', 70.37, 'ORDER-6889e1f03e604', 'Wallet used during checkout', '2025-07-30 09:12:16', '2025-07-30 09:12:16'),
(7, 1, 'debit', 444.62, 'ORDER-6889ea26e029e', 'Wallet used during checkout', '2025-07-30 09:47:18', '2025-07-30 09:47:18'),
(8, 1, 'debit', 268.68, 'ORDER-6889eaa20a2ef', 'Wallet used during checkout', '2025-07-30 09:49:22', '2025-07-30 09:49:22'),
(9, 2, 'debit', 959.18, 'ORDER-688a04a84f505', 'Wallet used during checkout', '2025-07-30 11:40:24', '2025-07-30 11:40:24'),
(10, 2, 'debit', 257.49, 'ORDER-688a05cc228bb', 'Wallet used during checkout', '2025-07-30 11:45:16', '2025-07-30 11:45:16'),
(11, 2, 'credit', 200.00, 'ADMIN-688a094b11f85', 'Wallet credited by Admin via panel', '2025-07-30 12:00:11', '2025-07-30 12:00:11'),
(15, 1, 'debit', 10.00, 'ORDER-688db586f2acd', 'Wallet used during checkout', '2025-08-02 06:51:50', '2025-08-02 06:51:50'),
(20, 1, 'debit', 10.00, 'ORDER-688dbb2712172', 'Wallet used during checkout', '2025-08-02 07:15:51', '2025-08-02 07:15:51'),
(25, 1, 'debit', 10.00, 'ORDER-688dbda85c1d0', 'Wallet used during checkout', '2025-08-02 07:26:32', '2025-08-02 07:26:32'),
(29, 1, 'debit', 51.98, 'ORDER-688dbe639b2da', 'Wallet used during checkout', '2025-08-02 07:29:39', '2025-08-02 07:29:39'),
(35, 2, 'debit', 51.98, 'ORDER-688dc271d8132', 'Wallet used during checkout', '2025-08-02 07:46:57', '2025-08-02 07:46:57'),
(36, 2, 'debit', 190.53, 'ORDER-688ddcd04b1bd', 'Wallet used during checkout', '2025-08-02 09:39:28', '2025-08-02 09:39:28'),
(37, 2, 'credit', 300.00, 'ADMIN-688df5b79fb2f', 'Wallet credited by Admin via panel', '2025-08-02 11:25:43', '2025-08-02 11:25:43'),
(38, 1, 'debit', 286.70, 'ORDER-688df9da7959e', 'Wallet used during checkout', '2025-08-02 11:43:22', '2025-08-02 11:43:22'),
(39, 2, 'credit', 1500.00, 'ADMIN-688e02487aa41', 'Wallet credited by Admin via panel', '2025-08-02 12:19:20', '2025-08-02 12:19:20'),
(42, 2, 'debit', 25.99, 'ORDER-689334bb47612', 'Wallet used during checkout', '2025-08-06 10:55:55', '2025-08-06 10:55:55'),
(43, 2, 'debit', 67.37, 'ORDER-6893368d457be', 'Wallet used during checkout', '2025-08-06 11:03:41', '2025-08-06 11:03:41'),
(44, 2, 'debit', 70.37, 'ORDER-6893380959026', 'Wallet used during checkout', '2025-08-06 11:10:01', '2025-08-06 11:10:01'),
(45, 2, 'debit', 67.37, 'ORDER-6893381c06769', 'Wallet used during checkout', '2025-08-06 11:10:20', '2025-08-06 11:10:20'),
(46, 2, 'debit', 70.37, 'ORDER-68935fc393d6a', 'Wallet used during checkout', '2025-08-06 13:59:31', '2025-08-06 13:59:31'),
(47, 2, 'debit', 410.47, 'ORDER-689596d04c11e', 'Wallet used during checkout', '2025-08-08 06:18:56', '2025-08-08 06:18:56'),
(48, 2, 'debit', 842.09, 'ORDER-68abc85fb2bf0', 'Wallet used during checkout', '2025-08-25 02:20:15', '2025-08-25 02:20:15'),
(49, 8, 'credit', 1000.00, 'Credit given', 'Wallet credited by Admin via panel', '2025-08-27 05:26:23', '2025-08-27 05:26:23'),
(50, 8, 'debit', 247.40, 'ORDER-68ae97aadbc8d', 'Wallet used during checkout', '2025-08-27 05:29:14', '2025-08-27 05:29:14'),
(51, 2, 'debit', 163.95, 'ORDER-68b2a73d14602', 'Wallet used during checkout', '2025-08-30 07:24:45', '2025-08-30 07:24:45'),
(52, 2, 'debit', 82.02, 'ORDER-68b7efe73a598', 'Wallet used during checkout', '2025-09-03 07:36:07', '2025-09-03 07:36:07'),
(53, 2, 'debit', 287.93, 'ORDER-68bfd3bb4518f', 'Wallet used during checkout', '2025-09-09 07:14:03', '2025-09-09 07:14:03'),
(54, 2, 'credit', 1.00, 'PAYBYBANK-68bfd44fd6fff', 'Pay by bank bonus', '2025-09-09 07:16:31', '2025-09-09 07:16:31'),
(55, 10, 'credit', 1.00, 'PAYBYBANK-68c0253711e2d', 'Pay by bank bonus', '2025-09-09 13:01:43', '2025-09-09 13:01:43'),
(56, 10, 'credit', 1.00, 'PAYBYBANK-68c025ce08ff0', 'Pay by bank bonus', '2025-09-09 13:04:14', '2025-09-09 13:04:14'),
(57, 2, 'credit', 1.00, 'PAYBYBANK-68c02eeced415', 'Pay by bank bonus', '2025-09-09 13:43:08', '2025-09-09 13:43:08'),
(58, 10, 'credit', 5000.00, NULL, 'Wallet credited by Admin via panel', '2025-09-09 13:55:44', '2025-09-09 13:55:44'),
(59, 10, 'debit', 254.93, 'ORDER-68c031f1005ff', 'Wallet used during checkout', '2025-09-09 13:56:01', '2025-09-09 13:56:01'),
(60, 10, 'credit', 1.00, 'PAYBYBANK-68c034a405f17', 'Pay by bank bonus', '2025-09-09 14:07:32', '2025-09-09 14:07:32'),
(61, 10, 'debit', 302.92, 'ORDER-68c034d27ae47', 'Wallet used during checkout', '2025-09-09 14:08:18', '2025-09-09 14:08:18'),
(62, 10, 'debit', 302.92, 'ORDER-68c03960134bb', 'Wallet used during checkout', '2025-09-09 14:27:44', '2025-09-09 14:27:44'),
(63, 2, 'credit', 1.00, 'PAYBYBANK-68c110b72ff21', 'Pay by bank bonus', '2025-09-10 05:46:31', '2025-09-10 05:46:31'),
(64, 2, 'credit', 1.00, 'PAYBYBANK-68c179c7f2e88', 'Pay by bank bonus', '2025-09-10 13:14:47', '2025-09-10 13:14:47'),
(65, 3, 'credit', 1000.00, 'add new wallet', 'Wallet credited by Admin via panel', '2025-09-10 13:28:34', '2025-09-10 13:28:34'),
(66, 3, 'debit', 400.00, NULL, 'Wallet debited by Admin via panel', '2025-09-10 13:29:18', '2025-09-10 13:29:18'),
(67, 2, 'credit', 1.00, 'PAYBYBANK-68c1b48e1200e', 'Pay by bank bonus', '2025-09-10 17:25:34', '2025-09-10 17:25:34'),
(68, 2, 'debit', 3717.07, NULL, 'Wallet debited by Admin via panel', '2025-09-11 08:43:39', '2025-09-11 08:43:39'),
(69, 10, 'credit', 1.00, 'PAYBYBANK-68d6852b59863', 'Pay by bank bonus', '2025-09-26 12:20:59', '2025-09-26 12:20:59'),
(70, 10, 'credit', 1.00, 'PAYBYBANK-BONUS-ORDER-159', 'Pay by bank bonus on mark as paid', '2025-10-06 21:11:38', '2025-10-06 21:11:38'),
(71, 2, 'credit', 1.00, 'PAYBYBANK-BONUS-ORDER-156', 'Pay by bank bonus (bulk mark paid)', '2025-10-06 21:13:28', '2025-10-06 21:13:28'),
(72, 2, 'credit', 1.00, 'PAYBYBANK-BONUS-ORDER-157', 'Pay by bank bonus (bulk mark paid)', '2025-10-06 21:13:28', '2025-10-06 21:13:28'),
(73, 2, 'credit', 1.00, 'PAYBYBANK-BONUS-ORDER-158', 'Pay by bank bonus (bulk mark paid)', '2025-10-06 21:13:28', '2025-10-06 21:13:28'),
(74, 2, 'credit', 1.00, 'PAYBYBANK-BONUS-ORDER-151', 'Pay by bank bonus (bulk mark paid)', '2025-10-06 21:13:40', '2025-10-06 21:13:40'),
(75, 10, 'credit', 1.00, 'PAYBYBANK-BONUS-ORDER-153', 'Pay by bank bonus (bulk mark paid)', '2025-10-06 21:13:40', '2025-10-06 21:13:40');

-- --------------------------------------------------------

--
-- Table structure for table `wishlists`
--

CREATE TABLE `wishlists` (
  `wishlist_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `mvariant_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `admins_email_unique` (`email`);

--
-- Indexes for table `bank_details`
--
ALTER TABLE `bank_details`
  ADD PRIMARY KEY (`bank_detail_id`);

--
-- Indexes for table `browsebanners`
--
ALTER TABLE `browsebanners`
  ADD PRIMARY KEY (`browsebanner_id`),
  ADD KEY `browsebanners_mcat_id_foreign` (`mcat_id`),
  ADD KEY `browsebanners_msubcat_id_foreign` (`msubcat_id`),
  ADD KEY `browsebanners_mproduct_id_foreign` (`mproduct_id`),
  ADD KEY `browsebanners_main_mcat_id_foreign` (`main_mcat_id`);

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`cart_item_id`),
  ADD KEY `cart_items_user_id_foreign` (`user_id`),
  ADD KEY `cart_items_mvariant_id_foreign` (`mvariant_id`);

--
-- Indexes for table `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`coupon_id`),
  ADD UNIQUE KEY `coupons_code_unique` (`code`),
  ADD KEY `coupons_main_mcat_id_foreign` (`main_mcat_id`);

--
-- Indexes for table `coupon_usages`
--
ALTER TABLE `coupon_usages`
  ADD PRIMARY KEY (`coupon_usage_id`),
  ADD UNIQUE KEY `coupon_usages_coupon_id_user_id_unique` (`coupon_id`,`user_id`),
  ADD KEY `coupon_usages_user_id_foreign` (`user_id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`rep_id`),
  ADD UNIQUE KEY `customers_email_unique` (`email`),
  ADD UNIQUE KEY `customers_rep_code_unique` (`rep_code`);

--
-- Indexes for table `customer_commissions`
--
ALTER TABLE `customer_commissions`
  ADD PRIMARY KEY (`customer_commission_id`),
  ADD KEY `customer_commissions_rep_id_foreign` (`rep_id`);

--
-- Indexes for table `delivery_methods`
--
ALTER TABLE `delivery_methods`
  ADD PRIMARY KEY (`delivery_method_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `fields`
--
ALTER TABLE `fields`
  ADD PRIMARY KEY (`field_id`);

--
-- Indexes for table `field_query_relations`
--
ALTER TABLE `field_query_relations`
  ADD KEY `field_query_relations_field_id_foreign` (`field_id`),
  ADD KEY `field_query_relations_query_id_foreign` (`query_id`);

--
-- Indexes for table `home_explore_deal_banners`
--
ALTER TABLE `home_explore_deal_banners`
  ADD PRIMARY KEY (`home_explore_deal_banner_id`),
  ADD KEY `home_explore_deal_banners_main_mcat_id_foreign` (`main_mcat_id`),
  ADD KEY `home_explore_deal_banners_mcat_id_foreign` (`mcat_id`),
  ADD KEY `home_explore_deal_banners_msubcat_id_foreign` (`msubcat_id`),
  ADD KEY `home_explore_deal_banners_mproduct_id_foreign` (`mproduct_id`);

--
-- Indexes for table `home_fruit_banners`
--
ALTER TABLE `home_fruit_banners`
  ADD PRIMARY KEY (`home_fruit_banner_id`),
  ADD KEY `home_fruit_banners_main_mcat_id_foreign` (`main_mcat_id`),
  ADD KEY `home_fruit_banners_mcat_id_foreign` (`mcat_id`),
  ADD KEY `home_fruit_banners_msubcat_id_foreign` (`msubcat_id`),
  ADD KEY `home_fruit_banners_mproduct_id_foreign` (`mproduct_id`);

--
-- Indexes for table `home_large_banners`
--
ALTER TABLE `home_large_banners`
  ADD PRIMARY KEY (`home_large_banner_id`),
  ADD KEY `home_large_banners_main_mcat_id_foreign` (`main_mcat_id`),
  ADD KEY `home_large_banners_mcat_id_foreign` (`mcat_id`),
  ADD KEY `home_large_banners_msubcat_id_foreign` (`msubcat_id`),
  ADD KEY `home_large_banners_mproduct_id_foreign` (`mproduct_id`);

--
-- Indexes for table `home_round_banners`
--
ALTER TABLE `home_round_banners`
  ADD PRIMARY KEY (`home_round_banner_id`),
  ADD KEY `home_round_banners_main_mcat_id_foreign` (`main_mcat_id`),
  ADD KEY `home_round_banners_mcat_id_foreign` (`mcat_id`),
  ADD KEY `home_round_banners_msubcat_id_foreign` (`msubcat_id`),
  ADD KEY `home_round_banners_mproduct_id_foreign` (`mproduct_id`);

--
-- Indexes for table `home_small_banners`
--
ALTER TABLE `home_small_banners`
  ADD PRIMARY KEY (`home_small_banner_id`),
  ADD KEY `home_small_banners_main_mcat_id_foreign` (`main_mcat_id`),
  ADD KEY `home_small_banners_mcat_id_foreign` (`mcat_id`),
  ADD KEY `home_small_banners_msubcat_id_foreign` (`msubcat_id`),
  ADD KEY `home_small_banners_mproduct_id_foreign` (`mproduct_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `loyalty_reward_banners`
--
ALTER TABLE `loyalty_reward_banners`
  ADD PRIMARY KEY (`loyalty_reward_banner_id`);

--
-- Indexes for table `main_categories`
--
ALTER TABLE `main_categories`
  ADD PRIMARY KEY (`main_mcat_id`);

--
-- Indexes for table `mbrands`
--
ALTER TABLE `mbrands`
  ADD PRIMARY KEY (`mbrand_id`);

--
-- Indexes for table `mcategories`
--
ALTER TABLE `mcategories`
  ADD PRIMARY KEY (`mcat_id`),
  ADD KEY `mcategories_main_mcat_id_foreign` (`main_mcat_id`);

--
-- Indexes for table `mcollection_autos`
--
ALTER TABLE `mcollection_autos`
  ADD PRIMARY KEY (`collection_auto_id`),
  ADD KEY `mcollection_autos_msubcat_id_foreign` (`msubcat_id`),
  ADD KEY `mcollection_autos_field_id_foreign` (`field_id`),
  ADD KEY `mcollection_autos_query_id_foreign` (`query_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mlocations`
--
ALTER TABLE `mlocations`
  ADD PRIMARY KEY (`mlocation_id`);

--
-- Indexes for table `moptions`
--
ALTER TABLE `moptions`
  ADD PRIMARY KEY (`moption_id`);

--
-- Indexes for table `mproducts`
--
ALTER TABLE `mproducts`
  ADD PRIMARY KEY (`mproduct_id`),
  ADD KEY `mproducts_mproduct_type_id_foreign` (`mproduct_type_id`),
  ADD KEY `mproducts_mbrand_id_foreign` (`mbrand_id`);

--
-- Indexes for table `mproduct_types`
--
ALTER TABLE `mproduct_types`
  ADD PRIMARY KEY (`mproduct_type_id`);

--
-- Indexes for table `mstocks`
--
ALTER TABLE `mstocks`
  ADD PRIMARY KEY (`mstock_id`),
  ADD KEY `mstocks_mlocation_id_foreign` (`mlocation_id`),
  ADD KEY `mstocks_mvariant_id_foreign` (`mvariant_id`);

--
-- Indexes for table `msubcategories`
--
ALTER TABLE `msubcategories`
  ADD PRIMARY KEY (`msubcat_id`),
  ADD UNIQUE KEY `msubcategories_msubcat_slug_unique` (`msubcat_slug`),
  ADD KEY `msubcategories_mcat_id_foreign` (`mcat_id`);

--
-- Indexes for table `mtags`
--
ALTER TABLE `mtags`
  ADD PRIMARY KEY (`mtag_id`);

--
-- Indexes for table `mvariants`
--
ALTER TABLE `mvariants`
  ADD PRIMARY KEY (`mvariant_id`),
  ADD KEY `mvariants_mproduct_id_foreign` (`mproduct_id`);

--
-- Indexes for table `mvariant_details`
--
ALTER TABLE `mvariant_details`
  ADD PRIMARY KEY (`mvariant_detail_id`),
  ADD KEY `mvariant_details_mvariant_id_foreign` (`mvariant_id`);

--
-- Indexes for table `new_products`
--
ALTER TABLE `new_products`
  ADD PRIMARY KEY (`new_product_id`),
  ADD KEY `new_products_mvariant_id_foreign` (`mvariant_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `orders_user_id_foreign` (`user_id`),
  ADD KEY `orders_user_company_address_id_foreign` (`user_company_address_id`),
  ADD KEY `orders_delivery_method_id_foreign` (`delivery_method_id`),
  ADD KEY `orders_coupon_id_foreign` (`coupon_id`),
  ADD KEY `orders_royalmail_order_identifier_index` (`royalmail_order_identifier`);

--
-- Indexes for table `order_commissions`
--
ALTER TABLE `order_commissions`
  ADD PRIMARY KEY (`order_commission_id`),
  ADD KEY `order_commissions_order_id_foreign` (`order_id`),
  ADD KEY `order_commissions_rep_id_foreign` (`rep_id`),
  ADD KEY `order_commissions_user_id_foreign` (`user_id`);

--
-- Indexes for table `order_fulfillments`
--
ALTER TABLE `order_fulfillments`
  ADD PRIMARY KEY (`order_fulfillment_id`),
  ADD KEY `order_fulfillments_order_id_foreign` (`order_id`);

--
-- Indexes for table `order_fulfillment_items`
--
ALTER TABLE `order_fulfillment_items`
  ADD PRIMARY KEY (`order_fulfillment_item_id`),
  ADD KEY `order_fulfillment_items_order_fulfillment_id_foreign` (`order_fulfillment_id`),
  ADD KEY `order_fulfillment_items_order_item_id_foreign` (`order_item_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_items_order_id_foreign` (`order_id`),
  ADD KEY `order_items_mvariant_id_foreign` (`mvariant_id`);

--
-- Indexes for table `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`page_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `product_vats`
--
ALTER TABLE `product_vats`
  ADD PRIMARY KEY (`product_vat_id`);

--
-- Indexes for table `product__offers`
--
ALTER TABLE `product__offers`
  ADD PRIMARY KEY (`product_offer_id`),
  ADD KEY `product__offers_mvariant_id_foreign` (`mvariant_id`);

--
-- Indexes for table `queries`
--
ALTER TABLE `queries`
  ADD PRIMARY KEY (`query_id`);

--
-- Indexes for table `referrals`
--
ALTER TABLE `referrals`
  ADD PRIMARY KEY (`ref_id`),
  ADD UNIQUE KEY `referrals_user_id_unique` (`user_id`),
  ADD KEY `referrals_referrer_id_foreign` (`referrer_id`);

--
-- Indexes for table `referral_invites`
--
ALTER TABLE `referral_invites`
  ADD PRIMARY KEY (`referral_invite_id`),
  ADD KEY `referral_invites_sender_user_id_foreign` (`sender_user_id`);

--
-- Indexes for table `service_solutions`
--
ALTER TABLE `service_solutions`
  ADD PRIMARY KEY (`service_solution_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`setting_id`),
  ADD UNIQUE KEY `settings_key_unique` (`key`);

--
-- Indexes for table `slider_headers`
--
ALTER TABLE `slider_headers`
  ADD PRIMARY KEY (`slider_header_id`);

--
-- Indexes for table `top_sellers`
--
ALTER TABLE `top_sellers`
  ADD PRIMARY KEY (`top_seller_id`),
  ADD KEY `top_sellers_mvariant_id_foreign` (`mvariant_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_referral_code_unique` (`referral_code`),
  ADD KEY `users_rep_id_foreign` (`rep_id`),
  ADD KEY `users_referred_by_foreign` (`referred_by`),
  ADD KEY `users_user_tag_id_foreign` (`user_tag_id`);

--
-- Indexes for table `user_company_addresses`
--
ALTER TABLE `user_company_addresses`
  ADD PRIMARY KEY (`user_company_address_id`),
  ADD KEY `user_company_addresses_user_id_foreign` (`user_id`);

--
-- Indexes for table `user_tags`
--
ALTER TABLE `user_tags`
  ADD PRIMARY KEY (`user_tag_id`),
  ADD UNIQUE KEY `user_tags_user_tag_name_unique` (`user_tag_name`);

--
-- Indexes for table `user_tag_prices`
--
ALTER TABLE `user_tag_prices`
  ADD PRIMARY KEY (`user_tag_price_id`),
  ADD KEY `user_tag_prices_user_tag_id_foreign` (`user_tag_id`),
  ADD KEY `user_tag_prices_mvariant_id_foreign` (`mvariant_id`);

--
-- Indexes for table `wallets`
--
ALTER TABLE `wallets`
  ADD PRIMARY KEY (`wallet_id`),
  ADD KEY `wallets_user_id_foreign` (`user_id`);

--
-- Indexes for table `wallet_transactions`
--
ALTER TABLE `wallet_transactions`
  ADD PRIMARY KEY (`wallet_transaction_id`),
  ADD KEY `wallet_transactions_wallet_id_foreign` (`wallet_id`);

--
-- Indexes for table `wishlists`
--
ALTER TABLE `wishlists`
  ADD PRIMARY KEY (`wishlist_id`),
  ADD KEY `wishlists_user_id_foreign` (`user_id`),
  ADD KEY `wishlists_mvariant_id_foreign` (`mvariant_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `bank_details`
--
ALTER TABLE `bank_details`
  MODIFY `bank_detail_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `browsebanners`
--
ALTER TABLE `browsebanners`
  MODIFY `browsebanner_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `cart_item_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=677;

--
-- AUTO_INCREMENT for table `coupons`
--
ALTER TABLE `coupons`
  MODIFY `coupon_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `coupon_usages`
--
ALTER TABLE `coupon_usages`
  MODIFY `coupon_usage_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `rep_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `customer_commissions`
--
ALTER TABLE `customer_commissions`
  MODIFY `customer_commission_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `delivery_methods`
--
ALTER TABLE `delivery_methods`
  MODIFY `delivery_method_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `fields`
--
ALTER TABLE `fields`
  MODIFY `field_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `home_explore_deal_banners`
--
ALTER TABLE `home_explore_deal_banners`
  MODIFY `home_explore_deal_banner_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `home_fruit_banners`
--
ALTER TABLE `home_fruit_banners`
  MODIFY `home_fruit_banner_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `home_large_banners`
--
ALTER TABLE `home_large_banners`
  MODIFY `home_large_banner_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `home_round_banners`
--
ALTER TABLE `home_round_banners`
  MODIFY `home_round_banner_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `home_small_banners`
--
ALTER TABLE `home_small_banners`
  MODIFY `home_small_banner_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `loyalty_reward_banners`
--
ALTER TABLE `loyalty_reward_banners`
  MODIFY `loyalty_reward_banner_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `main_categories`
--
ALTER TABLE `main_categories`
  MODIFY `main_mcat_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `mbrands`
--
ALTER TABLE `mbrands`
  MODIFY `mbrand_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `mcategories`
--
ALTER TABLE `mcategories`
  MODIFY `mcat_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `mcollection_autos`
--
ALTER TABLE `mcollection_autos`
  MODIFY `collection_auto_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=162;

--
-- AUTO_INCREMENT for table `mlocations`
--
ALTER TABLE `mlocations`
  MODIFY `mlocation_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `moptions`
--
ALTER TABLE `moptions`
  MODIFY `moption_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `mproducts`
--
ALTER TABLE `mproducts`
  MODIFY `mproduct_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mproduct_types`
--
ALTER TABLE `mproduct_types`
  MODIFY `mproduct_type_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `mstocks`
--
ALTER TABLE `mstocks`
  MODIFY `mstock_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=564;

--
-- AUTO_INCREMENT for table `msubcategories`
--
ALTER TABLE `msubcategories`
  MODIFY `msubcat_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mtags`
--
ALTER TABLE `mtags`
  MODIFY `mtag_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `mvariants`
--
ALTER TABLE `mvariants`
  MODIFY `mvariant_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=564;

--
-- AUTO_INCREMENT for table `mvariant_details`
--
ALTER TABLE `mvariant_details`
  MODIFY `mvariant_detail_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `new_products`
--
ALTER TABLE `new_products`
  MODIFY `new_product_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=160;

--
-- AUTO_INCREMENT for table `order_commissions`
--
ALTER TABLE `order_commissions`
  MODIFY `order_commission_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `order_fulfillments`
--
ALTER TABLE `order_fulfillments`
  MODIFY `order_fulfillment_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `order_fulfillment_items`
--
ALTER TABLE `order_fulfillment_items`
  MODIFY `order_fulfillment_item_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=534;

--
-- AUTO_INCREMENT for table `pages`
--
ALTER TABLE `pages`
  MODIFY `page_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_vats`
--
ALTER TABLE `product_vats`
  MODIFY `product_vat_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `product__offers`
--
ALTER TABLE `product__offers`
  MODIFY `product_offer_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `queries`
--
ALTER TABLE `queries`
  MODIFY `query_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `referrals`
--
ALTER TABLE `referrals`
  MODIFY `ref_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `referral_invites`
--
ALTER TABLE `referral_invites`
  MODIFY `referral_invite_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `service_solutions`
--
ALTER TABLE `service_solutions`
  MODIFY `service_solution_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `setting_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `slider_headers`
--
ALTER TABLE `slider_headers`
  MODIFY `slider_header_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `top_sellers`
--
ALTER TABLE `top_sellers`
  MODIFY `top_seller_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `user_company_addresses`
--
ALTER TABLE `user_company_addresses`
  MODIFY `user_company_address_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `user_tags`
--
ALTER TABLE `user_tags`
  MODIFY `user_tag_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `user_tag_prices`
--
ALTER TABLE `user_tag_prices`
  MODIFY `user_tag_price_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `wallets`
--
ALTER TABLE `wallets`
  MODIFY `wallet_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `wallet_transactions`
--
ALTER TABLE `wallet_transactions`
  MODIFY `wallet_transaction_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `wishlists`
--
ALTER TABLE `wishlists`
  MODIFY `wishlist_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=201;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `browsebanners`
--
ALTER TABLE `browsebanners`
  ADD CONSTRAINT `browsebanners_main_mcat_id_foreign` FOREIGN KEY (`main_mcat_id`) REFERENCES `main_categories` (`main_mcat_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `browsebanners_mcat_id_foreign` FOREIGN KEY (`mcat_id`) REFERENCES `mcategories` (`mcat_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `browsebanners_mproduct_id_foreign` FOREIGN KEY (`mproduct_id`) REFERENCES `mproducts` (`mproduct_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `browsebanners_msubcat_id_foreign` FOREIGN KEY (`msubcat_id`) REFERENCES `msubcategories` (`msubcat_id`) ON DELETE CASCADE;

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_mvariant_id_foreign` FOREIGN KEY (`mvariant_id`) REFERENCES `mvariants` (`mvariant_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_items_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `coupons`
--
ALTER TABLE `coupons`
  ADD CONSTRAINT `coupons_main_mcat_id_foreign` FOREIGN KEY (`main_mcat_id`) REFERENCES `main_categories` (`main_mcat_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `coupon_usages`
--
ALTER TABLE `coupon_usages`
  ADD CONSTRAINT `coupon_usages_coupon_id_foreign` FOREIGN KEY (`coupon_id`) REFERENCES `coupons` (`coupon_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `coupon_usages_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `customer_commissions`
--
ALTER TABLE `customer_commissions`
  ADD CONSTRAINT `customer_commissions_rep_id_foreign` FOREIGN KEY (`rep_id`) REFERENCES `customers` (`rep_id`) ON DELETE CASCADE;

--
-- Constraints for table `field_query_relations`
--
ALTER TABLE `field_query_relations`
  ADD CONSTRAINT `field_query_relations_field_id_foreign` FOREIGN KEY (`field_id`) REFERENCES `fields` (`field_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `field_query_relations_query_id_foreign` FOREIGN KEY (`query_id`) REFERENCES `queries` (`query_id`) ON DELETE CASCADE;

--
-- Constraints for table `home_fruit_banners`
--
ALTER TABLE `home_fruit_banners`
  ADD CONSTRAINT `home_fruit_banners_main_mcat_id_foreign` FOREIGN KEY (`main_mcat_id`) REFERENCES `main_categories` (`main_mcat_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `home_fruit_banners_mcat_id_foreign` FOREIGN KEY (`mcat_id`) REFERENCES `mcategories` (`mcat_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `home_fruit_banners_mproduct_id_foreign` FOREIGN KEY (`mproduct_id`) REFERENCES `mproducts` (`mproduct_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `home_fruit_banners_msubcat_id_foreign` FOREIGN KEY (`msubcat_id`) REFERENCES `msubcategories` (`msubcat_id`) ON DELETE CASCADE;

--
-- Constraints for table `home_round_banners`
--
ALTER TABLE `home_round_banners`
  ADD CONSTRAINT `home_round_banners_main_mcat_id_foreign` FOREIGN KEY (`main_mcat_id`) REFERENCES `main_categories` (`main_mcat_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `home_round_banners_mcat_id_foreign` FOREIGN KEY (`mcat_id`) REFERENCES `mcategories` (`mcat_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `home_round_banners_mproduct_id_foreign` FOREIGN KEY (`mproduct_id`) REFERENCES `mproducts` (`mproduct_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `home_round_banners_msubcat_id_foreign` FOREIGN KEY (`msubcat_id`) REFERENCES `msubcategories` (`msubcat_id`) ON DELETE CASCADE;

--
-- Constraints for table `mcategories`
--
ALTER TABLE `mcategories`
  ADD CONSTRAINT `mcategories_main_mcat_id_foreign` FOREIGN KEY (`main_mcat_id`) REFERENCES `main_categories` (`main_mcat_id`) ON DELETE CASCADE;

--
-- Constraints for table `mcollection_autos`
--
ALTER TABLE `mcollection_autos`
  ADD CONSTRAINT `mcollection_autos_field_id_foreign` FOREIGN KEY (`field_id`) REFERENCES `fields` (`field_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `mcollection_autos_msubcat_id_foreign` FOREIGN KEY (`msubcat_id`) REFERENCES `msubcategories` (`msubcat_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `mcollection_autos_query_id_foreign` FOREIGN KEY (`query_id`) REFERENCES `queries` (`query_id`) ON DELETE CASCADE;

--
-- Constraints for table `mproducts`
--
ALTER TABLE `mproducts`
  ADD CONSTRAINT `mproducts_mbrand_id_foreign` FOREIGN KEY (`mbrand_id`) REFERENCES `mbrands` (`mbrand_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `mproducts_mproduct_type_id_foreign` FOREIGN KEY (`mproduct_type_id`) REFERENCES `mproduct_types` (`mproduct_type_id`) ON DELETE CASCADE;

--
-- Constraints for table `mstocks`
--
ALTER TABLE `mstocks`
  ADD CONSTRAINT `mstocks_mlocation_id_foreign` FOREIGN KEY (`mlocation_id`) REFERENCES `mlocations` (`mlocation_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `mstocks_mvariant_id_foreign` FOREIGN KEY (`mvariant_id`) REFERENCES `mvariants` (`mvariant_id`) ON DELETE CASCADE;

--
-- Constraints for table `msubcategories`
--
ALTER TABLE `msubcategories`
  ADD CONSTRAINT `msubcategories_mcat_id_foreign` FOREIGN KEY (`mcat_id`) REFERENCES `mcategories` (`mcat_id`) ON DELETE CASCADE;

--
-- Constraints for table `mvariants`
--
ALTER TABLE `mvariants`
  ADD CONSTRAINT `mvariants_mproduct_id_foreign` FOREIGN KEY (`mproduct_id`) REFERENCES `mproducts` (`mproduct_id`) ON DELETE CASCADE;

--
-- Constraints for table `mvariant_details`
--
ALTER TABLE `mvariant_details`
  ADD CONSTRAINT `mvariant_details_mvariant_id_foreign` FOREIGN KEY (`mvariant_id`) REFERENCES `mvariants` (`mvariant_id`) ON DELETE CASCADE;

--
-- Constraints for table `new_products`
--
ALTER TABLE `new_products`
  ADD CONSTRAINT `new_products_mvariant_id_foreign` FOREIGN KEY (`mvariant_id`) REFERENCES `mvariants` (`mvariant_id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_coupon_id_foreign` FOREIGN KEY (`coupon_id`) REFERENCES `coupons` (`coupon_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_delivery_method_id_foreign` FOREIGN KEY (`delivery_method_id`) REFERENCES `delivery_methods` (`delivery_method_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_user_company_address_id_foreign` FOREIGN KEY (`user_company_address_id`) REFERENCES `user_company_addresses` (`user_company_address_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_commissions`
--
ALTER TABLE `order_commissions`
  ADD CONSTRAINT `order_commissions_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_commissions_rep_id_foreign` FOREIGN KEY (`rep_id`) REFERENCES `customers` (`rep_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_commissions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_fulfillments`
--
ALTER TABLE `order_fulfillments`
  ADD CONSTRAINT `order_fulfillments_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE;

--
-- Constraints for table `order_fulfillment_items`
--
ALTER TABLE `order_fulfillment_items`
  ADD CONSTRAINT `order_fulfillment_items_order_fulfillment_id_foreign` FOREIGN KEY (`order_fulfillment_id`) REFERENCES `order_fulfillments` (`order_fulfillment_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_fulfillment_items_order_item_id_foreign` FOREIGN KEY (`order_item_id`) REFERENCES `order_items` (`order_item_id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_mvariant_id_foreign` FOREIGN KEY (`mvariant_id`) REFERENCES `mvariants` (`mvariant_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE;

--
-- Constraints for table `product__offers`
--
ALTER TABLE `product__offers`
  ADD CONSTRAINT `product__offers_mvariant_id_foreign` FOREIGN KEY (`mvariant_id`) REFERENCES `mvariants` (`mvariant_id`) ON DELETE CASCADE;

--
-- Constraints for table `referrals`
--
ALTER TABLE `referrals`
  ADD CONSTRAINT `referrals_referrer_id_foreign` FOREIGN KEY (`referrer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `referrals_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `referral_invites`
--
ALTER TABLE `referral_invites`
  ADD CONSTRAINT `referral_invites_sender_user_id_foreign` FOREIGN KEY (`sender_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `top_sellers`
--
ALTER TABLE `top_sellers`
  ADD CONSTRAINT `top_sellers_mvariant_id_foreign` FOREIGN KEY (`mvariant_id`) REFERENCES `mvariants` (`mvariant_id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_referred_by_foreign` FOREIGN KEY (`referred_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `users_rep_id_foreign` FOREIGN KEY (`rep_id`) REFERENCES `customers` (`rep_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `users_user_tag_id_foreign` FOREIGN KEY (`user_tag_id`) REFERENCES `user_tags` (`user_tag_id`) ON DELETE SET NULL;

--
-- Constraints for table `user_company_addresses`
--
ALTER TABLE `user_company_addresses`
  ADD CONSTRAINT `user_company_addresses_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_tag_prices`
--
ALTER TABLE `user_tag_prices`
  ADD CONSTRAINT `user_tag_prices_mvariant_id_foreign` FOREIGN KEY (`mvariant_id`) REFERENCES `mvariants` (`mvariant_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_tag_prices_user_tag_id_foreign` FOREIGN KEY (`user_tag_id`) REFERENCES `user_tags` (`user_tag_id`) ON DELETE CASCADE;

--
-- Constraints for table `wallets`
--
ALTER TABLE `wallets`
  ADD CONSTRAINT `wallets_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `wallet_transactions`
--
ALTER TABLE `wallet_transactions`
  ADD CONSTRAINT `wallet_transactions_wallet_id_foreign` FOREIGN KEY (`wallet_id`) REFERENCES `wallets` (`wallet_id`) ON DELETE CASCADE;

--
-- Constraints for table `wishlists`
--
ALTER TABLE `wishlists`
  ADD CONSTRAINT `wishlists_mvariant_id_foreign` FOREIGN KEY (`mvariant_id`) REFERENCES `mvariants` (`mvariant_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wishlists_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
