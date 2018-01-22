<?php

// src/Surikat/BookingBundle/Controller/HomeController.php

namespace Surikat\BookingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends Controller
{
  public function indexAction()
  {
    $content = $this->get('templating')->render('SurikatBookingBundle:Default:index.html.twig');

    return new Response($content);
  }

  public function legalsAction()
  {
    return $this->render('SurikatBookingBundle:Default:legals.html.twig', array( ));
  }
}
