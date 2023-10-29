<?php

namespace App\Filters;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class UsersFilter extends QueryFilter
{
    public function email($value = null)
    {
        if (empty($value))
            return;

        $this->builder->where('users.email', 'like', '%' . $value . '%');
    }

    public function name($value = null)
    {
        if (empty($value))
            return;

        $this->builder->where('users.name', 'like', '%' . $value . '%');
    }

    public function surname($value = null)
    {
        if (empty($value))
            return;

        $this->builder->where('users.surname', 'like', '%' . $value . '%');
    }

    public function patronymic($value = null)
    {
        if (empty($value))
            return;

        $this->builder->where('users.patronymic', 'like', '%' . $value . '%');
    }

    public function role($value = null)
    {
        if(empty($value) || $value === 'false')
            return;

        $this->builder->where('role_id', $value);
    }
}
