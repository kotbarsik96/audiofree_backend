<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Order;
use App\Models\User;
use App\Exceptions\AuthExceptions;

class OrdersController extends Controller
{
    public function storeWaitingForUserdata(Request $request)
    {
        $user = User::authenticate($request);
        if(empty($user))
            return response(['error' => AuthExceptions::userNotLoggedIn()->getMessage()], 401);

        $validator = Validator::make($request->all(), [
            
        ], [

        ]);
    }
}
