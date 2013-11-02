<?php

//AdminController.php

class IndexController extends Controller
{
    public function index()
    {
        $this->set('title','Welcome to the Home Page:');
    }

    //if this is unccommented, the parent destructor must be called explicitly.
    //function __destruct()
    //{
        //parent::__destruct()
    //}
}

