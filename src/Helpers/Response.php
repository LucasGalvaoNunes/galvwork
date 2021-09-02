<?php

namespace Lucasgnunes\Galvwork\Helpers;

/**
 * Filename: Response.php
 * User: lucas
 * Date: 02/09/2021
 * Time: 02:07
 */
class Response
{
    public static function json(int $code = 200, array $data = null, string $message = null, $error = null) : string
    {
        // clear the old headers
        header_remove();
        // set the actual code
        http_response_code($code);
        // set the header to make sure cache is forced
        header("Cache-Control: no-transform,public,max-age=300,s-maxage=900");
        // treat this as json
        header('Content-Type: application/json');
        $status = array(
            100 => 'HTTP_CONTINUE',
            101 => 'HTTP_SWITCHING_PROTOCOLS',
            102 => 'HTTP_PROCESSING',
            103 => 'HTTP_EARLY_HINTS',
            200 => 'HTTP_OK',
            201 => 'HTTP_CREATED',
            202 => 'HTTP_ACCEPTED',
            203 => 'HTTP_NON_AUTHORITATIVE_INFORMATION',
            204 => 'HTTP_NO_CONTENT',
            205 => 'HTTP_RESET_CONTENT',
            206 => 'HTTP_PARTIAL_CONTENT',
            207 => 'HTTP_MULTI_STATUS',
            208 => 'HTTP_ALREADY_REPORTED',
            226 => 'HTTP_IM_USED',
            300 => 'HTTP_MULTIPLE_CHOICES',
            301 => 'HTTP_MOVED_PERMANENTLY',
            302 => 'HTTP_FOUND',
            303 => 'HTTP_SEE_OTHER',
            304 => 'HTTP_NOT_MODIFIED',
            305 => 'HTTP_USE_PROXY',
            306 => 'HTTP_RESERVED',
            307 => 'HTTP_TEMPORARY_REDIRECT',
            308 => 'HTTP_PERMANENTLY_REDIRECT',
            400 => 'HTTP_BAD_REQUEST',
            401 => 'HTTP_UNAUTHORIZED',
            402 => 'HTTP_PAYMENT_REQUIRED',
            403 => 'HTTP_FORBIDDEN',
            404 => 'HTTP_NOT_FOUND',
            405 => 'HTTP_METHOD_NOT_ALLOWED',
            406 => 'HTTP_NOT_ACCEPTABLE',
            407 => 'HTTP_PROXY_AUTHENTICATION_REQUIRED',
            408 => 'HTTP_REQUEST_TIMEOUT',
            409 => 'HTTP_CONFLICT',
            410 => 'HTTP_GONE',
            411 => 'HTTP_LENGTH_REQUIRED',
            412 => 'HTTP_PRECONDITION_FAILED',
            413 => 'HTTP_REQUEST_ENTITY_TOO_LARGE',
            414 => 'HTTP_REQUEST_URI_TOO_LONG',
            415 => 'HTTP_UNSUPPORTED_MEDIA_TYPE',
            416 => 'HTTP_REQUESTED_RANGE_NOT_SATISFIABLE',
            417 => 'HTTP_EXPECTATION_FAILED',
            418 => 'HTTP_I_AM_A_TEAPOT',
            421 => 'HTTP_MISDIRECTED_REQUEST',
            422 => 'HTTP_UNPROCESSABLE_ENTITY',
            423 => 'HTTP_LOCKED',
            424 => 'HTTP_FAILED_DEPENDENCY',
            425 => 'HTTP_TOO_EARLY',
            426 => 'HTTP_UPGRADE_REQUIRED',
            428 => 'HTTP_PRECONDITION_REQUIRED',
            429 => 'HTTP_TOO_MANY_REQUESTS',
            431 => 'HTTP_REQUEST_HEADER_FIELDS_TOO_LARGE',
            451 => 'HTTP_UNAVAILABLE_FOR_LEGAL_REASONS',
            500 => 'HTTP_INTERNAL_SERVER_ERROR',
            501 => 'HTTP_NOT_IMPLEMENTED',
            502 => 'HTTP_BAD_GATEWAY',
            503 => 'HTTP_SERVICE_UNAVAILABLE',
            504 => 'HTTP_GATEWAY_TIMEOUT',
            505 => 'HTTP_VERSION_NOT_SUPPORTED',
            506 => 'HTTP_VARIANT_ALSO_NEGOTIATES_EXPERIMENTAL',
            507 => 'HTTP_INSUFFICIENT_STORAGE',
            508 => 'HTTP_LOOP_DETECTED',
            510 => 'HTTP_NOT_EXTENDED',
            511 => 'HTTP_NETWORK_AUTHENTICATION_REQUIRED',
        );
        // ok, validation error, or failure
        header('Status: ' . $status[$code]);

        $response['status'] = $code < 300;
        if (!empty($data) > 0) {
            $response['data'] = $data;
        }
        if ($message) {
            $response['message'] = $message;
        }
        if ($error) {
            $response['error'] = $error;
        }

        // return the encoded json
        return json_encode($response,JSON_UNESCAPED_UNICODE);
    }
}