<?php

namespace App\Http\Controllers;

use App\Exceptions\TaxonomiesExceptions;
use Illuminate\Http\Request;
use App\Models\Taxonomies\Taxonomy;
use App\Exceptions\RolesExceptions;
use App\Models\User;
use App\Filters\TaxonomiesFilter;
use App\Models\Taxonomies\TaxonomyType;
use App\Models\Taxonomies\TaxonomiesMeta;
use enshrined\svgSanitize\Sanitizer;

class TaxonomiesController extends Controller
{
    /* $fields: ['brand' => 'Apple']. Проверяет, что Apple существует именно с taxonomy_type === brand. Так, если Apple существует в taxonomies, но у него, например, taxonomy_type === category, будет возвращен массив с ошибкой. В случае успешной проверки, будет возвращен тот же массив, что и зашел на входе ($fields)

    Этот метод нужен, т.к. валидатор проверяет просто наличие строки с такой колонкой, но не проеряет taxonomy_type */
    public function checkTaxonomies($fields)
    {
        foreach ($fields as $taxType => $name) {
            if (empty(Taxonomy::where('taxonomy_type', $taxType)->where('name', $name))) {
                $errors = [];
                $errors[$name] = $this->getTaxonomyTypeTitle($taxType) . ' ' . $name . ' не существует';
                return ['errors' => $errors];
            }
        }
        return $fields;
    }

    public function getTaxonomyTypeTitle($taxonomyType)
    {
        $tt = TaxonomyType::where('type', $taxonomyType)->first();
        if (empty($tt))
            return '<таксономия не существует>';

        return $tt->title;
    }

    public function all()
    {
        return [
            'brands' => Taxonomy::where('taxonomy_type', 'brand')->get(['id', 'name']),
            'categories' => Taxonomy::where('taxonomy_type', 'category')->get(['id', 'name']),
            'types' => Taxonomy::where('taxonomy_type', 'type')->get(['id', 'name']),
            'product_statuses' => Taxonomy::where('taxonomy_type', 'product_status')->get(['id', 'name'])
        ];
    }

    public function filter(TaxonomiesFilter $queryFilter, $taxType)
    {
        $request = $queryFilter->request;
        $queryFilter->taxonomyName = $taxType;
        $limit = $request->query('limit') ?? null;
        $offset = $request->query('offset') ?? null;

        $filtered = Taxonomy::where('taxonomy_type', $taxType)
            ->filter($queryFilter)
            ->select(['id', 'name', 'taxonomy_type']);
        $count = $filtered->count();
        $filtered = $this->getMeta($filtered->offsetLimit($limit, $offset)->get());
        return [
            'result' => $filtered,
            'total_count' => $count,
            'taxonomy_type' => $taxType
        ];
    }

    public function storeOrUpdate(Request $request, $taxType, $id = null)
    {
        $taxTypeExists = TaxonomyType::where('type', $taxType)->first();
        if (empty($taxTypeExists))
            return response(['error' => 'Тип таксономии ' . $taxType . ' не найден'], 404);

        // обновить название, если передан id
        if (!empty($id)) {
            if (!User::hasRight($request->cookie('user'), 'update_taxonomy', $request))
                return RolesExceptions::noRightsResponse();

            $taxonomy = Taxonomy::find($id);
            if (!empty($taxonomy)) {
                $nameIsNotUnique = Taxonomy::where('taxonomy_type', $taxType)
                    ->where('name', $request->name)
                    ->whereNot('id', $id)
                    ->first();
                if ($nameIsNotUnique) {
                    $taxonomyTypeTitle = $this->getTaxonomyTypeTitle($taxType);
                    return TaxonomiesExceptions::alreadyExistsResponse($taxonomyTypeTitle, $request->name);
                }

                $taxonomy->update(['name' => $request->name]);
                $this->storeMeta($taxonomy, $request->meta);
                return $taxonomy;
            }
        }

        // создать новую таксономию
        if (!User::hasRight($request->cookie('user'), 'add_taxonomy', $request))
            return RolesExceptions::noRightsResponse();

        $exists = Taxonomy::where('taxonomy_type', $taxType)
            ->where('name', $request->name)
            ->first();
        if ($exists) {
            return TaxonomiesExceptions::alreadyExistsResponse(
                $this->getTaxonomyTypeTitle($taxType),
                $request->name
            );
        }

        $taxonomy = Taxonomy::create([
            'taxonomy_type' => $taxType,
            'name' => $request->name,
        ]);
        $this->storeMeta($taxonomy, $request->meta);
        return $taxonomy;
    }

    /* metaValues: ['name' => 'value', 'name2' => 'value2'].
        если нужно удалить: ['name' => null]
        */
    public function storeMeta($taxonomy, $metaValues)
    {
        foreach ($metaValues as $key => $value) {
            if (!is_array($value))
                continue;
            if (!array_key_exists('store', $value))
                continue;

            $metaRow = TaxonomiesMeta::where('taxonomy_type', $taxonomy->taxonomy_type)
                ->where('taxonomy_name', $taxonomy->name)
                ->where('meta_name', $key)
                ->first();

            $valueToStore = $value['store'];
            if(empty($valueToStore)) {
                if($metaRow)
                    $metaRow->delete();
                return;
            }

            if (preg_match('/svg/i', $valueToStore)) {
                $sanitizer = new Sanitizer();
                $valueToStore = $sanitizer->sanitize($valueToStore);
            }

            if (empty($metaRow)) {
                $metaRow = TaxonomiesMeta::create([
                    'taxonomy_type' => $taxonomy->taxonomy_type,
                    'taxonomy_name' => $taxonomy->name,
                    'meta_name' => $key,
                    'meta_value' => $valueToStore,
                ]);
            } else {
                $metaRow->update([
                    'meta_name' => $key,
                    'meta_value' => $valueToStore
                ]);
            }

            return $metaRow;
        }
    }

    public function getMeta($taxonomies)
    {
        foreach ($taxonomies as $taxItemModel) {
            $properties = TaxonomiesMeta::where('taxonomy_type', $taxItemModel->taxonomy_type)
                ->where('taxonomy_name', $taxItemModel->name)
                ->get(['meta_name', 'meta_value']);
            $meta = [];
            foreach ($properties as $prop) {
                $meta[$prop->meta_name] = ['value' => $prop->meta_value];
            }
            $taxItemModel->meta = (object) $meta;
        }
        return $taxonomies;
    }

    public function handleDelete(Request $request, $taxType, $id = null)
    {
        if (!User::hasRight($request->cookie('user'), 'delete_taxonomy', $request))
            return RolesExceptions::noRightsResponse();

        // удалить одну
        if ($id) {
            $res = $this->delete($taxType, $id);
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
                $res = $this->delete($taxType, $id);
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

    public function delete($taxType, $id)
    {
        $taxonomy = Taxonomy::find($id);
        if (empty($taxonomy))
            return ['error' => TaxonomiesExceptions::notExists(), 'code' => 400];

        $meta = TaxonomiesMeta::where('taxonomy_type', $taxType)
            ->where('taxonomy_name', $taxonomy->name)
            ->get();
        foreach ($meta as $row) {
            $row->delete();
        }

        $taxDeletedMessage = 'Успешно удалено: ' . $this->getTaxonomyTypeTitle($taxType) . ' "' . $taxonomy->name . '"';
        $taxonomy->delete();
        return [
            'success' => true,
            'message' => $taxDeletedMessage,
            'error' => false
        ];
    }
}
