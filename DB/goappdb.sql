-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 21, 2025 at 09:31 AM
-- Server version: 10.11.10-MariaDB
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u904410008_goappdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `name`, `email`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', 'superadmin@gmail.com', '$2y$12$xV369rMDT6WB/JG.ckvvdus2vSqmwaOHfX8rvCmIWCA3lbWi3A8.y', NULL, '2025-01-10 18:35:10', '2025-01-10 18:35:10');

-- --------------------------------------------------------

--
-- Table structure for table `brands`
--

CREATE TABLE `brands` (
  `brand_id` bigint(20) UNSIGNED NOT NULL,
  `shop_id` bigint(20) UNSIGNED NOT NULL,
  `brand_name` varchar(255) NOT NULL,
  `brand_image` varchar(255) DEFAULT NULL,
  `brand_status` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `brands`
--

INSERT INTO `brands` (`brand_id`, `shop_id`, `brand_name`, `brand_image`, `brand_status`, `created_at`, `updated_at`) VALUES
(2, 4, 'Smok', 'goapp/images/brand/brand_6793972a9211c.png', 1, '2025-01-24 13:35:38', '2025-01-24 13:35:48'),
(3, 4, 'Vaporesso', 'goapp/images/brand/brand_679397429986c.png', 1, '2025-01-24 13:36:02', '2025-01-24 13:36:06'),
(5, 1, 'Adidas', 'goapp/images/brand/brand_679f9efe7167b.png', 1, '2025-02-02 16:36:14', '2025-02-02 16:37:30'),
(6, 1, 'Nike', 'goapp/images/brand/brand_679f9f065bad0.png', 1, '2025-02-02 16:36:22', '2025-02-02 16:37:31');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `cat_id` bigint(20) UNSIGNED NOT NULL,
  `cat_title` varchar(255) NOT NULL,
  `cat_slug` varchar(255) NOT NULL,
  `cat_desc` longtext DEFAULT NULL,
  `cat_image` varchar(255) DEFAULT NULL,
  `cat_type` enum('manual','auto') NOT NULL DEFAULT 'manual',
  `shop_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`cat_id`, `cat_title`, `cat_slug`, `cat_desc`, `cat_image`, `cat_type`, `shop_id`, `created_at`, `updated_at`) VALUES
(1, 'Mens Shoes', 'mens-shoes-679f9fc39a1d7', NULL, 'goapp/images/category/category_679f9fc36787d.png', 'auto', 1, '2025-02-02 16:39:31', '2025-02-02 16:39:31'),
(2, 'Men Cat', 'men-cat-679fa0297f3b6', 'Men Cat', 'goapp/images/category/category_679fa0294a16e.png', 'manual', 1, '2025-02-02 16:41:13', '2025-02-02 16:41:13'),
(3, 'manual category', 'manual-category-67a1cef691d7e', 'manual category testing', 'goapp/images/category/category_67a1cef641ce7.png', 'manual', 4, '2025-02-03 04:52:56', '2025-02-04 08:25:26'),
(4, 'auto category', 'auto-category-67a1cf3b106b5', 'auto category testing', 'goapp/images/category/category_67a1cf20de32c.png', 'auto', 4, '2025-02-03 04:53:49', '2025-02-04 08:26:35'),
(5, 'auto category 1', 'auto-category-1-67a1cf454c490', 'auto category testing 1', 'goapp/images/category/category_67a1cf4529883.png', 'auto', 4, '2025-02-03 05:56:16', '2025-02-04 08:26:45'),
(6, 'Shoes CATGORY', 'shoes-catgory-67a2049f5558e', NULL, 'goapp/images/category/category_67a2049f146ce.png', 'auto', 1, '2025-02-04 12:14:23', '2025-02-04 12:14:23'),
(7, 'Nik Brand', 'nik-brand-67a204e565065', NULL, 'goapp/images/category/category_67a204e5447d1.png', 'auto', 1, '2025-02-04 12:15:33', '2025-02-04 12:15:33'),
(8, 'Adi Brand', 'adi-brand-67a2053fb2c92', NULL, 'goapp/images/category/category_67a2053f7234a.png', 'auto', 1, '2025-02-04 12:17:03', '2025-02-04 12:17:03');

-- --------------------------------------------------------

--
-- Table structure for table `category_autos`
--

CREATE TABLE `category_autos` (
  `category_auto_id` bigint(20) UNSIGNED NOT NULL,
  `cat_id` bigint(20) UNSIGNED NOT NULL,
  `field_id` bigint(20) UNSIGNED NOT NULL,
  `query_id` bigint(20) UNSIGNED NOT NULL,
  `value` varchar(255) NOT NULL,
  `logical_operator` enum('all','any') NOT NULL DEFAULT 'all',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `category_autos`
--

INSERT INTO `category_autos` (`category_auto_id`, `cat_id`, `field_id`, `query_id`, `value`, `logical_operator`, `created_at`, `updated_at`) VALUES
(6, 4, 2, 1, 'Disposable Vape', 'all', '2025-02-04 08:26:35', '2025-02-04 08:26:35'),
(7, 5, 2, 1, 'Disposable Vape', 'all', '2025-02-04 08:26:45', '2025-02-04 08:26:45'),
(8, 5, 3, 1, 'Smok', 'all', '2025-02-04 08:26:45', '2025-02-04 08:26:45'),
(9, 6, 2, 1, 'Shoes', 'all', '2025-02-04 12:14:23', '2025-02-04 12:14:23'),
(10, 7, 3, 5, 'Nik', 'all', '2025-02-04 12:15:33', '2025-02-04 12:15:33'),
(11, 8, 3, 5, 'Adi', 'all', '2025-02-04 12:17:03', '2025-02-04 12:17:03');

-- --------------------------------------------------------

--
-- Table structure for table `category_manuals`
--

CREATE TABLE `category_manuals` (
  `category_manual_id` bigint(20) UNSIGNED NOT NULL,
  `cat_id` bigint(20) UNSIGNED NOT NULL,
  `product_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`product_ids`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `category_manuals`
--

INSERT INTO `category_manuals` (`category_manual_id`, `cat_id`, `product_ids`, `created_at`, `updated_at`) VALUES
(1, 1, '[]', '2025-02-02 16:39:31', '2025-02-02 16:39:31'),
(2, 2, '[\"4\"]', '2025-02-02 16:41:13', '2025-02-02 16:41:13'),
(3, 3, '[\"1\",\"2\"]', '2025-02-03 04:52:56', '2025-02-03 04:52:56'),
(4, 4, '[]', '2025-02-03 04:53:49', '2025-02-03 04:53:49');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fields`
--

CREATE TABLE `fields` (
  `field_id` bigint(20) UNSIGNED NOT NULL,
  `field_name` varchar(255) NOT NULL,
  `product_field_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `fields`
--

INSERT INTO `fields` (`field_id`, `field_name`, `product_field_name`, `created_at`, `updated_at`) VALUES
(1, 'Title', 'product_title', '2025-02-03 11:09:07', '2025-02-03 11:09:07'),
(2, 'Type', 'product_type_name', '2025-02-03 11:09:07', '2025-02-03 11:09:07'),
(3, 'Brand', 'brand_name', '2025-02-03 11:10:30', '2025-02-03 11:10:30'),
(4, 'Price', 'price', '2025-02-03 11:10:30', '2025-02-03 11:10:30'),
(5, 'Inventory stock\r\n', 'qty', '2025-02-03 11:10:30', '2025-02-03 11:10:30');

-- --------------------------------------------------------

--
-- Table structure for table `field_query_relations`
--

CREATE TABLE `field_query_relations` (
  `field_id` bigint(20) UNSIGNED NOT NULL,
  `query_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `field_query_relations`
--

INSERT INTO `field_query_relations` (`field_id`, `query_id`, `created_at`, `updated_at`) VALUES
(1, 1, '2025-02-03 11:17:38', '2025-02-03 11:17:38'),
(1, 2, '2025-02-03 11:17:38', '2025-02-03 11:17:38'),
(1, 3, '2025-02-03 11:18:39', '2025-02-03 11:18:39'),
(1, 4, '2025-02-03 11:18:39', '2025-02-03 11:18:39'),
(1, 5, '2025-02-03 11:10:30', '2025-02-03 11:10:30'),
(1, 6, '2025-02-03 11:15:14', '2025-02-03 11:15:14'),
(2, 1, '2025-02-03 11:15:14', '2025-02-03 11:15:14'),
(2, 2, '2025-02-03 11:15:14', '2025-02-03 11:15:14'),
(2, 3, '2025-02-03 11:15:14', '2025-02-03 11:15:14'),
(2, 4, '2025-02-03 11:15:14', '2025-02-03 11:15:14'),
(2, 5, '2025-02-03 11:15:14', '2025-02-03 11:15:14'),
(2, 6, '2025-02-03 11:15:14', '2025-02-03 11:15:14'),
(3, 1, '2025-02-03 11:15:14', '2025-02-03 11:15:14'),
(3, 2, '2025-02-03 11:15:14', '2025-02-03 11:15:14'),
(3, 3, '2025-02-03 11:15:14', '2025-02-03 11:15:14'),
(3, 4, '2025-02-03 11:15:14', '2025-02-03 11:15:14'),
(3, 5, '2025-02-03 11:15:14', '2025-02-03 11:15:14'),
(3, 6, '2025-02-03 11:15:14', '2025-02-03 11:15:14'),
(4, 1, '2025-02-03 11:15:14', '2025-02-03 11:15:14'),
(4, 2, '2025-02-03 11:15:14', '2025-02-03 11:15:14'),
(4, 7, '2025-02-03 11:15:14', '2025-02-03 11:15:14'),
(4, 8, '2025-02-03 11:15:14', '2025-02-03 11:15:14'),
(5, 1, '2025-02-03 11:15:14', '2025-02-03 11:15:14'),
(5, 7, '2025-02-03 11:15:14', '2025-02-03 11:15:14'),
(5, 8, '2025-02-03 11:15:14', '2025-02-03 11:15:14');

-- --------------------------------------------------------

--
-- Table structure for table `mbrands`
--

CREATE TABLE `mbrands` (
  `mbrand_id` bigint(20) UNSIGNED NOT NULL,
  `mbrand_name` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2025_01_02_095736_create_admins_table', 1),
(6, '2025_01_02_095815_create_shops_table', 1),
(7, '2025_01_02_095833_create_shop_users_table', 1),
(8, '2025_01_02_101314_create_product_types_table', 1),
(9, '2025_01_02_101533_create_brands_table', 1),
(10, '2025_01_02_101609_create_tags_table', 1),
(11, '2025_01_14_080317_create_option_names_table', 2),
(12, '2025_01_14_122321_create_products_table', 2),
(13, '2025_01_14_123124_create_variants_table', 2),
(14, '2025_01_14_123630_create_variant_details_table', 2),
(15, '2025_01_14_125331_create_product_tags_table', 2),
(16, '2025_01_22_075515_add_product_slug_to_products_table', 2),
(17, '2025_01_29_121342_create_categories_table', 3),
(18, '2025_01_29_121411_create_fields_table', 3),
(19, '2025_01_29_121428_create_queries_table', 3),
(20, '2025_01_29_121518_create_field_query_relations_table', 3),
(21, '2025_01_29_121650_create_category_autos_table', 3),
(22, '2025_01_29_121704_create_category_manuals_table', 3),
(23, '2025_03_04_060142_create_mproduct_types_table', 4),
(24, '2025_03_04_060324_create_mbrands_table', 4),
(25, '2025_03_04_060418_create_mtags_table', 4),
(31, '2025_03_04_074450_create_moptions_table', 4),
(34, '2025_03_13_125555_create_mvariant_details_table', 5),
(35, '2025_03_13_125605_create_mlocations_table', 5),
(36, '2025_03_13_125612_create_mstocks_table', 5),
(37, '2025_03_13_125530_create_mproducts_table', 6),
(38, '2025_03_13_125543_create_mvariants_table', 6),
(39, '2025_04_18_075624_create_pages_table', 7);

-- --------------------------------------------------------

--
-- Table structure for table `mlocations`
--

CREATE TABLE `mlocations` (
  `mlocation_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT 'default',
  `adresss` varchar(255) NOT NULL DEFAULT 'default location',
  `is_default` enum('true','false') NOT NULL DEFAULT 'false',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mlocations`
--

INSERT INTO `mlocations` (`mlocation_id`, `name`, `adresss`, `is_default`, `created_at`, `updated_at`) VALUES
(1, 'default', 'default', 'true', '2025-03-15 09:54:24', '2025-03-15 09:54:24');

-- --------------------------------------------------------

--
-- Table structure for table `moptions`
--

CREATE TABLE `moptions` (
  `moption_id` bigint(20) UNSIGNED NOT NULL,
  `moption_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `moptions`
--

INSERT INTO `moptions` (`moption_id`, `moption_name`, `created_at`, `updated_at`) VALUES
(1, 'Colour', '2025-03-12 06:14:53', '2025-03-12 06:14:53'),
(2, 'Size', '2025-03-12 06:15:00', '2025-03-12 06:15:49'),
(3, 'Flavour', '2025-03-12 06:15:06', '2025-03-12 06:15:06'),
(4, 'Strength', '2025-04-08 07:07:58', '2025-04-08 07:07:58');

-- --------------------------------------------------------

--
-- Table structure for table `mproducts`
--

CREATE TABLE `mproducts` (
  `mproduct_id` bigint(20) UNSIGNED NOT NULL,
  `mproduct_title` varchar(255) NOT NULL,
  `mproduct_image` varchar(255) DEFAULT NULL,
  `mproduct_slug` varchar(255) NOT NULL,
  `mproduct_desc` varchar(255) DEFAULT NULL,
  `status` enum('Draft','Active') NOT NULL DEFAULT 'Draft',
  `mproduct_type_id` bigint(20) UNSIGNED DEFAULT NULL,
  `mbrand_id` bigint(20) UNSIGNED DEFAULT NULL,
  `mtags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`mtags`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mproducts`
--

INSERT INTO `mproducts` (`mproduct_id`, `mproduct_title`, `mproduct_image`, `mproduct_slug`, `mproduct_desc`, `status`, `mproduct_type_id`, `mbrand_id`, `mtags`, `created_at`, `updated_at`) VALUES
(1, 'hello', 'goapp/images/mproduct/mproduct_67f54947504d7.png', 'hello-67f54947b3adc', '', 'Draft', NULL, NULL, '[]', '2025-04-08 16:05:27', '2025-04-08 16:05:27'),
(2, 'Fanta 355ml', 'goapp/images/mproduct/mproduct_67f555e01e593.png', 'fanta-355ml-67f555e07b4a8', 'Fanta 355ml', 'Active', NULL, NULL, '[]', '2025-04-08 16:59:12', '2025-04-08 16:59:12');

-- --------------------------------------------------------

--
-- Table structure for table `mproduct_types`
--

CREATE TABLE `mproduct_types` (
  `mproduct_type_id` bigint(20) UNSIGNED NOT NULL,
  `mproduct_type_name` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mstocks`
--

CREATE TABLE `mstocks` (
  `mstock_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` bigint(20) DEFAULT NULL,
  `mlocation_id` bigint(20) UNSIGNED NOT NULL,
  `mvariant_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mstocks`
--

INSERT INTO `mstocks` (`mstock_id`, `quantity`, `mlocation_id`, `mvariant_id`, `created_at`, `updated_at`) VALUES
(1, 0, 1, 1, '2025-04-08 16:05:27', '2025-04-08 16:05:27'),
(2, 10, 1, 2, '2025-04-08 16:59:12', '2025-04-08 16:59:12'),
(3, 9, 1, 3, '2025-04-08 16:59:12', '2025-04-08 16:59:12');

-- --------------------------------------------------------

--
-- Table structure for table `mtags`
--

CREATE TABLE `mtags` (
  `mtag_id` bigint(20) UNSIGNED NOT NULL,
  `mtag_name` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mvariants`
--

CREATE TABLE `mvariants` (
  `mvariant_id` bigint(20) UNSIGNED NOT NULL,
  `sku` varchar(255) NOT NULL,
  `mvariant_image` varchar(255) DEFAULT NULL,
  `price` double(8,2) DEFAULT NULL,
  `compare_price` double(8,2) DEFAULT NULL,
  `cost_price` double(8,2) DEFAULT NULL,
  `taxable` tinyint(4) NOT NULL DEFAULT 0,
  `barcode` varchar(255) DEFAULT NULL,
  `weight` double(8,2) DEFAULT NULL,
  `weightunit` enum('kg','g') NOT NULL DEFAULT 'kg',
  `isvalidatedetails` tinyint(4) NOT NULL DEFAULT 0,
  `mproduct_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mvariants`
--

INSERT INTO `mvariants` (`mvariant_id`, `sku`, `mvariant_image`, `price`, `compare_price`, `cost_price`, `taxable`, `barcode`, `weight`, `weightunit`, `isvalidatedetails`, `mproduct_id`, `created_at`, `updated_at`) VALUES
(1, 'LVKKM', NULL, 0.00, 0.00, 0.00, 0, '', 0.00, 'kg', 0, 1, '2025-04-08 16:05:27', '2025-04-08 16:05:27'),
(2, 'RRE3W-Pine', 'goapp/images/mvproduct/mvproduct_67f555e07b7dc.png', 2.50, 0.00, 0.00, 0, '', 0.00, 'kg', 1, 2, '2025-04-08 16:59:12', '2025-04-08 16:59:12'),
(3, 'RRE3W-Berry', 'goapp/images/mvproduct/mvproduct_67f555e091052.png', 2.50, 0.00, 0.00, 0, '', 0.00, 'kg', 1, 2, '2025-04-08 16:59:12', '2025-04-08 16:59:12');

-- --------------------------------------------------------

--
-- Table structure for table `mvariant_details`
--

CREATE TABLE `mvariant_details` (
  `mvariant_detail_id` bigint(20) UNSIGNED NOT NULL,
  `options` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`options`)),
  `option_value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`option_value`)),
  `mvariant_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mvariant_details`
--

INSERT INTO `mvariant_details` (`mvariant_detail_id`, `options`, `option_value`, `mvariant_id`, `created_at`, `updated_at`) VALUES
(1, '[]', '[]', 1, '2025-04-08 16:05:27', '2025-04-08 16:05:27'),
(2, '[\"Flavour\"]', '{\"Flavour\":\"pineapple\"}', 2, '2025-04-08 16:59:12', '2025-04-08 16:59:12'),
(3, '[\"Flavour\"]', '{\"Flavour\":\"berry\"}', 3, '2025-04-08 16:59:12', '2025-04-08 16:59:12');

-- --------------------------------------------------------

--
-- Table structure for table `option_names`
--

CREATE TABLE `option_names` (
  `option_id` bigint(20) UNSIGNED NOT NULL,
  `option_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `option_names`
--

INSERT INTO `option_names` (`option_id`, `option_name`, `created_at`, `updated_at`) VALUES
(1, 'Color', '2025-01-24 13:41:09', '2025-01-24 13:41:09'),
(2, 'Size', '2025-01-24 13:41:15', '2025-01-24 13:41:15'),
(3, 'Flavour', '2025-01-24 13:41:23', '2025-01-24 13:41:23'),
(4, 'Strength', '2025-01-24 13:41:29', '2025-01-24 13:41:29');

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE `pages` (
  `page_id` bigint(20) UNSIGNED NOT NULL,
  `page_name` varchar(255) NOT NULL,
  `page_slug` varchar(255) NOT NULL,
  `page_content` longtext NOT NULL,
  `page_status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pages`
--

INSERT INTO `pages` (`page_id`, `page_name`, `page_slug`, `page_content`, `page_status`, `created_at`, `updated_at`) VALUES
(1, 'Terms Conditions', 'terms-conditions', '<p><strong>Terms and Conditions</strong></p><p><strong>Effective Date:</strong> 18/04/2025</p><p>Welcome to <strong>Truewebapp Smart Solutions</strong>, a mobile application (“App”) developed and managed by <strong>Truewebpro UK Private Ltd</strong> (“we”, “our”, or “us”).</p><p>By accessing or using this App, you (“Retailer”, “User”, or “you”) agree to be bound by these Terms and Conditions. If you do not agree with any part of these terms, please do not use the App.</p><p><strong>1. App Access and Eligibility</strong></p><ul><li>The App is available <strong>exclusively to approved retail partners</strong> for the purpose of discovering, ordering, and reordering products from authorized suppliers.</li><li>Our platform is <strong>not open to the general public</strong>, and access is restricted to <strong>mobile app use only</strong>. Our website, <a href=\"http://www.truewebapp.com\" rel=\"noopener noreferrer\" target=\"_blank\">www.truewebapp.com</a>, is for informational purposes and does not support direct purchases.</li></ul><p><br></p><p><strong>2. Account Registration</strong></p><ul><li>To use the App, you must register and be verified as a retailer.</li><li>You agree to provide accurate and complete information during registration and to update your details as necessary.</li><li>You are responsible for maintaining the confidentiality of your login credentials.</li></ul><p class=\"ql-align-center\"><br></p><p><strong>3. Ordering Products</strong></p><ul><li>Retailers can browse supplier products, add them to their eCommerce cart, and place orders directly via the App.</li><li>Orders are subject to supplier availability and confirmation.</li><li>We reserve the right to cancel or modify orders due to product availability or errors.</li></ul><p class=\"ql-align-center\"><br></p><p><strong>4. Reorder and Favourites</strong></p><ul><li>The App offers features like <strong>Reorder</strong>, allowing retailers to quickly place repeat orders.</li><li>You can <strong>mark brands or products as Favourites</strong> for quicker access and improved ordering experience.</li></ul><p class=\"ql-align-center\"><br></p><p><strong>5. Reward Points</strong></p><ul><li>Retailers may earn reward points through qualifying purchases and activities within the App.</li><li>Points are non-transferable, have no cash value, and may only be redeemed within the App per applicable guidelines.</li><li>We reserve the right to modify or discontinue the rewards program at any time.</li></ul><p class=\"ql-align-center\"><br></p><p><strong>6. Payments</strong></p><ul><li>Payments can be made using the <strong>Bank Payment Option</strong> as specified at checkout.</li><li>You are responsible for ensuring timely and correct payments. Orders may be cancelled for non-payment or incorrect information.</li></ul><p class=\"ql-align-center\"><br></p><p><strong>7. License and Use</strong></p><ul><li>We grant you a limited, non-transferable, revocable license to use the App solely for your internal business purposes as a retailer.</li><li>You may not reverse engineer, modify, or exploit the App in any unauthorized way.</li></ul><p class=\"ql-align-center\"><br></p><p><strong>8. Prohibited Activities</strong></p><p>You agree not to:</p><ul><li>Use the App for any illegal or unauthorized purpose.</li><li>Attempt to gain unauthorized access to our systems or data.</li><li>Share or misuse reward points or user accounts.</li></ul><p class=\"ql-align-center\"><br></p><p><strong>9. Intellectual Property</strong></p><p>All content, branding, and features within the App are the property of <strong>Truewebpro UK Private Ltd</strong> and are protected by intellectual property laws.</p><p><strong>10. Termination</strong></p><p>We may suspend or terminate your access to the App at our sole discretion, without notice, if we believe you are in violation of these terms or are misusing the platform.</p><p><strong>11. Limitation of Liability</strong></p><p>We are not liable for any indirect, incidental, or consequential damages resulting from your use of the App. All purchases and transactions are between the retailer and supplier; we facilitate but do not guarantee supplier performance.</p><p><strong>12. Modifications</strong></p><p>We may update these Terms and Conditions at any time. Continued use of the App after changes means you accept the revised terms.</p><p><strong>13. Governing Law</strong></p><p>These terms are governed by the laws of the United Kingdom. Any disputes will be subject to the exclusive jurisdiction of the courts of England and Wales.</p><p><strong>14. Contact Us</strong></p><p>For any questions about these Terms and Conditions, please contact:</p><p><strong>Truewebpro UK Private Ltd</strong></p><p>Email: info@truewebapp.com</p><p>Website: <a href=\"http://www.truewebapp.com\" rel=\"noopener noreferrer\" target=\"_blank\">www.truewebapp.com</a></p>', 'active', '2025-04-18 17:20:00', '2025-04-18 17:20:00'),
(2, 'Privacy Policy', 'privacy-policy', '<p><strong>Privacy Policy</strong></p><p><strong>Effective Date:</strong> 18/04/2025</p><p>Welcome to <strong>Truewebapp Smart Solutions</strong> (the \"App\"), operated by <strong>Truewebpro UK Private Ltd</strong> (\"we\", \"our\", or \"us\"). This Privacy Policy outlines how we collect, use, disclose, and protect your personal data when you use our mobile application.</p><p>By accessing or using the App, you agree to the collection and use of information in accordance with this Privacy Policy. If you do not agree, please do not use the App.</p><p><strong>1. Information We Collect</strong></p><p>We collect the following types of information:</p><p><strong>a. Personal Information:</strong></p><ul><li>Name, email address, phone number</li><li>Business details (e.g., shop name, GST, address)</li></ul><p><strong>b. Usage Information:</strong></p><ul><li>Products browsed and ordered</li><li>Favourite brands</li><li>Reward points and reorder history</li></ul><p><strong>c. Device and App Information:</strong></p><ul><li>Device ID, operating system, and app version</li><li>Location (if enabled by you)</li></ul><p><strong>2. How We Use Your Information</strong></p><p>We use your data to:</p><ul><li>Facilitate order placement between retailers and suppliers</li><li>Manage your reward points and reorders</li><li>Customize your user experience (e.g., save favourite brands)</li><li>Provide customer support and service notifications</li><li>Improve app performance and add new features</li><li>Process payments and maintain transaction history</li></ul><p><strong>3. Data Sharing and Disclosure</strong></p><p>We <strong>do not sell</strong> your personal data. We may share your information:</p><ul><li>With suppliers to fulfil your orders</li><li>With payment gateways and banks (for payment in bank option)</li><li>With third-party service providers (e.g., cloud hosting, analytics)</li><li>When required by law or to protect our legal rights</li></ul><p><strong>4. Data Security</strong></p><p>We implement appropriate technical and organizational measures to protect your data from unauthorized access, alteration, disclosure, or destruction.</p><p><strong>5. Data Retention</strong></p><p>We retain your data only as long as necessary for the purposes mentioned in this policy, unless a longer retention period is required by law.</p><p><strong>6. Your Rights</strong></p><p>You have the right to:</p><ul><li>Access and update your personal data</li><li>Delete your account and associated data (subject to legal obligations)</li><li>Withdraw consent for marketing communications</li></ul><p>You can exercise these rights by contacting our support team.</p><p><strong>7. Cookies and Tracking</strong></p><p>The App may use cookies or similar technologies to enhance user experience, track behaviour, and collect analytics data.</p><p><strong>8. Children\'s Privacy</strong></p><p>The App is not intended for children under the age of 18. We do not knowingly collect personal data from children.</p><p><strong>9. Changes to this Privacy Policy</strong></p><p>We may update this policy from time to time. We will notify you of any material changes through the App or via email.</p><p><strong>10. Contact Us</strong></p><p>If you have questions or concerns regarding this Privacy Policy, you can contact us at:</p><p><strong>Truewebpro UK Private Ltd</strong></p><p> Email: info@truewebapp.com</p><p> Website: <a href=\"https://truewebapp.com\" rel=\"noopener noreferrer\" target=\"_blank\">https://truewebapp.com</a></p>', 'active', '2025-04-18 17:20:00', '2025-04-18 17:20:00');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `product_title` varchar(255) NOT NULL,
  `product_slug` varchar(255) NOT NULL,
  `product_image` varchar(255) NOT NULL,
  `shop_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `product_title`, `product_slug`, `product_image`, `shop_id`, `created_at`, `updated_at`) VALUES
(1, 'Crystal Prime 2000VG', 'crystal-prime-2000vg-67c01ca6d4934', 'goapp/images/product/product_67c01ca634454.png', 4, '2025-01-24 13:40:07', '2025-02-27 08:04:54'),
(2, 'Crystal Prime 7000K', 'crystal-prime-7000k-67a071742ddbd', 'goapp/images/product/product_679398cfdd90c.png', 4, '2025-01-24 13:42:40', '2025-02-03 07:34:12'),
(3, 'Crystal Prime 7000K', 'crystal-prime-7000k-67939c535af1d', 'goapp/images/product/product_67939c1d4804c.png', 4, '2025-01-24 13:56:45', '2025-01-24 13:57:39'),
(4, 'Mens Shoes', 'mens-shoes-67a2058a2a697', 'goapp/images/product/product_679f9f73804c9.png', 1, '2025-02-02 16:38:11', '2025-02-04 12:18:18'),
(5, 'Mens Shoes', 'mens-shoes-679fa119c522a', 'goapp/images/product/product_679fa1199cb4f.png', 1, '2025-02-02 16:45:13', '2025-02-02 16:45:13'),
(6, 'Crystal Prime 2000VG', 'crystal-prime-2000vg-67a212f18cfc5', 'goapp/images/product/product_67a212f13122b.png', 4, '2025-02-04 13:14:49', '2025-02-04 13:15:29'),
(7, 'Crystal Prime 2000VG', 'crystal-prime-2000vg-67a2135a7c8f7', 'goapp/images/product/product_67a2135a43111.png', 4, '2025-02-04 13:16:18', '2025-02-04 13:17:14'),
(8, 'Crystal Prime 2000VG', 'crystal-prime-2000vg-67a2137006c9a', 'goapp/images/product/product_67a2135a43111.png', 4, '2025-02-04 13:17:36', '2025-02-04 13:17:36'),
(9, 'Mens Shoes', 'mens-shoes-67a34e0825dd3', 'goapp/images/product/product_679f9f73804c9.png', 1, '2025-02-05 11:39:36', '2025-02-05 11:39:52'),
(10, 'Mens Shoes', 'mens-shoes-67a34e1e5efda', 'goapp/images/product/product_679f9f73804c9.png', 1, '2025-02-05 11:40:02', '2025-02-05 11:40:14'),
(11, 'Mens Shoes', 'mens-shoes-67a34e357a03e', 'goapp/images/product/product_679f9f73804c9.png', 1, '2025-02-05 11:40:25', '2025-02-05 11:40:37');

-- --------------------------------------------------------

--
-- Table structure for table `product_tags`
--

CREATE TABLE `product_tags` (
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `tag_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_tags`
--

INSERT INTO `product_tags` (`product_id`, `tag_id`, `created_at`, `updated_at`) VALUES
(5, 1, '2025-02-02 16:45:13', '2025-02-02 16:45:13'),
(5, 5, '2025-02-02 16:45:13', '2025-02-02 16:45:13'),
(2, 2, '2025-02-03 07:34:12', '2025-02-03 07:34:12'),
(2, 4, '2025-02-03 07:34:12', '2025-02-03 07:34:12'),
(4, 1, '2025-02-04 12:18:18', '2025-02-04 12:18:18'),
(4, 5, '2025-02-04 12:18:18', '2025-02-04 12:18:18'),
(4, 6, '2025-02-04 12:18:18', '2025-02-04 12:18:18'),
(6, 2, '2025-02-04 13:15:29', '2025-02-04 13:15:29'),
(6, 4, '2025-02-04 13:15:29', '2025-02-04 13:15:29'),
(7, 2, '2025-02-04 13:17:14', '2025-02-04 13:17:14'),
(7, 4, '2025-02-04 13:17:14', '2025-02-04 13:17:14'),
(8, 2, '2025-02-04 13:17:36', '2025-02-04 13:17:36'),
(8, 4, '2025-02-04 13:17:36', '2025-02-04 13:17:36'),
(9, 1, '2025-02-05 11:39:52', '2025-02-05 11:39:52'),
(9, 5, '2025-02-05 11:39:52', '2025-02-05 11:39:52'),
(9, 6, '2025-02-05 11:39:52', '2025-02-05 11:39:52'),
(10, 1, '2025-02-05 11:40:14', '2025-02-05 11:40:14'),
(10, 5, '2025-02-05 11:40:14', '2025-02-05 11:40:14'),
(10, 6, '2025-02-05 11:40:14', '2025-02-05 11:40:14'),
(11, 1, '2025-02-05 11:40:37', '2025-02-05 11:40:37'),
(11, 5, '2025-02-05 11:40:37', '2025-02-05 11:40:37'),
(11, 6, '2025-02-05 11:40:37', '2025-02-05 11:40:37'),
(1, 2, '2025-02-27 08:04:54', '2025-02-27 08:04:54'),
(1, 4, '2025-02-27 08:04:54', '2025-02-27 08:04:54');

-- --------------------------------------------------------

--
-- Table structure for table `product_types`
--

CREATE TABLE `product_types` (
  `product_type_id` bigint(20) UNSIGNED NOT NULL,
  `shop_id` bigint(20) UNSIGNED NOT NULL,
  `product_type_name` varchar(255) NOT NULL,
  `product_type_status` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_types`
--

INSERT INTO `product_types` (`product_type_id`, `shop_id`, `product_type_name`, `product_type_status`, `created_at`, `updated_at`) VALUES
(1, 1, 'Shoes', 1, '2025-01-10 13:15:09', '2025-02-05 11:12:26'),
(2, 5, 'shirt', 1, '2025-01-23 05:55:03', '2025-01-23 05:59:52'),
(3, 4, 'shirt', 1, '2025-01-23 05:55:13', '2025-02-27 08:04:05'),
(4, 4, 'Disposable Vape', 0, '2025-01-24 13:34:57', '2025-02-05 05:52:00'),
(5, 4, 'E-Liquid', 1, '2025-01-24 13:35:05', '2025-02-27 08:04:07'),
(6, 1, 'Clothing', 1, '2025-02-02 16:35:39', '2025-02-05 11:12:18'),
(7, 4, 'LongShirt', 0, '2025-02-05 05:49:30', '2025-02-05 05:52:02'),
(8, 1, 'Wallets', 0, '2025-02-05 11:12:40', '2025-02-05 11:12:40');

-- --------------------------------------------------------

--
-- Table structure for table `queries`
--

CREATE TABLE `queries` (
  `query_id` bigint(20) UNSIGNED NOT NULL,
  `query_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `queries`
--

INSERT INTO `queries` (`query_id`, `query_name`, `created_at`, `updated_at`) VALUES
(1, 'is equal to', '2025-02-03 11:15:14', '2025-02-03 11:15:14'),
(2, 'is not equal to', '2025-02-03 11:15:14', '2025-02-03 11:15:14'),
(3, 'starts with', '2025-02-03 11:10:30', '2025-02-03 11:10:30'),
(4, 'ends with', '2025-02-03 11:15:14', '2025-02-03 11:15:14'),
(5, 'contains', '2025-02-03 11:15:14', '2025-02-03 11:15:14'),
(6, 'does not contains', '2025-02-03 11:15:14', '2025-02-03 11:15:14'),
(7, 'greater than', '2025-02-03 11:15:14', '2025-02-03 11:15:14'),
(8, 'less than', '2025-02-03 11:15:14', '2025-02-03 11:15:14');

-- --------------------------------------------------------

--
-- Table structure for table `shops`
--

CREATE TABLE `shops` (
  `shop_id` bigint(20) UNSIGNED NOT NULL,
  `shop_name` varchar(255) NOT NULL,
  `shop_status` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `shops`
--

INSERT INTO `shops` (`shop_id`, `shop_name`, `shop_status`, `created_at`, `updated_at`) VALUES
(1, 'Shop No 1', 1, '2025-01-10 13:12:54', '2025-01-10 13:13:47'),
(2, 'Trueweb Pro', 1, '2025-01-13 09:51:32', '2025-01-13 09:52:18'),
(3, 'Trueweb 2nd Shop', 1, '2025-01-13 09:53:55', '2025-01-13 09:54:03'),
(4, 'Suraj Shop', 1, '2025-01-22 10:19:59', '2025-01-23 05:53:32'),
(5, 'suraj shop 2', 0, '2025-01-23 05:53:49', '2025-01-23 05:53:49'),
(6, 'Master Vape Shop', 1, '2025-01-24 13:49:16', '2025-01-24 13:51:25'),
(7, 'Master Snack Shop', 1, '2025-01-24 13:51:48', '2025-01-24 13:51:57'),
(8, 'sanjayshop', 0, '2025-02-04 12:12:02', '2025-02-04 12:12:02'),
(9, 'Jewellery Shop', 0, '2025-02-05 10:53:51', '2025-02-05 10:53:51');

-- --------------------------------------------------------

--
-- Table structure for table `shop_users`
--

CREATE TABLE `shop_users` (
  `shop_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `user_role` enum('owner','staff') NOT NULL DEFAULT 'staff',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `shop_users`
--

INSERT INTO `shop_users` (`shop_id`, `user_id`, `user_role`, `created_at`, `updated_at`) VALUES
(1, 1, 'owner', '2025-01-10 13:13:01', '2025-01-10 13:13:01'),
(1, 2, 'staff', '2025-01-10 13:14:46', '2025-01-10 13:14:46'),
(1, 3, 'staff', '2025-01-10 13:17:39', '2025-01-10 13:17:39'),
(1, 8, 'staff', '2025-03-04 10:38:01', '2025-03-04 10:38:01'),
(2, 4, 'owner', '2025-01-13 09:51:52', '2025-01-13 09:51:52'),
(3, 4, 'owner', '2025-01-13 09:54:01', '2025-01-13 09:54:01'),
(4, 5, 'owner', '2025-01-22 10:20:26', '2025-01-22 10:20:26'),
(4, 6, 'staff', '2025-01-23 05:54:51', '2025-01-23 05:54:51'),
(5, 5, 'owner', '2025-01-23 05:53:55', '2025-01-23 05:53:55'),
(6, 4, 'owner', '2025-01-24 13:51:21', '2025-01-24 13:51:21'),
(7, 4, 'owner', '2025-01-24 13:51:53', '2025-01-24 13:51:53'),
(8, 5, 'staff', '2025-02-05 05:54:31', '2025-02-05 05:54:31'),
(8, 6, 'owner', '2025-02-04 12:12:38', '2025-02-04 12:12:38');

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

CREATE TABLE `tags` (
  `tag_id` bigint(20) UNSIGNED NOT NULL,
  `shop_id` bigint(20) UNSIGNED NOT NULL,
  `tag_name` varchar(255) NOT NULL,
  `tag_status` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tags`
--

INSERT INTO `tags` (`tag_id`, `shop_id`, `tag_name`, `tag_status`, `created_at`, `updated_at`) VALUES
(1, 1, 'Sports Shoes', 1, '2025-01-10 13:16:18', '2025-02-02 16:38:26'),
(2, 4, 'VGA', 1, '2025-01-23 05:56:44', '2025-01-24 13:38:03'),
(3, 5, 'shirtsdsdsd', 0, '2025-01-23 05:59:58', '2025-01-23 05:59:58'),
(4, 4, 'PGA', 1, '2025-01-24 13:38:01', '2025-01-24 13:38:04'),
(5, 1, 'Casual Shoes', 1, '2025-02-02 16:36:35', '2025-02-02 16:38:26'),
(6, 1, 'Mens', 1, '2025-02-02 16:36:40', '2025-02-02 16:38:27'),
(7, 1, 'Womens', 1, '2025-02-02 16:36:46', '2025-02-02 16:38:28');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'kapil', 'kapilimpactmindz@gmail.com', NULL, '$2y$10$Rwf2WJuCC5dIxtBliewv9erTDi220RvfgpsDiGIZH461UAnVoBKD.', NULL, '2025-01-10 13:12:01', '2025-01-10 13:12:01'),
(2, 'kapil user', 'kplsharma8185@gmail.com', NULL, '$2y$10$1ggT.yg7Qw52zQ5tyLa/CuiLkyfPUG3xBZxTCFNR56xb2TGXYG6fG', NULL, '2025-01-10 13:14:46', '2025-01-10 13:14:46'),
(3, 'Sukhraj Singh', 'sukhraj.impactmindz@gmail.com', NULL, '$2y$10$LaTGzFZGPztPbLK4RYHsP.7Im532Yqzfx0/DMLEj0yachyE3ojBCO', NULL, '2025-01-10 13:16:15', '2025-01-10 13:16:15'),
(4, 'Rony Singh', 'info@truewebpro.co.uk', NULL, '$2y$10$ijlwe4ulIi/ffnt6pIAWX.sbG1cgaPkIFwu9JU2W4bvALtP1V.xaC', NULL, '2025-01-13 09:50:51', '2025-01-13 09:50:51'),
(5, 'Suraj', 'suraj.impactmindz@gmail.com', NULL, '$2y$10$R/nPH.VCpLDkMyivSbTyYOUIGEU758uKssQ3HbFfdADozogalVchG', NULL, '2025-01-22 10:20:26', '2025-01-22 10:20:26'),
(6, 'sanjay', 'sanjay.impactmindz@gmail.com', NULL, '$2y$10$FDGRjqAZVsjdxbynqq5svuji1keLpmdiZdPLKj.Ee1KdQdYvhOToG', 'ch3fMSAQOqjeDtFQ8IBvd7SSGJwIqav9D5HS7uI0V0wKXiuY3KBmQdNIHsyY', '2025-01-23 05:54:51', '2025-01-23 05:54:51'),
(7, 'sanju', 'sanjubora84@gmail.com', NULL, '$2y$10$c9daDGpdF6xmdziwLF8KKubyJv0fdbPdwIQ5cPcWDLNM8n2XHGUBG', NULL, '2025-02-04 12:09:12', '2025-02-04 12:09:12'),
(8, 'Shivam', 'shivam.impactmindz@gmail.com', NULL, '$2y$10$6IAQ7oBgDft64fMIgJLlr.SFeoS4CCJRJ461/1QxeHWtAV9pLTaL6', '78QlKkK33iTUHbyqz09eE25daGvD5AUof2n5fl0pVAfLj8szEU18AyrU8atU', '2025-03-04 05:06:38', '2025-03-10 11:53:57'),
(9, 'Wilsonhus', 'barbo.ursundin@gmail.com', NULL, '$2y$10$iCBdP9Tbn0nxZjlBqZgfPupDgV2VqMBhX7eJShsdjxy4Vmob0.wvq', NULL, '2025-03-13 17:05:59', '2025-03-13 17:05:59');

-- --------------------------------------------------------

--
-- Table structure for table `variants`
--

CREATE TABLE `variants` (
  `variant_id` bigint(20) UNSIGNED NOT NULL,
  `sku` varchar(255) NOT NULL,
  `qty` bigint(20) NOT NULL DEFAULT 10,
  `price` double(8,2) NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `product_type_id` bigint(20) UNSIGNED NOT NULL,
  `brand_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `variants`
--

INSERT INTO `variants` (`variant_id`, `sku`, `qty`, `price`, `product_id`, `product_type_id`, `brand_id`, `created_at`, `updated_at`) VALUES
(1, 'CP2K', 10, 20.00, 1, 3, 2, '2025-01-24 13:40:07', '2025-02-27 08:04:54'),
(2, 'CP200K', 2, 21.00, 2, 4, 2, '2025-01-24 13:42:40', '2025-01-24 13:42:40'),
(3, 'BLB1', 10, 2.00, 3, 4, 2, '2025-01-24 13:56:45', '2025-01-24 13:56:45'),
(4, 'MCS01-Black', 10, 100.00, 4, 1, 6, '2025-02-02 16:38:11', '2025-02-04 12:18:18'),
(5, 'MCS01-White', 10, 100.00, 5, 1, 5, '2025-02-02 16:45:13', '2025-02-02 16:45:13'),
(6, 'CP2K-copy-1', 10, 20.00, 6, 4, 2, '2025-02-04 13:14:49', '2025-02-04 13:14:49'),
(7, 'CP2K-copy-2', 10, 20.00, 7, 4, 2, '2025-02-04 13:16:18', '2025-02-04 13:16:18'),
(8, 'CP2K-copy-2-copy-1', 10, 20.00, 8, 4, 2, '2025-02-04 13:17:36', '2025-02-04 13:17:36'),
(9, 'MCS01-Blue', 10, 100.00, 9, 1, 6, '2025-02-05 11:39:36', '2025-02-05 11:39:52'),
(10, 'MCS01-Yellow', 10, 100.00, 10, 1, 6, '2025-02-05 11:40:02', '2025-02-05 11:40:14'),
(11, 'MCS01-Green', 10, 100.00, 11, 1, 6, '2025-02-05 11:40:25', '2025-02-05 11:40:37');

-- --------------------------------------------------------

--
-- Table structure for table `variant_details`
--

CREATE TABLE `variant_details` (
  `variant_detail_id` bigint(20) UNSIGNED NOT NULL,
  `option_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`option_ids`)),
  `options` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`options`)),
  `variant_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `variant_details`
--

INSERT INTO `variant_details` (`variant_detail_id`, `option_ids`, `options`, `variant_id`, `created_at`, `updated_at`) VALUES
(1, '[3]', '{\"3\":\"Raspberry\"}', 1, '2025-01-24 13:40:07', '2025-01-24 13:41:54'),
(2, '[1]', '{\"1\":\"red\"}', 2, '2025-01-24 13:42:40', '2025-02-03 07:34:12'),
(3, '[3]', '{\"3\":\"Blueberry\"}', 3, '2025-01-24 13:56:45', '2025-01-24 13:57:39'),
(4, '[1]', '{\"1\":\"Black\"}', 4, '2025-02-02 16:38:11', '2025-02-02 16:38:11'),
(5, '[1]', '{\"1\":\"White\"}', 5, '2025-02-02 16:45:13', '2025-02-02 16:45:13'),
(6, '[3]', '{\"3\":\"Raspberry\"}', 6, '2025-02-04 13:14:49', '2025-02-04 13:14:49'),
(7, '[3]', '{\"3\":\"Raspberry\"}', 7, '2025-02-04 13:16:18', '2025-02-04 13:16:18'),
(8, '[3]', '{\"3\":\"Raspberry\"}', 8, '2025-02-04 13:17:36', '2025-02-04 13:17:36'),
(9, '[1]', '{\"1\":\"Blue\"}', 9, '2025-02-05 11:39:36', '2025-02-05 11:39:52'),
(10, '[1]', '{\"1\":\"Yellow\"}', 10, '2025-02-05 11:40:02', '2025-02-05 11:40:14'),
(11, '[1]', '{\"1\":\"Green\"}', 11, '2025-02-05 11:40:25', '2025-02-05 11:40:37');

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
-- Indexes for table `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`brand_id`),
  ADD KEY `brands_shop_id_foreign` (`shop_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`cat_id`),
  ADD UNIQUE KEY `categories_cat_slug_unique` (`cat_slug`),
  ADD KEY `categories_shop_id_foreign` (`shop_id`);

--
-- Indexes for table `category_autos`
--
ALTER TABLE `category_autos`
  ADD PRIMARY KEY (`category_auto_id`),
  ADD KEY `category_autos_cat_id_foreign` (`cat_id`),
  ADD KEY `category_autos_field_id_foreign` (`field_id`),
  ADD KEY `category_autos_query_id_foreign` (`query_id`);

--
-- Indexes for table `category_manuals`
--
ALTER TABLE `category_manuals`
  ADD PRIMARY KEY (`category_manual_id`),
  ADD KEY `category_manuals_cat_id_foreign` (`cat_id`);

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
-- Indexes for table `mbrands`
--
ALTER TABLE `mbrands`
  ADD PRIMARY KEY (`mbrand_id`);

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
-- Indexes for table `option_names`
--
ALTER TABLE `option_names`
  ADD PRIMARY KEY (`option_id`),
  ADD UNIQUE KEY `option_names_option_name_unique` (`option_name`);

--
-- Indexes for table `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`page_id`),
  ADD UNIQUE KEY `pages_page_slug_unique` (`page_slug`),
  ADD KEY `pages_page_id_index` (`page_id`);

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
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `products_shop_id_foreign` (`shop_id`),
  ADD KEY `products_product_id_index` (`product_id`);

--
-- Indexes for table `product_tags`
--
ALTER TABLE `product_tags`
  ADD KEY `product_tags_product_id_foreign` (`product_id`),
  ADD KEY `product_tags_tag_id_foreign` (`tag_id`);

--
-- Indexes for table `product_types`
--
ALTER TABLE `product_types`
  ADD PRIMARY KEY (`product_type_id`),
  ADD KEY `product_types_shop_id_foreign` (`shop_id`);

--
-- Indexes for table `queries`
--
ALTER TABLE `queries`
  ADD PRIMARY KEY (`query_id`);

--
-- Indexes for table `shops`
--
ALTER TABLE `shops`
  ADD PRIMARY KEY (`shop_id`);

--
-- Indexes for table `shop_users`
--
ALTER TABLE `shop_users`
  ADD UNIQUE KEY `shop_users_shop_id_user_id_unique` (`shop_id`,`user_id`),
  ADD KEY `shop_users_user_id_foreign` (`user_id`);

--
-- Indexes for table `tags`
--
ALTER TABLE `tags`
  ADD PRIMARY KEY (`tag_id`),
  ADD KEY `tags_shop_id_foreign` (`shop_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `variants`
--
ALTER TABLE `variants`
  ADD PRIMARY KEY (`variant_id`),
  ADD KEY `variants_product_id_foreign` (`product_id`),
  ADD KEY `variants_product_type_id_foreign` (`product_type_id`),
  ADD KEY `variants_brand_id_foreign` (`brand_id`),
  ADD KEY `variants_variant_id_index` (`variant_id`);

--
-- Indexes for table `variant_details`
--
ALTER TABLE `variant_details`
  ADD PRIMARY KEY (`variant_detail_id`),
  ADD KEY `variant_details_variant_id_foreign` (`variant_id`),
  ADD KEY `variant_details_variant_detail_id_index` (`variant_detail_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `brands`
--
ALTER TABLE `brands`
  MODIFY `brand_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `cat_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `category_autos`
--
ALTER TABLE `category_autos`
  MODIFY `category_auto_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `category_manuals`
--
ALTER TABLE `category_manuals`
  MODIFY `category_manual_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fields`
--
ALTER TABLE `fields`
  MODIFY `field_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `mbrands`
--
ALTER TABLE `mbrands`
  MODIFY `mbrand_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `mlocations`
--
ALTER TABLE `mlocations`
  MODIFY `mlocation_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `moptions`
--
ALTER TABLE `moptions`
  MODIFY `moption_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `mproducts`
--
ALTER TABLE `mproducts`
  MODIFY `mproduct_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `mproduct_types`
--
ALTER TABLE `mproduct_types`
  MODIFY `mproduct_type_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mstocks`
--
ALTER TABLE `mstocks`
  MODIFY `mstock_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `mtags`
--
ALTER TABLE `mtags`
  MODIFY `mtag_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mvariants`
--
ALTER TABLE `mvariants`
  MODIFY `mvariant_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `mvariant_details`
--
ALTER TABLE `mvariant_details`
  MODIFY `mvariant_detail_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `option_names`
--
ALTER TABLE `option_names`
  MODIFY `option_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `pages`
--
ALTER TABLE `pages`
  MODIFY `page_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `product_types`
--
ALTER TABLE `product_types`
  MODIFY `product_type_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `queries`
--
ALTER TABLE `queries`
  MODIFY `query_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `shops`
--
ALTER TABLE `shops`
  MODIFY `shop_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `tags`
--
ALTER TABLE `tags`
  MODIFY `tag_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `variants`
--
ALTER TABLE `variants`
  MODIFY `variant_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `variant_details`
--
ALTER TABLE `variant_details`
  MODIFY `variant_detail_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `brands`
--
ALTER TABLE `brands`
  ADD CONSTRAINT `brands_shop_id_foreign` FOREIGN KEY (`shop_id`) REFERENCES `shops` (`shop_id`) ON DELETE CASCADE;

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_shop_id_foreign` FOREIGN KEY (`shop_id`) REFERENCES `shops` (`shop_id`) ON DELETE CASCADE;

--
-- Constraints for table `category_autos`
--
ALTER TABLE `category_autos`
  ADD CONSTRAINT `category_autos_cat_id_foreign` FOREIGN KEY (`cat_id`) REFERENCES `categories` (`cat_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `category_autos_field_id_foreign` FOREIGN KEY (`field_id`) REFERENCES `fields` (`field_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `category_autos_query_id_foreign` FOREIGN KEY (`query_id`) REFERENCES `queries` (`query_id`) ON DELETE CASCADE;

--
-- Constraints for table `category_manuals`
--
ALTER TABLE `category_manuals`
  ADD CONSTRAINT `category_manuals_cat_id_foreign` FOREIGN KEY (`cat_id`) REFERENCES `categories` (`cat_id`) ON DELETE CASCADE;

--
-- Constraints for table `field_query_relations`
--
ALTER TABLE `field_query_relations`
  ADD CONSTRAINT `field_query_relations_field_id_foreign` FOREIGN KEY (`field_id`) REFERENCES `fields` (`field_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `field_query_relations_query_id_foreign` FOREIGN KEY (`query_id`) REFERENCES `queries` (`query_id`) ON DELETE CASCADE;

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
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_shop_id_foreign` FOREIGN KEY (`shop_id`) REFERENCES `shops` (`shop_id`) ON DELETE CASCADE;

--
-- Constraints for table `product_tags`
--
ALTER TABLE `product_tags`
  ADD CONSTRAINT `product_tags_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_tags_tag_id_foreign` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`tag_id`) ON DELETE CASCADE;

--
-- Constraints for table `product_types`
--
ALTER TABLE `product_types`
  ADD CONSTRAINT `product_types_shop_id_foreign` FOREIGN KEY (`shop_id`) REFERENCES `shops` (`shop_id`) ON DELETE CASCADE;

--
-- Constraints for table `shop_users`
--
ALTER TABLE `shop_users`
  ADD CONSTRAINT `shop_users_shop_id_foreign` FOREIGN KEY (`shop_id`) REFERENCES `shops` (`shop_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `shop_users_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tags`
--
ALTER TABLE `tags`
  ADD CONSTRAINT `tags_shop_id_foreign` FOREIGN KEY (`shop_id`) REFERENCES `shops` (`shop_id`) ON DELETE CASCADE;

--
-- Constraints for table `variants`
--
ALTER TABLE `variants`
  ADD CONSTRAINT `variants_brand_id_foreign` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`brand_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `variants_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `variants_product_type_id_foreign` FOREIGN KEY (`product_type_id`) REFERENCES `product_types` (`product_type_id`) ON DELETE CASCADE;

--
-- Constraints for table `variant_details`
--
ALTER TABLE `variant_details`
  ADD CONSTRAINT `variant_details_variant_id_foreign` FOREIGN KEY (`variant_id`) REFERENCES `variants` (`variant_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
