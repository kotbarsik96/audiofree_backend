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
use App\Http\Controllers\TaxonomiesController;
use Illuminate\Support\Facades\DB;

class ProductsController extends Controller
{
    public function index(Request $request, $id)
    {
        $timestampsQuery = $request->query('timestamps');
        $selectTimestamps = $timestampsQuery === 'true'
            ? true : false;
        return Product::singleFullData($id, $selectTimestamps);
    }

    public function filter(QueryFilter $queryFilter)
    {
        $request = $queryFilter->request;
        $limit = $request->query('limit') ?? null;
        $offset = $request->query('offset') ?? null;

        $productQuery = Product::filter($queryFilter)
            ->offsetLimit($limit, $offset)
            ->mainData();
        if ($request->query('allData') === 'true' || $request->query('allData') === true)
            $productQuery->taxonomies()->timestamps();

        return $productQuery->get();
    }

    public function count()
    {
        return response(['count' => Product::all()->count()]);
    }

    public function storeValidationReq(Request $request, $ignoreId = null)
    {
        $reqs = [
            'name' => ['string', Rule::unique('products', 'name')->ignore($ignoreId), 'required'],
            'price' => 'numeric|required',
            'discount_price' => 'nullable|numeric',
            'description' => 'string',
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
                'category' => 'Категория',
                'type' => 'Тип',
                'image_id' => 'Изображение',
                'name' => 'Название'
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
        if(!is_numeric($code))
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
                if($res['error']) 
                    array_push($errors, $res['error']);
            }

            $message = 'Было удалено товаров: ' . count($deleted) . ' из ' . count($ids);

            return [
                'deleted' => $deleted,
                'message' => $message,
                'errors' => $errors
            ];
        }
    }
}
