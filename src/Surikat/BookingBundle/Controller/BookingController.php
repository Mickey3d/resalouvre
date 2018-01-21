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
      $configName = 'config-louvre';
      $setting = $configManager->loadConfigByName($configName);
      $form   = $this->createForm(SettingType::class, $setting);

      if ($request->isMethod('POST') && $form->handleRequest($request)->isValid())
        {
          $configManager->saveConfig($setting);
          $request->getSession()->getFlashBag()->add('success', 'Configuration bien enregistrée.');
          return $this->redirectToRoute('admin_booking_setting');
        }
        return $this->render('SurikatBookingBundle:Booking:setting.html.twig', array(
          'form' => $form->createView(),
        ));
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

        $configManager = $this->container->get('surikat_booking.configmanager');
        $setting = $configManager->loadConfigByName('config-louvre');
        $dailyHourLimit = $configManager->checkForDaylyHourLimit();
        // On crée les objets Booking et tickets via bookingEngine
        $bookingEngine = $this->container->get('surikat_booking.bookingengine');
        $booking =  $bookingEngine->createBooking();
        $ticket = $bookingEngine->createTicket();
        // On crée l'objet form
        $form   = $this->createForm(BookingType::class, $booking);
//dump($dailyHourLimit);die;
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid())
          {
            $em = $this->getDoctrine()->getManager();
            $booking =  $bookingEngine->loadPrices($booking);
            $booking =  $bookingEngine->validateBooking($booking);
            dump($booking);die;
            if ($booking->getValidate() == true) {
              $booking->setPaiementStatus('en Cours');
              $booking =   $bookingEngine->saveBooking($booking);
              $request->getSession()->getFlashBag()->add('success', 'Réservation bien enregistrée redirection vers la plateforme de paiement.');

              return $this->redirectToRoute('_new_booking', array('code' => $booking->getCode()));
            }
            else {
              $request->getSession()->getFlashBag()->add('warning', $booking->getErrors());
            }
          }
        return $this->render('SurikatBookingBundle:Booking:booking.html.twig', array(
            'form' => $form->createView(),
            'setting' => $setting,
            'dailyHourLimit' => $dailyHourLimit,
        ));
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
              $totalPrice = $booking->getTotalPrice();
              $this->get('surikat_booking.mailsenderengine')->sendMail($booking, $totalPrice);
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
    }

    public function SaveBookingAction(Request $request)
    {
      $data = $request->getContent();
      $booking = $this->get('jms_serializer')->deserialize($data, 'Surikat\BookingBundle\Entity\Booking', 'json');

      return new Response('OK for Test', Response::HTTP_CREATED);
    }

    public function DeleteBooking($code)
    {
        return $this->render('SurikatBookingBundle:Booking:show.html.twig', array('ticketsList'=>array()
            // ... Supprime une reservation de la base de donnée
        ));
    }



}
