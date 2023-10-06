<?php

namespace App\Http\Controllers\Variations;

use App\Exceptions\VariationsExceptions;
use Illuminate\Support\Facades\Validator;
use App\Models\VariationValue;
use App\Http\Controllers\Controller;

class VariationValuesController extends Controller
{
    public function storeValidator($data)
    {
        return Validator::make($data, [
            'variation_id' => 'exists:variations,id|required',
            'value' => 'unique:variation_values,value|required'
        ], VariationsExceptions::storeValueValidator());
    }

    public function store($data)
    {
        $validator = $this->storeValidator($data);

        if ($validator->fails()) {
            return [
                'value' => $data['value'],
                'errors' => $validator->errors()
            ];
        }

        $varValue = null;
        if (array_key_exists('id', $data)) {
            $varValue = VariationValue::find($data['id']);
            if ($varValue)
                $varValue->update($data);
        }

        if (empty($varValue))
            VariationValue::create($data);

        return $varValue;
    }
}