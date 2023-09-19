<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Organization;

class OrganizationController extends Controller
{
    public function getOrganizations(Request $request) {
        $organizations = Organization::get();
        return response([   
            'status' => TRUE,
           'organizations' => $organizations 
        ]);
    }

    public function getOrganization(Request $request) {
        $organization = Organization::where('id', $request->id)->with('attractions', 'stores', 'hotels', 'restaurants', 'tour')->first();

        if($organization) {
            return response([
                'status' => TRUE,
                'organization' => $organization,
            ]);
        }

        return response([
            'status' => FALSE,
            'organization' => 'Not Found'
        ], 404);
        
    }
}
