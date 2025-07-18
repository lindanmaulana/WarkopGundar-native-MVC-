<?php

namespace App\Core;

class Router
{

  private static array $routes = [];

  public static function add(string $method, string $path, string $controller, string $function)
  {
    // url mapping
    self::$routes[] = [
      'method' => $method,
      'path' => $path,
      'controller' => $controller,
      'function' => $function
    ];
  }

  public static function run()
  {
    $requestUrl = parse_url($_SERVER['PATH_INFO'] ?? $_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $requestMethod = $_SERVER['REQUEST_METHOD'];

    if ($requestUrl !== '/') {
      $requestUrl = rtrim($requestUrl, '/');
    }

    foreach (self::$routes as $route) {
      $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([^/]+)', $route['path']);
      $pattern = '#^' . $pattern . '$#';

      if (preg_match($pattern, $requestUrl, $matches)) {
        if ($requestMethod === $route['method']) {
          $controllerClass = $route['controller'];
          $functionName = $route['function'];

          $params = [];
          for ($i = 1; $i < count($matches); $i++) {
            $params[] = $matches[$i];
          }

          if (class_exists($controllerClass)) {
            $controllerInstance = new $controllerClass();
            if (method_exists($controllerInstance, $functionName)) {
              call_user_func_array([$controllerInstance, $functionName], [$params]);
              return;
            }
          }
        }
      }
    }

    http_response_code(404);
    echo "CONTROLLER NOT FOUND";
  }
}
