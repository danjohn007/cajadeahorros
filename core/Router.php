<?php
/**
 * Sistema de enrutamiento con URLs amigables
 * Sistema de Gestión Integral de Caja de Ahorros
 */

class Router {
    private $routes = [];
    private $params = [];

    public function add($route, $params = []) {
        // Convertir la ruta a expresión regular
        $route = preg_replace('/\//', '\\/', $route);
        $route = preg_replace('/\{([a-z]+)\}/', '(?P<\1>[a-z0-9-]+)', $route);
        $route = preg_replace('/\{([a-z]+):([^\}]+)\}/', '(?P<\1>\2)', $route);
        $route = '/^' . $route . '$/i';
        $this->routes[$route] = $params;
    }

    public function match($url) {
        foreach ($this->routes as $route => $params) {
            if (preg_match($route, $url, $matches)) {
                foreach ($matches as $key => $match) {
                    if (is_string($key)) {
                        $params[$key] = $match;
                    }
                }
                $this->params = $params;
                return true;
            }
        }
        return false;
    }

    public function dispatch($url) {
        $url = $this->removeQueryString($url);
        
        if ($this->match($url)) {
            $controller = $this->params['controller'];
            $controller = $this->convertToStudlyCaps($controller) . 'Controller';
            
            $controllerFile = APP_PATH . '/controllers/' . $controller . '.php';
            
            if (file_exists($controllerFile)) {
                require_once $controllerFile;
                $controllerObject = new $controller($this->params);
                
                $action = $this->params['action'] ?? 'index';
                $action = $this->convertToCamelCase($action);
                
                if (is_callable([$controllerObject, $action])) {
                    $controllerObject->$action();
                } else {
                    $this->error404("Método {$action} no encontrado en {$controller}");
                }
            } else {
                $this->error404("Controlador {$controller} no encontrado");
            }
        } else {
            $this->error404("Ruta no encontrada");
        }
    }

    protected function convertToStudlyCaps($string) {
        return str_replace(' ', '', ucwords(str_replace('-', ' ', $string)));
    }

    protected function convertToCamelCase($string) {
        return lcfirst($this->convertToStudlyCaps($string));
    }

    protected function removeQueryString($url) {
        if ($url != '') {
            $parts = explode('&', $url, 2);
            if (strpos($parts[0], '=') === false) {
                $url = $parts[0];
            } else {
                $url = '';
            }
        }
        return rtrim($url, '/');
    }

    protected function error404($message = "Página no encontrada") {
        http_response_code(404);
        require_once APP_PATH . '/views/errors/404.php';
        exit;
    }

    public function getParams() {
        return $this->params;
    }
}
