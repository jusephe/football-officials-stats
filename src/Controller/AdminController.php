<?php

namespace App\Controller;

use App\Entity\Assessor;
use App\Entity\Game;
use App\Entity\League;
use App\Entity\Official;
use App\Entity\Team;
use App\Form\GameType;
use App\Form\LeagueType;
use App\Form\TeamType;
use App\Repository\LeagueRepository;
use App\Repository\OfficialRepository;
use App\Repository\TeamRepository;
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
    public function addGame(Request $request, GameHtmlParser $gameHtmlParser, EntityManagerInterface $entityManager)
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

            // -------------------------------------- HEADER -----------------------------------------------
            $crawler_part = $crawler->filter('table table')->first();

            $leagueName = $crawler_part->filter('td')->eq(0)->text();
            $leagueName = trim($leagueName);
            // multiple spaces to only one
            $leagueName = preg_replace('/\s+/u', ' ', $leagueName);

            $league = $entityManager->getRepository(League::class)->findOneBy(['fullName' => $leagueName]);
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

            $round = $crawler_part->filter('td')->eq(2)->text();
            $game->setRound(trim($round));

            $season = $crawler_part->filter('td')->eq(6)->text();
            $season = trim($season);
            $game->setSeason($season);

            // date of game for info about part of the season
            $date = $crawler_part->filter('td')->eq(8)->text();
            $year = substr(strrchr($date, "."), 1, 4);
            if ($year === $season) $game->setIsAutumn(true);
            else $game->setIsAutumn(false);
            // -------------------------------------- END OF HEADER ----------------------------------------

            // ----------------------------------- TEAMS AND OFFICIALS -------------------------------------
            $crawler_part = $crawler->filter('table.vysledky')->first();


            $homeTeamInfo = $crawler_part->filter('b')->eq(0)->text();

            $homeTeamInfo = explode('-', $homeTeamInfo);
            $homeTeamClubId = trim($homeTeamInfo[0]);
            $homeTeamFullName = trim($homeTeamInfo[1]);

            $homeTeam = $entityManager->getRepository(Team::class)->findOneBy(['fullName' => $homeTeamFullName]);
            if ($homeTeam === null) {
                $this->addFlash('alert alert-danger',
                    'Nenalezen příslušný tým! Přidejte jej ve formuláři níže a pak prosím zkuste zápas vložit znovu.');

                $homeTeam = new Team();
                $homeTeam->setFullName($homeTeamFullName);
                $homeTeam->setClubId($homeTeamClubId);

                $teamForm = $this->createForm(TeamType::class, $homeTeam, [
                    'action' => $this->generateUrl('create_team'),
                ]);

                return $this->render('admin/create_team.html.twig', [
                    'form' => $teamForm->createView(),
                ]);
            }
            $game->setHomeTeam($homeTeam);



            $awayTeamInfo = $crawler_part->filter('b')->eq(1)->text();

            $awayTeamInfo = explode('-', $awayTeamInfo);
            $awayTeamClubId = trim($awayTeamInfo[0]);
            $awayTeamFullName = trim($awayTeamInfo[1]);

            $awayTeam = $entityManager->getRepository(Team::class)->findOneBy(['fullName' => $awayTeamFullName]);
            if ($awayTeam === null) {
                $this->addFlash('alert alert-danger',
                    'Nenalezen příslušný tým! Přidejte jej ve formuláři níže a pak prosím zkuste zápas vložit znovu.');

                $awayTeam = new Team();
                $awayTeam->setFullName($awayTeamFullName);
                $awayTeam->setClubId($awayTeamClubId);

                $teamForm = $this->createForm(TeamType::class, $awayTeam, [
                    'action' => $this->generateUrl('create_team'),
                ]);

                return $this->render('admin/create_team.html.twig', [
                    'form' => $teamForm->createView(),
                ]);
            }
            $game->setAwayTeam($awayTeam);



            $refereeName = $crawler_part->filter('td')->eq(3)->text();
            $refereeId = $crawler_part->filter('td')->eq(4)->text();

            $referee = $entityManager->getRepository(Official::class)->find($refereeId);
            if ($referee === null) {
                $referee = new Official();
                $referee->setId($refereeId);
                $referee->setName($refereeName);

                $entityManager->persist($referee);
                $entityManager->flush();
            }
            $game->setRefereeOfficial($referee);



            $AR1Name = $crawler_part->filter('td')->eq(7)->text();
            if (strpos($AR1Name, '(N)')) {  // amateur, not interested
                $AR1 = $entityManager->getRepository(Official::class)->find('00000000');
            }
            else {
                $AR1Id = $crawler_part->filter('td')->eq(8)->text();

                $AR1 = $entityManager->getRepository(Official::class)->find($AR1Id);
                if ($AR1 === null) {
                    $AR1 = new Official();
                    $AR1->setId($AR1Id);
                    $AR1->setName($AR1Name);

                    $entityManager->persist($AR1);
                    $entityManager->flush();
                }
            }
            $game->setAr1Official($AR1);



            $AR2Name = $crawler_part->filter('td')->eq(12)->text();
            if (strpos($AR2Name, '(N)')) {  // amateur, not interested
                $AR2 = $entityManager->getRepository(Official::class)->find('00000000');
            }
            else {
                $AR2Id = $crawler_part->filter('td')->eq(13)->text();

                $AR2 = $entityManager->getRepository(Official::class)->find($AR2Id);
                if ($AR2 === null) {
                    $AR2 = new Official();
                    $AR2->setId($AR2Id);
                    $AR2->setName($AR2Name);

                    $entityManager->persist($AR2);
                    $entityManager->flush();
                }
            }
            $game->setAr2Official($AR2);



            $assessorName = $crawler_part->filter('td')->eq(21)->text();
            if ($assessorName === '') {  // without assessor
                $assessor = $entityManager->getRepository(Assessor::class)->find('00000000');
            }
            else {
                $assessorId = $crawler_part->filter('td')->eq(22)->text();

                $assessor = $entityManager->getRepository(Assessor::class)->find($assessorId);
                if ($assessor === null) {
                    $assessor = new Assessor();
                    $assessor->setId($assessorId);
                    $assessor->setName($assessorName);

                    $entityManager->persist($assessor);
                    $entityManager->flush();
                }
            }
            $game->setAssessor($assessor);

            // ------------------------------- END OF TEAMS AND OFFICIALS ---------------------------------










            $gameForm = $this->createForm(GameType::class, $game, [
                'action' => $this->generateUrl('add_game_form'),
            ]);

            return $this->render('admin/add_game_form.html.twig', [
                'form' => $gameForm->createView(),
                'crawlerH' => $season,
                'crawler' => $year,
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
     * @Route("/admin/teams/create", name="create_team", defaults={"id": null})
     * @Route("/admin/teams/{id}/edit", name="edit_team", requirements={"id"="\d+"})
     */
    public function teamForm($id, Request $request, EntityManagerInterface $entityManager)
    {
        if ( $id === null ) $team = new Team();  // create
        else {  // edit
            $team = $entityManager->getRepository(Team::class)->find($id);
            if ( $team === null ) throw $this->createNotFoundException('Takový tým neexistuje!');
        }

        $form = $this->createForm(TeamType::class, $team)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($team);
            $entityManager->flush();

            $this->addFlash('alert alert-success', 'Tým byl úspěšně uložen.');

            return $this->redirectToRoute('teams');
        }

        return $this->render($id ? 'admin/edit_team.html.twig' : 'admin/create_team.html.twig', [
            'form' => $form->createView(),
            'team' => $team,
        ]);
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
