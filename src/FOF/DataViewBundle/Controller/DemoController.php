<?php

namespace FOF\DataViewBundle\Controller;

use DataView\Adapter\ArrayAdapter;
use DataView\DataView;
use DataView\Column;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use FOF\DataViewBundle\Entity\Company;
use FOF\DataViewBundle\Entity\Employee;

class DemoController extends Controller
{
    public function listAction()
    {
		$dataView = new DataView(new ArrayAdapter());

        $dataView->setSource(array(
            new Company('FreeOfficeFinder', 'http://www.freeofficefinder.com', array(
                new Employee('Bob', 'Dole', 'bob.dole@gmail.com', '1234567890', true),
                new Employee('John', 'Smith', 'john.smith@gmail.com', '923123145'),
            )),
            new Company('Acme', 'http://www.acme.com', array(
                new Employee('Skye', 'Collins', 'skye.collins@gmail.com', '7234567890', true),
                new Employee('Logan', 'Hunt', 'logan.hunt@gmail.com', '623123145'),
            )),
            new Company('IBM', 'http://www.ibm.com', array(
                new Employee('Sarah', 'Kerr', 'skye.collins@gmail.com', '3234567890', true),
            )),
            new Company('Sun', 'http://www.sun.com', array(
            )),
            new Company('Samsung', 'http://www.samsung.com', array(
                new Employee('Luke', 'Briggs', 'luke.briggs@gmail.com', '4234567890', true),
            )),
        ));

        $dataView->setColumns(array(
            new Column('name', 'Name'),
        ));

        $dataViewRequestHandler = $this->get('data_view_request_handler');
        $dataViewRequestHandler->bind($dataView, $this->getRequest());

        return $this->render('FOFDataViewBundle::list.html.twig', array(
            'dataView' => $dataView, 
            'form' => $dataViewRequestHandler->getForm()->createView(),
        ));
    }
}
