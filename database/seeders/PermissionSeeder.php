<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sql = "INSERT INTO `permissions` (`id`, `permission_name`, `roles`, `created_at`, `updated_at`) VALUES
            (1, 'view_users_list', '[\"super_admin\",\"admin\",\"customer_service_manager\",\"customer_service_employee\",\"employee\"]', '2023-09-27 16:23:07', '2023-09-27 16:26:59'),
            (2, 'view_organizations_list', '[\"super_admin\",\"admin\"]', '2023-09-27 16:27:17', '2023-09-27 16:27:17'),
            (3, 'view_tour_reservations_list', '[\"super_admin\",\"admin\",\"merchant_store_admin\",\"tour_operator_admin\",\"tour_operator_employee\",\"merchant_hotel_admin\",\"merchant_restaurant_admin\"]', '2023-09-27 16:27:41', '2023-11-20 06:05:11'),
            (4, 'view_admins_list', '[\"super_admin\"]', '2023-09-27 16:28:24', '2023-09-27 16:28:24'),
            (5, 'view_merchant_stores_list', '[\"super_admin\",\"admin\"]', '2023-09-27 16:28:44', '2023-09-27 16:28:44'),
            (6, 'view_merchant_restaurants_list', '[\"super_admin\",\"admin\"]', '2023-09-27 16:28:59', '2023-09-27 16:30:17'),
            (7, 'view_merchant_hotels_list', '[\"super_admin\",\"admin\"]', '2023-09-27 16:29:08', '2023-09-27 16:30:23'),
            (8, 'view_merchant_tour_providers_list', '[\"super_admin\",\"admin\"]', '2023-09-27 16:29:48', '2023-09-27 16:30:28'),
            (9, 'view_tours_list', '[\"super_admin\",\"tour_operator_admin\",\"tour_operator_employee\"]', '2023-09-27 16:30:11', '2023-09-29 07:44:31'),
            (10, 'view_attractions_list', '[\"super_admin\",\"admin\"]', '2023-09-27 16:32:36', '2023-09-27 16:32:36'),
            (11, 'view_transports_list', '[\"super_admin\"]', '2023-09-27 16:32:44', '2023-09-27 16:32:44'),
            (12, 'view_transactions_list', '[\"super_admin\"]', '2023-09-27 16:33:19', '2023-09-27 16:33:19'),
            (13, 'view_sales_report', '[\"super_admin\"]', '2023-09-27 16:33:33', '2023-09-27 16:33:41'),
            (14, 'view_interests_list', '[\"super_admin\",\"admin\"]', '2023-09-27 16:34:14', '2023-09-27 16:34:14'),
            (15, 'view_referrals_list', '[\"super_admin\",\"admin\",\"merchant_hotel_admin\"]', '2023-09-27 16:34:26', '2023-09-28 07:09:00'),
            (16, 'view_promo_codes_list', '[\"super_admin\"]', '2023-09-27 16:34:36', '2023-09-27 16:34:36'),
            (17, 'view_product_categories_list', '[\"super_admin\",\"admin\"]', '2023-09-27 16:34:51', '2023-09-27 16:34:54'),
            (18, 'view_roles_list', '[\"super_admin\"]', '2023-09-27 16:35:27', '2023-09-27 16:35:27'),
            (19, 'view_permissions_list', '[\"super_admin\"]', '2023-09-27 16:35:36', '2023-09-28 07:08:45'),
            (20, 'view_merchant_form', '[\"merchant_store_admin\",\"merchant_store_employee\",\"tour_operator_admin\",\"tour_operator_employee\",\"merchant_hotel_admin\",\"merchant_hotel_employee\",\"merchant_restaurant_admin\",\"merchant_restaurant_employee\"]', '2023-09-28 02:20:46', '2023-09-28 02:20:46'),
            (21, 'book_tour', '[\"super_admin\",\"admin\",\"merchant_store_admin\",\"tour_operator_admin\",\"tour_operator_employee\",\"merchant_hotel_admin\",\"merchant_restaurant_admin\"]', '2023-09-29 08:38:46', '2024-08-01 05:46:39'),
            (22, 'create_user', '[\"super_admin\",\"admin\"]', '2023-10-02 00:59:12', '2023-10-02 00:59:29'),
            (23, 'edit_user', '[\"super_admin\",\"admin\",\"bus_operator\",\"customer_service_manager\",\"customer_service_employee\"]', '2023-10-02 00:59:57', '2023-10-02 01:00:18'),
            (24, 'update_user', '[\"super_admin\",\"admin\"]', '2023-10-02 01:00:06', '2023-10-02 01:00:06'),
            (25, 'delete_user', '[\"super_admin\",\"admin\"]', '2023-10-02 01:06:02', '2023-10-02 01:06:02'),
            (26, 'create_admin', '[\"super_admin\"]', '2023-10-02 01:11:17', '2023-10-02 01:11:54'),
            (27, 'edit_admin', '[\"super_admin\",\"admin\"]', '2023-10-02 01:11:31', '2023-10-02 01:11:31'),
            (28, 'update_admin', '[\"super_admin\"]', '2023-10-02 01:11:40', '2023-10-02 01:11:40'),
            (29, 'delete_admin', '[\"super_admin\"]', '2023-10-02 01:12:25', '2023-10-02 01:12:25'),
            (30, 'create_organization', '[\"super_admin\",\"admin\"]', '2023-10-02 01:49:29', '2023-10-02 01:49:29'),
            (31, 'edit_organization', '[\"super_admin\",\"admin\"]', '2023-10-02 01:49:39', '2023-10-02 01:49:39'),
            (32, 'update_organization', '[\"super_admin\"]', '2023-10-02 01:49:45', '2023-10-02 01:49:45'),
            (33, 'delete_organization', '[\"super_admin\"]', '2023-10-02 01:49:55', '2023-10-02 01:49:55'),
            (34, 'view_ticket_passes_list', '[\"super_admin\",\"admin\"]', '2023-10-04 02:36:35', '2023-10-04 02:36:35'),
            (35, 'view_announcements_list', '[\"super_admin\"]', '2023-10-12 07:23:32', '2023-10-12 07:23:32'),
            (36, 'create_announcement', '[\"super_admin\",\"admin\"]', '2023-10-26 01:49:53', '2023-10-26 01:49:53'),
            (37, 'view_tour_badges_list', '[\"super_admin\",\"admin\"]', '2023-10-30 01:22:30', '2023-10-30 01:24:21'),
            (38, 'view_unavailable_dates_list', '[\"super_admin\",\"admin\"]', '2023-10-30 01:24:39', '2023-10-30 01:24:39'),
            (39, 'view_carts_list', '[\"super_admin\",\"admin\"]', '2023-11-15 15:48:28', '2023-11-15 15:48:28'),
            (40, 'edit_transaction', '[\"super_admin\",\"admin\"]', '2023-11-20 06:54:41', '2023-11-20 06:54:41'),
            (41, 'create_tour', '[\"super_admin\",\"admin\",\"tour_operator_admin\"]', '2023-11-20 07:43:19', '2023-11-20 07:43:19'),
            (42, 'view_products_list', '[\"super_admin\",\"admin\",\"merchant_store_admin\",\"merchant_store_employee\"]', '2024-01-28 13:29:37', '2024-01-28 13:29:37'),
            (43, 'view_rooms_list', '[\"super_admin\",\"admin\",\"merchant_hotel_admin\",\"merchant_hotel_employee\"]', '2024-01-28 13:30:07', '2024-01-28 13:30:07'),
            (44, 'view_foods_list', '[\"super_admin\",\"admin\",\"merchant_restaurant_admin\",\"merchant_restaurant_employee\"]', '2024-01-28 13:30:29', '2024-01-28 13:30:29'),
            (45, 'view_food_categories_list', '[\"super_admin\",\"admin\",\"merchant_restaurant_admin\",\"merchant_restaurant_employee\"]', '2024-01-28 13:31:13', '2024-01-28 13:31:13'),
            (46, 'view_hotel_reservations_list', '[\"super_admin\",\"admin\",\"merchant_hotel_admin\",\"merchant_hotel_employee\"]', '2024-01-28 13:45:16', '2024-01-28 13:45:16'),
            (47, 'view_restaurant_reservations_list', '[\"super_admin\",\"admin\",\"merchant_restaurant_admin\",\"merchant_restaurant_employee\"]', '2024-01-28 13:45:47', '2024-01-28 13:45:47'),
            (48, 'view_orders_list', '[\"super_admin\",\"admin\",\"merchant_store_admin\",\"merchant_store_employee\"]', '2024-02-19 00:15:23', '2024-02-19 00:15:23'),
            (49, 'view_orders_list', '[\"super_admin\",\"admin\",\"merchant_store_admin\",\"merchant_store_employee\"]', '2024-02-19 00:15:24', '2024-02-19 00:15:24'),
            (50, 'view_consumer_api_logs_list', '[\"super_admin\",\"admin\"]', '2024-03-14 14:23:55', '2024-03-14 14:23:55'),
            (51, 'view_travel_taxes_list', '[\"super_admin\",\"admin\"]', '2024-04-08 01:01:16', '2024-04-08 01:01:16'),
            (52, 'create_travel_tax', '[\"super_admin\",\"admin\"]', '2024-04-08 01:01:28', '2024-04-08 01:01:28'),
            (53, 'edit_travel_tax', '[\"super_admin\",\"admin\"]', '2024-04-08 01:01:43', '2024-04-08 01:01:43'),
            (54, 'update_maintenance_mode', '[\"super_admin\"]', '2024-04-10 01:57:27', '2024-04-10 01:57:27'),
            (55, 'view_consumer_api_logs_list', '[\"super_admin\",\"admin\"]', '2024-04-11 16:55:49', '2024-04-11 16:55:49'),
            (56, 'view_api_consumers_list', '[\"super_admin\",\"admin\"]', '2024-04-11 16:55:56', '2024-04-11 16:55:56'),
            (57, 'view_api_permissions_list', '[\"super_admin\"]', '2024-04-11 16:56:01', '2024-04-11 16:56:01'),
            (58, 'view_travel_tax_report', '[\"super_admin\",\"admin\",\"travel_tax_admin\"]', '2024-06-13 08:53:34', '2024-06-13 08:53:34'),
            (59, 'view_merchant_account_list', '[\"super_admin\",\"admin\"]', '2024-07-09 00:30:46', '2024-07-09 00:30:46'),
            (60, 'create_merchant_account', '[\"super_admin\",\"admin\"]', '2024-07-09 00:30:57', '2024-07-09 00:30:57'),
            (61, 'edit_merchant_account', '[\"super_admin\",\"admin\"]', '2024-07-09 00:31:08', '2024-07-09 00:31:08'),
            (62, 'delete_merchant_account', '[\"super_admin\",\"admin\"]', '2024-07-09 00:36:07', '2024-07-09 00:36:07');";

        DB::statement($sql);
    }
}
