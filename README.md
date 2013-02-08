FOFDataViewBundle
=================

Symfony2 bundle for the DataView PHP library

Usage
=================

This bundle has not been added to packagist yet. These instructions will change in the near future.

Add the bundle to composer.json:

```
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/FreeOfficeFinder/DataView"
    },
    {
        "type": "vcs",
        "url": "https://github.com/FreeOfficeFinder/FOFDataViewBundle"
    }
],
"require": {
    "fof/dataview-bundle": "*"
}
```

In your project root directory, run:

```
php composer.phar update
```

After the library and bundle are installed, add the bundle to AppKernel.php:

```
new FOF\DataViewBundle\FOFDataViewBundle(),
```

Then create a template for your list page:

```twig
{# file: AcmeDemoBundle:Office:list.html.twig #}

{% extends "AcmeDemoBundle::myLayout.html.twig" %}

{% use "FOFDataViewBundle::list.html.twig" %}

<form method="post" action="{{ url('my_route') }}">
    {{ block('filters') }}
    {{ block('table') }}
    {{ block('pagination') }}
</form>
```

Create a controller:

```php
public function listAction()
{
    $dataView = new \DataView\DataView(new \DataView\Adapter\DoctrineORM($this->getEntityManager()));

    // the data source can be a string specifying which entity type to use or a QueryBuilder instance
    $dataView->setSource('AcmeDemoBundle:Office');

    $dataView->addColumn(new \DataView\Column('address'));
    // one-to-one relationship
    $dataView->addColumn(new \DataView\Column('company.name'));

    // many-to-many relationship
    $dataView->addColumn(new \DataView\Column(
        'office_contact_associations.office_contact.first_name',
        // the heading to show for this column on the HTML table
        'Main Contact',
        // where Office->getPrimaryContact() gets the main contact
        // and OfficeContact->getFullName() returns the first and last names joined together
        // i.e. primary_contact and full_name are not actual database columns but only exist as methods on the entities
        'primaryContact.fullName'
    ));


    // this handles events such as the user clicking on a column to sort on it, adding a filter or paginating
    $dataViewRequestHandler = $this->get('data_view_request_handler');
    $dataViewRequestHandler->bind($dataView, $this->getRequest());

    return $this->render('AcmeDemoBundleBundle:Office:list.html.twig', array(
        'dataView' => $dataView, 
        'form' => $dataViewRequestHandler->getForm()->createView(),
    ));
}
```


What if we want to display all of the many-to-many's in a ul?

```php
$dataView->addColumn(new \DataView\Column(
    'office_contact_associations',
    'All Contacts',
    null,
    // the twig block 'office_contacts' will be called when rendering the contents of the cells in this column
    'office_contacts',
    // disables sorting and filter on this column since it is not possible
    false
));
```

```twig
{% block office_contacts %}
    <ul>
        {# result is the current record in the pager - i.e. an instance of Office, in this case #}
        {% for officeContactAssociation in result.officeContactAssociations %}
            <li>{{ officeContactAssociation.officeContact.fullName }}</li>
        {% endfor %}
    </ul>
{% endblock office_contacts %}
```
