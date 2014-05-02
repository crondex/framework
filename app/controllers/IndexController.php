<?php

use Crondex\Routing\Controller;

class IndexController extends Controller
{
    public function index()
    {
        $this->set('title','Welcome to the Home Page:');
    }
}

