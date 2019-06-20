<?php

namespace App\Site\Controller;

use App\Admin\Repository\AssessorRepository;
use App\Admin\Repository\OfficialRepository;
use App\Admin\Repository\PostRepository;
use App\Site\Repository\StatsRepository;
use App\Site\Service\SeasonsListMaker;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\PieChart;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class SiteController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(PostRepository $postRepository)
    {
        $posts = $postRepository->findAllOrderByPublished();

        return $this->render('site/index.html.twig', [
            'posts' => $posts
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
     * @Route("/prebor", name="prebor")
     */
    public function prebor(SeasonsListMaker $seasonsListMaker)
    {
        $seasonsWithParts = $seasonsListMaker->createSeasonsList('Přebor');

        return $this->render('site/prebor.html.twig', ['seasons' => $seasonsWithParts]);
    }

    /**
     * @Route("/a-trida", name="a_trida")
     */
    public function aTrida(SeasonsListMaker $seasonsListMaker)
    {
        $seasonsWithParts = $seasonsListMaker->createSeasonsList('1.A třída');

        return $this->render('site/a_trida.html.twig', ['seasons' => $seasonsWithParts]);
    }

    /**
     * @Route("/{league}/{season}/{part}", name="season_stats", requirements={
     *     "league"="prebor|a-trida",
     *     "season"="\d+",
     *     "part"="podzim|jaro"
     * })
     */
    public function seasonStats(StatsRepository $statsRepository, $league, $season, $part = null)
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

        return $this->render('site/season_stats.html.twig', [
            'league' => $league,
            'season' => $season,
            'part' => $part,
            'stats' => $stats,
            'redOffenceChart' => $redOffenceChart,
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
    public function officialProfile(OfficialRepository $officialRepository, $id)
    {
        $official = $officialRepository->find($id);
        if ($official === null) throw $this->createNotFoundException('Takový rozhodčí neexistuje!');

        return $this->render('site/official_profile.html.twig', [
            'official' => $official
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
    public function assessorProfile(AssessorRepository $assessorRepository, $id)
    {
        $assessor = $assessorRepository->find($id);
        if ($assessor === null) throw $this->createNotFoundException('Takový delegát neexistuje!');

        return $this->render('site/assessor_profile.html.twig', [
            'assessor' => $assessor
        ]);
    }

}
