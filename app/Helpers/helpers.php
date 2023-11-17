<?php

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

if (! function_exists('sendResponse')) {
    function sendResponse($result, $message, $code = Response::HTTP_OK): JsonResponse
    {
        $response = [
            'success'   => true,
            'message'   => $message,
            'data'      => $result,
        ];

        return response()->json($response, $code);
    }
}

if (! function_exists('sendError')) {
    function sendError($error, $code = Response::HTTP_UNAUTHORIZED, $errorMessages = []): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];

        if (!empty($errorMessages)) {
            $response['data'] = $errorMessages;
        }

        return response()->json($response, $code);
    }
}
