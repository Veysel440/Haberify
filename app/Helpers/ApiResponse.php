<?php

namespace App\Helpers;

class ApiResponse
{
    /**
     * Başarılı response.
     */
    public static function success($data = null, string $message = 'Başarılı', int $status = 200)
    {
        $response = [
            'success' => true,
            'message' => $message,
        ];

        if (!is_null($data)) {
            $response['data'] = $data;
        }

        return response()->json($response, $status);
    }

    /**
     * Hatalı response.
     */
    public static function error(
        string $message = 'Bir hata oluştu.',
        int $status = 400,
               $errors = null,
               $data = null
    ) {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if (!is_null($errors)) {
            $response['errors'] = $errors;
        }

        if (!is_null($data)) {
            $response['data'] = $data;
        }

        return response()->json($response, $status);
    }
}
