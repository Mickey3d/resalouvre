<?php
// tests/Surikat/BookingBundle/Services/ConfigManagerTest.php

namespace tests\Surikat\BookingBundle\Services;

use Surikat\BookingBundle\Services\ConfigManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;


class ConfigManagerTest extends KernelTestCase
{

  protected $em;

  /**
   * {@inheritDoc}
   */
  protected function setUp()
  {
    self::bootKernel();
    $this->em = static::$kernel->getContainer()
          ->get('doctrine')
          ->getManager();
  }

  protected function tearDown()
  {
      parent::tearDown();
      $this->em->close();
      $this->em = null;
  }


    /**
     * Vérifie si le type Journée est disponible pour le jour en cours
     *
     * @param date $date
     * @return bool
     */
  public function testCheckForDayType()
  {
    $configManager = new configManager($this->em);
    $timeNow = new \Datetime('now');
    $result = $configManager->checkForDayType();

    $this->assertEquals(false, $result);

    echo  '.
    .
    PROCEDURE TEST => SERVICE ConfigManager -> Test Fonction checkForDaylyHourLimit() :
    CONNEXION AU SERVICE Surikat\BookingBundle\Service\ConfigManager::checkForDaylyHourLimit = OK
    -> Heure actuelle -- Heure Limite pour Ticket Journée : 14h00   ---
    .
    ';
  }

  public function testCheckForDaylyHourLimit()
  {
    $configManager = new configManager($this->em);
    $timeNow = new \Datetime('now');
    $result = $configManager->checkForDaylyHourLimit();

    $this->assertEquals(false, $result);

    echo  '.
    .
    PROCEDURE TEST => SERVICE ConfigManager -> Test Fonction checkForDaylyHourLimit() :
    CONNEXION AU SERVICE Surikat\BookingBundle\Service\ConfigManager::checkForDaylyHourLimit = OK
     ---   Heure actuelle  -- Heure Limite pour reservation du jour (fermeture du Musée) : 19h00   ---
     .
     .
     ';
  }

    /**
     * Vérifie la disponibilité du jour donnée pour la commande des tickets
     *
     * @param date $date
     * @return $availability
     */
  public function testCheckAvailability()
  {
    $configManager = new configManager($this->em);
    $date = '28-12-2019';
    $result = $configManager->checkAvailability($date);

    $this->assertEquals(1000, $result);
    echo  '- PROCEDURE TEST => SERVICE ConfigManager -> Test Fonction checkAvailability() :
    CONNEXION AU SERVICE Surikat\BookingBundle\Service\ConfigManager::checkAvailability = OK
    --> Disponibilité le Samedi 28-12-2018 : '.$result. ' .
    .
    .
    ';

  }

    /**
     * Vérifie si la date ne correspond pas à un jour fermé
     *
     * @param date $date
     * @return bool
     */
  public function testCheckIfClosedDay()
  {
    $configManager = new configManager($this->em);
    $date = '31-12-2018';
    $result = $configManager->checkIfClosedDay($date);
    $this->assertEquals(true, $result);
    $this->assertTrue($result, ' Jour Fermé -> Ce jour est un Jour férié (ou de fermeture exeptionnel) : 31-12-2018 = Fermé');
    echo  '- PROCEDURE TEST => SERVICE ConfigManager -> Test Fonction checkAvailability() :
     CONNEXION AU SERVICE Surikat\BookingBundle\Service\ConfigManager::checkAvailability = OK
    -> Jour Fermé -> Ce jour est un Jour férié (ou de fermeture exeptionnel) : 31-12-2018 = '.$result. ' .
    .
    ';
  }

    /**
     * Vérifie si la date ne correspond pas à un jour de fermeure hebdomadaire
     *
     * @param date $date
     * @return bool
     */
  public function testCheckIfClosedWeekDay()
  {
    $configManager = new configManager($this->em);

    $date = '18-12-2018';

    $result = $configManager->checkIfClosedWeekDay($date);

    $this->assertEquals(true, $result);

    echo  '.
    .
    PROCEDURE TEST => SERVICE ConfigManager -> Test Fonction checkIfClosedWeekDay() :
    CONNEXION AU SERVICE Surikat\BookingBundle\Service\ConfigManager::checkIfClosedWeekDay = OK
    -> Jour Fermé -> Ce jour est un Mardi : 18-12-2018 = '.$result. ' .
    .
    ';

  }


}
