<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class IndexControllerTest extends WebTestCase
{
    public function testShowIndex()
    {
        $client = static::createClient();

        $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testTitleIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertSelectorTextContains('html #homepage-section h1.h2', 'SnowTricks');
    }
}
