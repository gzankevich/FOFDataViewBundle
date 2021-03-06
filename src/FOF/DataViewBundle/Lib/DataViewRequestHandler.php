<?php

namespace FOF\DataViewBundle\Lib;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Session\Session;
use Pagerfanta\Pagerfanta;

use DataView\Filter;
use DataView\DataView;
use FOF\DataViewBundle\Form\Type\DataViewType;

/**
 * The glue between a request and a DataView
 *
 * @author George Zankevich <gzankevich@gmail.com>
 */
class DataViewRequestHandler
{
    const SESSION_KEY = 'data_view';

    private $form = null;
    private $formFactory = null;
    private $session = null;

    /**
     * @param FormFactory $formFactory Used to instantiate the filter form
     * @param Session $session Stores settings between pages
     */
    public function __construct(FormFactory $formFactory, Session $session)
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
        $this->handleFilters($dataView, $request);

        // initial page load
        if($request->getMethod() == 'GET') {
            $this->clearSessionSettings();
        } else {
            $sessionSettings = $this->loadSessionSettings($dataView);

            $this->handleSortOrder($dataView, $request);
            $this->handlePagination($dataView->getPager(), $request, intval($sessionSettings['page']));

            $this->saveSessionSettings($dataView);
        }
    }

    /**
     * Clears the settings stored in the session
     *
     * @return null
     */
    protected function clearSessionSettings()
    {
        $this->session->clear(self::SESSION_KEY);

        // set the initial value or the first POST will result in page=0
        $this->session->set(self::SESSION_KEY, array('page' => 1));
    }

    /**
     * Loads settings from the session
     *
     * @param DataView $dataView The DataView instance to get the settings from
     * @return null
     */
    protected function loadSessionSettings(DataView $dataView)
    {
        return $this->session->get(self::SESSION_KEY);
    }

    /**
     * Saves settings to the session
     *
     * @param DataView $dataView The DataView instance to load the settings into
     * @return null
     */
    protected function saveSessionSettings(DataView $dataView)
    {
        $this->session->set(self::SESSION_KEY, array(
            'page' => $dataView->getPager()->getCurrentPage(), 
        ));
    }

    /**
     * Modify the current page depending on what is in the request
     *
     * @param DataView $dataView The DataView instance to modify
     * @param Request $request The Request to read input from
     * @return null
     */
    protected function handlePagination(Pagerfanta $pager, Request $request, $oldPage)
    {
        foreach($request->request->all() as $name => $order) {
            if($name == 'pagination_first_page') {
                $pager->setCurrentPage(1);
            } elseif($name == 'pagination_previous_page') {
                $pager->setCurrentPage($oldPage - 1);
            } elseif($name == 'pagination_next_page') {
                $pager->setCurrentPage($oldPage + 1);
            } elseif($name == 'pagination_last_page') {
                $pager->setCurrentPage($pager->getNbPages());
            }
        }

    }

    /**
     * Apply sorting to the relevant column
     *
     * @param DataView $dataView The DataView instance to modify
     * @param Request $request The Request to read input from
     * @return null
     */
    protected function handleSortOrder(DataView $dataView, Request $request)
    {
        foreach($request->request->all() as $columnName => $sortOrder) {
            if(strpos($columnName, 'sort_') === 0) {
                $dataView->applySortOrder(str_replace('__', '.', str_replace('sort_', '', $columnName)), $sortOrder);
                return;
            }
        }
    }

    /**
     * Applies filters
     *
     * @param DataView $dataView The DataView instance to modify
     * @param Request $request The Request to read input from
     * @return null
     */
    protected function handleFilters(DataView $dataView, Request $request)
    {
        $this->form = $this->formFactory->create($this->getDataViewType($dataView->getColumns()), $dataView);

        if($request->getMethod() == 'POST') {
            $this->form->bind($request);
        }
    }

    /**
     * Get an instance of the DataViewType
     *
     * @param array $columns An array of Column instances
     * @returns DataViewType
     */
    protected function getDataViewType(array $columns)
    {
        return new DataViewType($this->getColumnChoices($columns));
    }

    /**
     * Get an array of column choices for FilterType - key is the property path, value is the label
     *
     * This ignores columns which are not sortable.
     *
     * @param array $columns An array of Column instances
     * @returns array
     */
    protected function getColumnChoices(array $columns)
    {
        $columnChoices = array();
        foreach($columns as $column) {
            // sortable columnChoices are not filterable either
            if($column->isSortable()) {
                $columnChoices[$column->getPropertyPath()] = $column->getLabel();
            }
        }

        return $columnChoices;
    }

    /**
     * Get the filter form
     *
     * @return Form
     */
    public function getForm()
    {
        return $this->form;
    }
}
