<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @param $errors ,$message ,$code
     * @return json response with this feature
     */
    public function responseError($errors, $message, $code)
    {
        return response()->json(['message' => $message, 'errors' => $errors])->setStatusCode($code);
    }

    /**
     * @param  $errors ,$message ,$code
     * return json response with this feature
     * @return \Illuminate\Http\JsonResponse
     */
    public function userResponse($user, $token, $code)
    {
        return response()->json([
            'data' => $user,
            'meta' => [
                'token' => $token
            ],

        ])->setStatusCode($code);
    }

}
