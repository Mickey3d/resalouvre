<?php

namespace Surikat\BookingBundle\Controller;

use Surikat\BookingBundle\Entity\Booking as Booking;
use Surikat\BookingBundle\Form\BookingType;
use Surikat\BookingBundle\Entity\Setting;
use Surikat\BookingBundle\Form\SettingType;
use Surikat\BookingBundle\Entity\Ticket;
use Surikat\BookingBundle\Services\BookingEngine;
use Surikat\BookingBundle\Services\ConfigManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response as Response;


class BookingController extends Controller
{
    // Configuration de l'application
    public function ConfigBookingAction(Request $request)
    {
      $configManager = $this->container->get('surikat_booking.configmanager');
      // On récupère l'objet Setting correspondant a configName
      $configName = 'config-louvre';
      $setting = $configManager->loadConfigByName($configName);
      // On crée l'objet form
      $form   = $this->createForm(SettingType::class, $setting);

      if ($request->isMethod('POST') && $form->handleRequest($request)->isValid())
        {
          //  dump($form);die;
          $configManager->saveConfig($setting);
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

    // @var DateTime $date return $config
    // Vérification de la disponibiité pour une date donnée
    public function CheckForAvailabiliyAction($date)
    {

        $configManager = $this->container->get('surikat_booking.configmanager');
        $config = $configManager->loadConfigByDate($date);

        $data = $this->get('jms_serializer')->serialize($config, 'json');

        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    // @param BookingManager $bookingManager
    public function BookingAction(Request $request)
    {
        // On crée les objets Booking et tickets via bookingEngine
        $bookingEngine = $this->container->get('surikat_booking.bookingengine');
        $booking =  $bookingEngine->createBooking();
        $ticket = $bookingEngine->createTicket();
        // On crée l'objet form
        $form   = $this->createForm(BookingType::class, $booking);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid())
          {
            //    dump($form);die;
            $em = $this->getDoctrine()->getManager();
            $booking =  $bookingEngine->loadPrices($booking);
            $booking =  $bookingEngine->validateBooking($booking);
            // dump($booking);die;
            if ($booking->getValidate() == true) {
              $booking->setPaiementStatus('en Cours');
              $booking =   $bookingEngine->saveBooking($booking);
              $request->getSession()->getFlashBag()->add('success', 'Réservation bien enregistrée.');
            //   $booking = $this->get('jms_serializer')->serialize($booking, 'json');
              return $this->redirectToRoute('_new_booking', array('code' => $booking->getCode()));
            }
            else {
              $request->getSession()->getFlashBag()->add('warning', 'Erreur dans la réservation');
            }

          }

          // On passe la méthode createView() du formulaire à la vue afin qu'elle puisse afficher le formulaire toute seule
        return $this->render('SurikatBookingBundle:Booking:booking.html.twig', array(
            'form' => $form->createView(),
        ));
          /* TODO
          */
    }

    /**
     * @param Request $request
     * @param Booking $booking
     * @return Response
     * @param BookingManager $bookingManager
     */
    public function NewBookingAction(Request $request, Booking $booking)
    {

      $bookingEngine = $this->container->get('surikat_booking.bookingengine');

      if($request->isMethod('POST')) {

        $token = $request->get('stripeToken');
        \Stripe\Stripe::setApiKey("sk_test_XueAARsUA60HUaofMtPQxkW8");

        // Create a charge: this will charge the user's card
          try {
              $charge = \Stripe\Charge::create(array(
                  "amount" => $booking->getTotalPrice() * 100, // Amount in cents
                  "currency" => "eur",
                  "source" => $token,
                  "description" => "Paiement Stripe - Resaloure Reservation"
              ));
              $booking->setPaiementStatus('confirmé');
              $booking =   $bookingEngine->saveBooking($booking);
              $this->addFlash("success","Paiement validé, Réservation confirmé !");
              return $this->redirectToRoute('_new_booking', array('code' => $booking->getCode()));
              }
              catch(\Stripe\Error\Card $e) {
              $this->addFlash("warning","Erreur lors de la transaction :(");
              $booking->setPaiementStatus('erreur');
              $booking =   $bookingEngine->saveBooking($booking);
              return $this->redirectToRoute('_new_booking', array('code' => $booking->getCode()));
              // The card has been declined
            }

        }

        return $this->render('SurikatBookingBundle:Booking:new_booking.html.twig', array(
            // ...
            'booking' => $booking
          ));





          /*
          TODO -> Affiche l'application SurikatBookingBundle-API
          */
    }

    // @var string $code return $booking   => page test to find  a booking
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

    // @var string $code return $booking
    public function showBookingAction($code)
    {
      $em = $this->getDoctrine()->getManager();
      // On récupère la réservation correspondant au code
      $booking = $em
        ->getRepository('SurikatBookingBundle:Booking')
        ->findByCode($code);
      ;
      // dump($booking);die;
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

    public function DeleteBooking($code)
    {
        return $this->render('SurikatBookingBundle:Booking:show.html.twig', array('ticketsList'=>array()
            // ... Supprime une reservation de la base de donnée
        ));

        /* TODO -> Affiche le formulaire de recherche de réservation ou de ticket (Option selectionnable)
          */
    }



}
