<?php

namespace App\Models;

use Validator;

class Other
{
    public static function validate(array $data, array $rules, array $message = [])
    {
        $v = Validator::make($data, $rules, $message);
        if ($v->fails()) {
            return Other::response_json(400, $v->errors()->first());
        }

        return false;
    }

    public static function response_json($code, $message, $others = [])
    {
        return response()->json(array_merge([
            'code' => (int)$code,
            'message' => (string)$message
        ], $others), 200);
    }
}
