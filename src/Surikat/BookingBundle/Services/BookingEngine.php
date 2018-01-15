<?php
// src/Surikat/BookingBundle/Services/BookingEngine.php

namespace Surikat\BookingBundle\Services;

use Surikat\BookingBundle\Entity\Setting;
use Surikat\BookingBundle\Entity\Booking;
use Surikat\BookingBundle\Entity\Ticket;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Surikat\BookingBundle\Services\ConfigManager;
use Symfony\Component\Config\Definition\Exception\Exception;

class BookingEngine
{

    protected $em;
    protected $configManager;

    public function __construct(EntityManagerInterface $entityManager, ConfigManager $configManager)
    {
      $this->em = $entityManager;
      $this->configManager = $configManager;
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

    public function validateBooking(Booking $booking)
    {
        // step One - ClosedDays and ClosesWeekDays ValidateBookingAction
        $date = $booking->getBookingFor();
        $date = $date->format(DATE_RFC2822);
        $configManager = $this->configManager;
        $bookingToValidate = $booking;

        if ($closedDays = $configManager->checkIfClosedDay($date) || $closedWeekDays = $configManager->checkIfClosedWeekDay($date) ) {
          $booking->setValidate(false);
          $error = '- Code error_LI01 - Date non disponible à la réservation - Jour de fermeture';
          $booking->setErrors($error);
        //  dump($booking);
          return $booking;
        }
        else {

          $this->loadPrices($booking);

          if ($booking != $bookingToValidate) {
            $booking->setValidate(false);
            $error = '- Code error_PRI01 - Erreur dans la Validation des Prix
            - Les prix fournis par la réservation sont erroné
            - Ceci peut provenir d\'une mise à jour ou d\'une erreur de traitement';
            $booking->setErrors($error);
            return $booking;
          }
          else {
            // On récupère les paramètres de disponibilité via Setting checkAvailability()
            $availability =  $configManager->checkAvailability($date);
            $ticketsCount= count($booking->getTickets());

            if ($availability - $ticketsCount < 0) {

              $booking->setValidate(false);
              $error = '- Code error_DISPO01
              - Erreur dans la Disponibilité des Tickets
              - La disponibilité actuelle est inférieure à votre demande. Il reste '.$availability.' tickets disponibles à la vente et vous en souhaitez '.$ticketsCount;
              $booking->setErrors($error);
              return $booking;
            }
            else {
              $booking->setValidate(true);
              return $booking;
            }
          }

        }
    }


    public function saveBooking(Booking $booking)
    {
        $random = random_int(1, 9999999999999);
        $em = $this->em;
        $booking->setBookedAt(new \DateTime());
        // Génération code de réservation
        if ($booking->getCode() == null)
        {
          $booking->setCode("codigo".$random);
        // $booking->setCode(md5(uniqid(rand(), true)));
        }
        // $booking->setPaiementStatus("en cours");
        $em->persist($booking);
        // Enregistrement des Tickets liés à la réservation
        $tickets =  $booking->getTickets();
        foreach ($tickets as $ticket)
        {
          if ($ticket->getCode() == null)
          {
          $ticket->setCode('TICK'.$random);
          }
          $booking->addTicket($ticket);
          $em->persist($ticket);
        }

        $em->flush();

        return $booking;
    }


}
