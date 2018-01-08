<?php
// src/Surikat/BookingBundle/Services/BookingEngine.php

namespace Surikat\BookingBundle\Services;

use Surikat\BookingBundle\Entity\Setting;
use Surikat\BookingBundle\Entity\Booking;
use Surikat\BookingBundle\Entity\Ticket;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Config\Definition\Exception\Exception;

class BookingEngine
{

    protected $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
      $this->em = $entityManager;
    }

    public function createBooking()
    {
        $booking = new Booking();
        return $booking;
    }

    public function createTicket()
    {
        $ticket = new Ticket();
        return $ticket;
    }

    public function loadPrices(Booking $booking)
    {
        $totalPrice = 0;
        $tickets = $booking->getTickets();
        foreach ($tickets as $ticket) {
            $price = $this->priceCalculator($booking->getType(), $ticket->getBirthdate(), $ticket->getSpecialPrice());
            $ticket->setPrice($price);
            $totalPrice += $ticket->getPrice();
        }

        $booking->setTotalPrice($totalPrice);

        return $booking;
    }

    /**
     * @param int $type
     * @param /Datetime $birthdate
     * @param bool $specialPrice
     * @return float|null
     */
    public function priceCalculator($type, $birthdate, $specialPrice)
    {

      $config = $this->em
        ->getRepository('SurikatBookingBundle:Setting')
        ->findOneByConfigName('config-louvre');
      ;

      // AGE CALCULATOR
        $date = new \DateTime();

        $age = $date->diff($birthdate)->y;

      // PRICE SETTER
        $price = null;
        if ($age < $config->getDiscountCondition()) {
            $price = $config->getDiscount();
        } elseif ($age < $config->getChildPriceCondition()) {
            $price = $config->getChildPrice();
        } elseif ($age < $config->getNormalPriceCondition()) {
            $price = $config->getNormalPrice();
        } elseif ($age >= $config->getSeniorPriceCondition()) {
            $price = $config->getSeniorPrice();
        }
        // SPECIAL TARIF
       if ($specialPrice === true) {
            $price = $config->getSpecialPrice();
        }

        return $price;
    }


    public function saveBooking(Booking $booking)
    {
      $random = random_int(1, 9999999999999);
      $em = $this->em;
      $booking->setBookedAt(new \DateTime());
      // Génération code de réservation
      $booking->setCode("codigo".$random);
      // $booking->setCode(md5(uniqid(rand(), true)));
      $em->persist($booking);
      // Enregistrement des Tickets liés à la réservation
      $tickets =  $booking->getTickets();
      foreach ($tickets as $ticket) {
        $ticket->setCode('TICK'.$random);
        $booking->addTicket($ticket);
        $em->persist($ticket);
      }

      $em->flush();

      return $booking;

    }


}
