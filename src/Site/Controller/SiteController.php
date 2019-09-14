<?php

namespace App\Site\Controller;

use App\Admin\Repository\AssessorRepository;
use App\Admin\Repository\OfficialRepository;
use App\Admin\Repository\PostRepository;
use App\Admin\Repository\RedCardRepository;
use App\Site\Repository\AssessorStatsRepository;
use App\Site\Repository\OfficialStatsRepository;
use App\Site\Repository\SeasonStatsRepository;
use App\Site\Service\SeasonsListMaker;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\LineChart;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\PieChart;
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
    public function seasonStats(SeasonStatsRepository $statsRepository, RedCardRepository $redCardRepository,
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

        $redOffenceChart = new PieChart();
        $redOffenceChart->getData()->setArrayToDataTable($stats['RefereeRedOffence']);
        $redOffenceChart->getOptions()->setPieSliceText('value');
        $redOffenceChart->getOptions()->getChartArea()->setWidth('90%');
        $redOffenceChart->getOptions()->setBackgroundColor('transparent');

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
    public function officialProfile(OfficialRepository $officialRepository, OfficialStatsRepository $statsRepository, $id)
    {
        $official = $officialRepository->find($id);
        if ($official === null) throw $this->createNotFoundException('Takový rozhodčí neexistuje!');

        $currentYear = date('Y');
        $currentMonth = date('n');
        if($currentMonth < 8) {
            $seasons = [$currentYear-4, $currentYear-3, $currentYear-2, $currentYear-1]; // which seasons display in interaction stats tables
        }
        else $seasons = [$currentYear-3, $currentYear-2, $currentYear-1, $currentYear];

        $leagues = ['Přebor', '1.A třída']; // which leagues display in basic stats tables

        $stats = $statsRepository->getOfficialStats($id, $seasons, $leagues);

        $redOffenceChart = new PieChart();
        $redOffenceChart->getData()->setArrayToDataTable($stats['RefereeRedOffence']);
        $redOffenceChart->getOptions()->setPieSliceText('value');
        $redOffenceChart->getOptions()->getChartArea()->setWidth('90%');
        $redOffenceChart->getOptions()->setBackgroundColor('transparent');

        $cardsMinutesChart = new LineChart();
        $cardsMinutesChart->getData()->setArrayToDataTable($stats['RefereeCardsMinutes']);
        $cardsMinutesChart->getOptions()->getChartArea()->setWidth('90%');
        $cardsMinutesChart->getOptions()->getChartArea()->setTop('10%');
        $cardsMinutesChart->getOptions()->getLegend()->setPosition('bottom');
        $cardsMinutesChart->getOptions()->getHAxis()->setTicks([5,10,15,20,25,30,35,40,45,50,55,60,65,70,75,80,85,90]);
        $cardsMinutesChart->getOptions()->getVAxis()->getGridlines()->setCount(
            $stats['RefereeCardsMinutesMaxNumberOfCards'] + 1 );
        $cardsMinutesChart->getOptions()->getVAxis()->getMinorGridlines()->setCount(0);
        $cardsMinutesChart->getOptions()->setColors(['gold', 'crimson']);
        $cardsMinutesChart->getOptions()->setBackgroundColor('transparent');
        $cardsMinutesChart->getOptions()->setLineWidth(3);

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
    public function assessorProfile(AssessorRepository $assessorRepository, AssessorStatsRepository $statsRepository, $id)
    {
        $assessor = $assessorRepository->find($id);
        if ($assessor === null) throw $this->createNotFoundException('Takový delegát neexistuje!');

        $currentYear = date('Y');
        $currentMonth = date('n');
        if($currentMonth < 8) {
            $seasons = [$currentYear-4, $currentYear-3, $currentYear-2, $currentYear-1]; // which seasons display in interaction stats tables
        }
        else $seasons = [$currentYear-3, $currentYear-2, $currentYear-1, $currentYear];

        $leagues = ['Přebor', '1.A třída']; // which leagues display in basic stats tables

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
