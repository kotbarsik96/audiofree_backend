<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use App\Exceptions\TaxonomiesExceptions;
use Illuminate\Http\Request;
use App\Models\Taxonomies\Brand;
use App\Models\Taxonomies\Category;
use App\Models\Taxonomies\Type;
use App\Http\Controllers\AuthController;

class TaxonomiesController extends Controller
{
    public function taxonomyValidationReq(Request $request, $name, $title)
    {
        return Validator::make($request->all(), [
            'name' => 'required|string|unique:' . $name
        ], TaxonomiesExceptions::storeValidator($title));
    }

    public function store(Request $request, $taxName)
    {
        $rightCheck = AuthController::checkUserRight($request, 'add_taxonomy');
        if (!$rightCheck['has_right'])
            return $rightCheck;

        $title = '';
        $taxModel = null;
        switch ($taxName) {
            default:
                return TaxonomiesExceptions::notExists()->getMessage();
            case 'brands':
            case 'brand':
                $taxName = 'brands';
                $title = 'Бренд';
                $taxModel = Brand::class;
                break;
            case 'categories':
            case 'category':
                $taxName = 'categories';
                $title = 'Категория';
                $taxModel = Category::class;
                break;
            case 'types':
            case 'type':
                $taxName = 'types';
                $title = 'Тип';
                $taxModel = Type::class;
                break;
        }

        $validator = $this->taxonomyValidationReq($request, $taxName, $title);
        if ($validator->fails())
            return response(['errors' => $validator->errors()], 400);

        return $taxModel::create($validator->validated());
    }
}