<?php

if (! function_exists('result')) {

    function result($data = null, int $status_code = 200, $message = null)
    {
        switch ($status_code) {
            case 200:
                $result['status_code'] = $status_code;
                $result['message'] = ($message) ? $message : trans('messages.http_errors.200');
                $result['data'] = $data;
                break;
            case 201:
                $result['status_code'] = $status_code;
                $result['message'] = ($message) ? $message : trans('messages.http_errors.201');
                $result['data'] = $data;
                break;
            case 202:
                $result['status_code'] = $status_code;
                $result['message'] = ($message) ? $message : trans('messages.http_errors.202');
                $result['data'] = $data;
                break;
            case 400:
                $result['status_code'] = 400;
                $result['message'] = ($message) ? $message : trans('messages.http_errors.400');
                $result['data'] = $data;
                break;
            case 401:
                $result['status_code'] = 401;
                $result['message'] = ($message) ? $message : trans('messages.http_errors.401');
                $result['data'] = $data;
                break;
            case 403:
                $result['status_code'] = 403;
                $result['message'] = ($message) ? $message : trans('messages.http_errors.403');
                $result['data'] = $data;
                break;
            case 404:
                $result['status_code'] = $status_code;
                $result['message'] = ($message) ? $message : trans('messages.http_errors.404');
                $result['data'] = $data;
                break;
            case 500:
                $result['status_code'] = $status_code;
                $result['message'] = ($message) ? $message : trans('messages.http_errors.500');
                $result['data'] = $data;
                break;
            default:
                $result['status_code'] = $status_code;
                $result['message'] = $message;
                $result['data'] = $data;
                break;
        }

        return response()->json($result, $result['status_code'], []);
    }
}
