<?php

namespace App\Site\Repository;

use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\ParameterType;

class StatsRepository
{
    private $connection;

    public function __construct(Connection $DBALconnection)
    {
        $this->connection = $DBALconnection;
    }

    public function getSeasonStats($league, $season, $part)
    {
        $idsOfLeaguesSelectedLeagueLevel = $this->connection->fetchAll('SELECT league.id
                                                                        FROM league 
                                                                        WHERE league.short_name = ?',
                                                                        [$league]);
        
        $leaguesIds = array();
        
        foreach ($idsOfLeaguesSelectedLeagueLevel as $item) {
            $leaguesIds[] = $item['id'];
        }
        
        $stats = array();

        $stats['RefereeMatches'] = $this->getSeasonStatRefereeMatches($leaguesIds, $season, $part);
        /*
        $stats['Ar1Matches'] = $this->getSeasonStatAr1Matches($leaguesIds, $season, $part);
        $stats['Ar2Matches'] = $this->getSeasonStatAr2Matches($leaguesIds, $season, $part);
        $stats['Ar1AndAr2Matches'] = $this->getSeasonStatAr1AndAr2Matches($leaguesIds, $season, $part);
        $stats['OfficialMatches'] = $this->getSeasonStatOfficialMatches($leaguesIds, $season, $part);

        $stats['RefereeRed'] = $this->getSeasonStatRefereeRed($leaguesIds, $season, $part);
        $stats['RefereeRedOffence'] = $this->getSeasonStatRefereeRedOffence($leaguesIds, $season, $part);
        $stats['RefereeYellow'] = $this->getSeasonStatRefereeYellow($leaguesIds, $season, $part);
        $stats['RefereeYellowFirst'] = $this->getSeasonStatRefereeYellowFirst($leaguesIds, $season, $part);

        $stats['AssessorMatches'] = $this->getSeasonStatAssessorMatches($leaguesIds, $season, $part);
        $stats['AssessorRed'] = $this->getSeasonStatAssessorRed($leaguesIds, $season, $part);
        $stats['AssessorYellow'] = $this->getSeasonStatAssessorYellow($leaguesIds, $season, $part);

        $stats['RefereeAr'] = $this->getSeasonStatRefereeAr($leaguesIds, $season, $part);
        $stats['OfficialOfficial'] = $this->getSeasonStatOfficialOfficial($leaguesIds, $season, $part);
        $stats['RefereeAssessor'] = $this->getSeasonStatRefereeAssessor($leaguesIds, $season, $part);
        $stats['OfficialAssessor'] = $this->getSeasonStatOfficialAssessor($leaguesIds, $season, $part);
        $stats['OfficialTeam'] = $this->getSeasonStatOfficialTeam($leaguesIds, $season, $part);
        $stats['OfficialHomeTeam'] = $this->getSeasonStatOfficialHomeTeam($leaguesIds, $season, $part);
        $stats['AssessorTeam'] = $this->getSeasonStatAssessorTeam($leaguesIds, $season, $part);

        $stats['RefereeRedAvg'] = $this->getSeasonStatRefereeRedAvg($leaguesIds, $season, $part);
        $stats['RefereeYellowAvg'] = $this->getSeasonStatRefereeYellowAvg($leaguesIds, $season, $part);
        $stats['AssessorRedAvg'] = $this->getSeasonStatAssessorRedAvg($leaguesIds, $season, $part);
        $stats['AssessorYellowAvg'] = $this->getSeasonStatAssessorYellowAvg($leaguesIds, $season, $part);*/

        return $stats;
    }

    private function getSeasonStatRefereeMatches($leaguesIds, $season, $part)
    {
        if ($part === null) {
            return $this->connection->fetchAll('SELECT official.name, SUM(stat.number_of_matches) AS number_of_matches
                                                FROM stat_referee_matches AS stat
                                                JOIN official
                                                ON stat.official_id = official.id
                                                WHERE stat.league_id IN (?)
                                                AND stat.season = ?
                                                GROUP BY stat.official_id',
                                                [$leaguesIds, $season],
                                                [\Doctrine\DBAL\Connection::PARAM_INT_ARRAY, ParameterType::INTEGER]);
        }
        else {
            $isAutumn = 1;
            if ($part === 'jaro') $isAutumn = 0;

            return $this->connection->fetchAll('SELECT official.name, SUM(stat.number_of_matches) AS number_of_matches
                                                FROM stat_referee_matches AS stat
                                                JOIN official
                                                ON stat.official_id = official.id
                                                WHERE stat.league_id IN (?)
                                                AND stat.season = ?
                                                AND stat.is_autumn = ?
                                                GROUP BY stat.official_id',
                                                [$leaguesIds, $season, $isAutumn],
                                                [\Doctrine\DBAL\Connection::PARAM_INT_ARRAY, ParameterType::INTEGER, ParameterType::INTEGER]);
        }
    }

    private function getSeasonStatAr1Matches($leaguesIds, $season, $part)
    {
    }

    private function getSeasonStatAr2Matches($leaguesIds, $season, $part)
    {
    }

    private function getSeasonStatAr1AndAr2Matches($leaguesIds, $season, $part)
    {
    }

    private function getSeasonStatOfficialMatches($leaguesIds, $season, $part)
    {
    }

    private function getSeasonStatRefereeRed($leaguesIds, $season, $part)
    {
    }

    private function getSeasonStatRefereeRedOffence($leaguesIds, $season, $part)
    {
    }

    private function getSeasonStatRefereeYellow($leaguesIds, $season, $part)
    {
    }

    private function getSeasonStatRefereeYellowFirst($leaguesIds, $season, $part)
    {
    }

    private function getSeasonStatAssessorMatches($leaguesIds, $season, $part)
    {
    }

    private function getSeasonStatAssessorRed($leaguesIds, $season, $part)
    {
    }

    private function getSeasonStatAssessorYellow($leaguesIds, $season, $part)
    {
    }

    private function getSeasonStatRefereeAr($leaguesIds, $season, $part)
    {
    }

    private function getSeasonStatOfficialOfficial($leaguesIds, $season, $part)
    {
    }

    private function getSeasonStatRefereeAssessor($leaguesIds, $season, $part)
    {
    }

    private function getSeasonStatOfficialAssessor($leaguesIds, $season, $part)
    {
    }

    private function getSeasonStatOfficialTeam($leaguesIds, $season, $part)
    {
    }

    private function getSeasonStatOfficialHomeTeam($leaguesIds, $season, $part)
    {
    }

    private function getSeasonStatAssessorTeam($leaguesIds, $season, $part)
    {
    }

    private function getSeasonStatRefereeRedAvg($leaguesIds, $season, $part)
    {
    }

    private function getSeasonStatRefereeYellowAvg($leaguesIds, $season, $part)
    {
    }

    private function getSeasonStatAssessorRedAvg($leaguesIds, $season, $part)
    {
    }

    private function getSeasonStatAssessorYellowAvg($leaguesIds, $season, $part)
    {
    }

}
