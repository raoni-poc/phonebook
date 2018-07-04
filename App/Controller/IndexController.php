<?php

namespace App\Controller;

use App\Factory;

class IndexController extends Controller
{
    public function index()
    {
        header('Location: /phonebook');
    }
}