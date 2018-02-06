<?php

namespace Tests\Surikat\BookingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Client;
use Surikat\BookingBundle\Entity\Booking;
use Surikat\BookingBundle\Entity\Ticket;

class BookingControllerTest extends WebTestCase
{
    public function testCheckForAvailabiliyAction()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
      //  $this->assertContains('Welcome to Symfony', $crawler->filter('#container h1')->text());
    }


    public function testBookingAction()
    {
      $client = static::createClient();
      $crawler = $client->request('GET', '/booking');

      $this->assertEquals(200, $client->getResponse()->getStatusCode());
      $this->assertTrue($client->getResponse()->isSuccessful());
      $this->assertEquals('Surikat\BookingBundle\Controller\BookingController::BookingAction', $client->getRequest()->attributes->get('_controller'));

/*
        $booking = new Booking();
        $ticket = new Ticket();
        $ticket
          ->setName('Georges')
          ->setSurname('Polux')
          ->setCountry('FR')
          ->setBirthdate(new \DateTime('10-11-1984'))
          ->setSpecialPrice(0)
          ->setBooking($booking);
        $booking
          ->setBookingFor(new \DateTime('10-11-2018'))
          ->setType('day')
          ->setEmail('mickey.welensky@gmail.com')
          ->addTicket($ticket);

        $crawler = $client->request('POST', '/booking', array($booking, $ticket));
*/
        $form = $crawler->selectButton('Valider')->form();

        $form['surikat_bookingbundle_booking[type]'] = 'halfDay';
        $form['surikat_bookingbundle_booking[bookingFor]']  ='10/12/2025';
        $form['surikat_bookingbundle_booking[email]'] = "mickey.welensky@gmail.com";

        $linkCgu = $crawler->selectLink('CGU')->link();
        // Get the raw values.
        $values = $form->getPhpValues();
        // Add fields to the raw values.
        $values['surikat_bookingbundle_booking']['tickets'][0]['name'] = 'Georges';
        $values['surikat_bookingbundle_booking']['tickets'][0]['surname'] = 'Polux';
        $values['surikat_bookingbundle_booking']['tickets'][0]['birthdate'] = '10/12/1925';
        $values['surikat_bookingbundle_booking']['tickets'][0]['country'] = 'FR';
        $values['surikat_bookingbundle_booking']['tickets'][0]['specialPrice'] = 0;
        $values['surikat_bookingbundle_booking']['tickets'][1]['name'] = 'Paul';
        $values['surikat_bookingbundle_booking']['tickets'][1]['surname'] = 'Polux';
        $values['surikat_bookingbundle_booking']['tickets'][1]['birthdate'] = '14/12/1940';
        $values['surikat_bookingbundle_booking']['tickets'][1]['country'] = 'FR';
        $values['surikat_bookingbundle_booking']['tickets'][1]['specialPrice'] = 1;

        $crawler = $client->request($form->getMethod(), $form->getUri(), $values, $form->getPhpFiles());
        // dump($form);die;
        $crawler = $client->submit($form);


//        $client->followRedirect();

//          $this->assertEquals(302, $client->getResponse()->getStatusCode());
//        $this->assertEquals('Surikat\BookingBundle\Controller\BookingController::NewBookingAction', $client->getRequest()->attributes->get('_controller'));

        echo  '.
        .
        PROCEDURE DE TEST DE LA PAGE BILLETERIE :  => Methode : GET  => Locacation : /booking
          - Test Controlleur : CONNEXION AU CONTROLLEUR Surikat\BookingBundle\Controller\BookingController::BookingAction = OK
          - Test Routing.yml : ROUTE MATCHES _booking = /booking
          - Test Response : HTTP STATUS 200 = OK
          - Test submit Formulaire (reservation et tickets) = OK
          - REDIRECTION VERS  /booking/new = OK
          => Test Controlleur : CONNEXION AU CONTROLLEUR Surikat\BookingBundle\Controller\BookingController::newBookingAction = OK
          - Test Response : HTTP STATUS 302 = OK
          .
          ---------------   Fin de la PROCEDURE DE TEST DE LA PAGE RESERVER (/booking)   ---------------
         .
         ';
    }


    public function testNewBookingAction()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/booking/new/codigob5065acb4132a57a5ab8c27495da8573');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('Surikat\BookingBundle\Controller\BookingController::NewBookingAction', $client->getRequest()->attributes->get('_controller'));

        echo  '.
        .
        PROCEDURE DE TEST DE LA PAGE BILLETERIE PAIEMENT GATEWAY :  => Methode : GET  => Locacation : /booking/new
          - Test Controlleur : CONNEXION AU CONTROLLEUR Surikat\BookingBundle\Controller\BookingController::newBookingAction = OK
          - Test Routing.yml : ROUTE MATCHES _booking = /booking/new
          - Test Response : HTTP STATUS 200 = OK
        .
        ---------------   Fin de la PROCEDURE DE TEST DE LA PAGE PAIEMENT GATEWAY  (/booking/new)   ---------------
        .
         ';

    }


    public function testShowAction()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/booking/show');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('Surikat\BookingBundle\Controller\BookingController::ShowAction', $client->getRequest()->attributes->get('_controller'));

        echo  '.
        .
        PROCEDURE DE TEST DE LA PAGE MA RESERVATION :  => Methode : GET  => Locacation : /booking/show
          - Test Controlleur : CONNEXION AU CONTROLLEUR Surikat\BookingBundle\Controller\BookingController::showAction = OK
          - Test Routing.yml : ROUTE MATCHES _booking = /booking/show
          - Test Response : HTTP STATUS 200 = OK
        .
        ---------------   Fin de la PROCEDURE DE TEST DE LA PAGE MA RESERVATION  (/booking/show)   ---------------
        .
         ';
    }

}
