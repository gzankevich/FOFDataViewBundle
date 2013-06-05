<?php

namespace FOF\DataViewBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DemoController extends Controller
{
    public function listAction()
    {
        return new \Symfony\Component\HttpFoundation\Response('list');
    }
}
