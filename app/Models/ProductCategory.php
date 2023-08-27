<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    use HasFactory;
    protected $table = 'product_categories';
    protected $fillable = [
        'name',
        'description',
        'featured_image',
        'organization_ids',
    ];

    protected $appends = ['organizations'];

    public function getOrganizationsAttribute() {
        $organization_ids = json_decode($this->organization_ids, true);

        if (is_array($organization_ids) && !empty($organization_ids)) {
            $data = Organization::whereIn('id', $organization_ids)
                ->get()
                ->toArray();

            if (!empty($data)) {
                return $data;
            }
        }
    }
}
