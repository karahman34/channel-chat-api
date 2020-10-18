<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;

class Transformer
{
    public static function meta(bool $ok, string $message = null, $data = null)
    {
        $arr = [
            'ok' => $ok,
            'message' => $message,
        ];

        if (!is_null($data)) {
            $arr['data'] = $data;
        }

        return $arr;
    }

    /**
     * Success response json skeleton.
     *
     * @param   string  $message
     * @param   mixed    $data
     * @param   int     $status
     * @param   array   $headers
     *
     * @return  JsonResponse
     */
    public static function ok(string $message = null, $data = null, int $status = 200, array $headers = [])
    {
        return response()->json(self::meta(true, $message, $data), $status, $headers);
    }

    /**
     * Failed response json skeleton.
     *
     * @param   string  $message
     * @param   mixed    $data
     * @param   int     $status
     * @param   array   $headers
     *
     * @return  JsonResponse
     */
    public static function fail(string $message = null, $data = null, int $status = 500, array $headers = [])
    {
        return response()->json(self::meta(false, $message, $data), $status, $headers);
    }
}
