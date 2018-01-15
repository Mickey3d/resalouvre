<?php
// src/Surikat/BookingBundle/Services/MailSenderService.php

namespace Surikat\BookingBundle\Services;

use Surikat\BookingBundle\Entity\Booking;
use Symfony\Component\Templating\EngineInterface;


class MailSenderService
{

    protected $mailer;
    protected $templating;
    private $from = "mickey.welensky@gmail.com";
    private $reply = "mickey.welensky@gmail.com";
    private $name = "Resa_Louvre - Un ticket pour le Louvre";

    public function __construct(\Swift_Mailer $mailer, EngineInterface $templating)
    {
        $this->mailer = $mailer;
        $this->templating = $templating;
    }
    protected function sendMessage($to, $subject, $body, $totalPrice)
    {
        $mail = \Swift_Message::newInstance();
        $mail
            ->setFrom($this->from,$this->name)
            ->setTo($to)
            ->setSubject($subject)
            ->setBody( $this->templating->render('SurikatBookingBundle:Mail:confirmation_mail.html.twig', array('booking' => $body, 'orderAmount'=> $totalPrice)))
          //  ->setReplyTo($this->reply,$this->name)
            ->setContentType('text/html');
        $this->mailer->send($mail);
    }
    public function sendMail(Booking $booking, $totalPrice){
        $subject = "Reservation " . $booking->getCode() . " confirmation";
      //  $to = $booking->getEmail();
        $to = 'mickey.welensky@gmail.com';
        $this->sendMessage($to, $subject, $booking, $totalPrice);
    }


}
