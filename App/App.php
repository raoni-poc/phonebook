<?php

namespace App;

class App
{
    protected $services;

    public static function run()
    {
        $config = require 'config.php';;
        return new \App\Route($config['routes']);
    }

    public static function getConnection()
    {
        $config = require 'config.php';
        return Connection::connect(
            $config['database']['host'],
            $config['database']['dbname'],
            $config['database']['user'],
            $config['database']['password']
        );
    }
}