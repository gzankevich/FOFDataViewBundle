<?php

namespace FOF\DataViewBundle\Test;

use FOF\DataViewBundle\Lib\DataViewRequestHandler;

/**
 * Unit test for DataViewRequestHandler
 *
 * @author George Zankevich <gzankevich@gmail.com>
 */
class DataViewRequestHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers DataViewRequestHandler::bind
     */
    public function test_initialPageLoad()
    {
        $formFactory = $this->getMockBuilder('\Symfony\Component\Form\FormFactory')->disableOriginalConstructor()->getMock();

        $session = $this->getMockBuilder('\Symfony\Component\HttpFoundation\Session\Session')->disableOriginalConstructor()->getMock();
        $session
            ->expects($this->once())
            ->method('clear')
            ->with($this->equalTo('data_view'));

        $dataView = $this->getMockBuilder('\DataView\DataView')->disableOriginalConstructor()->getMock();
        $dataView
            ->expects($this->once())
            ->method('getColumns')
            ->will($this->returnValue(array()));

        $request = $this->getMockBuilder('\Symfony\Component\HttpFoundation\Request')->disableOriginalConstructor()->getMock();
        $request
            ->expects($this->exactly(2))
            ->method('getMethod')
            ->will($this->returnValue('GET'));

        $dataViewRequestHandler = new DataViewRequestHandler($formFactory, $session);
        $dataViewRequestHandler->bind($dataView, $request);
    }
}
