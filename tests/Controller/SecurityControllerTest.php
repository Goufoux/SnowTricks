<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    public function testLogin()
    {
        $client = static::createClient();

        $client->request('GET', '/login');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    // public function testFormLogin()
    // {
    //     $client = static::createClient();

    //     $crawler = $client->request('GET', '/login');

    //     $buttonNode = $crawler->selectButton('submit');

    //     $form = $buttonNode->form();

    //     $client->submit($form, [
    //         'email' => 'test',
    //         'password' => 'test'
    //     ]);

        // $form = $crawler->selectButton('button[type="submit"]')->form();

        // $form['email'] = 'test';
        // $form['password'] = 'test';

        // $crawler = $client->submit($form);
    // }
}
