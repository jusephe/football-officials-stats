<?php

namespace App\Tests\Site\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class SiteControllerTest extends WebTestCase
{
    private $client = null;

    public function setUp()
    {
        $this->client = static::createClient();
    }

    public function testIndex()
    {
        $this->client->request('GET', '/');

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

    public function testAbout()
    {
        $crawler = $this->client->request('GET', '/o-projektu');

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame('O projektu', $crawler->filter('h1')->text());
    }

    public function testPrebor()
    {
        $crawler = $this->client->request('GET', '/prebor');

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame('Přebor', $crawler->filter('h1')->text());
    }

    public function testATrida()
    {
        $crawler = $this->client->request('GET', '/a-trida');

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame('1.A třída', $crawler->filter('h1')->text());
    }

    public function testOfficials()
    {
        $crawler = $this->client->request('GET', '/rozhodci');

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame('Rozhodčí', $crawler->filter('h1')->text());
    }

    public function testAssessors()
    {
        $crawler = $this->client->request('GET', '/delegati');

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame('Delegáti', $crawler->filter('h1')->text());
    }

}
