<?php

namespace Tests\Surikat\BookingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BookingControllerTest extends WebTestCase
{
    public function testCheckForAvailabiliyAction()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Welcome to Symfony', $crawler->filter('#container h1')->text());
    }


    public function testBookingAction()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/booking');

        $form = $crawler->selectButton('submit')->form();
        $form['booking[date]'] = (new \DateTime('-1 day'))->format('d/m/Y');
        $form['booking[type]'] = 'halfDay';
        $form['booking[email]'] = 'mickey.welensky@gmail.com';
        $form['booking[tickets][0][name]'] = 'Georges';
        $form['booking[tickets][0][surname]'] = 'Polux';
        $form['booking[tickets][0][birthdate]'] = '10/12/1925';
        $form['booking[tickets][0][country]'] = 'FR';
        $form['booking[tickets][0][specialPrice]'] = 0;

        $form['commande_init[date]'] = '16/12/2018';
        $crawler = $client->submit($form);
  //      $this->assertEquals(302, $client->getResponse()->getStatusCode());

        $client->followRedirect();
        $this->assertEquals('Surikat\BookingBundle\Controller\BookingController::newBookingAction', $client->getRequest()->attributes->get('_controller'));

        echo $client->getResponse()->getContent();
    }


    public function testNewBookingAction()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Welcome to Symfony', $crawler->filter('#container h1')->text());
    }


    public function testShowAction()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Welcome to Symfony', $crawler->filter('#container h1')->text());
    }

}
