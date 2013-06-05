<?php

namespace FOF\DataViewBundle\Test\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DemoControllerTest extends WebTestCase
{
    public function testList()
    {
        $this->assertTrue(true);
        $client = static::createClient();
        $crawler = $client->request('GET', '/dataview/demo');

        echo $client->getResponse()->getContent();
    }
}
