<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\League;
use App\Entity\Team;
use App\Exception\LeagueNotFound;
use App\Exception\TeamNotFound;
use App\Form\GameType;
use App\Form\LeagueType;
use App\Form\TeamType;
use App\Service\GameBuilder;
use App\Service\StatsUpdater;
use Demontpx\ParsedownBundle\Parsedown;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin/add-game", name="add_game")
     */
    public function addGame(Request $request, GameBuilder $gameBuilder)
    {
        $sourceCodeForm = $this->createFormBuilder()
            ->add('sourceCode', TextareaType::class, [
                'label' => 'Zdrojový kód: '
            ])
            ->getForm();
        $sourceCodeForm->handleRequest($request);

        if ($sourceCodeForm->isSubmitted() && $sourceCodeForm->isValid()) {
            $sourceCode = $sourceCodeForm['sourceCode']->getData();

            try {
                $game = $gameBuilder->createGameFromHtml($sourceCode);
            }
            catch (\InvalidArgumentException $e) {
                $this->addFlash('alert alert-danger', $e->getMessage());

                return $this->render('admin/add_game.html.twig', [
                    'form' => $sourceCodeForm->createView(),
                ]);
            }
            catch (LeagueNotFound $e) {
                $this->addFlash('alert alert-danger',
                    'Nenalezena příslušná soutěž! Přidejte ji ve formuláři níže a pak prosím zkuste zápas vložit znovu.');

                $league = $e->getLeague();
                $leagueForm = $this->createForm(LeagueType::class, $league, [
                    'action' => $this->generateUrl('create_league'),
                ]);

                return $this->render('admin/create_league.html.twig', [
                    'form' => $leagueForm->createView(),
                ]);
            }
            catch (TeamNotFound $e) {
                $this->addFlash('alert alert-danger',
                    'Nenalezen příslušný tým! Přidejte jej ve formuláři níže a pak prosím zkuste zápas vložit znovu.');

                $team = $e->getTeam();
                $teamForm = $this->createForm(TeamType::class, $team, [
                    'action' => $this->generateUrl('create_team'),
                ]);

                return $this->render('admin/create_team.html.twig', [
                    'form' => $teamForm->createView(),
                ]);
            }

            $gameForm = $this->createForm(GameType::class, $game, [
                'action' => $this->generateUrl('add_game_form'),
            ]);

            return $this->render('admin/add_game_form.html.twig', [
                'form' => $gameForm->createView(),
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
     * @Route("/admin/update-stats", name="update_stats")
     */
    public function updateStats(StatsUpdater $statsUpdater)
    {
        $statsUpdater->updateAllStats();

        $this->addFlash('alert alert-success',
            'Statistiky byly úspěšně aktualizovány.');

        return $this->render('admin/base.html.twig');
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

        return $this->render('test.html.twig', ['user' => $text]);
    }

}
