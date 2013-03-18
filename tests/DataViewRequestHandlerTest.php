<?php

namespace FOF\DataViewBundle\Test;

use FOF\DataViewBundle\Lib\DataViewRequestHandler;

class DataViewRequestHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function testFoo()
    {
        $this->assertTrue(true);
    }

    public function test_initialPageLoad()
    {
        $formFactory = $this->getMockBuilder('\Symfony\Component\Form\FormFactory')->disableOriginalConstructor()->getMock();

        $session = $this->getMockBuilder('\Symfony\Component\HttpFoundation\Session\Session')->disableOriginalConstructor()->getMock();
        $session
            ->expects($this->once())
            ->method('clear')
            ->with($this->equalTo('data_view'));

        $dataView = $this->getMockBuilder('\DataView\DataView')->disableOriginalConstructor()->getMock();

        $request = $this->getMockBuilder('\Symfony\Component\HttpFoundation\Request')->disableOriginalConstructor()->getMock();
        $request
            ->expects($this->once())
            ->method('getMethod')
            ->will($this->returnValue('GET'));

        $dataViewRequestHandler = new DataViewRequestHandler($formFactory, $session);
        $dataViewRequestHandler->bind($dataView, $request);
    }
}
