<?php

namespace App\Http\Controllers\Products;

use App\Exceptions\RolesExceptions;
use App\Models\Products\Product;
use Illuminate\Http\Request;
use App\Filters\QueryFilter;
use App\Exceptions\ProductsExceptions;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Products\Variations\VariationsController;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use App\Models\User;

class ProductsController extends Controller
{
    public function index(Request $request, $id)
    {
        $timestampsQuery = $request->query('timestamps');
        $selectTimestamps = $timestampsQuery === 'true'
            ? true : false;
        return Product::singleFullData($id, $selectTimestamps);
    }

    public function filter(QueryFilter $request)
    {
        $queries = $request->queries();
        $limit = array_key_exists('limit', $queries) ? $queries['limit'] : null;
        $offset = array_key_exists('offset', $queries) ? $queries['offset'] : null;

        return Product::filter($request)
            ->offsetLimit($limit, $offset)
            ->mainData()
            ->get();
    }

    public function storeValidationReq(Request $request, $ignoreId = null)
    {
        $reqs = [
            'name' => ['string', Rule::unique('products', 'name')->ignore($ignoreId), 'required'],
            'price' => 'numeric|required',
            'discount_price' => 'nullable|numeric',
            'description' => 'string',
            'brand_id' => 'exists:brands,id|required',
            'category_id' => 'exists:categories,id|required',
            'type_id' => 'exists:types,id|required',
            'image_id' => 'nullable|numeric|exists:images,id'
        ];
        return Validator::make(
            $request->all(),
            $reqs,
            ProductsExceptions::storeValidator($request),
            [
                'brand' => 'Бренд',
                'category' => 'Категория',
                'type' => 'Тип'
            ]
        );
    }

    public function store(Request $request)
    {
        if (!User::hasRight($request->cookie('user'), 'add_product'))
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

        return [
            'product' => Product::singleFullData($product->id),
            'errors' => [
                'variations' => $storedVariations['errors'],
                'images' => $storedImages['errors'],
                'info' => $storedInfo['errors']
            ]
        ];
    }

    public function update(Request $request, $id)
    {
        if (!User::hasRight($request->cookie('user'), 'update_product'))
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

        return [
            'product' => Product::singleFullData($product->id),
            'errors' => [
                'variations' => $storedVariations['errors'],
                'images' => $storedImages['errors'],
                'info' => $storedInfo['errors']
            ]
        ];
    }

    public function delete(Request $request, $id)
    {
        if (!User::hasRight($request->cookie('user'), 'delete_product'))
            return RolesExceptions::noRightsResponse();

        $product = Product::find($id);

        if (empty($product))
            return response(['error' => ProductsExceptions::noProduct()->getMessage()], 400);

        $prodName = $product->name;
        $product->delete();
        return response([
            'success' => true,
            'error' => false,
            'message' => 'Успешно удалено: товар ' . $prodName
        ]);
    }
}