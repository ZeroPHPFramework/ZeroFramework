<?php

namespace App\Controllers;

use View;
use DB;

class HomeController
{
    public function index()
    {
        // $data = DB::fetch('SELECT * FROM users');
        return View::render('pages/home');
    }
}