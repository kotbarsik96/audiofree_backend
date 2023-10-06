<?php

namespace App\Http\Controllers\Variations;

use App\Exceptions\VariationsExceptions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Variation;
use App\Http\Controllers\Controller;

/* методы класса не рассчитаны на вызов посредством роутов, вызываются при создании/редактировании товара 
методы рассчитывают, что проверка прав уже была произведена ранее
 */
class VariationsController extends Controller
{
    public $variationNameRequirement = 'string|unique:variations,name|required';

    public function checkVariationValues($variationData)
    {
        $msg = 'Не передано ни одного значения для вариации ' . $variationData['name'];

        if (!array_key_exists('values', $variationData))
            throw new VariationsExceptions($msg);
        if (!is_array($variationData['values']))
            throw new VariationsExceptions($msg);
        if (count($variationData['values']) < 1)
            throw new VariationsExceptions($msg);

        return true;
    }

    public function storeVariationValues($variationData, $variation)
    {
        $values = [];
        $errors = [];
        $variationValuesController = new VariationValuesController();
        foreach ($variationData['values'] as $value) {
            $data = null;
            if(is_string($value)) {
                $data = [
                    'value' => $value,
                    'variation_id' => $variation->id
                ];
            } elseif(is_array($value)) {
                $data = [
                    'id' => $value['id'],
                    'value' => $value['value'],
                    'variation_id' => $variation->id
                ];
            }

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

    public function store($variationData = [], $productId)
    {
        $variationData['product_id'] = $productId;

        $variation = null;
        $validator = null;
        if (array_key_exists('id', $variationData))
            $variation = Variation::find($variationData['id']);

        if ($variation) {
            $reqs = ['product_id' => 'exists:products,id|required'];
            if ($variationData['name'] !== $variation->name)
                $reqs['name'] = $this->variationNameRequirement;

            $validator = Validator::make($variationData, $reqs, VariationsExceptions::updateValidator());
        } else {
            $validator = Validator::make($variationData, [
                'name' => $this->variationNameRequirement,
                'product_id' => 'exists:products,id|required'
            ], VariationsExceptions::storeValidator(), [
                'name' => 'название'
            ]);
        }

        if ($validator->fails())
            return ['variation' => false, 'errors' => $validator->errors()->messages()];

        try {
            $this->checkVariationValues($variationData);
        } catch (VariationsExceptions $err) {
            return [
                'variation' => false,
                'errors' => [$err->getMessage()]
            ];
        }

        $storeData = [
            'product_id' => $productId,
            'name' => $variationData['name']
        ];
        // обновить существующую вариацию
        if ($variation)
            $variation->update($storeData);
        // создать новую вариацию
        else
            $variation = Variation::create($storeData);

        // создание/обновление значений для вариации
        $storedVariationValues = $this->storeVariationValues($variationData, $variation);

        return [
            'variation' => $variation,
            'values' => $storedVariationValues['values'],
            'errors' => $storedVariationValues['errors']
        ];
    }
}