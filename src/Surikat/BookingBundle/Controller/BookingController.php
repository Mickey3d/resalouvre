<?php

namespace Surikat\BookingBundle\Controller;

use Surikat\BookingBundle\Entity\Booking;
use Surikat\BookingBundle\Form\BookingType;
use Surikat\BookingBundle\Entity\Setting;
use Surikat\BookingBundle\Form\SettingType;
use Surikat\BookingBundle\Entity\Ticket;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class BookingController extends Controller
{



    public function BookingAction()
    {

      return $this->render('SurikatBookingBundle:Booking:index.html.twig', array(
          // ... Affichage du moteur de recherche la réservation et des tickets
      ));
/*
       TODO -> Affiche l'application SurikatBookingBundle-API
        */
    }

    public function NewBookingAction(Request $request)
    {
      // On crée les objets Booking et tickets
      $booking = new Booking();
      $ticket = new Ticket();
      // On crée l'objet form
      $form   = $this->createForm(BookingType::class, $booking);
      $random = random_int(1, 9999999999999);

      if ($request->isMethod('POST') && $form->handleRequest($request)->isValid())
      {
        //    dump($form);die;
        $tickets =  $booking->getTickets();
        $em = $this->getDoctrine()->getManager();
        $booking->setCode("codigo".$random);
        $booking->setName('Paul');
        $booking->setLastName('Watson');
        $booking->setEmail('pololepolo@aul.zoc');
        $booking->setPaiementStatus('En Cours');
        $booking->setTotalPrice('20');
        $em->persist($booking);

        foreach ($tickets as $ticket) {

          $ticket->setCode('TICK'.$random);
          $booking->addTicket($ticket);
          $em->persist($ticket);
        }

        $em->flush();
        $request->getSession()->getFlashBag()->add('success', 'Réservation bien enregistrée.');
        return $this->redirectToRoute('_new_booking');
      }
      // On passe la méthode createView() du formulaire à la vue afin qu'elle puisse afficher le formulaire toute seule
      return $this->render('SurikatBookingBundle:Booking:new_booking.html.twig', array(
        'form' => $form->createView(),
      ));
      /* TODO
        */
  }

    public function ShowAction()
    {
        return $this->render('SurikatBookingBundle:Booking:show.html.twig', array('ticketsList'=>array()
            // ... Affichage du moteur de recherche la réservation et des tickets
        ));

        /* TODO -> Affiche le formulaire de recherche de réservation ou de ticket (Option selectionnable)

        SI $POST->isValid
        $searchType = Type de la recherche -> 2 options : Réservation ou Ticket
        $content = Contenu de la page

        Si $searchType = Réservation
        Si $bookingCode->isValid     &   identifiants (nom, prénom) -> OK
        Confirmation
        $content = Reservation sélectionnée

        return $this->render('SurikatBookingBundle:Booking:show_booking.html.twig', array(

        ));

        Si $searchType = Ticket
        Si $ticketCode->isValid     &   identifiants (nom, prénom,date de naissance) -> OK
        Confirmation
        $content = Ticket sélectionné

        return $this->render('SurikatBookingBundle:Booking:show_ticket.html.twig', array(

        ));

          */
    }

    public function showBookingAction($code)
    {
      $booking = new Booking;
      $em = $this->getDoctrine()->getManager();
      // On récupère la réservation correspondant au code
      $booking = $em
        ->getRepository('SurikatBookingBundle:Booking')
        ->findByCode($code);
      ;
      // dump($booking);die;
      /*
      $booking = new Booking();
      $booking
      ->setName($code)
      ->setLastName('Doe')
      ;
      */
      $data = $this->get('jms_serializer')->serialize($booking, 'json');

      $response = new Response($data);
      $response->headers->set('Content-Type', 'application/json');

      return $response;

    }

    public function ValidateBookingAction()
    {
        return $this->render('SurikatBookingBundle:Booking:show.html.twig', array('ticketsList'=>array()
            // ... Validation de la reservation et des ticket
        ));

        /* TODO ->
          */
    }

    public function SaveBookingAction(Request $request)
    {
      $data = $request->getContent();
      $booking = $this->get('jms_serializer')->deserialize($data, 'Surikat\BookingBundle\Entity\Booking', 'json');


      return new Response('OK for Test', Response::HTTP_CREATED);
        /* TODO ->
          */
    }

    public function CheckForAvailabiliyAction($date)
    {

        // On crée les Class Booking et ticket et availabilityTest
        $booking = new Booking();
        $ticket = new Ticket();
        $maDate = new \Datetime($date);
        $maDate->setDate('2013' , '01', '01');
        $em = $this->getDoctrine()->getManager();
        // On récupère la réservation correspondant à la date
        $bookings = $em
          ->getRepository('SurikatBookingBundle:Booking')
          ->findByBookingFor($maDate);
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

        if ($availability < 50)
        {
          $config->setTicketsLimit(5);
          $config->setMessages('Code: LI02',
          'Information: Limitation du nombre de tickets réservables à 5 par réservation',
          'Cause : Disponibilité réduite pour  ce jour : moins de 50 tickets encore disponible');
        }
        elseif ($availability < 20)
        {
          $config->setTicketsLimit(2);
          $config->setMessages('Code: LI03',
          'Information: Limitation du nombre de tickets réservables à 2 par réservation',
          'Cause : Disponibilité réduite pour  ce jour : moins de 20 tickets encore disponible');
        }
        elseif ($availability < 10)
        {
          $config->setTicketsLimit(1);
          $config->setMessages(array('Code: LI04',
          'Information: Limitation du nombre de tickets réservables à 1 par réservation',
          'Cause : Disponibilité réduite pour  ce jour : moins de 10 tickets encore disponible'));
        }
        elseif ($availability <= 0)
        {
          $config->setTicketsLimit(0);
          $config->setErrors(array('Code erreur: error_LI02',
          'Information: Date non disponible à la réservation',
          'Cause : Aucune disponibilité pour ce jour'));
        }
        else
        {
          $config->setTicketsLimit(30);
          $config->setMessages(array('Code: LI04
          Information: Limitation du nombre de tickets réservables à 1 par réservation
          Cause : Disponibilité réduite pour  ce jour : moins de 10 tickets encore disponible'));
          $config->setMessages(array('Code: LI04',
          'Information: Limitation du nombre de tickets réservables à 1 par réservation',
          'Cause : Disponibilité réduite pour  ce jour : moins de 10 tickets encore disponible'));
        }

        $data = $this->get('jms_serializer')->serialize($config, 'json');

        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    public function DeleteBooking($code)
    {
        return $this->render('SurikatBookingBundle:Booking:show.html.twig', array('ticketsList'=>array()
            // ... Supprime une reservation de la base de donnée
        ));

        /* TODO -> Affiche le formulaire de recherche de réservation ou de ticket (Option selectionnable)
          */
    }

    public function ConfigBookingAction(Request $request)
    {
      // On se connecte à l'entity manager
      $em = $this->getDoctrine()->getManager();
      // On récupère l'objet Setting correspondant a configName
      $setting = $em
        ->getRepository('SurikatBookingBundle:Setting')
        ->findOneByConfigName('config-louvre');
      ;
      // On crée l'objet form
      $form   = $this->createForm(SettingType::class, $setting);

      if ($request->isMethod('POST') && $form->handleRequest($request)->isValid())
      {
        //  dump($form);die;
        $em = $this->getDoctrine()->getManager();
        $em->persist($setting);
        $em->flush();
        $request->getSession()->getFlashBag()->add('success', 'Configuration bien enregistrée.');
        return $this->redirectToRoute('admin_booking_setting');
      }
      // On passe la méthode createView() du formulaire à la vue afin qu'elle puisse afficher le formulaire toute seule
      return $this->render('SurikatBookingBundle:Booking:setting.html.twig', array(
        'form' => $form->createView(),
      ));
      /* TODO ->
        */
  }

}
