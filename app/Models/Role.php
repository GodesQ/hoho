<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;
    
    const SUPER_ADMIN = "super_admin";
    const ADMIN = "admin";
    const BUS_OPERATOR = "bus_operator";
    const MERCHANT_HOTEL_ADMIN = "merchant_hotel_admin";
    const MERCHANT_HOTEL_EMPLOYEE = "merchant_hotel_employee";
    const MERCHANT_RESTAURANT_ADMIN = "merchant_restaurant_admin";
    const MERCHANT_RESTAURANT_EMPLOYEE = "merchant_restaurant_employee";
    const MERCHANT_STORE_ADMIN = "merchant_store_admin";
    const MERCHANT_STORE_EMPLOYEE = "merchant_store_employee";
    const TOUR_OPERATOR_ADMIN = "tour_operator_admin";
    const TOUR_OPERATOR_EMPLOYEE = "tour_operator_employee";
    const TRAVEL_TAX_ADMIN = "travel_tax_admin";

    protected $table = 'roles';
    protected $fillable = ['name', 'slug'];
}
