<?php

namespace FOF\DataViewBundle\Entity;

class Employee
{
    protected $firstName, $lastName, $email, $telephone = null;
    protected $isCompanyHead = false;

    public function __construct($firstName, $lastName, $email, $telephone, $isCompanyOwner = false)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->telephone = $telephone;
        $this->isCompanyOwner = $isCompanyOwner;
    }

    public function getFirstName()
    {
        return $this->firstName;
    }

    public function getLastName()
    {
        return $this->lastName;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getTelephone()
    {
        return $this->telephone;
    }

    public function getIsCompanyOwner()
    {
        return $this->isCompanyOwner;
    }
}
