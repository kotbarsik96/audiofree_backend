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

        $this->builder->where('name', 'like', '%' . $value . '%');
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

        if ($value === 'yes')
            $this->builder->whereNotNull('discount_price');
        elseif ($value === 'no')
            $this->builder->whereNull('discount_price');
    }

    public function filterByTaxonomy($titles = null, $whereIn, $select, $from, $subWhereIn)
    {
        $array = is_array($titles)
            ? $titles
            : array_filter([$titles]);
        if (count($array) < 1)
            return;

        $this->builder->whereIn(
            $whereIn,
            function (Builder $subquery) use ($array, $select, $from, $subWhereIn) {
                $subquery->select($select)
                    ->from($from)
                    ->whereIn($subWhereIn, $array);
            }
        );
    }

    public function brands($titles = null)
    {
        $this->filterByTaxonomy($titles, 'brand_id', 'brands.id', 'brands', 'brands.name');
    }

    public function categories($titles = null)
    {
        $this->filterByTaxonomy($titles, 'category_id', 'categories.id', 'categories', 'categories.name');
    }

    public function types($titles = null)
    {
        $this->filterByTaxonomy($titles, 'type_id', 'types.id', 'types', 'types.name');
    }

    public function product_statuses($titles = null)
    {
        $this->filterByTaxonomy($titles, 'product_status_id', 'product_statuses.id', 'product_statuses', 'product_statuses.name');
    }
}
