<?php

namespace FOF\DataViewBundle\Entity;

class Company
{
    protected $name, $website = null;
    protected $employees = array();

    public function __construct($name, $website, array $employees)
    {
        $this->name = $name;
        $this->website = $website;
        $this->employees = $employees;
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
}
