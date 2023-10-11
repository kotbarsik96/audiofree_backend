<?php

namespace App\Http\Controllers\Products;

use App\Exceptions\RolesExceptions;
use App\Models\Products\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Exceptions\ProductsExceptions;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Products\Variations\VariationsController;
use App\Http\Controllers\ProductsProductImagesController;
use App\Http\Controllers\ProductsProductInfoController;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;

class ProductsController extends Controller
{
    public function index($id)
    {
        return Product::singleFullData($id);
    }

    public function storeValidationReq(Request $request, $ignoreId = null)
    {
        $reqs = [
            'name' => ['string', Rule::unique('products', 'name')->ignore($ignoreId), 'required'],
            'price' => 'numeric|required',
            'discount_price' => 'nullable|numeric',
            'description' => 'string',
            'brand_id' => 'numeric|exists:brands,id|required',
            'category_id' => 'numeric|exists:categories,id|required',
            'type_id' => 'numeric|exists:types,id|required',
            'image_id' => 'nullable|numeric|exists:images,id'
        ];
        return Validator::make($request->all(), $reqs, ProductsExceptions::storeValidator($request));
    }

    public function store(Request $request)
    {
        $rightCheck = AuthController::checkUserRight($request, 'add_product');
        if (!$rightCheck['has_right'])
            return RolesExceptions::noRightsResponse();

        $validator = $this->storeValidationReq($request);
        if ($validator->fails())
            return response(['errors' => $validator->errors()], 400);

        $variationsController = new VariationsController();
        $productImagesController = new ProductImagesController();
        $productInfoController = new ProductInfoController();

        // создать товар
        $product = Product::create($validator->validated());
        // создать вариации товара и добавить для каждого из них значения
        $storedVariations = $variationsController->storeArray($request->variations, $product);
        // создать характеристики
        $storedInfo = $productInfoController->storeArray($request->info, $product);
        // связать с изображениями
        $storedImages = $productImagesController->storeArray($request->images, $product);

        return array_merge(
            Product::singleFullData($product->id),
            [
                'errors' => [
                    'variations' => $storedVariations['errors'],
                    'images' => $storedImages['errors'],
                    'info' => $storedInfo['errors']
                ]
            ]
        );
    }

    public function update(Request $request, $id)
    {
        $rightCheck = AuthController::checkUserRight($request, 'update_product');
        if (!$rightCheck['has_right'])
            return RolesExceptions::noRightsResponse();

        $validator = $this->storeValidationReq($request, $id);
        if ($validator->fails())
            return response(['errors' => $validator->errors()->messages()], 400);

        $product = Product::find($id);
        if (empty($product))
            return (['error' => ProductsExceptions::noProduct()->getMessage()]);

        $fields = $validator->validated();

        // если название изменено, проверить, что товара с новым названием нет
        if ($request->name !== $product->name) {
            $nameValidator = Validator::make(['name' => $request->name], [
                'name' => 'unique:products,name'
            ], [
                'name.unique' => 'Товар ' . $fields['name'] . ' уже существует'
            ]);

            if ($nameValidator->fails())
                return response(['errors' => $nameValidator->errors()], 400);
        }

        $variationsController = new VariationsController();
        $productImagesController = new ProductImagesController();
        $productInfoController = new ProductInfoController();

        $product->update($fields);
        // создать/обновить вариации товара и их значения
        $storedVariations = $variationsController->storeArray($request->variations, $product);
        // создать характеристики
        $storedInfo = $productInfoController->storeArray($request->info, $product);
        // связать с изображениями
        $storedImages = $productImagesController->storeArray($request->images, $product);
        // удалить вариации, характеристики и связи с изображениями, которые не пришли в $request, но присутствуют в таблице и связаны с данным $product->id
        $variationsController->clear($request->variations, $product->id);
        $productImagesController->clear($request->images, $product->id);
        $productInfoController->clear($request->info, $product->id);

        return array_merge(
            Product::singleFullData($product->id),
            [
                'errors' => [
                    'variations' => $storedVariations['errors'],
                    'images' => $storedImages['errors'],
                    'info' => $storedInfo['errors']
                ]
            ]
        );
    }

    public function delete(Request $request, $id)
    {
        $rightCheck = AuthController::checkUserRight($request, 'delete_product');
        if (!$rightCheck['has_right'])
            return RolesExceptions::noRightsResponse();

        $product = Product::find($id);

        if (empty($product))
            return response(['error' => ProductsExceptions::noProduct()->getMessage()], 400);

        $prodName = $product->name;
        $product->delete();
        return response([
            'success' => true,
            'error' => false,
            'message' => 'Успешно удалено: товар' . $prodName
        ]);
    }
}