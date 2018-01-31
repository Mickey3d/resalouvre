<?php

namespace Tests\Surikat\BookingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeControllerTest extends WebTestCase
{

    private $client = null;

    public function setUp()
    {
      $this->client = static::createClient();
    }

    public function testIndexAction()
    {
        $this->client->request('GET', '/');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testLegalsAction()
    {
        $this->client->request('GET', '/legals');

        $crawler = $this->client->request('GET', '/legals');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertContains('Editeur du Site', $crawler->filter('h4')->text());
    }
}
