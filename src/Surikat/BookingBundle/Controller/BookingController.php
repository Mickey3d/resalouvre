<?php

namespace Surikat\BookingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BookingController extends Controller
{
    public function NewBookingAction()
    {
        return $this->render('SurikatBookingBundle:Booking:new_booking.html.twig', array(
            // ...
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
            // ... Affichage de la réservation et des tickets
        ));

        /* TODO -> Affiche le formulaire de recherche de réservation ou de ticket (Option selectionnable)

        SI $POST->isValid
        $searchType = Type de la recherche -> 2 options : Réservation ou Ticket
        $content = Contenu de la page

        Si $searchType = Réservation
        Si $bookingCode->isValid     &   identifiants (nom, prénom,date de naissance) -> OK
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




}
