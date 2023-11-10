<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use App\Exceptions\TaxonomiesExceptions;
use Illuminate\Http\Request;
use App\Models\Taxonomies\Brand;
use App\Models\Taxonomies\Category;
use App\Models\Taxonomies\Type;
use App\Models\Taxonomies\ProductStatus;
use App\Exceptions\RolesExceptions;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Filters\TaxonomiesFilter;

class TaxonomiesController extends Controller
{
    public function taxonomyValidationReq(Request $request, $name, $title, $taxId = null)
    {
        return Validator::make($request->all(), [
            'name' => ['required', 'string', Rule::unique($name, 'name')->ignore($taxId)]
        ], [
            'name.required' => 'Не указано название для таксономии "' . $title . '"',
            'name.unique' => $title . ' с таким названием уже существует'
        ]);
    }

    /* применяется после того, как прошла проверка через Validator::make(). Принимает названия под ключами в $taxs, возвращает id в поля 'brand_id' и т.д. */
    public function translateTaxonomiesToIds($fields)
    {
        $taxs = [
            'brand' => Brand::class,
            'category' => Category::class,
            'type' => Type::class,
            'product_status' => ProductStatus::class
        ];

        foreach ($taxs as $name => $model) {
            if (!array_key_exists($name, $fields))
                continue;

            $fieldTaxName = $fields[$name];

            if (empty($fieldTaxName))
                continue;

            $taxonomyData = $model::where('name', $fieldTaxName)->first();
            if (empty($taxonomyData))
                continue;

            $fields[$name . '_id'] = $taxonomyData->id;
        }

        return $fields;
    }

    public function all()
    {
        return [
            'brands' => Brand::all(['id', 'name']),
            'categories' => Category::all(['id', 'name']),
            'types' => Type::all(['id', 'name']),
            'product_statuses' => ProductStatus::all(['id', 'name'])
        ];
    }

    public function filter(TaxonomiesFilter $queryFilter, $taxonomyTitle)
    {
        $model = null;
        switch ($taxonomyTitle) {
            case 'brand':
                $model = Brand::class;
                break;
            case 'category':
                $model = Category::class;
                break;
            case 'type':
                $model = Type::class;
                break;
            case 'product_status':
                $model = ProductStatus::class;
                break;
        }

        $request = $queryFilter->request;
        $queryFilter->taxonomyName = $taxonomyTitle;
        $limit = $request->query('limit') ?? null;
        $offset = $request->query('offset') ?? null;

        $filtered = $model::filter($queryFilter)
            ->select(['id', 'name']);
        $count = $filtered->count();
        return [
            'result' => $filtered->offsetLimit($limit, $offset)->get(),
            'total_count' => $count
        ];
    }

    public function getTaxData($taxName)
    {
        $model = null;
        switch ($taxName) {
            default:
                throw TaxonomiesExceptions::notExists();
            case 'brands':
            case 'brand':
                $name = 'brands';
                $title = 'Бренд';
                $model = Brand::class;
                break;
            case 'categories':
            case 'category':
                $name = 'categories';
                $title = 'Категория';
                $model = Category::class;
                break;
            case 'types':
            case 'type':
                $name = 'types';
                $title = 'Тип';
                $model = Type::class;
                break;
            case 'product_status':
            case 'product_statuses':
                $name = 'product_statuses';
                $title = 'Статус товара';
                $model = ProductStatus::class;
                break;
        }

        return [
            'name' => $name,
            'model' => $model,
            'title' => $title
        ];
    }

    public function storeOrUpdate(Request $request, $taxName, $id = null)
    {
        $taxData = null;
        try {
            $taxData = $this->getTaxData($taxName);
        } catch (TaxonomiesExceptions $err) {
            return ['error' => $err->getMessage()];
        }

        $validator = $this->taxonomyValidationReq($request, $taxData['name'], $taxData['title'], $id);
        if ($validator->fails())
            return response(['errors' => $validator->errors()], 400);

        if ($id) {
            $taxModelInst = $taxData['model']::find($id);
            if ($taxModelInst) {
                if (!User::hasRight($request->cookie('user'), 'update_taxonomy', $request))
                    return RolesExceptions::noRightsResponse();

                $taxModelInst->update($validator->validated());
                return $taxModelInst;
            }
        }

        if (!User::hasRight($request->cookie('user'), 'add_taxonomy', $request))
            return RolesExceptions::noRightsResponse();

        return $taxData['model']::create($validator->validated());
    }

    public function handleDelete(Request $request, $taxName, $id = null)
    {
        if (!User::hasRight($request->cookie('user'), 'delete_taxonomy', $request))
            return RolesExceptions::noRightsResponse();

        // удалить одну
        if ($id) {
            $res = $this->delete($taxName, $id);
            $code = array_key_exists('code', $res) ? $res['code'] : 200;
            return response($res, $code);
        }
        // удалить несколько
        else {
            $deleted = [];
            $errors = [];
            $queries = $request->all();
            $list = array_key_exists('idsList', $queries) ? $queries['idsList'] : [];
            foreach ($list as $id) {
                $res = $this->delete($taxName, $id);
                if ($res['error'])
                    array_push($errors, $res['error']);
                else
                    array_push($deleted, $res['message']);
            }
            return response([
                'deleted' => $deleted,
                'errors' => $errors,
                'message' => 'Было удалено таксономий: ' . count($deleted) . ' из ' . count($list)
            ]);
        }
    }

    public function delete($taxName, $id)
    {
        $taxData = null;
        try {
            $taxData = $this->getTaxData($taxName);
        } catch (TaxonomiesExceptions $err) {
            return ['error' => $err->getMessage(), 'code' => 400];
        }

        $taxonomy = $taxData['model']::find($id);
        if (empty($taxonomy))
            return ['error' => TaxonomiesExceptions::notExists(), 'code' => 400];

        $taxDeletedMessage = 'Успешно удалено: ' . $taxData['title'] . ' "' . $taxonomy->name . '"';
        $taxonomy->delete();
        return ['success' => true, 'message' => $taxDeletedMessage, 'error' => false];
    }
}
