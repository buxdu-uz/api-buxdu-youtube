<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * @param $message
     * @param array $result
     * @return JsonResponse
     */
    public function successResponse($message, $result = [])
    {
        $response = [
            'status' => true,
            'message' => $message,
            'data'    => $result
        ];
        return response()->json($response);
    }

    /**
     * Error response method.
     *
     * @param $message
     * @param array $result
     * @return JsonResponse
     */
    public function errorResponse($message, $result = []): JsonResponse
    {
        $response = [
            'status' => false,
            'error'=>true,
            'message' => $message
        ];

        if (!empty($result)) {
            $response['data'] = $result;
        }

        return response()->json($response);
    }
}
