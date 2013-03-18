<?php

namespace FOF\DataViewBundle\Lib;

use Symfony\Component\HttpFoundation\Request;

use DataView\Filter;
use DataView\DataView;
use FOF\DataViewBundle\Form\Type\DataViewType;

class DataViewRequestHandler
{
    const SESSION_KEY = 'data_view';

    protected $form, $formFactory, $session = null;
    protected $isBound = false;

    public function __construct($formFactory, $session)
    {
        $this->formFactory = $formFactory;
        $this->session     = $session;
    }

    /**
     * Bind a DataView to a request
     *
     * This will setup the filters, sort order and pagination based on the values in the request and session.
     *
     * @param DataView $dataView
     * @param Request $request
     * @return null
     */
    public function bind(DataView $dataView, Request $request)
    {
        // initial page load
        if($request->getMethod() == 'GET') {
            $this->clearSessionSettings();
        } else {
            $this->handleFilters($dataView, $request);

            // this will set the initial values on the DataView
            $this->loadSessionSettings($dataView);

            // these will override those values
            $this->handleSort($dataView, $request);
            $this->handlePagination($dataView, $request);

            $this->saveSessionSettings($dataView);
        }
    }

    protected function clearSessionSettings()
    {
        $this->session->clear(self::SESSION_KEY);
    }

    protected function loadSessionSettings($dataView)
    {
        $settings = $this->session->get(self::SESSION_KEY);

        $dataView->getPager()->setCurrentPage(intval($settings['page']));
    }

    protected function saveSessionSettings($dataView)
    {
        $this->session->set(self::SESSION_KEY, array(
            'page' => $dataView->getPager()->getCurrentPage(), 
        ));
    }

    protected function handlePagination(&$dataView, $request)
    {
        foreach($request->request->all() as $name => $order) {
            if($name == 'pagination_first_page') {
                $dataView->getPager()->setCurrentPage(1);
            } elseif($name == 'pagination_previous_page') {
                $dataView->getPager()->setCurrentPage($dataView->getPager()->getCurrentPage() - 1);
            } elseif($name == 'pagination_next_page') {
                $dataView->getPager()->setCurrentPage($dataView->getPager()->getCurrentPage() + 1);
            } elseif($name == 'pagination_last_page') {
                $dataView->getPager()->setCurrentPage($dataView->getPager()->getNbPages());
            }
        }

    }

    protected function handleSort($dataView, $request)
    {
        foreach($request->request->all() as $name => $order) {
            if(strpos($name, 'sort_') === 0) {
                $this->applySort($dataView, str_replace('__', '.', str_replace('sort_', '', $name)), $order);
                return;
            }
        }
    }

    protected function applySort($dataView, $propertyPath, $order)
    {
        foreach($dataView->getColumns() as $column) {
            if($column->getPropertyPath() === $propertyPath) {
                $column->setSortOrder($order);
            }
        }
    }

    protected function handleFilters($dataView, $request)
    {
        $columns = array();
        foreach($dataView->getColumns() as $column) {
            // sortable columns are not filterable either
            if($column->isSortable()) {
                $columns[$column->getPropertyPath()] = $column->getLabel();
            }
        }

        $this->form = $this->formFactory->create(new DataViewType($columns), $dataView);

        if($request->getMethod() == 'POST') {
            $this->form->bind($request);
        }
    }

    public function getForm()
    {
        return $this->form;
    }
}
