<?php

namespace App\Models\Products;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class ProductStatistics extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'sold',
        'in_favorites',
        'income'
    ];
    protected $table = 'product_statistics';

    /* данный метод принимает массив fields с ключами, обозначающими колонки с исчисляемыми значениями 'sold', 'income' и другими. Для каждой колонки будут получены текущие значения и прибавлены переданные в $fields
        Подсказка: в случае необходимости отнять, а не прибавить, нужно передать значение, умноженное на -1
    */
    public static function updateColumns(array $fields, $productId)
    {
        $row = self::where('product_id', $productId)->first();
        if (empty($row))
            $row = self::create(['product_id' => $productId]);

        $columnNames = ['sold', 'income', 'in_favorites'];
        foreach ($columnNames as $columnName) {
            if (array_key_exists($columnName, $fields)) {
                $currentValue = $row->$columnName ?? 0;
                $fields[$columnName] = $currentValue + $fields[$columnName];
                if ($fields[$columnName] < 0)
                    $fields[$columnName] = 0;
            }
        }

        $row->update($fields);
        return $row;
    }

    public static function createOrUpdate($productId, array $fields)
    {
        $row = self::where('product_id', $productId);
        if (empty($row))
            $row = self::create($fields);
        else
            $row->update($fields);

        return $row;
    }

    public static function userCanSeeStatistics($request, $productId)
    {
        $user = User::authenticate($request);
        if (empty($user))
            return false;

        $rolePriority = User::getRolePriority($user->id);
        if ($rolePriority >= 3)
            return false;

        return true;
    }
}
