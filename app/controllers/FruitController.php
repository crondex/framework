<?php

use Crondex\Routing\Controller;

class FruitController extends Controller
{
    public function apple()
    {
        echo "Apple is the fruit - this is being echoed from $fruit::apple()";
    }

    public function viewall()
    {
        $this->set('title','All Fruit');
        $this->set('fruit',$this->FruitModel->selectAll()); //this is calling a model method
    }

    //if this is unccommented, the parent destructor must be called explicitly.
    //function __destruct()
    //{
        //parent::__destruct()
    //}
}

