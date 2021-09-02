<?php


namespace Lucasgnunes\Galvwork\Router;


use Lucasgnunes\Galvwork\Enum\HttpStatusCodeEnum;
use Lucasgnunes\Galvwork\Exceptions\HttpException;
use Lucasgnunes\Galvwork\Helpers\Logger;
use Lucasgnunes\Galvwork\Helpers\Response;
use ReflectionClass;

/**
 * Class LGRouterResponse
 * @package Kernel\Router
 * @author Lucas Galvão Nunes <contato@lucasgnunes.dev>
 */
class LGRouterResponse
{

    /**
     * @param string $view
     * @return false|string
     */
    public static function view(string $view)
    {
        $view = VIEWS . "{$view}.php";
        if (file_exists($view)) {
            ob_start();
            require $view;;
            $html = ob_get_contents();
            ob_end_clean();
        } else {
            ob_start();
            require NOT_FOUND;
            $html = ob_get_contents();
            ob_end_clean();
        }
        return $html;
    }

    /**
     * @param string $class
     * @param string $method
     * @return false|string
     * @throws \ReflectionException
     */
    public static function json(string $class, string $method)
    {
        $class = ucfirst(strtolower($class)) . "Controller";
        $controllerPath = "\\Controller\\";
        if (defined('CONTROLLER_PATH')) {
            $controllerPath = CONTROLLER_PATH;
        }
        $namespaceClass = $controllerPath.$class;
        if(method_exists($namespaceClass, $method)){
            $item = new ReflectionClass($namespaceClass);
            $ins = $item->newInstance();

            try {
                return $ins->$method();
            } catch (HttpException $exception) {
                Logger::log('unattended-error')->warning($exception->getMessage());
                return Response::json($exception->getHttpCode(), null, null, $exception->getMessage());
            }
        }else{
            header_remove();
            http_response_code(HttpStatusCodeEnum::HTTP_METHOD_NOT_ALLOWED);
            header("Cache-Control: no-transform,public,max-age=300,s-maxage=900");
            header('Content-Type: application/json');
            header('Status: '.HttpStatusCodeEnum::HTTP_METHOD_NOT_ALLOWED);
            return json_encode(array(
                'status' => false,
                'message' => 'Method Not Allowed'
            ));
        }
    }
}