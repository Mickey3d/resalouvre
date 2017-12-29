<?php

namespace Surikat\BookingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * Booking
 *
 * @ORM\Table(name="booking")
 * @ORM\Entity(repositoryClass="Surikat\BookingBundle\Repository\BookingRepository")
 */
class Booking
{

     /**
     * @ORM\OneToMany(targetEntity="Surikat\BookingBundle\Entity\Ticket", mappedBy="booking")
     */
    private $tickets;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255, unique=true)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="lastName", type="string", length=255)
     */
    private $lastName;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="bookingDate", type="datetime")
     */
    private $bookingDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="bookedDate", type="datetime")
     */
    private $bookedDate;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * @var array
     *
     * @ORM\Column(name="ticketsList", type="array")
     */
    private $ticketsList;

    /**
     * @var int
     *
     * @ORM\Column(name="totalPrice", type="integer")
     */
    private $totalPrice;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="string", length=255, nullable=true)
     */
    private $comment;

    /**
     * @var string
     *
     * @ORM\Column(name="paiementStatus", type="string", length=255)
     */
    private $paiementStatus;

    /**
    * Construct bookedDate and tickets
    *
    * @param \DateTime $bookedDate
    *
    * @return Booking
    */

   public function __construct()
   {
       $this->bookedDate = new \Datetime();
       $this->bookingDate = new \Datetime();
       $this->tickets = new ArrayCollection();

   }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return Booking
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Booking
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return Booking
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Booking
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set bookingDate
     *
     * @param \DateTime $bookingDate
     *
     * @return Booking
     */
    public function setBookingDate($bookingDate)
    {
        $this->bookingDate = $bookingDate;

        return $this;
    }

    /**
     * Get bookingDate
     *
     * @return \DateTime
     */
    public function getBookingDate()
    {
        return $this->bookingDate;
    }

    /**
     * Set ticketsList
     *
     * @param array $ticketsList
     *
     * @return Booking
     */
    public function setTicketsList($ticketsList)
    {
        $this->ticketsList = $ticketsList;

        return $this;
    }

    /**
     * Get ticketsList
     *
     * @return array
     */
    public function getTicketsList()
    {
        return $this->ticketsList;
    }

    /**
     * Set totalPrice
     *
     * @param integer $totalPrice
     *
     * @return Booking
     */
    public function setTotalPrice($totalPrice)
    {
        $this->totalPrice = $totalPrice;

        return $this;
    }

    /**
     * Get totalPrice
     *
     * @return int
     */
    public function getTotalPrice()
    {
        return $this->totalPrice;
    }

    /**
     * Set comment
     *
     * @param string $comment
     *
     * @return Booking
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set paiementStatus
     *
     * @param string $paiementStatus
     *
     * @return Booking
     */
    public function setPaiementStatus($paiementStatus)
    {
        $this->paiementStatus = $paiementStatus;

        return $this;
    }

    /**
     * Get paiementStatus
     *
     * @return string
     */
    public function getPaiementStatus()
    {
        return $this->paiementStatus;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return Booking
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
    * set bookedDate
    *
    * @param \DateTime $bookedDate
    *
    * @return Booking
    */
    public function setBookedDate(\Datetime $bookedDate)
    {
      $this->bookeDate = $bookedDate;

      return $this;
    }

     /**
     * Get bookedDate
     *
     * @return \DateTime
     */
    public function getBookedDate()
    {
      return $this->bookedDate;
    }

    /**
     * Add ticket
     *
     * @param \Surikat\BookingBundle\Entity\Ticket $ticket
     *
     * @return Booking
     */
    public function addTicket(\Surikat\BookingBundle\Entity\Ticket $ticket)
    {
        $this->tickets[] = $ticket;

        // On lie la réservation au ticket
        $ticket->setBooking($this);

        return $this;
    }

    /**
     * Remove ticket
     *
     * @param \Surikat\BookingBundle\Entity\Ticket $ticket
     */
    public function removeTicket(\Surikat\BookingBundle\Entity\Ticket $ticket)
    {
        $this->tickets->removeElement($ticket);
    }

    /**
     * Get tickets
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTickets()
    {
        return $this->tickets;
    }
}
