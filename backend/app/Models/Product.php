<?php

namespace App\Models;

use App\Exceptions\ProductsExceptions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'discount_price',
        'description',
        'type_id',
        'brand_id',
        'category_id',
        'image_id'
    ];

    public static function singleFullData($id)
    {
        $product = self::find($id);
        if (empty($product))
            return ['error' => ProductsExceptions::noProduct()];

        $variations = Variation::where('product_id', $product->id)
            ->get();

        $arr = [
            'product' => $product,
            'variations' => []
        ];
        foreach ($variations as $variationModel) {
            $values = VariationValue::where('variation_id', $variationModel->id)
                ->get();

            array_push($arr['variations'], [
                'variation' => $variationModel,
                'values' => $values
            ]);
        }

        return $arr;
    }
}