<?php


namespace Lucasgnunes\Galvwork\Router;


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
        $namespaceClass = "\\Controller\\".$class;
        if(method_exists($namespaceClass, $method)){
            $item = new ReflectionClass($namespaceClass);
            $ins = $item->newInstance();
            return $ins->$method();
        }else{
            header_remove();
            http_response_code(500);
            header("Cache-Control: no-transform,public,max-age=300,s-maxage=900");
            header('Content-Type: application/json');
            header('Status: 500');
            return json_encode(array(
                'status' => false,
                'message' => 'Method Not Exist'
            ));
        }
    }
}