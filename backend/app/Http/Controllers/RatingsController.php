<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rating;
use App\Exceptions\RolesExceptions;

class RatingsController extends Controller
{
    public function store(Request $request, $productId, $ratingValue)
    {
        $rightCheck = AuthController::checkUserRight($request, 'add_rating');
        if (!$rightCheck['has_right'])
            return RolesExceptions::noRightsResponse();

        
    }
}
