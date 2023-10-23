<?php

namespace App\Http\Controllers\Products\Variations;

use App\Exceptions\VariationsExceptions;
use Illuminate\Support\Facades\Validator;
use App\Models\Products\Variation;
use App\Http\Controllers\Controller;

/* методы класса не рассчитаны на вызов посредством роутов, вызываются при создании/редактировании товара 
методы рассчитывают, что проверка прав уже была произведена ранее
 */
class VariationsController extends Controller
{
    public function storeArray($variationsToStore, $product)
    {
        if(is_string($variationsToStore))
            $variationsToStore = json_decode($variationsToStore);

        $stored = [];
        $errors = [];
        foreach ($variationsToStore as $variationData) {
            $storedVariation = $this->store($variationData, $product->id);
            $errors = array_merge($errors, $storedVariation['errors']);
            if ($storedVariation['stored']) {
                $stored[] = $storedVariation['stored'];
            }
        }

        return [
            'stored' => $stored,
            'errors' => $errors
        ];
    }

    public function store($variationData = [], $productId)
    {
        $variationData['product_id'] = $productId;

        $validator = Validator::make(
            $variationData,
            [
                'name' => ['string', 'required'],
                'product_id' => 'exists:products,id|required'
            ],
            VariationsExceptions::storeValidator(),
            ['name' => 'название']
        );

        if ($validator->fails())
            return ['stored' => false, 'errors' => $validator->errors()->messages()];

        $variation = Variation::where('name', $variationData['name'])
            ->where('product_id', $productId)
            ->first();

        try {
            $this->checkVariationValues($variationData);
        } catch (VariationsExceptions $err) {
            return [
                'stored' => false,
                'errors' => [$err->getMessage()]
            ];
        }
        // конец валидации

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

        $variationValuesController = new VariationValuesController();
        // создание/обновление значений для вариации
        $storedVariationValues = $variationValuesController->storeArray($variationData, $variation);
        // удаление значений, не пришедших в $variationData
        $variationValuesController->clear($variationData, $variation);

        return [
            'stored' => $variation,
            'values' => $storedVariationValues['values'],
            'errors' => $storedVariationValues['errors']
        ];
    }

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

    // удалит те вариации, name которых отсутствует в списке, но связаны с товаром по product_id
    public function clear($variations, $productId)
    {
        $allProductVariations = Variation::where('product_id', $productId)
            ->get();
        foreach ($allProductVariations as $variationModel) {
            $arrKey = null;
            foreach ($variations as $key => $variationData) {
                if ($variationData['name'] !== $variationModel->name)
                    continue;

                $arrKey = $key;
            }

            // вариация присутствует в списке, оставить
            if (is_numeric($arrKey))
                continue;
            // вариации нет в списке, удалить
            $variationModel->delete();
        }
    }
}