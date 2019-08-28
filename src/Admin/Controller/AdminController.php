<?php

namespace App\Admin\Controller;

use App\Admin\Entity\Assessor;
use App\Admin\Entity\Game;
use App\Admin\Entity\League;
use App\Admin\Entity\NominationList;
use App\Admin\Entity\Official;
use App\Admin\Entity\Post;
use App\Admin\Entity\Team;
use App\Admin\Exception\LeagueNotFound;
use App\Admin\Exception\TeamNotFound;
use App\Admin\Form\AssessorType;
use App\Admin\Form\ChangePasswordType;
use App\Admin\Form\GamePunishmentsType;
use App\Admin\Form\GameType;
use App\Admin\Form\LeagueType;
use App\Admin\Form\AddNominationListType;
use App\Admin\Form\OfficialNominationListsType;
use App\Admin\Form\OfficialType;
use App\Admin\Form\PostType;
use App\Admin\Form\TeamType;
use App\Admin\Functionality\GameFunctionality;
use App\Admin\Repository\AssessorRepository;
use App\Admin\Repository\GameRepository;
use App\Admin\Repository\LeagueRepository;
use App\Admin\Repository\OfficialRepository;
use App\Admin\Repository\PostRepository;
use App\Admin\Repository\StatsRepository;
use App\Admin\Repository\TeamRepository;
use App\Admin\Service\GameBuilder;
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
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
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

                return $this->render('admin/basics/create_league.html.twig', [
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

                return $this->render('admin/basics/create_team.html.twig', [
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
     * @Route("/admin/games", name="games")
     */
    public function games(GameRepository $gameRepository)
    {
        $games = $gameRepository->findAllOrderByAdded();

        return $this->render('admin/games.html.twig', [
            'games' => $games
        ]);
    }

    /**
     * @Route("/admin/games/{id}/delete", methods={"POST"}, name="delete_game", requirements={"id"="\d+"})
     */
    public function deleteGame(Request $request, EntityManagerInterface $entityManager, $id) {

        $game = $entityManager->getRepository(Game::class)->find($id);
        if ( $game === null ) {
            throw $this->createNotFoundException('Takový zápas neexistuje!');
        }

        if (!$this->isCsrfTokenValid('game_delete', $request->request->get('token'))) {
            return $this->redirectToRoute('games');
        }

        $entityManager->remove($game);
        $entityManager->flush();

        $this->addFlash('alert alert-success',
            'Zápas byl úspěšně smazán. (Nezapomeňte aktualizovat statistiky!)');

        return $this->redirectToRoute('games');
    }

    /**
     * @Route("/admin/update-stats", name="update_stats")
     */
    public function updateStats(StatsRepository $statsRepository)
    {
        $statsRepository->updateAllStats();

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
                    $seasonEndYear = substr($choice+1, 2);
                    return "$choice/$seasonEndYear";  // pure season
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
    public function addPunishments(Request $request, EntityManagerInterface $entityManager, $gameId)
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
    public function addNominationLists(Request $request, EntityManagerInterface $entityManager, $year, $part)
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
    public function editNominationLists(Request $request, EntityManagerInterface $entityManager, $officialId)
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

        return $this->render('admin/basics/leagues.html.twig', [
            'leagues' => $leagues
        ]);
    }

    /**
     * @Route("/admin/leagues/create", name="create_league", defaults={"id": null})
     * @Route("/admin/leagues/{id}/edit", name="edit_league", requirements={"id"="\d+"})
     */
    public function leagueForm(Request $request, EntityManagerInterface $entityManager, $id)
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

        return $this->render($id ? 'admin/basics/edit_league.html.twig' : 'admin/basics/create_league.html.twig', [
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

        return $this->render('admin/basics/teams.html.twig', [
            'teams' => $teams
        ]);
    }

    /**
     * @Route("/admin/teams/create", name="create_team", defaults={"id": null})
     * @Route("/admin/teams/{id}/edit", name="edit_team", requirements={"id"="\d+"})
     */
    public function teamForm(Request $request, EntityManagerInterface $entityManager, $id)
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

        return $this->render($id ? 'admin/basics/edit_team.html.twig' : 'admin/basics/create_team.html.twig', [
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

        return $this->render('admin/basics/officials.html.twig', [
            'officials' => $officials
        ]);
    }

    /**
     * @Route("/admin/officials/create", name="create_official", defaults={"id": null})
     * @Route("/admin/officials/{id}/edit", name="edit_official")
     */
    public function officialForm(Request $request, EntityManagerInterface $entityManager, $id)
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

        return $this->render($id ? 'admin/basics/edit_official.html.twig' : 'admin/basics/create_official.html.twig', [
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

        return $this->render('admin/basics/assessors.html.twig', [
            'assessors' => $assessors
        ]);
    }

    /**
     * @Route("/admin/assessors/create", name="create_assessor", defaults={"id": null})
     * @Route("/admin/assessors/{id}/edit", name="edit_assessor")
     */
    public function assessorForm(Request $request, EntityManagerInterface $entityManager, $id)
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

        return $this->render($id ? 'admin/basics/edit_assessor.html.twig' : 'admin/basics/create_assessor.html.twig', [
            'form' => $form->createView(),
            'assessor' => $assessor,
        ]);
    }

    /**
     * @Route("/admin/posts", name="posts")
     */
    public function posts(PostRepository $postRepository)
    {
        $posts = $postRepository->findAllOrderByPublished();

        return $this->render('admin/posts.html.twig', [
            'posts' => $posts
        ]);
    }

    /**
     * @Route("/admin/posts/create", name="create_post", defaults={"id": null})
     * @Route("/admin/posts/{id}/edit", name="edit_post", requirements={"id"="\d+"})
     */
    public function postForm(Request $request, EntityManagerInterface $entityManager, Parsedown $parsedown, $id)
    {
        if ($id === null) $post = new Post();  // create
        else {  // edit
            $post = $entityManager->getRepository(Post::class)->find($id);
            if ($post === null) throw $this->createNotFoundException('Taková novinka neexistuje!');
        }

        $form = $this->createForm(PostType::class, $post)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $html = $parsedown->text($form['contentsMd']->getData());

            $post->setContentsHtml($html);
            $post->setAdmin($this->getUser());
            if($post->getPublished() === null) {  // set published only when creating new
                $now = new \DateTime();
                $now->format('Y-m-d H:i:s');
                $post->setPublished($now);
            }

            $entityManager->persist($post);
            $entityManager->flush();

            $this->addFlash('alert alert-success', 'Novinka byla úspěšně uložena.');

            return $this->redirectToRoute('posts');
        }

        return $this->render($id ? 'admin/edit_post.html.twig' : 'admin/create_post.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/posts/{id}/delete", methods={"POST"}, name="delete_post", requirements={"id"="\d+"})
     */
    public function deletePost(Request $request, EntityManagerInterface $entityManager, $id) {

        $post = $entityManager->getRepository(Post::class)->find($id);
        if ( $post === null ) {
            throw $this->createNotFoundException('Taková novinka neexistuje!');
        }

        if (!$this->isCsrfTokenValid('post_delete', $request->request->get('token'))) {
            return $this->redirectToRoute('posts');
        }

        $entityManager->remove($post);
        $entityManager->flush();

        $this->addFlash('alert alert-success', 'Novinka byla úspěšně smazána.');

        return $this->redirectToRoute('posts');
    }


    /**
     * @Route("/admin/change-password", name="change_password")
     */
    public function changePassword(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($encoder->encodePassword($user, $form->get('newPassword')->getData()));
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('alert alert-success', 'Heslo bylo úspěšně změněno.');

            return $this->render('admin/change_password.html.twig', [
                'form' => $form->createView(),
            ]);
        }

        return $this->render('admin/change_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
