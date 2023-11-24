<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Image;
use App\Http\Controllers\Products\ProductImagesController;

class ScheduleTask extends Model
{
    use HasFactory;

    public static function clearVerifyEmailTable()
    {
        DB::table('verify_email')
            ->where('updated_at', '<', DB::raw('NOW() - INTERVAL 10 MINUTE'))
            ->delete();
    }

    public static function clearOldImages()
    {
        $tablesToCheck = Image::getTablesWhereExist();
        $oldImages = DB::table('images')
            ->select('id', 'path')
            ->where('updated_at', '<', DB::raw('NOW() - INTERVAL 1 DAY'))
            ->get();

        foreach ($oldImages as $imageData) {
            $isPresentedInAnyTable = false;

            foreach ($tablesToCheck as $tableName) {
                $found = DB::table($tableName)
                    ->where('image_id', $imageData->id)
                    ->first();

                if ($found) {
                    $isPresentedInAnyTable = true;
                    break;
                }
            }

            // если изображения нет ни в одной таблице и оно старше указанного времени, удалить его из папки и из БД
            if (!$isPresentedInAnyTable) {
                $path = public_path() . '/' . $imageData->path;

                if (file_exists($path))
                    unlink($path);

                $imageModel = Image::find($imageData->id);
                if ($imageModel)
                    $imageModel->delete();
            }
        }
    }

    public static function clearRemovedImages()
    {
        $allImages = Image::all();
        foreach ($allImages as $imageModel) {
            $path = public_path() . '/' . $imageModel->path;
            if (file_exists($path))
                continue;

            // файла не существует - удалить из бд
            $imageModel->delete();
        }
    }

    public static function resizeProductImages()
    {
        $productImagesController = new ProductImagesController();
        $images = Image::where('path', 'like', '%' . '/products' . '%')->get();
        foreach ($images as $imageModel) {
            $path = public_path($imageModel->path . $imageModel->name);
            foreach ($productImagesController->resizes as $resize) {
                $notExists = !file_exists($path . '_' . $resize . '.' . $imageModel->extension)
                    || !file_exists($path . '_' . $resize . '.webp');
                if ($notExists)
                    $productImagesController->resizeImages($imageModel, $resize);
            }
        }
    }
}
