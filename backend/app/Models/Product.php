<?php

namespace App\Models;

use App\Exceptions\ProductsExceptions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Rating;

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

        $ratingValue = Rating::where('product_id', $product->id)
            ->avg('value') ?? 0;
        $ratingAmount = Rating::where('product_id', $product->id)
            ->count();

        $variations = Variation::where('product_id', $product->id)
            ->get();

        $arr = [
            'product' => $product,
            'rating' => [
                'value' => (int) $ratingValue,
                'amount' => $ratingAmount
            ],
            'variations' => [],
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