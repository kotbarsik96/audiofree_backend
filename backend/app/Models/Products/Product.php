<?php

namespace App\Models\Products;

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

    /* $needExtra == false - не отобразит такие поля, как: "created_at", "updated_at", не укажет id у таксономий и изображений, а вернет только названия таксономий и пути к изображениям. 
        $needExtra == true - отобразит указанные выше поля, покажет id у таксономий и изображений
    */
    public static function singleFullData($id, $needExtra = false)
    {
        $product = self::select('products.id', 'products.name', 'products.price', 'products.discount_price', 'products.description', 'types.name AS type', 'brands.name AS brand', 'categories.name AS category', 'images.path AS image_path')
            ->leftJoin('types', 'products.type_id', '=', 'types.id')
            ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->leftJoin('images', 'products.image_id', '=', 'images.id')
            ->find($id);
        if (empty($product))
            return ['error' => ProductsExceptions::noProduct()->getMessage()];

        $ratingValue = Rating::where('product_id', $product->id)
            ->avg('value') ?? 0;
        $ratingAmount = Rating::where('product_id', $product->id)
            ->count();

        $info = ProductInfo::select('id', 'name', 'value')
            ->where('product_id', $product->id)
            ->get();
        $images = ProductImage::select('product_images.id', 'images.id as image_id', 'images.path')
            ->leftJoin('images', 'product_images.image_id', '=', 'images.id')
            ->where('product_id', $product->id)
            ->get();
        $variations = Variation::select('id', 'name')
            ->where('product_id', $product->id)
            ->get();

        $arr = [
            'product' => $product,
            'rating' => [
                'value' => (int) $ratingValue,
                'amount' => $ratingAmount
            ],
            'info' => $info,
            'images' => $images,
            'variations' => [],
        ];
        foreach ($variations as $variationModel) {
            $values = VariationValue::select('id', 'value')
                ->where('variation_id', $variationModel->id)
                ->get();

            array_push($arr['variations'], [
                'variation' => $variationModel,
                'values' => $values
            ]);
        }

        return $arr;
    }
}