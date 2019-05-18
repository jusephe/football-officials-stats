<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\League;
use App\Form\GameType;
use App\Form\LeagueType;
use App\Repository\LeagueRepository;
use App\Service\GameHtmlParser;
use Demontpx\ParsedownBundle\Parsedown;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin/add-game", name="add_game")
     */
    public function addGame(Request $request, GameHtmlParser $gameHtmlParser, LeagueRepository $leagueRepository)
    {
        $sourceCodeForm = $this->createFormBuilder()
            ->add('sourceCode', TextareaType::class, [
                'label' => 'Zdrojový kód: '
            ])
            ->getForm();
        $sourceCodeForm->handleRequest($request);

        if ($sourceCodeForm->isSubmitted() && $sourceCodeForm->isValid()) {
            $sourceCode = $sourceCodeForm['sourceCode']->getData();

            //$game = $gameHtmlParser->createGame($sourceCode);  CHCI TO




            // toto vsecko pujde pryc
            $game = new Game();

            $crawler = new Crawler($sourceCode);
            $crawler = $crawler->filter('div.book.zapis-report');

            $crawler_head = $crawler->filter('table table')->first();

            $leagueName = $crawler_head->filter('td')->first()->text();
            $leagueName = trim($leagueName);
            // multiple spaces to only one
            $leagueName = preg_replace('/\s+/', ' ', $leagueName);

            $league = $leagueRepository->findOneBy(['fullName' => $leagueName]);
            if ($league === null) {
                $this->addFlash('alert alert-danger',
                    'Nenalezena příslušná soutěž! Přidejte ji ve formuláři níže a pak prosím zkuste zápas vložit znovu.');

                $league = new League();
                $league->setFullName($leagueName);

                $leagueForm = $this->createForm(LeagueType::class, $league, [
                    'action' => $this->generateUrl('create_league'),
                ]);

                return $this->render('admin/create_league.html.twig', [
                    'form' => $leagueForm->createView(),
                ]);
            }
            $game->setLeague($league);

            $round = $crawler_head->filter('td')->eq(2)->text();
            $round = trim($round);
            $game->setRound($round);

            $season = $crawler_head->filter('td')->eq(6)->text();
            $season = trim($season);
            $game->setSeason($season);

            // date of game for info about part of the season
            $date = $crawler_head->filter('td')->eq(8)->text();
            $year = substr(strrchr($date, "."), 1, 4);
            if ($year === $season) $game->setIsAutumn(true);
            else $game->setIsAutumn(false);


            




            







            $gameForm = $this->createForm(GameType::class, $game, [
                'action' => $this->generateUrl('add_game_form'),
            ]);

            return $this->render('admin/add_game_form.html.twig', [
                'form' => $gameForm->createView(),
                'crawlerH' => $leagueName,
                'crawler' => $date,
            ]);
        }

        return $this->render('admin/add_game.html.twig', [
            'form' => $sourceCodeForm->createView(),
        ]);
    }

    /**
     * @Route("/admin/add-game-form", name="add_game_form")
     */
    public function addGameForm(Request $request, EntityManagerInterface $entityManager)
    {
        $game = new Game();

        $form = $this->createForm(GameType::class, $game)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($game);
            $entityManager->flush();

            $this->addFlash('alert alert-success',
                'Zápas byl úspěšně vložen. (Nezapomeňte po posledním zápase aktualizovat statistiky!)');

            return $this->redirectToRoute('add_game');
        }

        if ($form->isSubmitted() && ! $form->isValid()) {
            $this->addFlash('alert alert-danger',
                'Zápas nebyl vložen! Opravte označené chyby ve formuláři.');
        }

        return $this->render('admin/add_game_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/punishments", name="punishments")
     */
    public function punishments()
    {

        return $this->render('admin/base.html.twig');
    }

    /**
     * @Route("/admin/nomination-list", name="nomination_list")
     */
    public function nominationList()
    {

        return $this->render('admin/base.html.twig');
    }

    /**
     * @Route("/admin/leagues", name="leagues")
     */
    public function leagues()
    {

        return $this->render('admin/base.html.twig');
    }

    /**
     * @Route("/admin/leagues/create", name="create_league", defaults={"id": null})
     * @Route("/admin/leagues/{id}/edit", name="edit_league", requirements={"id"="\d+"})
     */
    public function leagueForm($id, Request $request, EntityManagerInterface $entityManager)
    {
        if ( $id === null ) $league = new League();  // create
        else {  // edit
            $league = $entityManager->getRepository(League::class)->find($id);
            if ( $league === null ) throw $this->createNotFoundException('Taková soutěž neexistuje!');
        }

        $form = $this->createForm(LeagueType::class, $league)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($league);
            $entityManager->flush();

            $this->addFlash('alert alert-success', 'Soutěž byla úspěšně uložena.');

            return $this->redirectToRoute('leagues');
        }

        return $this->render($id ? 'admin/edit_league.html.twig' : 'admin/create_league.html.twig', [
            'form' => $form->createView(),
            'league' => $league,
        ]);
    }

    /**
     * @Route("/admin/teams", name="teams")
     */
    public function teams()
    {

        return $this->render('admin/base.html.twig');
    }

    /**
     * @Route("/admin/officials-asssessors", name="officialsAssessors")
     */
    public function officialsAssessors()
    {

        return $this->render('admin/base.html.twig');
    }

    /**
     * @Route("/admin/news", name="news")
     */
    public function news(Parsedown $parsedown)
    {
        $text = $parsedown->text('Hello _Parsedown_!'); # prints: <p>Hello <em>Parsedown</em>!</p>

        return $this->render('test.html.twig', ['text' => $text]);
    }

}
