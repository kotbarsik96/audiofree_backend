<?php

namespace App\Models\Products;

use App\Exceptions\ProductsExceptions;
use App\Models\FilterableModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use App\Models\UserEntities\Cart;
use App\Models\UserEntities\CartProduct;

class Product extends FilterableModel
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'discount_price',
        'description',
        'quantity',
        'product_status_id',
        'type_id',
        'brand_id',
        'category_id',
        'image_id'
    ];
    protected $casts = [
        'rating_value' => 'float'
    ];

    public static function scopeCurrentPrice(Builder $builder)
    {
        $builder->addSelect(
            DB::raw('IF(products.discount_price, products.discount_price, products.price) AS current_price')
        );
    }

    public static function scopeMainData(Builder $builder)
    {
        $builder->currentPrice()
            ->addSelect(
                'products.id',
                'products.name',
                'products.price',
                'products.discount_price',
                'products.quantity',
                'products.product_status_id',
                'products.description',
                'images.path AS image_path',
                'images.id AS image_id',
                DB::raw('avg(ratings.value) as rating_value'),
                DB::raw('count(*) as rating_count')
            )
            ->leftJoin('images', 'products.image_id', '=', 'images.id')
            ->leftJoin('ratings', 'products.id', '=', 'ratings.product_id')
            ->groupBy('products.id');
    }

    public function scopeSort(Builder $builder, $sortValue)
    {
        if (empty($sortValue))
            return;

        $split = explode('|', $sortValue);
        $sortBy = $split[0];
        $sortType = array_key_exists(1, $split) ? $split[1] : 'asc' ?? 'desc';

        $builder->orderBy($sortBy, $sortType);
    }

    public static function scopeTaxonomies(Builder $builder)
    {
        $builder->addSelect(
            'types.name AS type',
            'brands.name AS brand',
            'categories.name AS category',
            'product_statuses.name AS product_status'
        )
            ->leftJoin('types', 'products.type_id', '=', 'types.id')
            ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->leftJoin('product_statuses', 'products.product_status_id', '=', 'product_statuses.id');
    }

    public static function scopeCheapest(Builder $builder)
    {
        $bCopy = clone $builder;
        $res = $bCopy->currentPrice()->orderBy('current_price', 'asc')->first();
        if (empty($res))
            return 0;
        if (empty($res->current_price))
            return 0;

        return $res->current_price;
    }

    public static function scopeMostExpensive(Builder $builder)
    {
        $bCopy = clone $builder;
        $res = $bCopy->currentPrice()->orderBy('current_price', 'desc')->first();
        if (empty($res))
            return 0;
        if (empty($res->current_price))
            return 0;

        return $res->current_price;
    }

    public static function singleFullData($id, $selectTimestamps = false, $selectStatistics = false)
    {
        $product = self::mainData()
            ->taxonomies();
        if ($selectTimestamps)
            $product->timestamps();
        if($selectStatistics)
            $product->statistics();

        $product = $product->find($id);

        if (empty($product))
            return ['error' => ProductsExceptions::noProduct()->getMessage()];

        $product = self::addOuterData($product);
        return $product;
    }

    public static function scopeTimestamps(Builder $builder)
    {
        $builder->addSelect('products.updated_at', 'products.created_at');
    }

    public static function scopeStatistics(Builder $builder)
    {
        $builder->addSelect('product_statistics.sold', 'product_statistics.in_favorites', 'product_statistics.income')
            ->leftJoin('product_statistics', 'products.id', '=', 'product_statistics.product_id')
            ->groupBy('product_statistics.id');
    }

    /* добавляет данные из других таблиц: характеристики, вариации, галерея */
    public static function addOuterData(Product $product)
    {
        if (empty($product))
            return $product;

        $info = ProductInfo::where('product_id', $product->id)
            ->get(['product_info.id', 'product_info.name', 'product_info.value']);
        $images = ProductImage::leftJoin('images', 'product_images.image_id', '=', 'images.id')
            ->where('product_id', $product->id)
            ->get(['product_images.id', 'images.id as image_id', 'images.path']);
        $variations = Variation::where('product_id', $product->id)
            ->get(['variations.id', 'variations.name']);

        $productVariations = [];
        foreach ($variations as $variationModel) {
            $values = VariationValue::where('variation_id', $variationModel->id)
                ->get(['id', 'value']);

            array_push($productVariations, [
                'variation' => $variationModel,
                'values' => $values
            ]);
        }

        $product->info = $info;
        $product->images = $images;
        $product->variations = $productVariations;

        return $product;
    }

    /* возвращает количество товара, доступное для добавления в корзину конкретному пользователю. То есть, учитывает, сколько товаров пользователь уже добавил в корзину */
    public static function getAvailableQuantity($product, $userId)
    {
        $quantity = is_array($product)
            ? $product['quantity']
            : $product->quantity;
        if ($userId) {
            $userCart = Cart::where('user_id', $userId)->first();
            if (empty($userCart))
                $userCart = Cart::create(['user_id' => $userId]);

            $currentProductInCart = CartProduct::where('cart_id', $userCart->id)
                ->where('product_id', $product->id)
                ->get();
            foreach ($currentProductInCart as $prodData) {
                $quantity = $quantity - $prodData->quantity;
            }
        }
        return $quantity;
    }
}
