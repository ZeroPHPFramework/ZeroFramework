<?php

namespace App\Controllers;

use Zero\Lib\View;

class HomeController
{
    public function index()
    {
        return View::render('pages/home');
    }
}