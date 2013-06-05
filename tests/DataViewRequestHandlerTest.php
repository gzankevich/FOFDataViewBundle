<?php

namespace FOF\DataViewBundle\Test;

use DataView\Test\BaseUnitTest;
use FOF\DataViewBundle\Lib\DataViewRequestHandler;

/**
 * Unit test for DataViewRequestHandler
 *
 * @author George Zankevich <gzankevich@gmail.com>
 */
class DataViewRequestHandlerTest extends BaseUnitTest 
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
     * @covers DataViewRequestHandler::loadSessionSettings
     * @covers DataViewRequestHandler::saveSessionSettings
     */
    public function testBind_post()
    {
        $formFactory = $this->getMockBuilder('\Symfony\Component\Form\FormFactory')->disableOriginalConstructor()->getMock();

        $session = $this->getMockBuilder('\Symfony\Component\HttpFoundation\Session\Session')->disableOriginalConstructor()->getMock();
        $session->expects($this->once())->method('get')->with($this->equalTo('data_view'))->will($this->returnValue(array('page' => 2)));
        $session->expects($this->once())->method('set')->with($this->equalTo('data_view'), $this->equalTo(array('page' => 3)));

        $pager = $this->getMockBuilder('Pagerfanta\Pagerfanta')->disableOriginalConstructor()->getMock();
        $pager->expects($this->once())->method('getCurrentPage')->will($this->returnValue(3));

        $dataView = $this->getMockBuilder('\DataView\DataView')->disableOriginalConstructor()->getMock();
        $dataView->expects($this->exactly(2))->method('getPager')->will($this->returnValue($pager));

        $request = $this->getMockBuilder('\Symfony\Component\HttpFoundation\Request')->disableOriginalConstructor()->getMock();
        $request->expects($this->once())->method('getMethod')->will($this->returnValue('POST'));

        $dataViewRequestHandler = $this->getMock('\FOF\DataViewBundle\Lib\DataViewRequestHandler', 
            array('handleSort', 'handlePagination', 'handleFilters'), array($formFactory, $session));
        $dataViewRequestHandler->expects($this->once())->method('handleSort')->with($this->equalTo($dataView), $this->equalTo($request));
        $dataViewRequestHandler->expects($this->once())->method('handlePagination')->with($this->equalTo($pager), $this->equalTo($request));
        $dataViewRequestHandler->expects($this->once())->method('handleFilters')->with($this->equalTo($dataView), $this->equalTo($request));

        $dataViewRequestHandler->bind($dataView, $request);
    }

    /**
     * @covers DataViewRequestHandler::handlePagination
     */
    public function testHandlePagination_firstPage()
    {
        $session = $this->getMockBuilder('\Symfony\Component\HttpFoundation\Session\Session')->disableOriginalConstructor()->getMock();
        $formFactory = $this->getMockBuilder('\Symfony\Component\Form\FormFactory')->disableOriginalConstructor()->getMock();

        $dataView = $this->getMockBuilder('\DataView\DataView')->disableOriginalConstructor()->getMock();

        $parameterBag = $this->getMock('\Symfony\Component\HttpFoundation\ParameterBag');
        $parameterBag->expects($this->once())->method('all')->will($this->returnValue(array('pagination_first_page' => '')));

        $request = $this->getMockBuilder('\Symfony\Component\HttpFoundation\Request')->disableOriginalConstructor()->getMock();
        $request->request = $parameterBag;

        $pager = $this->getMockBuilder('Pagerfanta\Pagerfanta')->disableOriginalConstructor()->getMock();
        $pager->expects($this->once())->method('setCurrentPage')->with($this->equalTo(1));

        $dataViewRequestHandler = new DataViewRequestHandler($formFactory, $session);
        $this->callNonPublicMethod($dataViewRequestHandler, 'handlePagination', array($pager, $request, 10));
    }

    /**
     * @covers DataViewRequestHandler::handlePagination
     */
    public function testHandlePagination_lastPage()
    {
        $session = $this->getMockBuilder('\Symfony\Component\HttpFoundation\Session\Session')->disableOriginalConstructor()->getMock();
        $formFactory = $this->getMockBuilder('\Symfony\Component\Form\FormFactory')->disableOriginalConstructor()->getMock();

        $pager = $this->getMockBuilder('Pagerfanta\Pagerfanta')->disableOriginalConstructor()->getMock();
        $pager->expects($this->once())->method('getNbPages')->will($this->returnValue(10));
        $pager->expects($this->once())->method('setCurrentPage')->with($this->equalTo(10));

        $dataView = $this->getMockBuilder('\DataView\DataView')->disableOriginalConstructor()->getMock();

        $parameterBag = $this->getMock('\Symfony\Component\HttpFoundation\ParameterBag');
        $parameterBag->expects($this->once())->method('all')->will($this->returnValue(array('pagination_last_page' => '')));

        $request = $this->getMockBuilder('\Symfony\Component\HttpFoundation\Request')->disableOriginalConstructor()->getMock();
        $request->request = $parameterBag;

        $dataViewRequestHandler = new DataViewRequestHandler($formFactory, $session);
        $this->callNonPublicMethod($dataViewRequestHandler, 'handlePagination', array($pager, $request, 5));
    }

    public function testHandlePagination_nextPage()
    {
        $session = $this->getMockBuilder('\Symfony\Component\HttpFoundation\Session\Session')->disableOriginalConstructor()->getMock();
        $formFactory = $this->getMockBuilder('\Symfony\Component\Form\FormFactory')->disableOriginalConstructor()->getMock();

        $pager = $this->getMockBuilder('Pagerfanta\Pagerfanta')->disableOriginalConstructor()->getMock();
        $pager->expects($this->once())->method('setCurrentPage')->with($this->equalTo(6));

        $parameterBag = $this->getMock('\Symfony\Component\HttpFoundation\ParameterBag');
        $parameterBag->expects($this->once())->method('all')->will($this->returnValue(array('pagination_next_page' => '')));

        $request = $this->getMockBuilder('\Symfony\Component\HttpFoundation\Request')->disableOriginalConstructor()->getMock();
        $request->request = $parameterBag;

        $dataViewRequestHandler = new DataViewRequestHandler($formFactory, $session);
        $this->callNonPublicMethod($dataViewRequestHandler, 'handlePagination', array($pager, $request, 5));
    }

    public function testHandlePagination_previousPage()
    {
        $session = $this->getMockBuilder('\Symfony\Component\HttpFoundation\Session\Session')->disableOriginalConstructor()->getMock();
        $formFactory = $this->getMockBuilder('\Symfony\Component\Form\FormFactory')->disableOriginalConstructor()->getMock();

        $pager = $this->getMockBuilder('Pagerfanta\Pagerfanta')->disableOriginalConstructor()->getMock();
        $pager->expects($this->once())->method('setCurrentPage')->with($this->equalTo(4));

        $parameterBag = $this->getMock('\Symfony\Component\HttpFoundation\ParameterBag');
        $parameterBag->expects($this->once())->method('all')->will($this->returnValue(array('pagination_previous_page' => '')));

        $request = $this->getMockBuilder('\Symfony\Component\HttpFoundation\Request')->disableOriginalConstructor()->getMock();
        $request->request = $parameterBag;

        $dataViewRequestHandler = new DataViewRequestHandler($formFactory, $session);
        $this->callNonPublicMethod($dataViewRequestHandler, 'handlePagination', array($pager, $request, 5));
    }

}
