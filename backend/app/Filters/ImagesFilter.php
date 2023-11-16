<?php

namespace App\Filters;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class ImagesFilter extends QueryFilter
{
    public function original_name($value = null)
    {
        if (empty($value) || $value === 'false')
            return;

        $this->builder->where('images.original_name', 'like', '%'  . $value . '%');
    }

    public function user_email($value = null)
    {
        if (empty($value) || $value === 'false')
            return;

        if ($value === 'self') {
            $user = User::find($this->request->cookie('user'));
            if (!empty($user))
                $value = $user->email;
        }

        $this->builder->where('images.user_id', function ($query) use ($value) {
            $query->select('users.id')
                ->from('users')
                ->where('users.email', $value);
        });
    }

    public function idsList($values = null)
    {
        if (!is_array($values))
            return;

        return $this->builder->whereIn('images.id', $values);
    }
}
