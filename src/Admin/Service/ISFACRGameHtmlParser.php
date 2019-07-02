<?php

namespace App\Admin\Service;

use App\Admin\Entity\Assessor;
use App\Admin\Entity\Game;
use App\Admin\Entity\League;
use App\Admin\Entity\Offence;
use App\Admin\Entity\Official;
use App\Admin\Entity\RedCard;
use App\Admin\Entity\Team;
use App\Admin\Entity\YellowCard;
use App\Admin\Exception\LeagueNotFound;
use App\Admin\Exception\TeamNotFound;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DomCrawler\Crawler;

class ISFACRGameHtmlParser
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function parseHtml(Crawler $crawler, Game $game)
    {
        $crawler = $crawler->filter('div.book.zapis-report');

        // -------------------------------------- BASIC INFO -------------------------------------------
        $crawler_part = $crawler->filter('table table')->first();

        $league = $this->findLeague($crawler_part);
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
        // ------------------------------------ END OF BASIC INFO --------------------------------------

        // ----------------------------------- TEAMS AND OFFICIALS -------------------------------------
        $crawler_part = $crawler->filter('table.vysledky')->first();

        $homeTeam = $this->findTeam($crawler_part, 0);
        $game->setHomeTeam($homeTeam);

        $awayTeam = $this->findTeam($crawler_part, 1);
        $game->setAwayTeam($awayTeam);

        $referee = $this->findReferee($crawler_part);
        $game->setRefereeOfficial($referee);

        $AR1 = $this->findAR($crawler_part, 7);
        $game->setAr1Official($AR1);

        $AR2 = $this->findAR($crawler_part, 12);
        $game->setAr2Official($AR2);

        $assessor = $this->findAssessor($crawler_part, 21);
        $game->setAssessor($assessor);
        // -------------------------------- END OF TEAMS AND OFFICIALS ---------------------------------

        // ------------------------------------------ CARDS --------------------------------------------
        $this->findYellowCards($game, $crawler);

        // home team red cards
        $this->findRedCards($game, $crawler, 4, $homeTeam);

        // away team red cards
        $this->findRedCards($game, $crawler, 5, $awayTeam);
        // -------------------------------------- END OF CARDS -----------------------------------------
    }


    private function findLeague(Crawler $crawler_part)
    {
        $leagueName = $crawler_part->filter('td')->eq(0)->text();
        $leagueName = trim($leagueName);
        // multiple spaces to only one
        $leagueName = preg_replace('/\s+/u', ' ', $leagueName);

        $league = $this->entityManager->getRepository(League::class)->findOneBy(['fullName' => $leagueName]);
        if ($league === null) {
            $league = new League();
            $league->setFullName($leagueName);

            throw new LeagueNotFound($league);
        }

        return $league;
    }

    private function findTeam(Crawler $crawler_part, $position)
    {
        $teamInfo = $crawler_part->filter('b')->eq($position)->text();

        $teamInfo = explode('-', $teamInfo);
        $teamClubId = trim($teamInfo[0]);
        $teamFullName = trim($teamInfo[1]);

        $team = $this->entityManager->getRepository(Team::class)
            ->findOneBy(['fullName' => $teamFullName]);
        if ($team === null) {
            $team = new Team();
            $team->setFullName($teamFullName);
            $team->setClubId($teamClubId);

            throw new TeamNotFound($team);
        }

        return $team;
    }

    private function findReferee(Crawler $crawler_part)
    {
        $refereeName = $crawler_part->filter('td')->eq(3)->text();
        $refereeId = $crawler_part->filter('td')->eq(4)->text();

        $referee = $this->entityManager->getRepository(Official::class)->find($refereeId);
        if ($referee === null) {
            $referee = new Official();
            $referee->setId($refereeId);
            $referee->setName($refereeName);

            $this->entityManager->persist($referee);
            $this->entityManager->flush();
        }

        return $referee;
    }

    private function findAR(Crawler $crawler_part, $position)
    {
        $ARName = $crawler_part->filter('td')->eq($position)->text();
        if (strpos($ARName, '(N)')) {  // amateur, not interested
            $AR = $this->entityManager->getRepository(Official::class)->find('00000000');
        }
        else {
            $ARId = $crawler_part->filter('td')->eq($position+1)->text();

            $AR = $this->entityManager->getRepository(Official::class)->find($ARId);
            if ($AR === null) {
                $AR = new Official();
                $AR->setId($ARId);
                $AR->setName($ARName);

                $this->entityManager->persist($AR);
                $this->entityManager->flush();
            }
        }

        return $AR;
    }

    private function findAssessor(Crawler $crawler_part, $position)
    {
        $assessorName = $crawler_part->filter('td')->eq($position)->text();
        if ($assessorName === '') {  // without assessor
            $assessor = $this->entityManager->getRepository(Assessor::class)->find('00000000');
        }
        else {
            $assessorId = $crawler_part->filter('td')->eq($position+1)->text();

            $assessor = $this->entityManager->getRepository(Assessor::class)->find($assessorId);
            if ($assessor === null) {
                $assessor = new Assessor();
                $assessor->setId($assessorId);
                $assessor->setName($assessorName);

                $this->entityManager->persist($assessor);
                $this->entityManager->flush();
            }
        }

        return $assessor;
    }

    private function findYellowCards(Game $game, Crawler $crawler)
    {
        for ($i=0; $i<=1; ++$i) {  // for cycle - home and away team
            $crawler_part = $crawler->filter('table.vysledky.hraci table')->eq($i);
            $playersCount = $crawler_part->filter('tr')->count();

            for ($j=1; $j<$playersCount; ++$j) {
                $crawler_playerRow = $crawler_part->filter('tr')->eq($j);

                $firstYellowMinute = $crawler_playerRow->filter('td')->eq(5)->text();
                if (strpos($firstYellowMinute, '+')) {  // something like 45+2, need to convert to 45
                    $firstYellowMinute = substr($firstYellowMinute, 0, 2);
                }
                if ($firstYellowMinute !== '') {  // with yellow card
                    $yellow1 = new YellowCard();
                    if ($firstYellowMinute > 90) $firstYellowMinute = 90;
                    $yellow1->setMinute($firstYellowMinute);
                    $game->addYellowCard($yellow1);

                    // check for second yellow
                    $secondYellowMinute = $crawler_playerRow->filter('td')->eq(6)->text();
                    if (strpos($secondYellowMinute, '+')) {  // something like 45+2, need to convert to 45
                        $secondYellowMinute = substr($secondYellowMinute, 0, 2);
                    }
                    if ($secondYellowMinute !== '') {  // with second yellow card
                        $yellow2 = new YellowCard();
                        if ($secondYellowMinute > 90) $secondYellowMinute = 90;
                        $yellow2->setMinute($secondYellowMinute);
                        $game->addYellowCard($yellow2);
                    }
                }
            }
        }
    }

    private function findRedCards(Game $game, Crawler $crawler, $position, Team $team)
    {
        $crawler_part = $crawler->filter('table.vysledky.hraci table')->eq($position);
        $count = $crawler_part->filter('tr')->count();
        if ($count > 1) {  // there are some red cards
            for ($i=1; $i<($count); $i+=2) {
                $crawler_playerRow = $crawler_part->filter('tr')->eq($i);

                $playerName = trim($crawler_playerRow->filter('td')->eq(0)->text());

                $minute = $crawler_playerRow->filter('td')->eq(2)->text();
                if (strpos($minute, '+')) {  // something like 45+2, need to convert to 45
                    $minute = substr($minute, 0, 2);
                }
                if ($minute > 90) $minute = 90;

                $fullDescription = trim($crawler_part->filter('tr')->eq($i+1)->text());
                $partsOfFullDescription = preg_split('/,([^,]*),/', $fullDescription, 2, PREG_SPLIT_DELIM_CAPTURE);
                $offenceFullName = ltrim($partsOfFullDescription[1]);
                $description = trim($partsOfFullDescription[2]);

                // fix
                if ($offenceFullName === 'Použití pohoršujících') {
                    $offenceFullName = 'Použití pohoršujících, urážlivých nebo ponižujících výroků nebo gest';
                    $description = substr(strstr($description, ','), 2);
                }
                $offence = $this->entityManager->getRepository(Offence::class)
                    ->findOneBy(['fullName' => $offenceFullName]);

                $red = new RedCard();
                $red->setTeam($team);
                $red->setPerson($playerName);
                $red->setMinute($minute);
                $red->setDescription($description);
                $red->setOffence($offence);

                $game->addRedCard($red);
            }
        }
    }

}
