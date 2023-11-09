<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\Products\ProductImage;
use App\Models\Image;
use App\Models\Products\Product;
use App\Filesystem\FilesystemActions;
use App\Http\Controllers\ImagesController;

class ProductImagesController extends Controller
{
    public function storeArray($idsToStore, $product)
    {
        $stored = [];
        $errors = [];

        $this->moveToProductsFolder(Image::find($product->image_id), $product->id);

        foreach ($idsToStore as $idOrObj) {
            $id = $idOrObj;
            if (!is_numeric($id)) {
                $id = $idOrObj['id'];
            }

            $storedId = $this->store($id, $product->id);
            $errors = array_merge($errors, $storedId['errors']);
            if ($storedId['stored']) {
                $stored[] = $storedId['stored'];
            }
        }

        return [
            'stored' => $stored,
            'errors' => $errors
        ];
    }

    /* переместить изображение в папку images/products/[$product->name]/ */
    public function moveToProductsFolder($imageModel, $productId)
    {
        if (empty($imageModel))
            return;

        $productModel = Product::find($productId);
        if (empty($productModel))
            return;

        $pathToProductFolder = FilesystemActions::strToPathAcceptable(
            'images/products/' . $productModel->name
        );
        $fullpathToProductFolder = public_path() . '/' . $pathToProductFolder;
        // создать папку с названием товара, если ее нет
        if (!file_exists($fullpathToProductFolder))
            mkdir($fullpathToProductFolder, 0777, true);

        $oldPathToImage = public_path() . '/' . $imageModel->path;
        $imageName = basename($oldPathToImage);

        // если изображение уже в нужной папке, отменить выполнение
        if ($fullpathToProductFolder . '/' . $imageName === $oldPathToImage)
            return;

        // переместить товар в папку с названием этого товара
        rename($oldPathToImage, $fullpathToProductFolder . '/' . $imageName);

        if (count(scandir(dirname($oldPathToImage))) < 3)
            rmdir(dirname($oldPathToImage));

        // обновить запись в бд в соответствии с новым путем к файлу
        $imageModel->update([
            'path' => $pathToProductFolder . '/' . $imageName
        ]);
    }

    public function store($imageId, $productId)
    {
        $validator = Validator::make(['image_id' => $imageId, 'product_id' => $productId], [
            'image_id' => ['required', 'exists:images,id'],
            'product_id' => ['required', 'exists:products,id']
        ], [
            'image_id.exists' => 'Такого изображения не существует',
            'product_id.exists' => 'Такого товара не существует',
            'image_id.required' => 'Изображение не передано',
            'product_id.required' => 'Товар не передан',
        ]);

        if ($validator->fails())
            return ['stored' => false, 'errors' => $validator->errors()->messages()];

        $imageModel = Image::find($imageId);
        if ($imageModel)
            $this->moveToProductsFolder($imageModel, $productId);

        $productImage = ProductImage::where('image_id', $imageId)
            ->where('product_id', $productId)
            ->first();

        if (empty($productImage)) {
            $productImage = ProductImage::create([
                'image_id' => $imageId,
                'product_id' => $productId,
            ]);
        }

        return [
            'stored' => $productImage,
            'errors' => [],
        ];
    }

    public function clear($images, $productId)
    {
        $allProductImages = ProductImage::where('product_id', $productId)
            ->get();

        foreach ($allProductImages as $productImageModel) {
            $arrKey = null;
            foreach ($images as $key => $id) {
                if ((int) $id !== (int) $productImageModel->image_id)
                    continue;

                $arrKey = $key;
                break;
            }

            // есть в списке, оставить
            if (is_numeric($arrKey))
                continue;

            $productImageModel->delete();
        }
    }

    public function destroy($productId)
    {
        $product = Product::find($productId);
        if (empty($product))
            return;

        $mainImage = Image::find($product->image_id);
        $images = ProductImage::where('product_id', $productId)->get();

        $imagesController = new ImagesController();
        if ($mainImage)
            $imagesController->deleteByIdOrImage($mainImage->id);

        foreach ($images as $row) {
            if (empty($row))
                continue;
            $imagesController->deleteByIdOrImage($row->image_id);
        }
    }
}
