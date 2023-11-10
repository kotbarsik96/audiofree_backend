<?php

namespace App\Filters;

use App\Models\Taxonomies\ProductStatus;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class TaxonomiesFilter extends QueryFilter
{
    public $taxonomyName = '';

    public function name($value = null)
    {
        if (empty($value))
            return;

        $this->builder->where('name', 'like', '%' . $value . '%');
    }

    /* корректно сработает, если таксономия === 'brand'. Выберет только те бренды, к которым принадлежит товаров >= $productsMin, причем, эти товары должны иметь статус "Активен" */
    public function brand_with_products($productsMin)
    {
        $activeStatusId = ProductStatus::where('name', 'Активен')->first()->id;

        $productsMin = (int) $productsMin;
        if ($this->taxonomyName !== 'brand' || is_nan($productsMin))
            return;

        $this->builder->whereIn('brands.id', function (Builder $query) use ($productsMin, $activeStatusId) {
            $query->select('brand')->from(function (Builder $subquery) use ($productsMin, $activeStatusId) {
                $subquery->selectRaw('brands.id as brand, count(products.id) as count')
                    ->from('brands')
                    ->leftJoin('products', function ($join) use ($activeStatusId) {
                        $join->on('products.brand_id', '=', 'brands.id')
                            ->where('products.product_status_id', $activeStatusId);
                    })
                    ->groupBy('brands.id')
                    ->having('count', '>=', $productsMin);
            });
        });
    }
}
