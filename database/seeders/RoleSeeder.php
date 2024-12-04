<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            [
                'id' => 1,
                'name' => 'Super Admin',
                'slug' => 'super_admin',
                'created_at' => Carbon::parse('2023-08-25 06:56:37'),
                'updated_at' => Carbon::parse('2023-08-25 06:56:37'),
            ],
            [
                'id' => 2,
                'name' => 'Admin',
                'slug' => 'admin',
                'created_at' => Carbon::parse('2023-08-25 06:56:52'),
                'updated_at' => Carbon::parse('2023-08-25 06:56:52'),
            ],
            [
                'id' => 3,
                'name' => 'Bus Operator',
                'slug' => 'bus_operator',
                'created_at' => Carbon::parse('2023-08-25 06:58:57'),
                'updated_at' => Carbon::parse('2023-08-25 06:58:57'),
            ],
            [
                'id' => 4,
                'name' => 'Merchant Store Admin',
                'slug' => 'merchant_store_admin',
                'created_at' => Carbon::parse('2023-08-25 16:32:01'),
                'updated_at' => Carbon::parse('2023-09-16 07:45:23'),
            ],
            [
                'id' => 5,
                'name' => 'Merchant Store Employee',
                'slug' => 'merchant_store_employee',
                'created_at' => Carbon::parse('2023-08-25 16:32:20'),
                'updated_at' => Carbon::parse('2023-09-16 07:45:07'),
            ],
            [
                'id' => 6,
                'name' => 'Customer Service Manager',
                'slug' => 'customer_service_manager',
                'created_at' => Carbon::parse('2023-08-25 16:32:39'),
                'updated_at' => Carbon::parse('2023-08-25 16:32:39'),
            ],
            [
                'id' => 7,
                'name' => 'Customer Service Employee',
                'slug' => 'customer_service_employee',
                'created_at' => Carbon::parse('2023-08-25 16:32:56'),
                'updated_at' => Carbon::parse('2023-08-25 16:32:56'),
            ],
            [
                'id' => 8,
                'name' => 'Tour Operator Admin',
                'slug' => 'tour_operator_admin',
                'created_at' => Carbon::parse('2023-08-25 16:33:27'),
                'updated_at' => Carbon::parse('2023-08-25 16:33:27'),
            ],
            [
                'id' => 9,
                'name' => 'Tour Operator Employee',
                'slug' => 'tour_operator_employee',
                'created_at' => Carbon::parse('2023-08-25 16:33:41'),
                'updated_at' => Carbon::parse('2023-08-25 16:33:41'),
            ],
            [
                'id' => 10,
                'name' => 'Organization Admin',
                'slug' => 'organization_admin',
                'created_at' => Carbon::parse('2023-08-25 16:34:07'),
                'updated_at' => Carbon::parse('2023-08-25 16:34:07'),
            ],
            [
                'id' => 11,
                'name' => 'Organization Employee',
                'slug' => 'organization_employee',
                'created_at' => Carbon::parse('2023-08-25 16:34:26'),
                'updated_at' => Carbon::parse('2023-08-25 16:34:26'),
            ],
            [
                'id' => 12,
                'name' => 'Employee',
                'slug' => 'employee',
                'created_at' => Carbon::parse('2023-08-25 16:34:49'),
                'updated_at' => Carbon::parse('2023-08-25 16:34:49'),
            ],
            [
                'id' => 13,
                'name' => 'Merchant Hotel Admin',
                'slug' => 'merchant_hotel_admin',
                'created_at' => Carbon::parse('2023-09-22 03:32:43'),
                'updated_at' => Carbon::parse('2023-09-22 03:32:43'),
            ],
            [
                'id' => 14,
                'name' => 'Merchant Hotel Employee',
                'slug' => 'merchant_hotel_employee',
                'created_at' => Carbon::parse('2023-09-22 03:33:11'),
                'updated_at' => Carbon::parse('2023-09-22 03:33:11'),
            ],
            [
                'id' => 15,
                'name' => 'Merchant Restaurant Admin',
                'slug' => 'merchant_restaurant_admin',
                'created_at' => Carbon::parse('2023-09-22 03:39:25'),
                'updated_at' => Carbon::parse('2023-09-22 03:39:25'),
            ],
            [
                'id' => 16,
                'name' => 'Merchant Restaurant Employee',
                'slug' => 'merchant_restaurant_employee',
                'created_at' => Carbon::parse('2023-09-22 03:41:35'),
                'updated_at' => Carbon::parse('2023-09-22 03:41:35'),
            ],
            [
                'id' => 18,
                'name' => 'Travel Tax Admin',
                'slug' => 'travel_tax_admin',
                'created_at' => Carbon::parse('2024-06-13 08:17:56'),
                'updated_at' => Carbon::parse('2024-06-13 08:17:56'),
            ],
        ];

        // Insert data into the roles table
        DB::table('roles')->insert($roles);



    }
}
