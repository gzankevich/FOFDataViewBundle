<?php

namespace FOF\DataViewBundle\Test\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DemoControllerTest extends WebTestCase
{
    public function testList_pagination()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/dataviewtest/demo');

        // page 1
        $this->assertEquals(2, $crawler->filter('table tbody tr')->count(), 'Page 1 has 2 rows');
        $this->assertEquals(9, $crawler->filter('table tbody>tr:first-child>td')->count(), 'Number of columns');
        $this->assertEquals(1, $crawler->filter('#current_page_number')->text(), 'Current page number');

        $this->assertTableContents($crawler, array(
            array('FreeOfficeFinder', 'http://www.freeofficefinder.com', 'Interlink House, Maygrove Road', 'Bob', 'Dole', 'bob.dole@gmail.com', '1234567890', 'listItems' => array('John Smith', 'George Zankevich')),
            array('Acme', 'http://www.acme.com', '123 Fake Street', 'Skye', 'Collins', 'skye.collins@gmail.com', '7234567890', 'listItems' => array('Logan Hunt')),
        ));

        // Next page (page 2)
        $form = $crawler->selectButton('pagination_next_page')->form(array());
        $crawler = $client->submit($form);

        $this->assertEquals(2, $crawler->filter('table tbody tr')->count(), 'Page 2 has 2 rows');
        $this->assertEquals(2, $crawler->filter('#current_page_number')->text(), 'Next goes to page 2');

        $this->assertTableContents($crawler, array(
            array('IBM', 'http://www.ibm.com', '76/78 Upper Ground', 'Sarah', 'Kerr', 'skye.collins@gmail.com', '3234567890', ''),
            array('Sun', 'http://www.sun.com', '55 King William St', '', '', '', '', ''),
        ));

        // Previous page (page 1)
        $form = $crawler->selectButton('pagination_previous_page')->form(array());
        $crawler = $client->submit($form);

        $this->assertEquals(1, $crawler->filter('#current_page_number')->text(), 'Previous goes back to page 1');

        $this->assertTableContents($crawler, array(
            array('FreeOfficeFinder'),
            array('Acme'),
        ));

        // Last page (page 3)
        $form = $crawler->selectButton('pagination_last_page')->form(array());
        $crawler = $client->submit($form);

        $this->assertEquals(1, $crawler->filter('table tbody tr')->count(), 'Page 3 has 1 row');
        $this->assertEquals(3, $crawler->filter('#current_page_number')->text(), 'Last goes to page 3');

        $this->assertTableContents($crawler, array(
            array('Samsung', 'http://www.samsung.com', '105 Challenger Rd', 'Luke', 'Briggs', 'luke.briggs@gmail.com', '4234567890', ''),
        ));

        // First page (page 1)
        $form = $crawler->selectButton('pagination_first_page')->form(array());
        $crawler = $client->submit($form);

        $this->assertEquals(1, $crawler->filter('#current_page_number')->text(), 'Last goes to page 3');

        //echo $client->getResponse()->getContent();
    }

    protected function assertTableContents($crawler, array $expectedValues)
    {
        foreach($expectedValues as $rowNumber => $expectedRowContents) {
            $columnCounter = 1;

            foreach($expectedRowContents as $type => $expectedCellContent) {
                if(is_numeric($type)) {
                    $this->assertTableCellContent($crawler, $expectedCellContent, $rowNumber + 1, $columnCounter);
                } elseif($type == 'listItems') {
                    foreach($expectedCellContent as $listItemNumber => $expectedListItemContent) {
                        $this->assertTableCellListItemContent($crawler, $expectedListItemContent, $rowNumber + 1, $columnCounter, $listItemNumber + 1);
                    }
                }

                $columnCounter++;
            }
        }
    }

    protected function assertTableCellContent($crawler, $expectedValue, $rowNumber, $columnNumber)
    {
        $this->assertEquals(
            $expectedValue, 
            trim($crawler->filter("table tbody>tr:nth-child({$rowNumber})>td:nth-child({$columnNumber})")->text()), 
            "Cell content: '{$expectedValue}' at {$rowNumber}, {$columnNumber}"
        );
    }

    protected function assertTableCellListItemContent($crawler, $expectedValue, $rowNumber, $columnNumber, $listItemNumber)
    {
        $this->assertEquals(
            $expectedValue, 
            trim($crawler->filter("table tbody>tr:nth-child({$rowNumber})>td:nth-child({$columnNumber}) ul>li:nth-child({$listItemNumber})")->text()), 
            "List item content: '{$expectedValue}' at row: {$rowNumber}, column: {$columnNumber}, item: {$listItemNumber}"
        );

    }
}
