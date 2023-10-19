<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Filters\QueryFilter;

class FilterableModel extends Model
{
    public function scopeFilter(Builder $builder, QueryFilter $request)
    {
        return $request->apply($builder);
    }

    public function scopeOffsetLimit(Builder $query, $limit, $offset)
    {
        if (!$limit && !$offset)
            return $query;

        if (!$limit && $offset)
            return $query->offset($offset);

        if ($limit && !$offset)
            return $query->limit($limit);

        return $query->offset($offset)->limit($limit);
    }
}