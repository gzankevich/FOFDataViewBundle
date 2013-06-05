<?php

namespace FOF\DataViewBundle\Entity;

class Company
{
    protected $name = null;
    protected $website = null;
    protected $employees = array();
    protected $building = null;

    public function __construct($name, $website, array $employees, Building $building)
    {
        $this->name      = $name;
        $this->website   = $website;
        $this->employees = $employees;
        $this->building  = $building;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getWebsite()
    {
        return $this->website;
    }

    public function getEmployees()
    {
        return $this->employees;
    }

    public function getOwner()
    {
        foreach($this->getEmployees() as $employee) {
            if($employee->getIsCompanyOwner()) return $employee;
        }
    }

    public function getBuilding()
    {
        return $this->building;
    }
}
