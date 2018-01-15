<?php

namespace Surikat\BookingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Setting
 *
 * @ORM\Table(name="setting")
 * @ORM\Entity(repositoryClass="Surikat\BookingBundle\Repository\SettingRepository")
 */
class Setting
{
    /**
     * @var int
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="configName", type="string", length=255)
     */
    private $configName;

    /**
     * @var int
     * @ORM\Column(name="dailyTicketLimit", type="integer")
     */
    private $dailyTicketLimit;

    /**
     * @var time
     * @ORM\Column(name="dayTicketHourLimit", type="time", nullable=true)
     */
    private $dayTicketHourLimit;

    /**
     * @var int
     * @ORM\Column(name="discount", type="integer")
     */
    private $discount;

    /**
     * @var int
     * @ORM\Column(name="discountCondition", type="integer")
     */
    private $discountCondition;

    /**
     * @var int
     * @ORM\Column(name="childPrice", type="integer")
     */
    private $childPrice;

    /**
     * @var int
     * @ORM\Column(name="childPriceCondition", type="integer")
     */
    private $childPriceCondition;

    /**
     * @var int
     * @ORM\Column(name="normalPrice", type="integer")
     */
    private $normalPrice;

    /**
     * @var int
     * @ORM\Column(name="normalPriceCondition", type="integer")
     */
    private $normalPriceCondition;

    /**
     * @var int
     * @ORM\Column(name="seniorPrice", type="integer")
     */
    private $seniorPrice;

    /**
     * @var int
     * @ORM\Column(name="seniorPriceCondition", type="integer")
     */
    private $seniorPriceCondition;

    /**
     * @var int
     * @ORM\Column(name="specialPrice", type="integer")
     */
    private $specialPrice;

    /**
     * @var array|null
     * @ORM\Column(name="closedDays", type="array", nullable=true)
     */
    private $closedDays;

    /**
     * @var array|null
     * @ORM\Column(name="closedWeekDays", type="array", nullable=true)
     */
    private $closedWeekDays;

    /**
     * @var int|null
     * @ORM\Column(name="availability", type="integer", nullable=true)
     */
    private $availability;

    /**
     * @var int
     * @ORM\Column(name="ticketsLimit", type="integer", nullable=true)
     */
    private $ticketsLimit;

    /**
     * @var string|null
     * @ORM\Column(name="messages", type="string", nullable=true)
     */
    private $messages;

    /**
     * @var string|null
     * @ORM\Column(name="errors", type="string", nullable=true)
     */
    private $errors;


    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set dailyTicketLimit.
     *
     * @param int $dailyTicketLimit
     *
     * @return Setting
     */
    public function setDailyTicketLimit($dailyTicketLimit)
    {
        $this->dailyTicketLimit = $dailyTicketLimit;

        return $this;
    }

    /**
     * Get dailyTicketLimit.
     *
     * @return int
     */
    public function getDailyTicketLimit()
    {
        return $this->dailyTicketLimit;
    }

    /**
     * Set dayTicketHourLimit.
     *
     * @param int $dayTicketHourLimit
     *
     * @return Setting
     */
    public function setDayTicketHourLimit($dayTicketHourLimit)
    {
        $this->dayTicketHourLimit = $dayTicketHourLimit;

        return $this;
    }

    /**
     * Get dayTicketHourLimit.
     *
     * @return int
     */
    public function getDayTicketHourLimit()
    {
        return $this->dayTicketHourLimit;
    }

    /**
     * Set discount.
     *
     * @param int $discount
     *
     * @return Setting
     */
    public function setDiscount($discount)
    {
        $this->discount = $discount;

        return $this;
    }

    /**
     * Get discount.
     *
     * @return int
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * Set discountCondition.
     *
     * @param int $discountCondition
     *
     * @return Setting
     */
    public function setDiscountCondition($discountCondition)
    {
        $this->discountCondition = $discountCondition;

        return $this;
    }

    /**
     * Get discountCondition.
     *
     * @return int
     */
    public function getDiscountCondition()
    {
        return $this->discountCondition;
    }

    /**
     * Set childPrice.
     *
     * @param int $childPrice
     *
     * @return Setting
     */
    public function setChildPrice($childPrice)
    {
        $this->childPrice = $childPrice;

        return $this;
    }

    /**
     * Get childPrice.
     *
     * @return int
     */
    public function getChildPrice()
    {
        return $this->childPrice;
    }

    /**
     * Set childPriceCondition.
     *
     * @param int $childPriceCondition
     *
     * @return Setting
     */
    public function setChildPriceCondition($childPriceCondition)
    {
        $this->childPriceCondition = $childPriceCondition;

        return $this;
    }

