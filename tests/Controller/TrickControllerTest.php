<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TrickControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $client->request('GET', '/trick/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testNew()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/trick/new');

        $buttonNode = $crawler->filter('input[type=submit]');

        $form = $buttonNode->form();

        $client->submit($form, [
            'trick[name]' => 'test',
            'trick[description]' => 'Test',
            'trick[trickGroup]' => 251
        ]);
    }
}
