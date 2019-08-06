<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TrickControllerTest extends WebTestCase
{
    public function index()
    {
        $client = static::createClient();

        $client->request('GET', '/trick');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
