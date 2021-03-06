<?php
// src/Surikat/BookingBundle/Services/ConfigManager.php

namespace Surikat\BookingBundle\Services;


use Surikat\BookingBundle\Entity\Setting;
use Surikat\BookingBundle\Entity\Booking;
use Surikat\BookingBundle\Entity\Ticket;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Config\Definition\Exception\Exception;

class ConfigManager
{

    protected $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    public function createConfig()
    {
        $config = new Setting();
        return $config;
    }

    public function loadConfigByName($configName)
    {
        $em = $this->em;
        $config = $em
          ->getRepository('SurikatBookingBundle:Setting')
          ->findOneByConfigName($configName);
          ;

        return $config;
    }

    public function saveConfig(Setting $config)
    {
        $em = $this->em;
        $em->persist($config);
        $em->flush();
    }

    /**
     * Vérifie si le type Journée est disponible pour le jour en cours
     *
     * @param date $date
     * @return bool
     */
    public function checkForDayType()
    {
        $config = $this->em
          ->getRepository('SurikatBookingBundle:Setting')
          ->findOneByConfigName('config-louvre');
          ;
        $dayTicketHourLimit = $config->getDayTicketHourLimit();
        $dayTicketHourLimit = $dayTicketHourLimit->format(DATE_RFC2822);
        $timeLimit = date("H:i:s",strtotime($dayTicketHourLimit));
        $timeNow = date("H:i:s");

       // dump($timeLimit);die;
        // On compare les horraires, si inferieur retourne true, sinon retourne false
        if($timeNow >= $timeLimit)
        {
          return true;
        }
        else
        {
          return false;
        }
    }

    public function checkForDaylyHourLimit()
    {
        $config = $this->em
          ->getRepository('SurikatBookingBundle:Setting')
          ->findOneByConfigName('config-louvre');
          ;
        $daylyHourLimit = $config->getDaylyHourLimit();
        $daylyHourLimit = $daylyHourLimit->format(DATE_RFC2822);
        $timeLimit = date("H:i:s",strtotime($daylyHourLimit));
        $timeNow = date("H:i:s");

       // dump($timeLimit);die;
        // On compare les horraires, si superieur ou égale retourne true, sinon retourne false
        if($timeNow >= $timeLimit)
        {
          return true;
        }
        else
        {
          return false;
        }
    }

    /**
     * Vérifie la disponibilité du jour donnée pour la commande des tickets
     *
     * @param date $date
     * @return $availability
     */
    public function checkAvailability($date)
    {
      $em = $this->em;
      // On crée les Class Booking et ticket et la variable $date
      $booking = new Booking();
      $ticket = new Ticket();
      $date = new \Datetime($date);
      $date->setTime(00, 00, 00);
      //  date->setFormat('yyyy-mm-dd');
      // On récupère la réservation correspondant à la date
      $bookings = $em
        ->getRepository('SurikatBookingBundle:Booking')
        ->findByBookingFor($date);
      ;
      // On récupère la Configuration via Setting
      $config = $em
        ->getRepository('SurikatBookingBundle:Setting')
        ->findOneByConfigName('config-louvre');
        ;
        // On définie TicketCount, le nombre de tickets déjà reservé pour ce jour
        $ticketCount = 0;

      foreach ($bookings as $booking) {
          $tickets = $booking->getTickets();
          if ($booking->getPaiementStatus() != 'erreur') {
            $count = count($tickets);
            $ticketCount = $ticketCount + $count;
          }
          else {
            $ticketCount = $ticketCount;
          }
      }

      $dailyTicketsLimit = $config->getDailyTicketLimit();
        // dump($dailyTicketsLimit);die;
      $availability =  $dailyTicketsLimit - $ticketCount;

      return $availability;
    }

    /**
     * Vérifie si la date ne correspond pas à un jour fermé
     *
     * @param date $date
     * @return bool
     */
    public function checkIfClosedDay($date)
    {
        $em = $this->em;
        // On récupère la Configuration via Setting
        $config = $em
          ->getRepository('SurikatBookingBundle:Setting')
          ->findOneByConfigName('config-louvre');
          ;
        $closedDays = $config->getClosedDays();
        $dateToCompare = new \Datetime($date);
        // On compare les dates, si égale retourne true, sinon retourne null
        foreach ($closedDays as $closedDay) {
          $closedDay = new \Datetime($closedDay);
      //    dump($closedDay);die;
          if ($closedDay  == $dateToCompare) {
            return true;
          }
        }
    }

