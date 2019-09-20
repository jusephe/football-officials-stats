<?php

namespace App\Site\Controller;

use App\Admin\Repository\AssessorRepository;
use App\Admin\Repository\OfficialRepository;
use App\Admin\Repository\PostRepository;
use App\Admin\Repository\RedCardRepository;
use App\Site\Repository\AssessorStatsRepository;
use App\Site\Repository\OfficialStatsRepository;
use App\Site\Repository\SeasonStatsRepository;
use App\Site\Service\ChartFactory;
use App\Site\Service\ProfileConfig;
use App\Site\Service\SeasonsListMaker;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SiteController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(PostRepository $postRepository)
    {
        $posts = $postRepository->findBy([], ['id' => 'DESC'], 5);  // order by published

        return $this->render('site/index.html.twig', [
            'posts' => $posts
        ]);
    }

    /**
     * @Route("/novinky", name="old_posts")
     */
    public function oldPosts(PostRepository $postRepository, Request $request, PaginatorInterface $paginator)
    {
        $queryBuilder = $postRepository->getWithSearchQueryBuilderOrderByPublished();

        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 2),
            5
        );

        return $this->render('site/old_posts.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("/novinky/{id}", name="post", requirements={"id"="\d+"})
     */
    public function post(PostRepository $postRepository, $id)
    {
        $post = $postRepository->find($id);
        if ($post === null) throw $this->createNotFoundException('Taková novinka neexistuje!');

        return $this->render('site/post.html.twig', [
            'post' => $post
        ]);
    }

    /**
     * @Route("/o-projektu", name="about")
     */
    public function about()
    {
        return $this->render('site/about.html.twig');
    }

    /**
     * @Route("/{league}", name="league", requirements={
     *     "league"="prebor|a-trida"
     * })
     */
    public function league(SeasonsListMaker $seasonsListMaker, $league)
    {
        $slug = $league;

        switch ($league) {
            case 'prebor':
                $league = 'Přebor';
                break;
            case 'a-trida':
                $league = '1.A třída';
                break;
        }

        $seasonsWithParts = $seasonsListMaker->createSeasonsList($league);

        return $this->render('site/league.html.twig', [
            'seasons' => $seasonsWithParts,
            'league' => $league,
            'slug' => $slug,
        ]);
    }

    /**
     * @Route("/{league}/{season}/{part}", name="season_stats", requirements={
     *     "league"="prebor|a-trida",
     *     "season"="\d+",
     *     "part"="podzim|jaro"
     * })
     */
    public function seasonStats(ChartFactory $chartFactory, SeasonStatsRepository $statsRepository, RedCardRepository $redCardRepository,
                                $league, $season, $part = null)
    {
        switch ($league) {
            case 'prebor':
                $league = 'Přebor';
                break;
            case 'a-trida':
                $league = '1.A třída';
                break;
        }

        $stats = $statsRepository->getSeasonStats($league, $season, $part);
        if (empty($stats['RefereeMatches'])) {
            throw $this->createNotFoundException('Pro tuto sezónu nejsou statistiky k dispozici!');
        }
        $redOffenceChart = $chartFactory->createRedOffence($stats['RefereeRedOffence']);
        $redCards = $redCardRepository->findByLeagueSeasonPart($league, $season, $part);

        return $this->render('site/season_stats.html.twig', [
            'league' => $league,
            'season' => $season,
            'part' => $part,
            'stats' => $stats,
            'redOffenceChart' => $redOffenceChart,
            'redCards' => $redCards,
        ]);
    }

    /**
     * @Route("/rozhodci", name="site_officials")
     */
    public function officials(OfficialRepository $officialRepository)
    {
        $officials = $officialRepository->findAllOrderByName();

        return $this->render('site/officials.html.twig', [
            'officials' => $officials
        ]);
    }

    /**
     * @Route("/rozhodci/{id}", name="official_profile")
     */
    public function officialProfile(ProfileConfig $profileConfig, ChartFactory $chartFactory,
                                    OfficialRepository $officialRepository, OfficialStatsRepository $statsRepository, $id)
    {
        $official = $officialRepository->find($id);
        if ($official === null) throw $this->createNotFoundException('Takový rozhodčí neexistuje!');

        $seasons = $profileConfig->getSeasons();
        $leagues = $profileConfig->getLeagues();
        $stats = $statsRepository->getOfficialStats($id, $seasons, $leagues);
        $redOffenceChart = $chartFactory->createRedOffence($stats['RefereeRedOffence']);
        $cardsMinutesChart = $chartFactory->createCardsMinutes(
            $stats['RefereeCardsMinutes'], $stats['RefereeCardsMinutesMaxNumberOfCards']);

        return $this->render('site/official_profile.html.twig', [
            'official' => $official,
            'stats' => $stats,
            'redOffenceChart' => $redOffenceChart,
            'cardsMinutesChart' => $cardsMinutesChart,
            'seasons' => $seasons,
            'leagues' => $leagues,
        ]);
    }

    /**
     * @Route("/delegati", name="site_assessors")
     */
    public function assessors(AssessorRepository $assessorRepository)
    {
        $assessors = $assessorRepository->findAllOrderByName();

        return $this->render('site/assessors.html.twig', [
            'assessors' => $assessors
        ]);
    }

    /**
     * @Route("/delegati/{id}", name="assessor_profile")
     */
    public function assessorProfile(ProfileConfig $profileConfig,
                                    AssessorRepository $assessorRepository, AssessorStatsRepository $statsRepository, $id)
    {
        $assessor = $assessorRepository->find($id);
        if ($assessor === null) throw $this->createNotFoundException('Takový delegát neexistuje!');

        $seasons = $profileConfig->getSeasons();
        $leagues = $profileConfig->getLeagues();
        $stats = $statsRepository->getAssessorStats($id, $seasons, $leagues);

        return $this->render('site/assessor_profile.html.twig', [
            'assessor' => $assessor,
            'stats' => $stats,
            'seasons' => $seasons,
            'leagues' => $leagues,
        ]);
    }

    /**
     * @Route("/pro-rozhodci", name="for_officials")
     */
    public function forOfficials()
    {
        return $this->render('site/for_officials.html.twig');
    }

    /**
     * @Route("/pro-rozhodci/ustni-zkouseni", name="oral_exam")
     */
    public function oralExam()
    {
        return $this->render('site/oral_exam.html.twig');
    }

}
