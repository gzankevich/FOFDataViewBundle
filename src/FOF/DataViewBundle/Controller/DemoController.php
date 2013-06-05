<?php

namespace FOF\DataViewBundle\Controller;

use DataView\Adapter\ArrayAdapter;
use DataView\DataView;
use DataView\Column;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use FOF\DataViewBundle\Entity\Company;
use FOF\DataViewBundle\Entity\Employee;
use FOF\DataViewBundle\Entity\Building;

class DemoController extends Controller
{
    public function listAction()
    {
		$dataView = new DataView(new ArrayAdapter());

        $dataView->setSource(array(
            new Company('FreeOfficeFinder', 'http://www.freeofficefinder.com', array(
                new Employee('John', 'Smith', 'john.smith@gmail.com', '923123145'),
                new Employee('George', 'Zankevich', 'gzankevich@gmail.com', '523123145'),
                new Employee('Bob', 'Dole', 'bob.dole@gmail.com', '1234567890', true),
            ), new Building('Interlink House, Maygrove Road')),
            new Company('Acme', 'http://www.acme.com', array(
                new Employee('Skye', 'Collins', 'skye.collins@gmail.com', '7234567890', true),
                new Employee('Logan', 'Hunt', 'logan.hunt@gmail.com', '623123145'),
            ), new Building('123 Fake Street')),
            new Company('IBM', 'http://www.ibm.com', array(
                new Employee('Sarah', 'Kerr', 'skye.collins@gmail.com', '3234567890', true),
            ), new Building('76/78 Upper Ground')),
            new Company('Sun', 'http://www.sun.com', array(
            ), new Building('55 King William St')),
            new Company('Samsung', 'http://www.samsung.com', array(
                new Employee('Luke', 'Briggs', 'luke.briggs@gmail.com', '4234567890', true),
            ), new Building('105 Challenger Rd')),
        ));

        $dataView->setColumns(array(
            new Column('name'),
            new Column('website'),
            // one-to-one
            new Column('building.address'),
            new Column('owner.first_name', 'Owner First Name'),
            new Column('owner.last_name', 'Owner Last Name'),
            new Column('owner.email', 'Owner Email'),
            new Column('owner.telephone', 'Owner Telephone'),
            // one-to-many
            new Column('employees', 'Employees', null, 'employees_column', false),
            new Column(null, '', null, 'actions_column'),
        ));

        $dataViewRequestHandler = $this->get('data_view_request_handler');
        $dataViewRequestHandler->bind($dataView, $this->getRequest());

        // this must be called after DataViewRequestHandler::bind or the pager will be created without the filters and sort order being applied to the query
        // use a low number of results per page so that we don't need a tonne of data to test pagination
        $dataView->getPager()->setMaxPerPage(2);

        return $this->render('FOFDataViewBundle:Demo:list.html.twig', array(
            'dataView' => $dataView, 
            'form' => $dataViewRequestHandler->getForm()->createView(),
        ));
    }
}
