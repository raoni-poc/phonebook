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

    protected function view($action, $layout = true)
    {
        $this->action = $action;
        if ($layout == true && file_exists("App/Views/layout.phtml")) {
            include_once "App/Views/layout.phtml";
        } else {
            $this->content();
        }
    }

    protected function content()
    {
        $current = get_class($this);
        $current = explode('\\', $current);
        $current = end($current);
        $singleClassName = strtolower(str_replace("Controller", "", str_replace("App\\Controllers\\", "", $current)));

        include_once "App/Views/" . $singleClassName . "/" . $this->action . ".phtml";
    }
}