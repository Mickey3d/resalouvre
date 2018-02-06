<?php
// test/Surikat/BookingBundle/Services/BookingEngineTest.php

namespace Tests\Surikat\BookingBundle\Services;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Surikat\BookingBundle\Entity\Booking;
use Surikat\BookingBundle\Entity\Ticket;
use Surikat\BookingBundle\Services\BookingEngine;
use Surikat\BookingBundle\Services\ConfigManager;

class BookingEngineTest extends webTestCase
{

  protected $em;
  protected $bookingEngine;
  protected $configManager;

  /**
   * {@inheritDoc}
   */
  protected function setUp()
  {
    $client = static::createClient();
    $container = $client->getContainer();

    $entityManager = $this->em = $container->get('doctrine.orm.entity_manager');
    $configManager = $this->configManager = $container->get('surikat_booking.configmanager');

    $bookingEngine = new BookingEngine($entityManager, $configManager);
  }

    /**
     * @param int $type
     * @param /Datetime $birthdate
     * @param bool $specialPrice
     * @return float|null
     */

  public function testPriceCalculator()
    {
      $type = "halfDay";
      $birthdate = new \DateTime('31-12-1980');
      $specialPrice = false;
      $bookingEngine = new BookingEngine($this->em, $this->configManager);
      $result = $bookingEngine->priceCalculator($type, $birthdate, $specialPrice);

      $this->assertEquals(8, $result);

      echo  '.
      .
      PROCEDURE TEST => SERVICE BookingEngine -> Test Fonction priceCalculator() :
      CONNEXION AU SERVICE Surikat\BookingBundle\Service\BookingEngine::priceCalculator = OK
      -> $type = halfDay -- $birthdate =  31-12-1980 ---  $specialPrice =  false  ---
      -> assertEquals(8)
      .
      ';
    }

  public function testValidateBooking()
    {
      $booking = new Booking();
      $ticket = new Ticket();
      $ticket
        ->setBirthdate(new \DateTime('10-11-1984'))
        ->setSpecialPrice(false)
        ->setBooking($booking);
      $booking
        ->setBookingFor(new \DateTime('10-11-2018'))
        ->setType('day')
        ->addTicket($ticket);

//      $validateBooking = $this->bookingEngine->validateBooking($booking);

//      $this->assertEquals(true, $validateBooking->validate());

    }



}