    /**
     * Get childPriceCondition.
     *
     * @return int
     */
    public function getChildPriceCondition()
    {
        return $this->childPriceCondition;
    }

    /**
     * Set normalPrice.
     *
     * @param int $normalPrice
     *
     * @return Setting
     */
    public function setNormalPrice($normalPrice)
    {
        $this->normalPrice = $normalPrice;

        return $this;
    }

    /**
     * Get normalPrice.
     *
     * @return int
     */
    public function getNormalPrice()
    {
        return $this->normalPrice;
    }

    /**
     * Set normalPriceCondition.
     *
     * @param int $normalPriceCondition
     *
     * @return Setting
     */
    public function setNormalPriceCondition($normalPriceCondition)
    {
        $this->normalPriceCondition = $normalPriceCondition;

        return $this;
    }

    /**
     * Get normalPriceCondition.
     *
     * @return int
     */
    public function getNormalPriceCondition()
    {
        return $this->normalPriceCondition;
    }

    /**
     * Set seniorPrice.
     *
     * @param int $seniorPrice
     *
     * @return Setting
     */
    public function setSeniorPrice($seniorPrice)
    {
        $this->seniorPrice = $seniorPrice;

        return $this;
    }

    /**
     * Get seniorPrice.
     *
     * @return int
     */
    public function getSeniorPrice()
    {
        return $this->seniorPrice;
    }

    /**
     * Set seniorPriceCondition.
     *
     * @param int $seniorPriceCondition
     *
     * @return Setting
     */
    public function setSeniorPriceCondition($seniorPriceCondition)
    {
        $this->seniorPriceCondition = $seniorPriceCondition;

        return $this;
    }

    /**
     * Get seniorPriceCondition.
     *
     * @return int
     */
    public function getSeniorPriceCondition()
    {
        return $this->seniorPriceCondition;
    }

    /**
     * Set specialPrice.
     *
     * @param int $specialPrice
     *
     * @return Setting
     */
    public function setSpecialPrice($specialPrice)
    {
        $this->specialPrice = $specialPrice;

        return $this;
    }

    /**
     * Get specialPrice.
     *
     * @return int
     */
    public function getSpecialPrice()
    {
        return $this->specialPrice;
    }

    /**
     * Set closedDays.
     *
     * @param array|null $closedDays
     *
     * @return Setting
     */
    public function setClosedDays($closedDays = null)
    {
        $this->closedDays = $closedDays;

        return $this;
    }

    /**
     * Get closedDays.
     *
     * @return array|null
     */
    public function getClosedDays()
    {
        return $this->closedDays;
    }

    /**
     * Set closedWeekDays.
     *
     * @param array|null $closedWeekDays
     *
     * @return Setting
     */
    public function setClosedWeekDays($closedWeekDays = null)
    {
        $this->closedWeekDays = $closedWeekDays;

        return $this;
    }

    /**
     * Get closedWeekDays.
     *
     * @return array|null
     */
    public function getClosedWeekDays()
    {
        return $this->closedWeekDays;
    }

    /**
     * Set availability.
     *
     * @param int|null $availability
     *
     * @return Setting
     */
    public function setAvailability($availability = null)
    {
        $this->availability = $availability;

        return $this;
    }

    /**
     * Get availability.
     *
     * @return int|null
     */
    public function getAvailability()
    {
        return $this->availability;
    }

    /**
     * Set ticketsLimit.
     *
     * @param int $ticketsLimit
     *
     * @return Setting
     */
    public function setTicketsLimit($ticketsLimit)
    {
        $this->ticketsLimit = $ticketsLimit;

        return $this;
    }

    /**
     * Get ticketsLimit.
     *
     * @return int
     */
    public function getTicketsLimit()
    {
        return $this->ticketsLimit;
    }

    /**
     * Set messages.
     *
     * @param string|null $messages
     *
     * @return Setting
     */
    public function setMessages($messages = null)
    {
        $this->messages = $messages;

        return $this;
    }

    /**
     * Get messages.
     *
     * @return array|null
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * Set errors.
     *
     * @param string|null $errors
     *
     * @return Setting
     */
    public function setErrors($errors = null)
    {
        $this->errors = $errors;

        return $this;
    }

    /**
     * Get errors.
     *
     * @return string|null
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Set configName.
     *
     * @param string $configName
     *
     * @return Setting
     */
    public function setConfigName($configName)
    {
        $this->configName = $configName;

        return $this;
    }

    /**
     * Get configName.
     *
     * @return string
     */
    public function getConfigName()
    {
        return $this->configName;
    }

}
