<?php

namespace App;

class Route
{
    protected $route;

    public function __construct($routes)
    {
        $this->initRoutes($routes);
        $this->run($this->getUrl());
    }

    protected function initRoutes($routes)
    {
        $this->setRoute($routes);
    }

    protected function run($url)
    {
        array_walk($this->route, function ($route) use ($url) {
            if ($url == $route['route']) {
                $class = "App\\Controller\\" . ucfirst($route['controller']);
                $controller = new $class();
                $action = $route['action'];
                $controller->$action();
            }
        });
    }

    protected function setRoute(array $routes)
    {
        $this->route = $routes;
    }

    protected function getUrl()
    {
        return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    }
}