<?php

namespace App\Services;
use ErrorException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;

class FileService {
    /**
     * Upload the image to the directory path.
     * 
     * @param string $path
     * @param string $filename
     * @param mixed $file
     * @return bool|string
     */
    public static function upload(string $path, string $filename, $file) {
        return Storage::disk('public')->putFileAs($path, $file, $filename);
    }

    /**
     * Remove the image to the directory path.
     * 
     * @param mixed $path
     * @param mixed $filename
     * @return bool
     */
    public static function remove($path, $filename) {
        $old_image_path = $path . '/' . $filename;

        if (Storage::disk('public')->exists($old_image_path)) {
            return Storage::disk('public')->delete($old_image_path);
        }

        return false;
    }

    /**
     * Upload the following image fields to the directory path and save them in the database based on the model.
     * 
     * @param string $path
     * @param array $image_fields
     * @param string $base_name
     * @param Model $model 
     * @param Request $request
     * @throws ErrorException|InvalidArgumentException
    */
    public function uploadAndSaveFiles(string $path, array $image_fields, string $base_name, Model $model, Request $request) {
        try {
            if(!is_array($image_fields)) throw new InvalidArgumentException("Invalid type argument.");
            
            foreach ($image_fields as $field) {
                if ($request->hasFile($field) && $field != 'images') {
                    $file = $request->file($field);

                    $image_file_name = "{$base_name}_{$field}." . $file->getClientOriginalExtension();

                    // Remove image from the directory of $path
                    FileService::remove($path, $image_file_name);

                    // Upload new file in the directory of $path
                    FileService::upload($path, $image_file_name, $file);

                    // Update the model based on the field.
                    $model->update([$field => $image_file_name]);
                }
            }

        } catch (ErrorException $e) {
            throw $e;
        } catch (InvalidArgumentException $e) {
            throw $e;
        }
    }
}
