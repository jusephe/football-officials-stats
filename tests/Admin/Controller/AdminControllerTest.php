<?php

namespace App\Tests\Admin\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class AdminControllerTest extends WebTestCase
{
    private $client = null;

    public function setUp()
    {
        $this->client = static::createClient();
    }

    public function urlProvider()
    {
        yield ['/admin'];
        yield ['/admin/add-game'];
        yield ['/admin/add-game-form'];
        yield ['/admin/update-stats'];
        yield ['/admin/punishments'];
        yield ['/admin/punishments/add/1'];
        yield ['/admin/nomination-lists'];
        yield ['/admin/nomination-lists/add/2019/Jaro'];
        yield ['/admin/nomination-lists/edit/00000000'];
        yield ['/admin/leagues'];
        yield ['/admin/leagues/create'];
        yield ['/admin/leagues/1/edit'];
        yield ['/admin/teams'];
        yield ['/admin/teams/create'];
        yield ['/admin/teams/1/edit'];
        yield ['/admin/officials'];
        yield ['/admin/officials/create'];
        yield ['/admin/officials/00000000/edit'];
        yield ['/admin/assessors'];
        yield ['/admin/assessors/create'];
        yield ['/admin/assessors/00000000/edit'];
        yield ['/admin/posts'];
        yield ['/admin/posts/create'];
        yield ['/admin/posts/1/edit'];
    }

    /**
     * @dataProvider urlProvider
     */
    public function testPageIsRedirectedToLoginFormIfNotLoggedIn($url)
    {
        $this->client->request('GET', $url);

        $this->assertTrue($this->client->getResponse()->isRedirect('/login'));
    }

    public function testDeletePostIsRedirectedToLoginFormIfNotLoggedIn()
    {
        $this->client->request('POST', '/admin/posts/1/delete');

        $this->assertTrue($this->client->getResponse()->isRedirect('/login'));
    }

    private function logIn()
    {
        $session = $this->client->getContainer()->get('session');

        $firewallName = 'main';
        $firewallContext = 'main';

        $token = new UsernamePasswordToken('admin', null, $firewallName, ['ROLE_ADMIN']);
        $session->set('_security_'.$firewallContext, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }

    public function testAddGame()
    {
        $this->logIn();
        $crawler = $this->client->request('GET', '/admin/add-game');

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame('Přidat zápas', $crawler->filter('h1')->text());

        $game_html = file_get_contents('tests/Admin/test_game.html');

        $crawler = $this->client->submitForm('Odeslat', [
            'form[sourceCode]' => $game_html,
        ]);

        // Přidání soutěže, nebo Přidání týmu, nebo Přidání zápasu
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertStringStartsWith('Přidání ', $crawler->filter('h1')->text());
    }

    public function testAddGameForm()
    {
        $this->logIn();
        $crawler = $this->client->request('GET', '/admin/add-game-form');

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame('Přidání zápasu', $crawler->filter('h1')->text());
    }

    public function testPunishments()
    {
        $this->logIn();
        $crawler = $this->client->request('GET', '/admin/punishments');

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame('Tresty', $crawler->filter('h1')->text());
    }

    public function testNominationLists()
    {
        $this->logIn();
        $crawler = $this->client->request('GET', '/admin/nomination-lists');

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame('Listiny', $crawler->filter('h1')->text());
    }

    public function testLeagues()
    {
        $this->logIn();
        $crawler = $this->client->request('GET', '/admin/leagues');

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame('Soutěže', $crawler->filter('h1')->text());

        $crawler = $this->client->clickLink('+ Přidat novou soutěž');

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame('Přidání soutěže', $crawler->filter('h1')->text());
    }

    public function testTeams()
    {
        $this->logIn();
        $crawler = $this->client->request('GET', '/admin/teams');

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame('Týmy', $crawler->filter('h1')->text());

        $crawler = $this->client->clickLink('+ Přidat nový tým');

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame('Přidání týmu', $crawler->filter('h1')->text());
    }

    public function testOfficials()
    {
        $this->logIn();
        $crawler = $this->client->request('GET', '/admin/officials');

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame('Rozhodčí', $crawler->filter('h1')->text());

        $crawler = $this->client->clickLink('+ Přidat nového rozhodčího');

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame('Přidání rozhodčího', $crawler->filter('h1')->text());
    }

    public function testAssessors()
    {
        $this->logIn();
        $crawler = $this->client->request('GET', '/admin/assessors');

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame('Delegáti', $crawler->filter('h1')->text());

        $crawler = $this->client->clickLink('+ Přidat nového delegáta');

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame('Přidání delegáta', $crawler->filter('h1')->text());
    }

    public function testPosts()
    {
        $this->logIn();
        $crawler = $this->client->request('GET', '/admin/posts');

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame('Novinky', $crawler->filter('h1')->text());

        $crawler = $this->client->clickLink('+ Přidat novinku');

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame('Přidání novinky', $crawler->filter('h1')->text());
    }

    public function testPostFormAndDeletePost()
    {
        $this->logIn();

        // CREATE new post
        $crawler = $this->client->request('GET', '/admin/posts/create');

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame('Přidání novinky', $crawler->filter('h1')->text());

        $title = 'Mike Dean patronem webu rozhodci.eu! xsfwefevew';

        $this->client->submitForm('Publikovat', [
            'post[title]' => $title,
            'post[contentsMd]' => '**je to tak!**',
        ]);

        $crawler = $this->client->followRedirect();

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame('Novinky', $crawler->filter('h1')->text());
        $this->assertSame('Novinka byla úspěšně uložena.', $crawler->filter('div.alert')->text());

        // EDIT our new post
        $crawler = $this->client->clickLink('Editovat');

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame('Editace novinky', $crawler->filter('h1')->text());

        $this->client->submitForm('Publikovat', [
            'post[contentsMd]' => '**je to tak! je to bauch**',
        ]);

        $crawler = $this->client->followRedirect();

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame('Novinky', $crawler->filter('h1')->text());
        $this->assertSame('Novinka byla úspěšně uložena.', $crawler->filter('div.alert')->text());

        // DELETE our post
        $this->client->clickLink('Smazat');

        $this->client->submitForm('Ano, smazat');

        $crawler = $this->client->followRedirect();

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame('Novinky', $crawler->filter('h1')->text());
        $this->assertSame('Novinka byla úspěšně smazána.', $crawler->filter('div.alert')->text());

        $this->assertEquals(
            0,
            $crawler->filter('table:contains("' . $title . '")')->count(),
            'Novinka nebyla uspesne smazana.'
        );

    }

}
