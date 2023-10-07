<?php

namespace App\Http\Controllers;

use App\Exceptions\RolesExceptions;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Exceptions\ProductsExceptions;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Variations\VariationsController;
use App\Models\Variation;

class ProductsController extends Controller
{
    public $storeAndUpdateReqs = [
        'price' => 'numeric|required',
        'discount_price' => 'nullable|numeric',
        'description' => 'string',
        'brand_id' => 'numeric|exists:brands,id|required',
        'category_id' => 'numeric|exists:categories,id|required',
        'type_id' => 'numeric|exists:types,id|required',
        'image_id' => 'numeric|exists:images,id'
    ];

    public function storeValidationReq(Request $request)
    {
        $reqs = array_merge($this->storeAndUpdateReqs, [
            'name' => 'unique:products,name|string|required',
        ]);
        return Validator::make($request->all(), $reqs, ProductsExceptions::storeValidator($request));
    }

    public function updateValidationReq(Request $request)
    {
        return Validator::make(
            $request->all(),
            $this->storeAndUpdateReqs,
            ProductsExceptions::storeValidator($request)
        );
    }

    public function storeVariations($variationsToStore, $product)
    {
        $variations = [];
        $errors = [];
        $variationsController = new VariationsController();
        foreach ($variationsToStore as $variationData) {
            $storedVariation = $variationsController->store($variationData, $product->id);
            $errors = array_merge($errors, $storedVariation['errors']);
            if ($storedVariation['variation']) {
                $variations[] = $storedVariation['variation'];
            }
        }

        return [
            'variations' => $variations,
            'errors' => $errors
        ];
    }

    // удалит те вариации, name которых отсутствует в списке, но связаны с товаром по product_id
    public function clearVariations($variations, $productId)
    {
        $allProductVariations = Variation::where('product_id', $productId)
            ->get();
        foreach ($allProductVariations as $variationModel) {
            $arrKey = null;
            foreach ($variations as $key => $variationData) {
                if ($variationData['name'] !== $variationModel->name)
                    continue;

                $arrKey = $key;
            }

            // вариация присутствует в списке, оставить
            if (is_numeric($arrKey))
                continue;
            // вариации нет в списке, удалить
            $variationModel->delete();
        }
    }

    public function store(Request $request)
    {
        $rightCheck = AuthController::checkUserRight($request, 'add_product');
        if (!$rightCheck['has_right'])
            return RolesExceptions::noRightsResponse();

        $validator = $this->storeValidationReq($request);
        if ($validator->fails())
            return response(['errors' => $validator->errors()], 400);

        // создать товар
        $product = Product::create($validator->validated());
        // создать вариации товара и добавить для каждого из них значения
        $storedVariations = $this->storeVariations($request->variations, $product);

        return array_merge(
            Product::singleFullData($product->id),
            ['errors' => $storedVariations['errors']]
        );
    }

    public function update(Request $request, $id)
    {
        $rightCheck = AuthController::checkUserRight($request, 'update_product');
        if (!$rightCheck['has_right'])
            return RolesExceptions::noRightsResponse();

        $validator = $this->updateValidationReq($request);
        if ($validator->fails())
            return response(['errors' => $validator->errors()], 400);

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

        $product->update($fields);
        // создать/обновить вариации товара и их значения
        $storedVariations = $this->storeVariations($request->variations, $product);
        // удалить вариации, которые не пришли в $request, но присутствуют в таблице variations и связаны с данным $product->id
        $this->clearVariations($request->variations, $product->id);

        return array_merge(
            Product::singleFullData($product->id),
            ['errors' => $storedVariations['errors']]
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