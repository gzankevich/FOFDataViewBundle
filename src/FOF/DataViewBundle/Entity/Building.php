<?php

namespace FOF\DataViewBundle\Entity;

class Building
{
    protected $address = null;

    public function __construct($address)
    {
        $this->address = $address;
    }

    public function getAddress()
    {
        return $this->address;
    }
}
