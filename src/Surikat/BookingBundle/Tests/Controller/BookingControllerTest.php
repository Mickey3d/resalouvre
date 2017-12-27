<?php

namespace Surikat\BookingBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BookingControllerTest extends WebTestCase
{
    public function testNewbooking()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/NewBooking');
    }

    public function testShow()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/Show');
    }

}
