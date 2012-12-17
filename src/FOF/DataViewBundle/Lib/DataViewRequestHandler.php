<?php

namespace FOF\DataViewBundle\Lib;

class DataViewRequestHandler
{
    /**
     * Modifies a DataView instance based on the POSTed parameters of a Request
     *
     * In other words, this will apply any user selected filters, the sort order and pagination.
     */
    public function bind($dataView, $request)
    {
        $parameters = $request->request->all();

        foreach($parameters as $name => $value) {
            if(strpos($name, 'sort_') === 0) {
                $this->handleSort($dataView, $name, $value);
            }
        }

    }

    protected function handleSort($dataView, $propertyPath, $order)
    {
        $propertyPath = str_replace('__', '.', str_replace('sort_', '', $propertyPath));

        foreach($dataView->getColumns() as $column) {
            if($column->getPropertyPath() === $propertyPath) {
                $column->setSortOrder($order);
            }
        }
    }
}
