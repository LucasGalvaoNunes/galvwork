<?php


namespace Lucasgnunes\Galvwork\Controller;


use Monolog\Handler\StreamHandler;
use Monolog\Logger;

/**
 * Class AbstractController
 * @package Kernel\Controller
 * @author Lucas Galvão Nunes <contato@lucasgnunes.dev>
 */
abstract class AbstractController
{
    /**
     * @param int $code
     * @param array|null $data
     * @param string|null $message
     * @param null $error
     * @return string
     */
    function jsonReponse(int $code = 200, array $data = null, string $message = null, $error = null) : string
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
            200 => '200 OK',
            400 => '400 Bad Request',
            404 => '404 Not Found',
            422 => 'Unprocessable Entity',
            500 => '500 Internal Server Error'
        );
        // ok, validation error, or failure
        header('Status: ' . $status[$code]);

        $response['status'] = $code < 300;
        if (count($data) > 0) {
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

    /**
     * @param $className
     * @return Logger
     */
    function log($className)
    {
        $log = new Logger($className);
        $log->pushHandler(new StreamHandler(ROOT.'logs/'.$className.'.log', Logger::WARNING));

        return $log;
    }
}