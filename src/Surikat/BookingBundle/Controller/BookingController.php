<?php

namespace Surikat\BookingBundle\Controller;

use Surikat\BookingBundle\Entity\Booking;
use Surikat\BookingBundle\Form\BookingType;
use Surikat\BookingBundle\Entity\Ticket;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class BookingController extends Controller
{
    public function NewBookingAction(Request $request)
    {
        // On crée l'objet Booking
        $booking = new Booking();
        // On crée l'objet form
        $form   = $this->createForm(BookingType::class, $booking);
        // On passe la méthode createView() du formulaire à la vue afin qu'elle puisse afficher le formulaire toute seule
        return $this->render('SurikatBookingBundle:Booking:new_booking.html.twig', array(
          'form' => $form->createView(),
        ));

        /* TODO -> Contrôle de la disponibilité des Tickets de la date choisie
              $ticketAvailableQuantity = (int) Quantité de tickets disponible pour le jour sélectionnée
              $content = Contenu de la page
                      - Si disponibilité > 0  -> $content formulaire de réservation de ticket
                      - Si disponibilité < 20 places -> Affiche un flash message (Variable de SESSION)
                      signalant le nombre de place restante en plus du formulaire de réservation
                      d'un ticket
                      - Si disponibilité des tickets = O -> $content = message
                      invitant à saisir une autre date
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
      $em = $this->getDoctrine()->getManager();

      // On récupère la Reservation $code
      $booking = $em->getRepository('SurikatBookingBundle:Booking')->find($code);



      // On récupère la liste des tickets de cette réservation
      $ticketsList = $em
        ->getRepository('SurikatBookingBundle:Ticket')
        ->findBy(array('booking' => $booking))
      ;

      return $this->render('SurikatBookingBundle:Booking:show_booking.html.twig', array(
        'code'           => $code,
        'booking'           => $booking,
        'ticketsList' => $ticketsList
      ));
    }



}
