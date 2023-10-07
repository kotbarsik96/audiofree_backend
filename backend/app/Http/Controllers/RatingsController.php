<?php

namespace App\Http\Controllers;

use App\Exceptions\AuthExceptions;
use App\Exceptions\ProductsExceptions;
use Illuminate\Http\Request;
use App\Models\Rating;
use App\Models\Product;
use App\Exceptions\RolesExceptions;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\RatingsExceptions;

class RatingsController extends Controller
{
    public function store(Request $request, $productId, $ratingValue)
    {
        $rightCheck = AuthController::checkUserRight($request, 'add_rating');
        if (!$rightCheck['has_right'])
            return RolesExceptions::noRightsResponse();

        $userId = $request->cookie('user');

        $validatableFields = ['value' => $ratingValue, 'product_id' => $productId];
        $validator = Validator::make($validatableFields, [
            'value' => 'numeric|required',
            'product_id' => 'exists:products,id|required'
        ], [
            'required' => 'Не указано: :attribute',
            'value.numeric' => 'Значение рейтинга должно быть числом',
            'product_id.exists' => 'Товар не существует'
        ]);

        if ($validator->fails())
            return response(['errors' => $validator->errors()], 400);

        $fields = $validator->validated();

        $hasRatingByUser = Rating::where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();
        // если пользователь уже ставил оценку, ее нужно обновить, а не добавить
        if ($hasRatingByUser)
            return $this->update($userId, $productId, $fields['value'], $hasRatingByUser);

        return Rating::create([
            'value' => $fields['value'],
            'product_id' => $productId,
            'user_id' => $userId
        ]);
    }

    public function update($userId, $productId, $ratingValue, $ratingModel)
    {
        $ratingModel->update([
            'value' => $ratingValue,
            'product_id' => $productId,
            'user_id' => $userId
        ]);
        return $ratingModel;
    }

    public function delete(Request $request, $productId)
    {
        $userId = $request->cookie('user');
        if (empty($userId))
            return ['error' => AuthExceptions::userNotLoggedIn()];

        $product = Product::find($productId);
        $productName = $product ? $product->name : '(название не найдено)';

        $rating = Rating::where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if (empty($rating))
            return response(['error' => RatingsExceptions::noRating($productName)->getMessage()], 400);

        $rating->delete();
        return response(['success' => true, 'message' => 'Оценка товара ' . $productName . ' удалена']);
    }
}