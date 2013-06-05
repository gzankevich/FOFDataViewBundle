<?php

namespace FOF\DataViewBundle\Test\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DemoControllerTest extends WebTestCase
{
    public function testList_pagination()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/dataviewtest/demo');

        $this->assertEquals(2, $crawler->filter('table tbody tr')->count());
        $this->assertEquals(9, $crawler->filter('table tbody>tr:first-child>td')->count());

        $this->assertTableContents($crawler, array(
            array('FreeOfficeFinder', 'http://www.freeofficefinder.com', 'Interlink House, Maygrove Road', 'Bob', 'Dole', 'bob.dole@gmail.com', '1234567890', 'listItems' => array('John Smith', 'George Zankevich')),
            array('Acme', 'http://www.acme.com', '123 Fake Street', 'Skye', 'Collins', 'skye.collins@gmail.com', '7234567890', 'listItems' => array('Logan Hunt')),
        ));

        $form = $crawler->selectButton('Next')->form(array());
        $crawler = $client->submit($form);

        echo $client->getResponse()->getContent();
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
