<?php

namespace App\Http\Controllers\Products;

use App\Exceptions\RolesExceptions;
use App\Models\Products\Product;
use Illuminate\Http\Request;
use App\Filters\ProductsFilter;
use App\Exceptions\ProductsExceptions;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Products\Variations\VariationsController;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Http\Controllers\TaxonomiesController;

class ProductsController extends Controller
{
    public function index(Request $request, $id)
    {
        $product = Product::find($id);
        if(empty($product))
            return null;

        // загрузит только внешние данные товара (характеристики, вариации и др.)
        $isOnlyOuter = $request->query('onlyOuterData');
        if ($isOnlyOuter && $isOnlyOuter !== 'false') {
            $productObj = new Product();
            $productObj->id = $id;
            return $productObj::addOuterData($productObj);
        }

        $timestampsQuery = $request->query('timestamps');
        $selectTimestamps = $timestampsQuery === 'true'
            ? true : false;
        $product = Product::singleFullData($id, $selectTimestamps);
        $product->available_quantity = Product::getAvailableQuantity(
            $product,
            $request->cookie('user')
        );
        return $product;
    }

    public function filter(ProductsFilter $queryFilter)
    {
        function getAvailableQuantity($productsArray, $request)
        {
            foreach ($productsArray as $product) {
                $product->available_quantity = Product::getAvailableQuantity(
                    $product,
                    $request->cookie('user')
                );
            }
            return $productsArray;
        }

        $request = $queryFilter->request;
        $limit = $request->query('limit') ?? null;
        $offset = $request->query('offset') ?? null;
        $except = $request->query('except') ?? [];

        if ($request->query('idsList')) {
            $idsList = $request->query('idsList');
            if (!is_array($idsList))
                return response(['error' => 'Не передан список id'], 400);

            $products = Product::mainData()
                ->sort($request->query('sortValue'))
                ->filter($queryFilter)
                ->whereIn('products.id', $idsList)
                ->offsetLimit($limit, $offset)
                ->get();
            $products = getAvailableQuantity(
                $products,
                $request
            );
            return $products;
        }

        $cheapest = Product::cheapest();
        $mostExpensive = Product::mostExpensive();
        $productQuery = Product::filter($queryFilter);
        foreach ($except as $exceptId) {
            if (!is_numeric($exceptId))
                continue;
            $productQuery->where('products.id', '!=', $exceptId);
        }
        $totalCount = $productQuery->count();

        $productQuery->sort($request->query('sortValue'))
            ->offsetLimit($limit, $offset)
            ->mainData();
        if ($request->query('allData') === 'true' || $request->query('allData') === true)
            $productQuery->taxonomies()->timestamps();

        $products = getAvailableQuantity($productQuery->get(), $request);

        return [
            'result' => $products,
            'total_count' => $totalCount,
            'cheapest_price' => $cheapest,
            'most_expensive_price' => $mostExpensive
        ];
    }

    public function storeValidationReq(Request $request, $ignoreId = null)
    {
        $reqs = [
            'name' => ['string', Rule::unique('products', 'name')->ignore($ignoreId), 'required'],
            'price' => 'numeric|required',
            'discount_price' => 'nullable|numeric',
            'description' => 'string',
            'quantity' => 'numeric',
            'product_status' => 'exists:product_statuses,name|required',
            'brand' => 'exists:brands,name|required',
            'category' => 'exists:categories,name|required',
            'type' => 'exists:types,name|required',
            'image_id' => 'nullable|numeric|exists:images,id'
        ];
        return Validator::make(
            $request->all(),
            $reqs,
            ProductsExceptions::storeValidator($request),
            [
                'brand' => 'Бренд',
                'price' => 'Цена',
                'discount_price' => 'Цена по скидке',
                'category' => 'Категория',
                'type' => 'Тип',
                'image_id' => 'Изображение',
                'name' => 'Название',
                'quantity' => 'Количество',
                'product_status' => 'Статус'
            ]
        );
    }

    public function store(Request $request)
    {
        if (!User::hasRight($request->cookie('user'), 'add_product', $request))
            return RolesExceptions::noRightsResponse();

        $validator = $this->storeValidationReq($request);
        if ($validator->fails())
            return response(['errors' => $validator->errors()], 400);

        $variationsController = new VariationsController();
        $productImagesController = new ProductImagesController();
        $productInfoController = new ProductInfoController();

        // создать товар
        $taxonomiesController = new TaxonomiesController();
        $fields = $taxonomiesController->translateTaxonomiesToIds($validator->validated());
        $product = Product::create($fields);
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
        if (!User::hasRight($request->cookie('user'), 'update_product', $request))
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

        $taxonomiesController = new TaxonomiesController();
        $fields = $taxonomiesController->translateTaxonomiesToIds($fields);

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

    public function handleDelete(Request $request, $id = null)
    {
        if (!User::hasRight($request->cookie('user'), 'delete_product', $request))
            return RolesExceptions::noRightsResponse();

        $res = $this->delete($request, $id);
        $hasCode = array_key_exists('code', $res);
        $code = $hasCode ? $res['code'] : null;
        if (!is_numeric($code))
            $code = 200;
        if ($hasCode)
            unset($res['code']);

        return response($res, $code);
    }

    public function delete(Request $request, $id = null)
    {
        // удалить один товар, переданный по $id в route
        if (is_numeric($id)) {
            $product = Product::find($id);

            if (empty($product))
                return ['error' => ProductsExceptions::noProduct()->getMessage(), 'code' => 400];

            $prodName = $product->name;
            $productImagesController = new ProductImagesController();
            $productImagesController->destroy($product->id);
            $product->delete();
            return [
                'success' => true,
                'error' => false,
                'message' => 'Успешно удалено: товар ' . $prodName,
                'name' => $prodName,
            ];
        }
        // удалить несколько товаров, id которых переданы в массиве idsList
        else {
            $requestData = $request->all();
            if (!array_key_exists('idsList', $requestData))
                return ['error' => 'Переданы некорректные данные', 'code' => 400];

            $ids = $requestData['idsList'];
            if (!is_array($ids))
                return ['error' => 'Переданы некорректные данные', 'code' => 400];

            $deleted = [];
            $errors = [];
            foreach ($ids as $idFromList) {
                $subRequest = Request::create(
                    '/api/product/delete',
                    'DELETE',
                    $request->all(),
                    $request->cookie()
                );
                $res = $this->delete($subRequest, $idFromList);
                if (array_key_exists('name', $res))
                    array_push($deleted, $res['name']);
                if ($res['error'])
                    array_push($errors, $res['error']);
            }

            $message = 'Было удалено товаров: ' . count($deleted) . ' из ' . count($ids);

            return [
                'deleted' => $deleted,
                'message' => $message,
                'errors' => $errors
            ];
        }

        return ['success' => true];
    }
}
