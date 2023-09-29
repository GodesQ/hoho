<?php
namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

use App\Models\MerchantTourProvider;
use App\Models\Merchant;


class MerchantTourProviderService {

    public function CreateMerchantTourProvider(Request $request) {
        $data = $request->except('_token', 'featured_image');

        // First, Create a merchant
        $merchant = Merchant::create($data);

        // Save if the featured image exist in request
        if($request->hasFile('featured_image')) {
            $file = $request->file('featured_image');
            $name = Str::snake(Str::lower($request->name));
            $file_name = $name . '.' . $file->getClientOriginalExtension();
            $save_file = $file->move(public_path() . '/assets/img/tour_providers/' . $merchant->id, $file_name);

            $merchant->update([
                'featured_image' => $file_name
            ]);
        } else {
            $file_name = null;
        }

        if($merchant) {
            // Second, Create Tour Provider Data
            $merchant_tour_provider = MerchantTourProvider::create(array_merge($data, [
                'merchant_id' => $merchant->id
            ]));

            if($merchant_tour_provider) return [
                'status' => TRUE,
                'merchant' => $merchant,
                'merchant_tour_provider' => $merchant_tour_provider
            ];
        }

        return [
            'status' => FALSE,
            'merchant' => null,
            'merchant_tour_provider' => null
        ];
    }

    public function UpdateMerchantTourProvider(Request $request) {
        $data = $request->except('_token');
        $tour_provider = MerchantTourProvider::where('id', $request->id)->with('merchant')->firstOrFail();

        $update_tour_provider = $tour_provider->update($data);

        // Save if the featured image exist in request
        if($request->hasFile('featured_image')) {
            $file = $request->file('featured_image');
            $name = Str::snake(Str::lower($request->name));
            $file_name = $name . '.' . $file->getClientOriginalExtension();

            $old_upload_image = public_path('assets/img/tour_providers/') . $tour_provider->merchant->id . '/' . $tour_provider->merchant->featured_image;

            if($old_upload_image)  @unlink($old_upload_image);

            $file->move(public_path() . '/assets/img/tour_providers/' . $tour_provider->merchant->id, $file_name);
        } else {
            $file_name = $tour_provider->merchant->featured_image;
        }

        $update_merchant = $tour_provider->merchant->update(array_merge($data, ['featured_image' => $file_name]));

        if($update_tour_provider && $update_merchant) {
            return [
                'status' => TRUE,
                'merchant' => $tour_provider->merchant,
                'merchant_tour_provider' => $tour_provider
            ];
        }

        return [
            'status' => FALSE,
            'merchant' => null,
            'merchant_tour_provider' => null
        ];
    }
}
?>