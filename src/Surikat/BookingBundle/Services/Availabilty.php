<?php
// src/Surikat/BookingBundle/Services/Availability.php

namespace Surikat\BookingBundle\Services;

use Surikat\BookingBundle\Entity\Setting;


class Availability
{
  private $availability;

  public function __construct($availability)
  {
    $this->availability = $availability;
  }

  /**
   * Vérifie si la date ne correspond pas à un jour fermé
   *
   * @param date $date
   * @return bool
   */
  public function checkIfClosedDay($date)
  {

  }

  /**
   * Vérifie la disponibilité du jour donnée pour la commande des tickets
   *
   * @param date $date
   * @return $availability
   */
  public function checkAvailability($date)
  {


  }
}
