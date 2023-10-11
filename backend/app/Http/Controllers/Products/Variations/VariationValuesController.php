<?php

namespace App\Http\Controllers\Products\Variations;

use App\Exceptions\VariationsExceptions;
use Illuminate\Support\Facades\Validator;
use App\Models\Products\VariationValue;
use App\Http\Controllers\Controller;

class VariationValuesController extends Controller
{
    public function storeValidator($data)
    {
        return Validator::make($data, [
            'variation_id' => 'exists:variations,id|required'
        ], VariationsExceptions::storeValueValidator());
    }

    public function store($data)
    {
        $validator = $this->storeValidator($data);

        if ($validator->fails()) {
            return [
                'value' => $data['value'],
                'errors' => $validator->errors()->messages()
            ];
        }

        $varValue = VariationValue::where('value', $data['value'])
            ->where('variation_id', $data['variation_id'])
            ->first();

        if (empty($varValue))
            VariationValue::create($data);
        else
            $varValue->update($data);

        return $varValue;
    }

    public function storeArray($variationData, $variation)
    {
        $values = [];
        $errors = [];
        $variationValuesController = new VariationValuesController();
        foreach ($variationData['values'] as $value) {
            $data = [
                'value' => $value,
                'variation_id' => $variation->id
            ];

            $variationValue = $variationValuesController->store($data);
            $isError = is_array($variationValue)
                && array_key_exists('error', $variationValue)
                && $variationValue['error'];

            if ($isError)
                $errors[] = $variationValue;
            else
                $values[] = $variationValue;
        }

        return [
            'values' => $values,
            'errors' => $errors
        ];
    }

    public function clear($variationData, $variation)
    {
        $allVarValues = VariationValue::where('variation_id', $variation->id)
            ->get();

        foreach ($allVarValues as $varValueModel) {
            $arrKey = null;
            foreach ($variationData['values'] as $key => $varValue) {
                if (is_array($varValue))
                    $varValue = $varValue['value'];

                if ($varValue !== $varValueModel->value)
                    continue;

                $arrKey = $key;
            }

            // значение есть в $variationData, оставить
            if (is_numeric($arrKey))
                continue;

            // значение отсутствует в $variationData, удалить
            $varValueModel->delete();
        }
    }
}