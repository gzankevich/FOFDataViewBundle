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
     * @covers DataViewRequestHandler::clearSessionSettings
     */
    public function testBind_initialPageLoad()
    {
        $formFactory = $this->getMockBuilder('\Symfony\Component\Form\FormFactory')->disableOriginalConstructor()->getMock();

        $session = $this->getMockBuilder('\Symfony\Component\HttpFoundation\Session\Session')->disableOriginalConstructor()->getMock();
        $session->expects($this->once())->method('clear')->with($this->equalTo('data_view'));

        $dataView = $this->getMockBuilder('\DataView\DataView')->disableOriginalConstructor()->getMock();

        $request = $this->getMockBuilder('\Symfony\Component\HttpFoundation\Request')->disableOriginalConstructor()->getMock();
        $request->expects($this->once())->method('getMethod')->will($this->returnValue('GET'));

        $dataViewRequestHandler = $this->getMock('\FOF\DataViewBundle\Lib\DataViewRequestHandler', 
            array('handleFilters'), array($formFactory, $session));
        $dataViewRequestHandler->expects($this->once())->method('handleFilters')->with($this->equalTo($dataView), $this->equalTo($request));

        $dataViewRequestHandler->bind($dataView, $request);
    }

    /**
     * @covers DataViewRequestHandler::bind
     * @covers DataViewRequestHandler::clearSessionSettings
     * @covers DataViewRequestHandler::saveSessionSettings
     */
    public function testBind_post()
    {
        $formFactory = $this->getMockBuilder('\Symfony\Component\Form\FormFactory')->disableOriginalConstructor()->getMock();

        $session = $this->getMockBuilder('\Symfony\Component\HttpFoundation\Session\Session')->disableOriginalConstructor()->getMock();
        $session->expects($this->once())->method('get')->with($this->equalTo('data_view'))->will($this->returnValue(array('page' => 2)));
        $session->expects($this->once())->method('set')->with($this->equalTo('data_view'), $this->equalTo(array('page' => 3)));

        $dataView = $this->getMockBuilder('\DataView\DataView')->disableOriginalConstructor()->getMock();
        $dataView->expects($this->once())->method('setCurrentPage')->with($this->equalTo(2));
        $dataView->expects($this->once())->method('getCurrentPage')->will($this->returnValue(3));

        $request = $this->getMockBuilder('\Symfony\Component\HttpFoundation\Request')->disableOriginalConstructor()->getMock();

        $dataViewRequestHandler = $this->getMock('\FOF\DataViewBundle\Lib\DataViewRequestHandler', 
            array('handleSort', 'handlePagination', 'handleFilters'), array($formFactory, $session));
        $dataViewRequestHandler->expects($this->once())->method('handleSort')->with($this->equalTo($dataView), $this->equalTo($request));
        $dataViewRequestHandler->expects($this->once())->method('handlePagination')->with($this->equalTo($dataView), $this->equalTo($request));
        $dataViewRequestHandler->expects($this->once())->method('handleFilters')->with($this->equalTo($dataView), $this->equalTo($request));

        $dataViewRequestHandler->bind($dataView, $request);
    }
}
