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

    public function loadConfigByDate($date)
    {
        $em = $this->em;
        // On crée les Class Booking et ticket et la variable $date
        $booking = new Booking();
        $ticket = new Ticket();
        $date = new \Datetime($date);
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
            $count = count($tickets);
            $ticketCount = $ticketCount + $count;
        }

        $dailyTicketsLimit = $config->getDailyTicketLimit();
          // dump($dailyTicketsLimit);die;
        $availability =  $dailyTicketsLimit - $ticketCount;
        // dump($availability);die;
        $config->setAvailability($availability);

        if ($availability <= 0)
          {
              $config->setTicketsLimit(0);
              $error = array('Code' => 'error_LI02',
              'Information' => 'Date non disponible à la réservation',
              'Cause' => 'Aucune disponibilité pour ce jour');
              $config->setErrors($error);
          }
          elseif ($availability < 10)
          {
            $config->setTicketsLimit(1);
            $message = array('Code' => 'LI04',
            'Information' => 'Limitation du nombre de tickets réservables à 1 par réservation',
            'Cause' => 'Disponibilité réduite pour  ce jour : moins de 10 tickets encore disponible');
            $config->setMessages($message);
          }
          elseif ($availability < 20)
          {
            $config->setTicketsLimit(2);
            $message = array('Code' => 'LI03',
            'Information' => 'Limitation du nombre de tickets réservables à 2 par réservation',
            'Cause' => 'Disponibilité réduite pour  ce jour : moins de 20 tickets encore disponible');
            $config->setMessages($message);
          }
          elseif ($availability < 50)
          {
            $config->setTicketsLimit(5);
            $message = array('Code' => 'LI02',
            'Information' => 'Limitation du nombre de tickets réservables à 5 par réservation',
            'Cause' => 'Disponibilité réduite pour  ce jour : moins de 50 tickets encore disponible');
            $config->setMessages($message);
          }
          else
          {
            $config->setTicketsLimit(30);
            $message = array('Code' => 'LI01',
            'Information' => 'Limitation du nombre de tickets réservables à 30 par réservation',
            'Cause' => 'Disponibilité maximal pour une réservation');
            $config->setMessages($message);
          }

          return $config;
    }

  /**
   * Vérifie si ...
   *
   * @param string $var
   * @return bool
   */
  public function function($var)
  {

  }

}
