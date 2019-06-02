<?php

namespace App\Controller;

use App\Entity\Assessor;
use App\Entity\Game;
use App\Entity\League;
use App\Entity\NominationList;
use App\Entity\Official;
use App\Entity\Team;
use App\Exception\LeagueNotFound;
use App\Exception\TeamNotFound;
use App\Form\AssessorType;
use App\Form\GamePunishmentsType;
use App\Form\GameType;
use App\Form\LeagueType;
use App\Form\AddNominationListType;
use App\Form\OfficialNominationListsType;
use App\Form\OfficialType;
use App\Form\TeamType;
use App\Functionality\GameFunctionality;
use App\Repository\AssessorRepository;
use App\Repository\GameRepository;
use App\Repository\LeagueRepository;
use App\Repository\OfficialRepository;
use App\Repository\TeamRepository;
use App\Service\GameBuilder;
use App\Service\StatsUpdater;
use Demontpx\ParsedownBundle\Parsedown;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\Valid;

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
                $this->addFlash('alert alert-warning',
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
                $this->addFlash('alert alert-warning',
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

        return $this->redirectToRoute('add_game');
    }

    /**
     * @Route("/admin/punishments", name="punishments")
     */
    public function punishments(Request $request, GameRepository $gameRepository, GameFunctionality $gameFunctionality)
    {
        $form = $this->createFormBuilder()
            ->add('league', EntityType::class, [
                'label' => 'Soutěž:',
                'class' => League::class,
                'choice_label' => 'fullName',
            ])
            ->add("season", ChoiceType::class, [
                "label" => "Sezóna:",
                "choices" => $gameFunctionality->getDistinctSeasons(),
                'choice_label' => function ($choice) {
                    return $choice;  // pure season
                },
            ])
            ->add("round", ChoiceType::class, [
                "label" => "Kolo:",
                "choices" => $gameFunctionality->getDistinctRounds(),
                'choice_label' => function ($choice) {
                    return $choice;  // pure round
                },
            ])
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $games = $gameRepository->findBy([
                'league' => $form['league']->getData(),
                'season' => $form['season']->getData(),
                'round' => $form['round']->getData()
            ]);

            if (empty($games)) {
                $this->addFlash('alert alert-warning', 'Pro zadané údaje nebyly nalezeny žádné zápasy!');
            }

            return $this->render('admin/punishments.html.twig', [
                'form' => $form->createView(),
                'games' => $games,
            ]);
        }

        return $this->render('admin/punishments.html.twig', [
            'form' => $form->createView(),
            'games' => null,
        ]);
    }

    /**
     * @Route("/admin/punishments/add/{gameId}", name="add_punishments", requirements={"gameId"="\d+"})
     */
    public function addPunishments($gameId, Request $request, EntityManagerInterface $entityManager)
    {
        $game = $entityManager->getRepository(Game::class)->find($gameId);

        if ($game === null) {
            throw $this->createNotFoundException('Zápas nebyl nalezen!');
        }

        $form = $this->createForm(GamePunishmentsType::class, $game)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($game);
            $entityManager->flush();

            $this->addFlash('alert alert-success',
                'Tresty byly úspěšně uloženy.');

            return $this->render('admin/add_punishments.html.twig', [
                'form' => $form->createView(),
                'game' => $game,
            ]);
        }

        if ($form->isSubmitted() && ! $form->isValid()) {
            $this->addFlash('alert alert-danger',
                'Tresty nebyly uloženy! Opravte označené chyby ve formuláři.');
        }

        return $this->render('admin/add_punishments.html.twig', [
            'form' => $form->createView(),
            'game' => $game,
        ]);
    }

    /**
     * @Route("/admin/nomination-lists", name="nomination_lists")
     */
    public function nominationLists(Request $request)
    {
        $seasonForm = $this->createFormBuilder()
            ->add('year', IntegerType::class, [
                'label' => 'Rok:',
                'constraints' => [
                    new Range(['min' => 1950, 'max' => 2070]),
                ],
            ])
            ->add("partOfSeason", ChoiceType::class, [
                "label" => "Část:",
                "choices" => ['Jaro' => 'Jaro', 'Podzim' => 'Podzim'],
                'placeholder' => 'Vyberte část',
            ])
            ->getForm();
        $seasonForm->handleRequest($request);

        if ($seasonForm->isSubmitted() && $seasonForm->isValid()) {
            return $this->redirectToRoute('add_nomination_lists', [
                'year' => $seasonForm['year']->getData(),
                'part' => $seasonForm['partOfSeason']->getData(),
            ]);
        }

        $searchForm = $this->createFormBuilder()
            ->add('official', EntityType::class, [
                'label' => 'Rozhodčí:',
                'class' => Official::class,
                'query_builder' => function (OfficialRepository $or) {
                    return $or->createQueryBuilder('o')
                        ->orderBy('o.name', 'ASC');
                },
                'choice_label' => function ($official) {
                    return $official->getNameWithId();
                },
                'placeholder' => 'Vyberte rozhodčího',
            ])
            ->getForm();
        $searchForm->handleRequest($request);

        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            return $this->redirectToRoute('edit_nomination_lists', [
                'officialId' => $searchForm['official']->getData()->getId(),
            ]);
        }

        return $this->render('admin/nomination_lists.html.twig', [
            'seasonForm' => $seasonForm->createView(),
            'searchForm' => $searchForm->createView(),
        ]);
    }

    /**
     * @Route("/admin/nomination-lists/add/{year}/{part}", name="add_nomination_lists")
     */
    public function addNominationLists($year, $part, Request $request, EntityManagerInterface $entityManager)
    {
        $possibleOfficials = $entityManager->getRepository(Official::class)->findWithoutNominationList($year, $part);

        if (empty($possibleOfficials)) {
            $this->addFlash('alert alert-info', 'Pro daný půlrok nebyli nalezeni žádní rozhodčí bez zadané listiny.');

            return $this->redirectToRoute('nomination_lists');
        }

        $newLists = array();
        foreach ($possibleOfficials as $official) {
            $list = new NominationList();
            $list->setOfficial($official);
            $list->setYear($year);
            $list->setPartOfSeason($part);
            $newLists[] = $list;
        }

        $form = $this->createFormBuilder()
            ->add('nominationLists', CollectionType::class, [
                'label' => false,
                'entry_type' => AddNominationListType::class,
                'data' => $newLists,
                'entry_options' => ['label' => false],
                'constraints' => [
                    new Valid(),
                ],
            ])
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $nominationLists = $form['nominationLists']->getData();

            foreach ($nominationLists as $nominationList) {
                $official = $nominationList->getOfficial();
                $official->addNominationList($nominationList);
                $entityManager->persist($official);
                $entityManager->persist($nominationList);
            }
            $entityManager->flush();

            $this->addFlash('alert alert-success', 'Listiny byly úspěšně uloženy.');

            return $this->redirectToRoute('nomination_lists');
        }

        if ($form->isSubmitted() && ! $form->isValid()) {
            $this->addFlash('alert alert-danger', 'Listiny nebyly uloženy! Opravte označené chyby ve formuláři.');
        }

        return $this->render('admin/add_nomination_lists.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/nomination-lists/edit/{officialId}", name="edit_nomination_lists")
     */
    public function editNominationLists($officialId, Request $request, EntityManagerInterface $entityManager)
    {
        $official = $entityManager->getRepository(Official::class)->find($officialId);

        if ($official === null) {
            throw $this->createNotFoundException('Rozhodčí nebyl nalezen!');
        }

        $form = $this->createForm(OfficialNominationListsType::class, $official)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($official);
            $entityManager->flush();

            $this->addFlash('alert alert-success',
                'Listiny byly úspěšně změněny.');

            return $this->render('admin/edit_nomination_lists.html.twig', [
                'form' => $form->createView(),
                'official' => $official,
            ]);
        }

        if ($form->isSubmitted() && ! $form->isValid()) {
            $this->addFlash('alert alert-danger',
                'Listiny nebyly uloženy! Opravte označené chyby ve formuláři.');
        }

        return $this->render('admin/edit_nomination_lists.html.twig', [
            'form' => $form->createView(),
            'official' => $official,
        ]);
    }

    /**
     * @Route("/admin/leagues", name="leagues")
     */
    public function leagues(LeagueRepository $leagueRepository)
    {
        $leagues = $leagueRepository->findAll();

        return $this->render('admin/leagues.html.twig', [
            'leagues' => $leagues
        ]);
    }

    /**
     * @Route("/admin/leagues/create", name="create_league", defaults={"id": null})
     * @Route("/admin/leagues/{id}/edit", name="edit_league", requirements={"id"="\d+"})
     */
    public function leagueForm($id, Request $request, EntityManagerInterface $entityManager)
    {
        if ($id === null) $league = new League();  // create
        else {  // edit
            $league = $entityManager->getRepository(League::class)->find($id);
            if ($league === null) throw $this->createNotFoundException('Taková soutěž neexistuje!');
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
    public function teams(TeamRepository $teamRepository)
    {
        $teams = $teamRepository->findAllOrderByFullName();

        return $this->render('admin/teams.html.twig', [
            'teams' => $teams
        ]);
    }

    /**
     * @Route("/admin/teams/create", name="create_team", defaults={"id": null})
     * @Route("/admin/teams/{id}/edit", name="edit_team", requirements={"id"="\d+"})
     */
    public function teamForm($id, Request $request, EntityManagerInterface $entityManager)
    {
        if ($id === null) $team = new Team();  // create
        else {  // edit
            $team = $entityManager->getRepository(Team::class)->find($id);
            if ($team === null) throw $this->createNotFoundException('Takový tým neexistuje!');
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
     * @Route("/admin/officials", name="officials")
     */
    public function officials(OfficialRepository $officialRepository)
    {
        $officials = $officialRepository->findAllOrderByName();

        return $this->render('admin/officials.html.twig', [
            'officials' => $officials
        ]);
    }

    /**
     * @Route("/admin/officials/create", name="create_official", defaults={"id": null})
     * @Route("/admin/officials/{id}/edit", name="edit_official")
     */
    public function officialForm($id, Request $request, EntityManagerInterface $entityManager)
    {
        if ($id === null) $official = new Official();  // create
        else {  // edit
            $official = $entityManager->getRepository(Official::class)->find($id);
            if ($official === null) throw $this->createNotFoundException('Takový rozhodčí neexistuje!');
        }

        $form = $this->createForm(OfficialType::class, $official)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($official);
            $entityManager->flush();

            $this->addFlash('alert alert-success', 'Rozhodčí byl úspěšně uložen.');

            return $this->redirectToRoute('officials');
        }

        return $this->render($id ? 'admin/edit_official.html.twig' : 'admin/create_official.html.twig', [
            'form' => $form->createView(),
            'official' => $official,
        ]);
    }

    /**
     * @Route("/admin/assessors", name="assessors")
     */
    public function assessors(AssessorRepository $assessorRepository)
    {
        $assessors = $assessorRepository->findAllOrderByName();

        return $this->render('admin/assessors.html.twig', [
            'assessors' => $assessors
        ]);
    }

    /**
     * @Route("/admin/assessors/create", name="create_assessor", defaults={"id": null})
     * @Route("/admin/assessors/{id}/edit", name="edit_assessor")
     */
    public function assessorForm($id, Request $request, EntityManagerInterface $entityManager)
    {
        if ($id === null) $assessor = new Assessor();  // create
        else {  // edit
            $assessor = $entityManager->getRepository(Assessor::class)->find($id);
            if ($assessor === null) throw $this->createNotFoundException('Takový delegát neexistuje!');
        }

        $form = $this->createForm(AssessorType::class, $assessor)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($assessor);
            $entityManager->flush();

            $this->addFlash('alert alert-success', 'Delegát byl úspěšně uložen.');

            return $this->redirectToRoute('assessors');
        }

        return $this->render($id ? 'admin/edit_assessor.html.twig' : 'admin/create_assessor.html.twig', [
            'form' => $form->createView(),
            'assessor' => $assessor,
        ]);
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
