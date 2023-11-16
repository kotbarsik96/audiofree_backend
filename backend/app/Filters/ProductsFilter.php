<?php

namespace App\Filters;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class ProductsFilter extends QueryFilter
{
    public function name($value = null)
    {
        if (empty($value))
            return;

        $this->builder->where('products.name', 'like', '%' . $value . '%');
    }

    public function nameOrBrand($value = null)
    {
        if (empty($value))
            return;

        $this->builder->where('products.brand', 'like', '%' . $value . '%')
            ->orWhere('products.name', 'like', '%' . $value . '%');
    }

    public function current_price($value = null)
    {
        if (empty($value))
            return;

        $this->builder->whereRaw('IF(discount_price IS NULL, price = ?, discount_price = ?)', [$value, $value]);
    }

    public function price_range($values)
    {
        if (!is_array($values))
            return;

        $min = array_key_exists('min', $values) ? (int) $values['min'] ?? 0 : 0;
        $max = array_key_exists('max', $values) ? (int) $values['max'] ?? 0 : 0;

        if ($max === 0)
            return;

        $this->builder->whereRaw('IF(discount_price, discount_price >= ? AND discount_price <= ?, price >= ? AND price <= ?)', [$min, $max, $min, $max]);
    }

    public function has_discount($value = null)
    {
        if (empty($value) || $value === 'no_matter')
            return;

        if ($value === 'yes' || $value === 'true')
            $this->builder->whereNotNull('discount_price')
                ->where('discount_price', '>', '0');
        elseif ($value === 'no' || $value === 'false')
            $this->builder->whereNull('discount_price')
                ->orWhere('discount_price', '<=', '0');
    }

    public function filterByTaxonomy($titles = null, $whereIn)
    {
        $array = is_array($titles)
            ? $titles
            : array_filter([$titles]);
        if (count($array) < 1)
            return;

        $this->builder->whereIn($whereIn, $array);
    }

    public function brands($titles = null)
    {
        $this->filterByTaxonomy($titles, 'products.brand');
    }

    public function categories($titles = null)
    {
        $this->filterByTaxonomy($titles, 'products.category');
    }

    public function types($titles = null)
    {
        $this->filterByTaxonomy($titles, 'products.type');
    }

    public function product_statuses($titles = null)
    {
        $this->filterByTaxonomy($titles, 'products.product_status');
    }

    public function product_status_active($value = null)
    {
        if (empty($value) || $value === 'false')
            return;

        $this->product_statuses(['Активен']);
    }

    public function in_stock($value = null)
    {
        if (empty($value) || $value === 'false') {
            $this->builder->where('products.quantity', '<', '1');
        } else {
            $this->builder->where('products.quantity', '>', '0');
        }
    }

    public function is_favorite($value = null)
    {
        $userId = $this->request->cookie('user');
        if (empty($userId))
            $userId = $this->request->get('userId');

        if (empty($userId))
            return;

        $subQueryCallback = function (Builder $subquery) use ($userId) {
            $subquery->select('favorites_product.product_id')
                ->from('favorites')
                ->leftJoin('favorites_product', 'favorites_product.favorites_id', '=', 'favorites.id')
                ->where('favorites.user_id', $userId);
        };

        if (empty($value) || $value === 'false') {
            $this->builder->whereNotIn('products.id', $subQueryCallback);
        } else {
            $this->builder->whereIn('products.id', $subQueryCallback);
        }
    }

    public function bestsellers_first($value = null)
    {
        if (empty($value) || $value === 'false')
            return;

        $this->builder->statistics()->orderBy('product_statistics.sold', 'desc');
    }
}