    /**
     * Vérifie si la date ne correspond pas à un jour de fermeure hebdomadaire
     *
     * @param date $date
     * @return bool
     */
    public function checkIfClosedWeekDay($date)
    {
        $em = $this->em;
        // On récupère la Configuration via Setting
        $config = $em
          ->getRepository('SurikatBookingBundle:Setting')
          ->findOneByConfigName('config-louvre');
          ;
        $closedWeekDays = $config->getClosedWeekDays();
        $dateToCompare = date('w', strtotime($date));
        // On compare les dates, si égale retourne true, sinon retourne null
        foreach ($closedWeekDays as $closedWeekDay) {
          if ($closedWeekDay  == $dateToCompare) {
            return true;
          }
        }

    }

    public function loadConfigByDate($date)
    {
        $em = $this->em;
        // On récupère la Configuration via Setting
        $config = $em
          ->getRepository('SurikatBookingBundle:Setting')
          ->findOneByConfigName('config-louvre');
          ;
          // On vérifie si $date n'est pas dans la liste des jours de fermeture
        // Si le jour demandé est un jour fermé on renvois une erreur et une disponibilité 0
        if ($closedDays = $this->checkIfClosedDay($date) || $closedWeekDays = $this->checkIfClosedWeekDay($date) ) {
          $config->setTicketsLimit(0);
          $config->setAvailability(0);
          $config->setDailyTicketLimit(0);
          $error = ' Date non disponible à la réservation
          - Jour de fermeture';
          $config->setErrors($error);
          return $config;
        }


        // On récupère les paramètres de disponibilité via Setting checkAvailability()
        $availability =  $this->checkAvailability($date);
        // dump($availability);die;
        $config->setAvailability($availability);

        if ($availability <= 0)
          {
              $config->setTicketsLimit(0);
              $error = ' Malheureusement cette Date n\'est pas disponible à la réservation
              - Aucune disponibilité pour ce jour';
              $config->setErrors($error);
          }
          elseif ($availability < 10)
          {
            $config->setTicketsLimit(1);
            $message = ' Limitation du nombre de tickets réservables à 1 par réservation
            - Disponibilité réduite pour  ce jour : moins de 10 tickets encore disponible';
            $config->setMessages($message);
          }
          elseif ($availability < 20)
          {
            $config->setTicketsLimit(2);
            $message = ' Limitation du nombre de tickets réservables à 2 par réservation
            - Disponibilité réduite pour  ce jour : moins de 20 tickets encore disponible';
            $config->setMessages($message);
          }
          elseif ($availability < 50)
          {
            $config->setTicketsLimit(5);
            $message = ' Limitation du nombre de tickets réservables à 5 par réservation
            - Disponibilité réduite pour  ce jour : moins de 50 tickets encore disponible';
            $config->setMessages($message);
          }
          else
          {
            $config->setTicketsLimit(30);
            $message = ' Limitation du nombre de tickets réservables à 30 par réservation
            - Disponibilité maximal pour une réservation';
            $config->setMessages($message);
          }

          return $config;
    }

  /**
   *
   * @param string $status
   */
  public function bookingPurgeByStatus($status)
  {
    $booking = new Booking();
    $bookings = $this->em
      ->getRepository('SurikatBookingBundle:Booking')
      ->findByPaiementStatus($status);
    ;
    foreach ($bookings as $booking) {
      $this->em->remove($booking);
      $this->em->flush();
    }
  }

  public function bookingPurgeByDate($date)
  {
    $repository = $this->em
      ->getDoctrine()
      ->getManager()
      ->getRepository('SurikatBookingBundle:Booking')
    ;
    $bookings = $repository->getBookingBefore($date);

    foreach ($bookings as $booking) {
      $this->em->remove($booking);
      $this->em->flush();
    }
  }


}
