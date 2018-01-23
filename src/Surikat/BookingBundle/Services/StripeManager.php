<?php
// src/Surikat/BookingBundle/Services/StripeManager.php

namespace Surikat\BookingBundle\Services;


use Surikat\BookingBundle\Entity\Setting;
use Surikat\BookingBundle\Entity\Booking;
use Surikat\BookingBundle\Entity\Ticket;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Config\Definition\Exception\Exception;

class StripeManager
{

    protected $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }




    public function stripeBookingCharge(Booking $booking, $totalPrice, $token)
    {

        $config = $this->em
          ->getRepository('SurikatBookingBundle:Setting')
          ->findOneByConfigName('config-louvre');
          ;
        $stripeKey = $config->getStripeApiKey();
        // Set your secret key: remember to change this to your live secret key in production
        // See your keys here: https://dashboard.stripe.com/account/apikeys
        \Stripe\Stripe::setApiKey($stripeKey);
        // Create a charge: this will charge the user's card
        try {
            \Stripe\Charge::create(array(
                "amount" => $totalPrice * 100, // Amount 
                "currency" => "eur",
                "source" => $token,
                "description" => "Paiement Stripe - Billeterie du Louvre - Reservation",
            ));
            $booking->setPaiementStatus('confirmé');
        } catch(\Stripe\Error\Card $e) {
            // The card has been declined
            $booking->setPaiementStatus('erreur');
        }
        return $booking;
    }
}
