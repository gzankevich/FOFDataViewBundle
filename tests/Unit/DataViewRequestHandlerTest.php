<?php

namespace FOF\DataViewBundle\Test\Unit;

use DataView\Test\BaseUnitTest;
use FOF\DataViewBundle\Lib\DataViewRequestHandler;
use FOF\DataViewBundle\Form\Type\DataViewType;

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
        $session->expects($this->once())->method('set')->with($this->equalTo('data_view'), $this->equalTo(array('page' => 1)));

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
            array('handleSortOrder', 'handlePagination', 'handleFilters'), array($formFactory, $session));
        $dataViewRequestHandler->expects($this->once())->method('handleSortOrder')->with($this->equalTo($dataView), $this->equalTo($request));
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
        $pager->expects($this->once())->method('setAllowOutOfRangePages')->with($this->equalTo(true));

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
        $pager->expects($this->once())->method('setAllowOutOfRangePages')->with($this->equalTo(true));

        $dataView = $this->getMockBuilder('\DataView\DataView')->disableOriginalConstructor()->getMock();

        $parameterBag = $this->getMock('\Symfony\Component\HttpFoundation\ParameterBag');
        $parameterBag->expects($this->once())->method('all')->will($this->returnValue(array('pagination_last_page' => '')));

        $request = $this->getMockBuilder('\Symfony\Component\HttpFoundation\Request')->disableOriginalConstructor()->getMock();
        $request->request = $parameterBag;

        $dataViewRequestHandler = new DataViewRequestHandler($formFactory, $session);
        $this->callNonPublicMethod($dataViewRequestHandler, 'handlePagination', array($pager, $request, 5));
    }

    /**
     * @covers DataViewRequestHandler::handlePagination
     */
    public function testHandlePagination_nextPage()
    {
        $session = $this->getMockBuilder('\Symfony\Component\HttpFoundation\Session\Session')->disableOriginalConstructor()->getMock();
        $formFactory = $this->getMockBuilder('\Symfony\Component\Form\FormFactory')->disableOriginalConstructor()->getMock();

        $pager = $this->getMockBuilder('Pagerfanta\Pagerfanta')->disableOriginalConstructor()->getMock();
        $pager->expects($this->once())->method('setCurrentPage')->with($this->equalTo(6));
        $pager->expects($this->once())->method('setAllowOutOfRangePages')->with($this->equalTo(true));

        $parameterBag = $this->getMock('\Symfony\Component\HttpFoundation\ParameterBag');
        $parameterBag->expects($this->once())->method('all')->will($this->returnValue(array('pagination_next_page' => '')));

        $request = $this->getMockBuilder('\Symfony\Component\HttpFoundation\Request')->disableOriginalConstructor()->getMock();
        $request->request = $parameterBag;

        $dataViewRequestHandler = new DataViewRequestHandler($formFactory, $session);
        $this->callNonPublicMethod($dataViewRequestHandler, 'handlePagination', array($pager, $request, 5));
    }

    /**
     * @covers DataViewRequestHandler::handlePagination
     */
    public function testHandlePagination_previousPage()
    {
        $session = $this->getMockBuilder('\Symfony\Component\HttpFoundation\Session\Session')->disableOriginalConstructor()->getMock();
        $formFactory = $this->getMockBuilder('\Symfony\Component\Form\FormFactory')->disableOriginalConstructor()->getMock();

        $pager = $this->getMockBuilder('Pagerfanta\Pagerfanta')->disableOriginalConstructor()->getMock();
        $pager->expects($this->once())->method('setCurrentPage')->with($this->equalTo(4));
        $pager->expects($this->once())->method('setAllowOutOfRangePages')->with($this->equalTo(true));

        $parameterBag = $this->getMock('\Symfony\Component\HttpFoundation\ParameterBag');
        $parameterBag->expects($this->once())->method('all')->will($this->returnValue(array('pagination_previous_page' => '')));

        $request = $this->getMockBuilder('\Symfony\Component\HttpFoundation\Request')->disableOriginalConstructor()->getMock();
        $request->request = $parameterBag;

        $dataViewRequestHandler = new DataViewRequestHandler($formFactory, $session);
        $this->callNonPublicMethod($dataViewRequestHandler, 'handlePagination', array($pager, $request, 5));
    }

    /**
     * @covers DataViewRequestHandler::handleSort
     */
    public function testHandleSort_none()
    {
        $session = $this->getMockBuilder('\Symfony\Component\HttpFoundation\Session\Session')->disableOriginalConstructor()->getMock();
        $formFactory = $this->getMockBuilder('\Symfony\Component\Form\FormFactory')->disableOriginalConstructor()->getMock();

        $parameterBag = $this->getMock('\Symfony\Component\HttpFoundation\ParameterBag');
        $parameterBag->expects($this->once())->method('all')->will($this->returnValue(array()));

        $request = $this->getMockBuilder('\Symfony\Component\HttpFoundation\Request')->disableOriginalConstructor()->getMock();
        $request->request = $parameterBag;

        $dataView = $this->getMockBuilder('\DataView\DataView')->disableOriginalConstructor()->getMock();

        $dataViewRequestHandler = new DataViewRequestHandler($formFactory, $session);
        $this->callNonPublicMethod($dataViewRequestHandler, 'handleSortOrder', array($dataView, $request));
    }

    /**
     * @covers DataViewRequestHandler::handleSort
     */
    public function testHandleSort_desc()
    {
        $session = $this->getMockBuilder('\Symfony\Component\HttpFoundation\Session\Session')->disableOriginalConstructor()->getMock();
        $formFactory = $this->getMockBuilder('\Symfony\Component\Form\FormFactory')->disableOriginalConstructor()->getMock();

        $parameterBag = $this->getMock('\Symfony\Component\HttpFoundation\ParameterBag');
        $parameterBag->expects($this->once())->method('all')->will($this->returnValue(array('sort_name' => 'DESC')));

        $request = $this->getMockBuilder('\Symfony\Component\HttpFoundation\Request')->disableOriginalConstructor()->getMock();
        $request->request = $parameterBag;

        $dataView = $this->getMockBuilder('\DataView\DataView')->disableOriginalConstructor()->getMock();
        $dataView->expects($this->once())->method('applySortOrder')->with($this->equalTo('name'), $this->equalTo('DESC'));

        $dataViewRequestHandler = new DataViewRequestHandler($formFactory, $session);
        $this->callNonPublicMethod($dataViewRequestHandler, 'handleSortOrder', array($dataView, $request));
    }
    
    /**
     * @covers DataViewRequestHandler::handleFilters
     */
    public function testHandleFilters_initialPageLoad()
    {
        $session = $this->getMockBuilder('\Symfony\Component\HttpFoundation\Session\Session')->disableOriginalConstructor()->getMock();

        $dataViewType = $this->getMockBuilder('\FOF\DataViewBundle\Form\Type')->getMock();

        $dataView = $this->getMockBuilder('\DataView\DataView')->disableOriginalConstructor()->getMock();
        $dataView->expects($this->once())->method('getColumns')->will($this->returnValue(array()));

        $formFactory = $this->getMockBuilder('\Symfony\Component\Form\FormFactory')->disableOriginalConstructor()->getMock();
        $formFactory->expects($this->once())->method('create')->with($this->equalTo($dataViewType), $dataView);

        $request = $this->getMockBuilder('\Symfony\Component\HttpFoundation\Request')->disableOriginalConstructor()->getMock();
        $request->expects($this->once())->method('getMethod')->will($this->returnValue('GET'));

        $dataViewRequestHandler = $this->getMock('\FOF\DataViewBundle\Lib\DataViewRequestHandler', 
            array('getDataViewType'), array($formFactory, $session));
        $dataViewRequestHandler->expects($this->once())->method('getDataViewType')->will($this->returnValue($dataViewType));

        $this->callNonPublicMethod($dataViewRequestHandler, 'handleFilters', array($dataView, $request));
    }

    /**
     * @covers DataViewRequestHandler::handleFilters
     */
    public function testHandleFilters_post()
    {
        $session = $this->getMockBuilder('\Symfony\Component\HttpFoundation\Session\Session')->disableOriginalConstructor()->getMock();

        $dataViewType = $this->getMockBuilder('\FOF\DataViewBundle\Form\Type')->getMock();

        $dataView = $this->getMockBuilder('\DataView\DataView')->disableOriginalConstructor()->getMock();
        $dataView->expects($this->once())->method('getColumns')->will($this->returnValue(array()));

        $request = $this->getMockBuilder('\Symfony\Component\HttpFoundation\Request')->disableOriginalConstructor()->getMock();
        $request->expects($this->once())->method('getMethod')->will($this->returnValue('POST'));

        $form = $this->getMockBuilder('\Symfony\Component\Form\Form')->disableOriginalConstructor()->getMock();
        $form->expects($this->once())->method('bind')->with($this->equalTo($request));

        $formFactory = $this->getMockBuilder('\Symfony\Component\Form\FormFactory')->disableOriginalConstructor()->getMock();
        $formFactory->expects($this->once())->method('create')->with($this->equalTo($dataViewType), $dataView)->will($this->returnValue($form));

        $dataViewRequestHandler = $this->getMock('\FOF\DataViewBundle\Lib\DataViewRequestHandler', 
            array('getDataViewType'), array($formFactory, $session));
        $dataViewRequestHandler->expects($this->once())->method('getDataViewType')->will($this->returnValue($dataViewType));

        $this->callNonPublicMethod($dataViewRequestHandler, 'handleFilters', array($dataView, $request));
    }

    /**
     * @covers DataViewRequestHandler::getDataViewType
     */
    public function testGetDataViewType()
    {
        $dataViewRequestHandler = $this->getMockBuilder('\FOF\DataViewBundle\Lib\DataViewRequestHandler')
            ->disableOriginalConstructor()->setMethods(array('getColumnChoices'))->getMock();
        $dataViewRequestHandler->expects($this->once())->method('getColumnChoices')->with($this->equalTo(array()))->will($this->returnValue(array()));

        $dataViewType = $this->callNonPublicMethod($dataViewRequestHandler, 'getDataViewType', array(array()));

        $this->assertTrue($dataViewType instanceOf $dataViewType);
    }

    /**
     * @covers DataViewRequestHandler::getColumnChoices
     */
    public function testGetColumnChoices()
    {
        $column1 = $this->getMockBuilder('\DataView\Column')->disableOriginalConstructor()->getMock();
        $column1->expects($this->once())->method('isSortable')->will($this->returnValue(false));

        $column2 = $this->getMock('\DataView\Column', array(), array('foo', 'bar'));
        $column2->expects($this->once())->method('isSortable')->will($this->returnValue(true));
        $column2->expects($this->once())->method('getPropertyPath')->will($this->returnValue('foo'));
        $column2->expects($this->once())->method('getLabel')->will($this->returnValue('bar'));

        $dataViewRequestHandler = $this->getMockBuilder('\FOF\DataViewBundle\Lib\DataViewRequestHandler')
            ->disableOriginalConstructor()->getMock();

        $columnChoices = $this->callNonPublicMethod($dataViewRequestHandler, 'getColumnChoices', array(array($column1, $column2)));

        $this->assertEquals(array('foo' => 'bar'), $columnChoices);
    }
}
