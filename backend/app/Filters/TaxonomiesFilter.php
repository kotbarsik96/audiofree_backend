<?php

namespace App\Filters;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use App\Models\Taxonomies\Taxonomy;

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
        $productsMin = (int) $productsMin;
        if ($this->taxonomyName !== 'brand' || is_nan($productsMin))
            return;

        $this->builder->whereIn('taxonomies.id', function (Builder $query) use ($productsMin) {
            $query->select('brand')->from(function (Builder $subquery) use ($productsMin) {
                $subquery->selectRaw('taxonomies.id as brand, count(products.id) as count')
                    ->from('taxonomies')
                    ->leftJoin('products', function ($join) {
                        $join->on('products.brand', '=', 'taxonomies.name')
                            ->where('products.product_status', 'Активен');
                    })
                    ->groupBy('taxonomies.id')
                    ->having('count', '>=', $productsMin);
            });
        });
    }
}
