<?php

namespace FOF\DataViewBundle\Test\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DemoControllerTest extends WebTestCase
{
    public function testList()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/dataview/demo');

        $this->assertEquals('list', $client->getResponse()->getContent());
    }
}
