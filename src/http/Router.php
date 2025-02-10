<?php
namespace src\http;


class Router
{
    protected static array $routes = [];

    public static function get($route, $controller, $action): void
    {
        self::$routes['get'][$route] = [$controller, $action];
    }
    public static function post($route, $controller, $action): void
    {
        self::$routes['post'][$route] = [$controller, $action];
    }

    public static function put($route, $controller, $action): void
    {
        self::$routes['put'][$route] = [$controller, $action];
    }

    public static function patch($route, $controller, $action): void
    {
        self::$routes['patch'][$route] = [$controller, $action];
    }


    public static function resolve($requestUri, $requestMethod): void
    {
        $path = parse_url($requestUri, PHP_URL_PATH);
        $method = strtolower($requestMethod);

        if (isset(self::$routes[$method][$path])) {
            self::callAction(self::$routes[$method][$path]);
        }
        foreach (self::$routes[$method] as $route => $handler) {
            $pattern = self::convertToRegex($route);
            if (preg_match($pattern, $path, $matches)) {
                $params = array_slice($matches, 1);
                self::callAction($handler, $params);
                return;
            }
        }
        self::get404();
    }

    private static function callAction(array $handler, array $params = []): void
    {
        [$controller, $action] = $handler;
        $controllerPath = "app/controllers/{$controller}.php";

        if (file_exists($controllerPath)) {
            require_once $controllerPath;
            $controller = "App\\controllers\\{$controller}";

            if (class_exists($controller)) {
                $controllerInstance = new $controller();
                if (method_exists($controllerInstance, $action)) {
                    $controllerInstance->$action(...$params);
                    return;
                }
            }
        }
        self::get404();
    }


    private static function convertToRegex(string $route): string
    {
        return '#^' . preg_replace('/\{(\w+)\}/', '(\d+)', $route) . '$#';
    }


    public static function get404(): void
    {
        echo "404";
    }
}
