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
        $crawler = $this->client->request('GET', '/');
        $client = $this->client;
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('Surikat\BookingBundle\Controller\HomeController::indexAction', $this->client->getRequest()->attributes->get('_controller'));
        $this->assertEquals(1, $crawler->filter('html:contains("Bienvenue sur la billeterie du Musée du Louvre")')->count());


        $linkBilleterie = $crawler->selectLink('Accéder à la Billeterie')->link();
        $this->assertEquals('http://localhost/booking', $linkBilleterie->getUri());

        $linkMaReservation = $crawler->selectLink('Rechercher ma Réservation')->link();
        $this->assertEquals('http://localhost/booking/show', $linkMaReservation->getUri());



        echo  '.
        .
        PROCEDURE DE TEST DE LA PAGE ACCUEIL :  => Methode : GET  => Locacation : /
          - Test Controlleur : CONNEXION AU CONTROLLEUR Surikat\BookingBundle\Controller\HomeController::IndexAction = OK
          - Test Routing.yml : ROUTE MATCHES surikat_booking_homepage = /
          - Test Response : HTTP STATUS 200 = OK
          - Test Page Content : Homepage Content  => Bienvenue sur la billeterie du Musée du Louvre = OK
          - Test Lien Billeterie : HTTP STATUS 200 = OK - Lien :'.$linkBilleterie->getUri().
        ' .
          - Test Lien Rechercher ma Réservation : HTTP STATUS 200 = OK - Lien : '.$linkMaReservation->getUri().
        '.
         .
         ---------------   Fin de la PROCEDURE DE TEST DE LA PAGE ACCUEIL   ---------------';
    }

    public function testLegalsAction()
    {
        $this->client->request('GET', '/legals');
        $client = $this->client;
        $crawler = $this->client->request('GET', '/legals');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('html:contains("Mentions Légales")')->count());
        $this->assertEquals('Surikat\BookingBundle\Controller\HomeController::LegalsAction', $this->client->getRequest()->attributes->get('_controller'));

        $linkCgu = $crawler->selectLink('CGU')->link();
        $this->assertEquals('http://localhost/legals#CGU', $linkCgu->getUri());

        $linkLegals = $crawler->selectLink('Mentions Légales')->link();
        $this->assertEquals('http://localhost/legals#legals', $linkLegals->getUri());

        echo  ' .
        .
        PROCEDURE DE TEST DE LA PAGE MENTIONS LEGALES CGU :  => Methode : GET  => Locacation : /legals
        .
         - Test Controlleur : CONNEXION AU CONTROLLEUR Surikat\BookingBundle\Controller\HomeController::legalsAction = OK
         - Test Routing.yml : ROUTE MATCHES _legals = /legals
         - Test Response : HTTP STATUS 200 = OK
         - Test Page Content : Legals Content  => Mentions Légales = OK
         - Test Lien CGU = OK - Lien : '.$linkCgu->getUri().
        '.
         - Test Lien Mentions Légales = OK  Lien : '.$linkLegals->getUri().
        '.
        ---------------   Fin de la PROCEDURE DE TEST DE LA PAGE MENTIONS LEGALES CGU   ---------------
        .
        ';
    }
}
