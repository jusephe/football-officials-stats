<?php

namespace App\Controller;

use App\Entity\Game;
use App\Form\GameType;
use App\Service\GameHtmlParser;
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
    public function addGame(Request $request, GameHtmlParser $gameHtmlParser)
    {
        $sourceCodeForm = $this->createFormBuilder()
            ->add('sourceCode', TextareaType::class, [
                'label' => 'Zdrojový kód: '
            ])
            ->getForm();

        $sourceCodeForm->handleRequest($request);

        if ($sourceCodeForm->isSubmitted() && $sourceCodeForm->isValid()) {
            $sourceCode = $sourceCodeForm['sourceCode']->getData();

            $game = $gameHtmlParser->createGame($sourceCode);

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
     * @Route("/admin/leagues", name="leagues")
     */
    public function leagues()
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
