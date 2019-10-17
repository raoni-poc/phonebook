<?php

namespace App\Controller;

abstract class Controller
{
    protected $views;
    protected $entity;
    protected $repository;
    private $action;

    public function __construct()
    {
        $this->views = new \stdClass;
    }

    protected function view($action, $layout = true, $vars = [])
    {
        $this->action = $action;
        if ($layout == true && file_exists("App/Views/layout.phtml")) {
            include_once "App/Views/layout.phtml";
        } else {
            $this->content($vars);
        }
    }

    protected function content()
    {
        $current = get_class($this);
        $current = explode('\\', $current);
        $current = end($current);
        $singleClassName = strtolower(str_replace("Controller", "", str_replace("App\\Controllers\\", "", $current)));
        $path = $singleClassName . "/" . $this->action;
        $this->render($path);
    }

    protected function render($path = '')
    {
        include_once "App/Views/" . $path . ".phtml";
    }

    protected function getViewContent($path, $vars)
    {
        $content = file_get_contents("App/Views/" . $path . ".phtml");
        foreach ($vars as $key => $var) {
            $content = str_replace('$'.$key, $var, $content);
        }
        return $content;
    }
}
