<?php

namespace App\Support;

class ApiResponse
{
    public static function ok($data = null, string $message = 'ok', int $code = 200)
    { return response()->json(['status'=>'success','message'=>$message,'data'=>$data], $code); }

    public static function error(string $message = 'error', int $code = 400, $errors = null)
    { return response()->json(['status'=>'error','message'=>$message,'errors'=>$errors], $code); }
}
