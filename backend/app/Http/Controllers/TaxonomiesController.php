<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use App\Exceptions\TaxonomiesExceptions;
use Illuminate\Http\Request;
use App\Models\Taxonomies\Brand;
use App\Models\Taxonomies\Category;
use App\Models\Taxonomies\Type;
use App\Http\Controllers\AuthController;
use App\Exceptions\RolesExceptions;
use Illuminate\Validation\Rule;

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
                $rightCheck = AuthController::checkUserRight($request, 'update_taxonomy');
                if (!$rightCheck['has_right'])
                    return RolesExceptions::noRightsResponse();

                $taxModelInst->update($validator->validated());
                return $taxModelInst;
            }
        }

        $rightCheck = AuthController::checkUserRight($request, 'add_taxonomy');
        if (!$rightCheck['has_right'])
            return RolesExceptions::noRightsResponse();

        return $taxData['model']::create($validator->validated());
    }

    public function delete(Request $request, $taxName, $id)
    {
        $rightCheck = AuthController::checkUserRight($request, 'delete_taxonomy');
        if (!$rightCheck['has_right'])
            return RolesExceptions::noRightsResponse();

        $taxData = null;
        try {
            $taxData = $this->getTaxData($taxName);
        } catch (TaxonomiesExceptions $err) {
            return ['error' => $err->getMessage()];
        }

        $taxonomy = $taxData['model']::find($id);
        if (empty($taxonomy))
            return response(['error' => TaxonomiesExceptions::notExists()]);

        $taxDeletedMessage = 'Успешно удалено: ' . $taxData['title'] . ' "' . $taxonomy->name . '"';
        $taxonomy->delete();
        return response(['success' => true, 'message' => $taxDeletedMessage]);
    }
}