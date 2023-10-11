<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\Products\ProductImage;

class ProductImagesController extends Controller
{
    public function storeArray($idsToStore, $product)
    {
        $stored = [];
        $errors = [];

        foreach ($idsToStore as $id) {
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

        $productImage = ProductImage::where('image_id', $imageId)
            ->where('product_id', $productId)
            ->first();

        if (empty($productImage)) {
            $productImage = ProductImage::create([
                'image_id' => $imageId,
                'product_id' => $productId
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
}