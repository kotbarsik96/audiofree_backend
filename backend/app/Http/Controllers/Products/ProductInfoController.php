<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\Products\ProductInfo;

class ProductInfoController extends Controller
{
    public function storeArray($infoToStore, $product)
    {
        $stored = [];
        $errors = [];

        foreach ($infoToStore as $info) {
            $storedInfo = $this->store($info, $product->id);
            $errors = array_merge($errors, $storedInfo['errors']);
            if ($storedInfo['stored']) {
                $stored[] = $storedInfo['stored'];
            }
        }

        return [
            'stored' => $stored,
            'errors' => $errors
        ];
    }

    public function store($infoData, $productId)
    {
        $infoData['product_id'] = $productId;

        $validator = Validator::make(
            $infoData,
            [
                'product_id' => ['required', 'exists:products,id'],
                'name' => ['required', 'string'],
                'value' => ['required', 'string'],
            ],
            [
                'required' => 'Не передано поле ":attribute"',
                'name.unique' => 'Характеристика :value уже существует'
            ],
            [
                'product_id' => 'id товара',
                'name' => 'название характеристики',
                'value' => 'значение характеристики'
            ]
        );

        if ($validator->fails())
            return ['stored' => false, 'errors' => $validator->errors()->messages()];

        $info = ProductInfo::where('name', $infoData['name'])
            ->where('product_id', $productId)
            ->first();

        if($info)
        $info->update($validator->validated());
        else 
        $info = ProductInfo::create($validator->validated());

        return [
            'stored' => $info,
            'errors' => [],
        ];
    }

    public function clear($infoArr, $productId)
    {
        $allProductInfo = ProductInfo::where('product_id', $productId)
            ->get();

        foreach ($allProductInfo as $infoModel) {
            $arrKey = null;
            foreach ($infoArr as $key => $infoData) {
                if ($infoData['name'] !== $infoModel->name)
                    continue;

                $arrKey = $key;
                break;
            }

            // есть в списке, оставить
            if (is_numeric($arrKey))
                continue;

            $infoModel->delete();
        }
    }
}