<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\League;
use App\Entity\Offence;
use App\Entity\Official;
use App\Entity\RedCard;
use App\Entity\Team;
use App\Entity\YellowCard;
use App\Form\GameType;
use Demontpx\ParsedownBundle\Parsedown;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin/add-game", name="add_game")
     */
    public function addGame(Request $request)
    {
        $sourceCodeForm = $this->createFormBuilder()
            ->add('sourceCode', TextareaType::class, ['label' => 'Zdrojový kód: '])
            ->getForm();

        $sourceCodeForm->handleRequest($request);

        if ($sourceCodeForm->isSubmitted() && $sourceCodeForm->isValid()) {

            // zpracovani dat

            $game = new Game();

            $rozh = $this->getDoctrine()
                ->getRepository(Official::class)
                ->find('12345678');
            $tym = $this->getDoctrine()
                ->getRepository(Team::class)
                ->find(1);
            $liga = $this->getDoctrine()
                ->getRepository(League::class)
                ->find(1);
            $off = $this->getDoctrine()
                ->getRepository(Offence::class)
                ->find(1);

            $yellow = new YellowCard();
            $yellow->setMinute(17);
            $game->addYellowCard($yellow);

            $yellow2 = new YellowCard();
            $yellow2->setMinute(88);
            $game->addYellowCard($yellow2);

            $red = new RedCard();
            $red->setMinute(17);
            $red->setPerson('Tomas Repka');
            $red->setTeam($tym);
            $red->setOffence($off);
            $game->addRedCard($red);

            $red2 = new RedCard();
            $red2->setMinute(19);
            $red2->setPerson('Tomas Repka2');
            $red2->setTeam($tym);
            $red2->setOffence($off);
            $game->addRedCard($red2);

            $game->setSeason(2018);
            $game->setIsAutumn(true);
            $game->setRound(8);
            $game->setRefereeOfficial($rozh);
            $game->setAwayTeam($tym);
            $game->setHomeTeam($tym);
            $game->setLeague($liga);


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
    public function addGameForm(Request $request)
    {
        $game = new Game();

        $form = $this->createForm(GameType::class, $game);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

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
     * @Route("/admin/teams", name="teams")
     */
    public function teams()
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
     * @Route("/admin/news", name="news")
     */
    public function news(Parsedown $parsedown)
    {
        $text = $parsedown->text('Hello _Parsedown_!'); # prints: <p>Hello <em>Parsedown</em>!</p>

        return $this->render('test.html.twig', ['text' => $text]);
    }

}
