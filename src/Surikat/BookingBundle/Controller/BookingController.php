<?php

namespace Surikat\BookingBundle\Controller;

use Surikat\BookingBundle\Entity\Booking as Booking;
use Surikat\BookingBundle\Form\BookingType;
use Surikat\BookingBundle\Entity\Setting;
use Surikat\BookingBundle\Form\SettingType;
use Surikat\BookingBundle\Entity\Ticket;
use Surikat\BookingBundle\Services\BookingEngine;
use Surikat\BookingBundle\Services\ConfigManager;
use Surikat\BookingBundle\Services\StripeManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response as Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;


class BookingController extends Controller
{
    // Configuration de l'application
    public function ConfigBookingAction(Request $request)
    {
      // On vérifie que l'utilisateur dispose bien du rôle ROLE_ADMIN
      if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
        // Sinon on déclenche une exception « Accès interdit »
        throw new AccessDeniedException('Accès limité aux Administrateur.');
      }
      $configManager = $this->container->get('surikat_booking.configmanager');
      $setting = $configManager->loadConfigByName('config-louvre');
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
        $bookingEngine = $this->container->get('surikat_booking.bookingengine');
        $booking =  $bookingEngine->createBooking();
        $ticket = $bookingEngine->createTicket();
        $form   = $this->createForm(BookingType::class, $booking);
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid())
          {
            $em = $this->getDoctrine()->getManager();
            $booking =  $bookingEngine->loadPrices($booking);
            $booking =  $bookingEngine->validateBooking($booking);
            if ($booking->getValidate() == true) {
              $booking->setPaiementStatus('en Cours');
              $booking =   $bookingEngine->saveBooking($booking, 1);
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
      $stripeManager = $this->container->get('surikat_booking.stripemanager');

      if($request->isMethod('POST')) {
        $totalPrice = $booking->getTotalPrice();
        $token = $request->get('stripeToken');
        $booking = $stripeManager->stripeBookingCharge($booking, $totalPrice, $token);

        if ($booking->getPaiementStatus() == "confirmé") {
          $booking =   $bookingEngine->saveBooking($booking, 0);
          $this->get('surikat_booking.mailsenderengine')->sendMail($booking, $totalPrice);
          $this->addFlash("success","Paiement validé, Réservation confirmé !");
          return $this->redirectToRoute('_new_booking', array('code' => $booking->getCode()));
        }else {
          $this->addFlash("warning","Erreur lors de la transaction");
          $booking =   $bookingEngine->saveBooking($booking, 0);
          return $this->redirectToRoute('_new_booking', array('code' => $booking->getCode()));
        }
      }
        return $this->render('SurikatBookingBundle:Booking:new_booking.html.twig', array(
            'booking' => $booking
          ));
    }

    // @var string $code return $booking   => page test to find  a booking
    public function ShowAction(Request $request)
    {
        $bookingEngine = $this->container->get('surikat_booking.bookingengine');

        if ($request->isMethod('POST'))
          {
            $email = $request->request->get('email');
            $code = $request->request->get('code');
            $em = $this->getDoctrine()->getManager();
            $booking = $em
                ->getRepository('SurikatBookingBundle:Booking')
                ->findOneByCode($code);
                ;
            if (isset($booking) && $booking->getEmail() == $email) {
              $request->getSession()->getFlashBag()->add('success', 'Votre Réservation a été trouvé.');
              return $this->redirectToRoute('_new_booking', array('code' => $booking->getCode()));
            }
            else {
              $request->getSession()->getFlashBag()->add('warning', 'Aucune correspondance en base de donnée. Vérifiez votre code ou le mail fourni pour la réservation');
            }
          }
        return $this->render('SurikatBookingBundle:Booking:show.html.twig', array(
        ));
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

    public function DeleteBookingAction(Request $request, Booking $booking)
    {
      $em = $this->getDoctrine()->getManager();
      $form = $this->get('form.factory')->create();
      if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
          $em->remove($booking);
          $em->flush();
          $request->getSession()->getFlashBag()->add('info', "La Réservation a bien été annulée.");
          return $this->redirectToRoute('surikat_booking_homepage');
      }

        return $this->render('SurikatBookingBundle:Booking:delete.html.twig', array(
          'booking' => $booking,
          'form'   => $form->createView(),
        ));
    }



}
