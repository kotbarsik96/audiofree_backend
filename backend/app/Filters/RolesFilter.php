<?php

namespace App\Filters;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class RolesFilter extends QueryFilter
{
    public function name($value = null)
    {
        if (empty($value))
            return;

        $this->builder->where('name', 'like', '%' . $value . '%');
    }

    public function priority($value = null)
    {
        if(empty($value))
            return;

        $this->builder->where('priority', $value);
    }
}
