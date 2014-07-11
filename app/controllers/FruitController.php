<?php

use Crondex\Routing\Controller;

class FruitController extends Controller
{
    public function apple()
    {
        echo "Apple is the fruit - this is being echoed from \$fruit::apple()";
    }

    public function viewall()
    {
        $this->set('title','All Fruit');
        $this->set('fruit',$this->model->selectAll()); //this is calling a model method
    }
}

