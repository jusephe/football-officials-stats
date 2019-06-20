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
        $leaguesIdsOfSelectedLeagueLevel = $this->connection->fetchAll('SELECT league.id
                                                                        FROM league 
                                                                        WHERE league.short_name = ?',
                                                                        [$league]);
        $leaguesIds = array();
        foreach ($leaguesIdsOfSelectedLeagueLevel as $leagueId) {
            $leaguesIds[] = $leagueId['id'];
        }
        
        $stats = array();

        $stats['RefereeMatches'] = $this->getSeasonStatRefereeMatches($leaguesIds, $season, $part);
        $stats['Ar1Matches'] = $this->getSeasonStatAr1Matches($leaguesIds, $season, $part);
        $stats['Ar2Matches'] = $this->getSeasonStatAr2Matches($leaguesIds, $season, $part);
        $stats['Ar1AndAr2Matches'] = $this->getSeasonStatAr1AndAr2Matches($leaguesIds, $season, $part);
        $stats['OfficialMatches'] = $this->getSeasonStatOfficialMatches($leaguesIds, $season, $part);

        $stats['RefereeYellow'] = $this->getSeasonStatRefereeYellow($leaguesIds, $season, $part);
        $stats['RefereeYellowAvg'] = $this->getSeasonStatRefereeYellowAvg($leaguesIds, $season, $part);
        $stats['RefereeYellowFirst'] = $this->getSeasonStatRefereeYellowFirst($leaguesIds, $season, $part);
        $stats['RefereeRed'] = $this->getSeasonStatRefereeRed($leaguesIds, $season, $part);
        $stats['RefereeRedAvg'] = $this->getSeasonStatRefereeRedAvg($leaguesIds, $season, $part);
        $stats['RefereeRedOffence'] = $this->transformStatRefereeRedOffence(
            $this->getSeasonStatRefereeRedOffence($leaguesIds, $season, $part)
        );

        $stats['RefereeAr'] = $this->getSeasonStatRefereeAr($leaguesIds, $season, $part);
        $stats['OfficialOfficial'] = $this->getSeasonStatOfficialOfficial($leaguesIds, $season, $part);
        $stats['RefereeAssessor'] = $this->getSeasonStatRefereeAssessor($leaguesIds, $season, $part);
        $stats['OfficialAssessor'] = $this->getSeasonStatOfficialAssessor($leaguesIds, $season, $part);
        $stats['OfficialTeam'] = $this->getSeasonStatOfficialTeam($leaguesIds, $season, $part);
        $stats['OfficialHomeTeam'] = $this->getSeasonStatOfficialHomeTeam($leaguesIds, $season, $part);

        $stats['AssessorMatches'] = $this->getSeasonStatAssessorMatches($leaguesIds, $season, $part);
        $stats['AssessorYellow'] = $this->getSeasonStatAssessorYellow($leaguesIds, $season, $part);
        $stats['AssessorYellowAvg'] = $this->getSeasonStatAssessorYellowAvg($leaguesIds, $season, $part);
        $stats['AssessorRed'] = $this->getSeasonStatAssessorRed($leaguesIds, $season, $part);
        $stats['AssessorRedAvg'] = $this->getSeasonStatAssessorRedAvg($leaguesIds, $season, $part);
        $stats['AssessorTeam'] = $this->getSeasonStatAssessorTeam($leaguesIds, $season, $part);

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
        if ($part === null) {
            return $this->connection->fetchAll('SELECT official.name, SUM(stat.number_of_matches) AS number_of_matches
                                                FROM stat_ar1_matches AS stat
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
                                                FROM stat_ar1_matches AS stat
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

    private function getSeasonStatAr2Matches($leaguesIds, $season, $part)
    {
        if ($part === null) {
            return $this->connection->fetchAll('SELECT official.name, SUM(stat.number_of_matches) AS number_of_matches
                                                FROM stat_ar2_matches AS stat
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
                                                FROM stat_ar2_matches AS stat
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

    private function getSeasonStatAr1AndAr2Matches($leaguesIds, $season, $part)
    {
        if ($part === null) {
            return $this->connection->fetchAll('SELECT official.name, SUM(stat.number_of_matches) AS number_of_matches
                                                FROM stat_ar1_and_ar2_matches AS stat
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
                                                FROM stat_ar1_and_ar2_matches AS stat
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

    private function getSeasonStatOfficialMatches($leaguesIds, $season, $part)
    {
        if ($part === null) {
            return $this->connection->fetchAll('SELECT official.name, SUM(stat.number_of_matches) AS number_of_matches
                                                FROM stat_official_matches AS stat
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
                                                FROM stat_official_matches AS stat
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


    private function getSeasonStatRefereeYellow($leaguesIds, $season, $part)
    {
        if ($part === null) {
            return $this->connection->fetchAll('SELECT official.name, SUM(stat.number_of_cards) AS number_of_cards
                                                FROM stat_referee_yellow AS stat
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

            return $this->connection->fetchAll('SELECT official.name, SUM(stat.number_of_cards) AS number_of_cards
                                                FROM stat_referee_yellow AS stat
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

    private function getSeasonStatRefereeYellowAvg($leaguesIds, $season, $part)
    {
        if ($part === null) {
            return $this->connection->fetchAll('SELECT official.name, (IFNULL(c.number_of_cards, 0 )/m.number_of_matches) AS cards_avg
                                                FROM(
                                                    (SELECT stat.official_id AS official_id, SUM(stat.number_of_matches) AS number_of_matches
                                                    FROM stat_referee_matches AS stat
                                                    WHERE stat.league_id IN (?)
                                                    AND stat.season = ?
                                                    GROUP BY stat.official_id) m
                                                    LEFT JOIN
                                                    (SELECT stat.official_id AS official_id, SUM(stat.number_of_cards) AS number_of_cards
                                                    FROM stat_referee_yellow AS stat
                                                    WHERE stat.league_id IN (?)
                                                    AND stat.season = ?
                                                    GROUP BY stat.official_id) c
                                                    ON m.official_id = c.official_id)
                                                JOIN official
                                                ON m.official_id = official.id',
                                                [$leaguesIds, $season,
                                                    $leaguesIds, $season],
                                                [\Doctrine\DBAL\Connection::PARAM_INT_ARRAY, ParameterType::INTEGER,
                                                    \Doctrine\DBAL\Connection::PARAM_INT_ARRAY, ParameterType::INTEGER]);
        }
        else {
            $isAutumn = 1;
            if ($part === 'jaro') $isAutumn = 0;

            return $this->connection->fetchAll('SELECT official.name, (IFNULL(c.number_of_cards, 0 )/m.number_of_matches) AS cards_avg
                                                FROM(
                                                    (SELECT stat.official_id AS official_id, SUM(stat.number_of_matches) AS number_of_matches
                                                    FROM stat_referee_matches AS stat
                                                    WHERE stat.league_id IN (?)
                                                    AND stat.season = ?
                                                    AND stat.is_autumn = ?
                                                    GROUP BY stat.official_id) m
                                                    LEFT JOIN
                                                    (SELECT stat.official_id AS official_id, SUM(stat.number_of_cards) AS number_of_cards
                                                    FROM stat_referee_yellow AS stat
                                                    WHERE stat.league_id IN (?)
                                                    AND stat.season = ?
                                                    AND stat.is_autumn = ?
                                                    GROUP BY stat.official_id) c
                                                    ON m.official_id = c.official_id)
                                                JOIN official
                                                ON m.official_id = official.id',
                                                [$leaguesIds, $season, $isAutumn,
                                                    $leaguesIds, $season, $isAutumn],
                                                [\Doctrine\DBAL\Connection::PARAM_INT_ARRAY, ParameterType::INTEGER, ParameterType::INTEGER,
                                                    \Doctrine\DBAL\Connection::PARAM_INT_ARRAY, ParameterType::INTEGER, ParameterType::INTEGER]);
        }
    }

    private function getSeasonStatRefereeYellowFirst($leaguesIds, $season, $part)
    {
        if ($part === null) {
            return $this->connection->fetchAll('SELECT official.name, AVG(stat.minute) AS first_avg_min
                                                FROM stat_referee_yellow_first AS stat
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

            return $this->connection->fetchAll('SELECT official.name, AVG(stat.minute) AS first_avg_min
                                                FROM stat_referee_yellow_first AS stat
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

    private function getSeasonStatRefereeRed($leaguesIds, $season, $part)
    {
        if ($part === null) {
            return $this->connection->fetchAll('SELECT official.name, SUM(stat.number_of_cards) AS number_of_cards
                                                FROM stat_referee_red AS stat
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

            return $this->connection->fetchAll('SELECT official.name, SUM(stat.number_of_cards) AS number_of_cards
                                                FROM stat_referee_red AS stat
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

    private function getSeasonStatRefereeRedAvg($leaguesIds, $season, $part)
    {
        if ($part === null) {
            return $this->connection->fetchAll('SELECT official.name, (IFNULL(c.number_of_cards, 0 )/m.number_of_matches) AS cards_avg
                                                FROM(
                                                    (SELECT stat.official_id AS official_id, SUM(stat.number_of_matches) AS number_of_matches
                                                    FROM stat_referee_matches AS stat
                                                    WHERE stat.league_id IN (?)
                                                    AND stat.season = ?
                                                    GROUP BY stat.official_id) m
                                                    LEFT JOIN
                                                    (SELECT stat.official_id AS official_id, SUM(stat.number_of_cards) AS number_of_cards
                                                    FROM stat_referee_red AS stat
                                                    WHERE stat.league_id IN (?)
                                                    AND stat.season = ?
                                                    GROUP BY stat.official_id) c
                                                    ON m.official_id = c.official_id)
                                                JOIN official
                                                ON m.official_id = official.id',
                                                [$leaguesIds, $season,
                                                    $leaguesIds, $season],
                                                [\Doctrine\DBAL\Connection::PARAM_INT_ARRAY, ParameterType::INTEGER,
                                                    \Doctrine\DBAL\Connection::PARAM_INT_ARRAY, ParameterType::INTEGER]);
        }
        else {
            $isAutumn = 1;
            if ($part === 'jaro') $isAutumn = 0;

            return $this->connection->fetchAll('SELECT official.name, (IFNULL(c.number_of_cards, 0 )/m.number_of_matches) AS cards_avg
                                                FROM(
                                                    (SELECT stat.official_id AS official_id, SUM(stat.number_of_matches) AS number_of_matches
                                                    FROM stat_referee_matches AS stat
                                                    WHERE stat.league_id IN (?)
                                                    AND stat.season = ?
                                                    AND stat.is_autumn = ?
                                                    GROUP BY stat.official_id) m
                                                    LEFT JOIN
                                                    (SELECT stat.official_id AS official_id, SUM(stat.number_of_cards) AS number_of_cards
                                                    FROM stat_referee_red AS stat
                                                    WHERE stat.league_id IN (?)
                                                    AND stat.season = ?
                                                    AND stat.is_autumn = ?
                                                    GROUP BY stat.official_id) c
                                                    ON m.official_id = c.official_id)
                                                JOIN official
                                                ON m.official_id = official.id',
                                                [$leaguesIds, $season, $isAutumn,
                                                    $leaguesIds, $season, $isAutumn],
                                                [\Doctrine\DBAL\Connection::PARAM_INT_ARRAY, ParameterType::INTEGER, ParameterType::INTEGER,
                                                    \Doctrine\DBAL\Connection::PARAM_INT_ARRAY, ParameterType::INTEGER, ParameterType::INTEGER]);
        }
    }

    private function getSeasonStatRefereeRedOffence($leaguesIds, $season, $part)
    {
        if ($part === null) {
            return $this->connection->fetchAll('SELECT offence.short_name, SUM(stat.number_of_cards) AS number_of_cards
                                                FROM stat_referee_red_offence AS stat
                                                JOIN offence
                                                ON stat.offence_id = offence.id
                                                WHERE stat.league_id IN (?)
                                                AND stat.season = ?
                                                GROUP BY stat.offence_id
                                                ORDER BY number_of_cards DESC',
                                                [$leaguesIds, $season],
                                                [\Doctrine\DBAL\Connection::PARAM_INT_ARRAY, ParameterType::INTEGER]);
        }
        else {
            $isAutumn = 1;
            if ($part === 'jaro') $isAutumn = 0;

            return $this->connection->fetchAll('SELECT offence.short_name, SUM(stat.number_of_cards) AS number_of_cards
                                                FROM stat_referee_red_offence AS stat
                                                JOIN offence
                                                ON stat.offence_id = offence.id
                                                WHERE stat.league_id IN (?)
                                                AND stat.season = ?
                                                AND stat.is_autumn = ?
                                                GROUP BY stat.offence_id
                                                ORDER BY number_of_cards DESC',
                                                [$leaguesIds, $season, $isAutumn],
                                                [\Doctrine\DBAL\Connection::PARAM_INT_ARRAY, ParameterType::INTEGER, ParameterType::INTEGER]);
        }
    }

    // transforms to array which can be use as source for chart with CMENGoogleChartsBundle
    private function transformStatRefereeRedOffence($statArray)
    {
        $newStatArray = array();

        $newStatArray[] = ['Důvod', 'Počet'];
        foreach ($statArray as $row) {
            $newStatArray[] = [ $row['short_name'], (int)$row['number_of_cards'] ];
        }

        return $newStatArray;
    }


    private function getSeasonStatRefereeAr($leaguesIds, $season, $part)
    {
        if ($part === null) {
            return $this->connection->fetchAll('SELECT referee.name AS referee_name, ar.name AS ar_name, SUM(stat.number_of_matches) AS number_of_matches
                                                FROM stat_referee_ar AS stat
                                                JOIN official AS referee
                                                ON stat.referee_official_id = referee.id
                                                JOIN official AS ar
                                                ON stat.ar_official_id = ar.id
                                                WHERE stat.league_id IN (?)
                                                AND stat.season = ?
                                                GROUP BY stat.referee_official_id, stat.ar_official_id',
                                                [$leaguesIds, $season],
                                                [\Doctrine\DBAL\Connection::PARAM_INT_ARRAY, ParameterType::INTEGER]);
        }
        else {
            $isAutumn = 1;
            if ($part === 'jaro') $isAutumn = 0;

            return $this->connection->fetchAll('SELECT referee.name AS referee_name, ar.name AS ar_name, SUM(stat.number_of_matches) AS number_of_matches
                                                FROM stat_referee_ar AS stat
                                                JOIN official AS referee
                                                ON stat.referee_official_id = referee.id
                                                JOIN official AS ar
                                                ON stat.ar_official_id = ar.id
                                                WHERE stat.league_id IN (?)
                                                AND stat.season = ?
                                                AND stat.is_autumn = ?
                                                GROUP BY stat.referee_official_id, stat.ar_official_id',
                                                [$leaguesIds, $season, $isAutumn],
                                                [\Doctrine\DBAL\Connection::PARAM_INT_ARRAY, ParameterType::INTEGER, ParameterType::INTEGER]);
        }
    }

    private function getSeasonStatOfficialOfficial($leaguesIds, $season, $part)
    {
        if ($part === null) {
            return $this->connection->fetchAll('SELECT official1.name AS official1_name, official2.name AS official2_name, SUM(stat.number_of_matches) AS number_of_matches
                                                FROM stat_official_official AS stat
                                                JOIN official AS official1
                                                ON stat.official_id1 = official1.id
                                                JOIN official AS official2
                                                ON stat.official_id2 = official2.id
                                                WHERE stat.league_id IN (?)
                                                AND stat.season = ?
                                                GROUP BY stat.official_id1, stat.official_id2',
                                                [$leaguesIds, $season],
                                                [\Doctrine\DBAL\Connection::PARAM_INT_ARRAY, ParameterType::INTEGER]);
        }
        else {
            $isAutumn = 1;
            if ($part === 'jaro') $isAutumn = 0;

            return $this->connection->fetchAll('SELECT official1.name AS official1_name, official2.name AS official2_name, SUM(stat.number_of_matches) AS number_of_matches
                                                FROM stat_official_official AS stat
                                                JOIN official AS official1
                                                ON stat.official_id1 = official1.id
                                                JOIN official AS official2
                                                ON stat.official_id2 = official2.id
                                                WHERE stat.league_id IN (?)
                                                AND stat.season = ?
                                                AND stat.is_autumn = ?
                                                GROUP BY stat.official_id1, stat.official_id2',
                                                [$leaguesIds, $season, $isAutumn],
                                                [\Doctrine\DBAL\Connection::PARAM_INT_ARRAY, ParameterType::INTEGER, ParameterType::INTEGER]);
        }
    }

    private function getSeasonStatRefereeAssessor($leaguesIds, $season, $part)
    {
        if ($part === null) {
            return $this->connection->fetchAll('SELECT official.name AS referee_name, assessor.name AS assessor_name, SUM(stat.number_of_matches) AS number_of_matches
                                                FROM stat_referee_assessor AS stat
                                                JOIN official
                                                ON stat.official_id = official.id
                                                JOIN assessor
                                                ON stat.assessor_id = assessor.id
                                                WHERE stat.league_id IN (?)
                                                AND stat.season = ?
                                                GROUP BY stat.official_id, stat.assessor_id',
                                                [$leaguesIds, $season],
                                                [\Doctrine\DBAL\Connection::PARAM_INT_ARRAY, ParameterType::INTEGER]);
        }
        else {
            $isAutumn = 1;
            if ($part === 'jaro') $isAutumn = 0;

            return $this->connection->fetchAll('SELECT official.name AS referee_name, assessor.name AS assessor_name, SUM(stat.number_of_matches) AS number_of_matches
                                                FROM stat_referee_assessor AS stat
                                                JOIN official
                                                ON stat.official_id = official.id
                                                JOIN assessor
                                                ON stat.assessor_id = assessor.id
                                                WHERE stat.league_id IN (?)
                                                AND stat.season = ?
                                                AND stat.is_autumn = ?
                                                GROUP BY stat.official_id, stat.assessor_id',
                                                [$leaguesIds, $season, $isAutumn],
                                                [\Doctrine\DBAL\Connection::PARAM_INT_ARRAY, ParameterType::INTEGER, ParameterType::INTEGER]);
        }
    }

    private function getSeasonStatOfficialAssessor($leaguesIds, $season, $part)
    {
        if ($part === null) {
            return $this->connection->fetchAll('SELECT official.name AS official_name, assessor.name AS assessor_name, SUM(stat.number_of_matches) AS number_of_matches
                                                FROM stat_official_assessor AS stat
                                                JOIN official
                                                ON stat.official_id = official.id
                                                JOIN assessor
                                                ON stat.assessor_id = assessor.id
                                                WHERE stat.league_id IN (?)
                                                AND stat.season = ?
                                                GROUP BY stat.official_id, stat.assessor_id',
                                                [$leaguesIds, $season],
                                                [\Doctrine\DBAL\Connection::PARAM_INT_ARRAY, ParameterType::INTEGER]);
        }
        else {
            $isAutumn = 1;
            if ($part === 'jaro') $isAutumn = 0;

            return $this->connection->fetchAll('SELECT official.name AS official_name, assessor.name AS assessor_name, SUM(stat.number_of_matches) AS number_of_matches
                                                FROM stat_official_assessor AS stat
                                                JOIN official
                                                ON stat.official_id = official.id
                                                JOIN assessor
                                                ON stat.assessor_id = assessor.id
                                                WHERE stat.league_id IN (?)
                                                AND stat.season = ?
                                                AND stat.is_autumn = ?
                                                GROUP BY stat.official_id, stat.assessor_id',
                                                [$leaguesIds, $season, $isAutumn],
                                                [\Doctrine\DBAL\Connection::PARAM_INT_ARRAY, ParameterType::INTEGER, ParameterType::INTEGER]);
        }
    }

    private function getSeasonStatOfficialTeam($leaguesIds, $season, $part)
    {
        if ($part === null) {
            return $this->connection->fetchAll('SELECT official.name AS official_name, team.short_name AS team_name, SUM(stat.number_of_matches) AS number_of_matches
                                                FROM stat_official_team AS stat
                                                JOIN official
                                                ON stat.official_id = official.id
                                                JOIN team
                                                ON stat.team_id = team.id
                                                WHERE stat.league_id IN (?)
                                                AND stat.season = ?
                                                GROUP BY stat.official_id, stat.team_id',
                                                [$leaguesIds, $season],
                                                [\Doctrine\DBAL\Connection::PARAM_INT_ARRAY, ParameterType::INTEGER]);
        }
        else {
            $isAutumn = 1;
            if ($part === 'jaro') $isAutumn = 0;

            return $this->connection->fetchAll('SELECT official.name AS official_name, team.short_name AS team_name, SUM(stat.number_of_matches) AS number_of_matches
                                                FROM stat_official_team AS stat
                                                JOIN official
                                                ON stat.official_id = official.id
                                                JOIN team
                                                ON stat.team_id = team.id
                                                WHERE stat.league_id IN (?)
                                                AND stat.season = ?
                                                AND stat.is_autumn = ?
                                                GROUP BY stat.official_id, stat.team_id',
                                                [$leaguesIds, $season, $isAutumn],
                                                [\Doctrine\DBAL\Connection::PARAM_INT_ARRAY, ParameterType::INTEGER, ParameterType::INTEGER]);
        }
    }

    private function getSeasonStatOfficialHomeTeam($leaguesIds, $season, $part)
    {
        if ($part === null) {
            return $this->connection->fetchAll('SELECT official.name AS official_name, team.short_name AS team_name, SUM(stat.number_of_matches) AS number_of_matches
                                                FROM stat_official_home_team AS stat
                                                JOIN official
                                                ON stat.official_id = official.id
                                                JOIN team
                                                ON stat.team_id = team.id
                                                WHERE stat.league_id IN (?)
                                                AND stat.season = ?
                                                GROUP BY stat.official_id, stat.team_id',
                                                [$leaguesIds, $season],
                                                [\Doctrine\DBAL\Connection::PARAM_INT_ARRAY, ParameterType::INTEGER]);
        }
        else {
            $isAutumn = 1;
            if ($part === 'jaro') $isAutumn = 0;

            return $this->connection->fetchAll('SELECT official.name AS official_name, team.short_name AS team_name, SUM(stat.number_of_matches) AS number_of_matches
                                                FROM stat_official_home_team AS stat
                                                JOIN official
                                                ON stat.official_id = official.id
                                                JOIN team
                                                ON stat.team_id = team.id
                                                WHERE stat.league_id IN (?)
                                                AND stat.season = ?
                                                AND stat.is_autumn = ?
                                                GROUP BY stat.official_id, stat.team_id',
                                                [$leaguesIds, $season, $isAutumn],
                                                [\Doctrine\DBAL\Connection::PARAM_INT_ARRAY, ParameterType::INTEGER, ParameterType::INTEGER]);
        }
    }


    private function getSeasonStatAssessorMatches($leaguesIds, $season, $part)
    {
        if ($part === null) {
            return $this->connection->fetchAll('SELECT assessor.name, SUM(stat.number_of_matches) AS number_of_matches
                                                FROM stat_assessor_matches AS stat
                                                JOIN assessor
                                                ON stat.assessor_id = assessor.id
                                                WHERE stat.league_id IN (?)
                                                AND stat.season = ?
                                                GROUP BY stat.assessor_id',
                                                [$leaguesIds, $season],
                                                [\Doctrine\DBAL\Connection::PARAM_INT_ARRAY, ParameterType::INTEGER]);
        }
        else {
            $isAutumn = 1;
            if ($part === 'jaro') $isAutumn = 0;

            return $this->connection->fetchAll('SELECT assessor.name, SUM(stat.number_of_matches) AS number_of_matches
                                                FROM stat_assessor_matches AS stat
                                                JOIN assessor
                                                ON stat.assessor_id = assessor.id
                                                WHERE stat.league_id IN (?)
                                                AND stat.season = ?
                                                AND stat.is_autumn = ?
                                                GROUP BY stat.assessor_id',
                                                [$leaguesIds, $season, $isAutumn],
                                                [\Doctrine\DBAL\Connection::PARAM_INT_ARRAY, ParameterType::INTEGER, ParameterType::INTEGER]);
        }
    }

    private function getSeasonStatAssessorYellow($leaguesIds, $season, $part)
    {
        if ($part === null) {
            return $this->connection->fetchAll('SELECT assessor.name, SUM(stat.number_of_cards) AS number_of_cards
                                                FROM stat_assessor_yellow AS stat
                                                JOIN assessor
                                                ON stat.assessor_id = assessor.id
                                                WHERE stat.league_id IN (?)
                                                AND stat.season = ?
                                                GROUP BY stat.assessor_id',
                                                [$leaguesIds, $season],
                                                [\Doctrine\DBAL\Connection::PARAM_INT_ARRAY, ParameterType::INTEGER]);
        }
        else {
            $isAutumn = 1;
            if ($part === 'jaro') $isAutumn = 0;

            return $this->connection->fetchAll('SELECT assessor.name, SUM(stat.number_of_cards) AS number_of_cards
                                                FROM stat_assessor_yellow AS stat
                                                JOIN assessor
                                                ON stat.assessor_id = assessor.id
                                                WHERE stat.league_id IN (?)
                                                AND stat.season = ?
                                                AND stat.is_autumn = ?
                                                GROUP BY stat.assessor_id',
                                                [$leaguesIds, $season, $isAutumn],
                                                [\Doctrine\DBAL\Connection::PARAM_INT_ARRAY, ParameterType::INTEGER, ParameterType::INTEGER]);
        }
    }

    private function getSeasonStatAssessorYellowAvg($leaguesIds, $season, $part)
    {
        if ($part === null) {
            return $this->connection->fetchAll('SELECT assessor.name, (IFNULL(c.number_of_cards, 0 )/m.number_of_matches) AS cards_avg
                                                FROM(
                                                    (SELECT stat.assessor_id AS assessor_id, SUM(stat.number_of_matches) AS number_of_matches
                                                    FROM stat_assessor_matches AS stat
                                                    WHERE stat.league_id IN (?)
                                                    AND stat.season = ?
                                                    GROUP BY stat.assessor_id) m
                                                    LEFT JOIN
                                                    (SELECT stat.assessor_id AS assessor_id, SUM(stat.number_of_cards) AS number_of_cards
                                                    FROM stat_assessor_yellow AS stat
                                                    WHERE stat.league_id IN (?)
                                                    AND stat.season = ?
                                                    GROUP BY stat.assessor_id) c
                                                    ON m.assessor_id = c.assessor_id)
                                                JOIN assessor
                                                ON m.assessor_id = assessor.id',
                                                [$leaguesIds, $season,
                                                    $leaguesIds, $season],
                                                [\Doctrine\DBAL\Connection::PARAM_INT_ARRAY, ParameterType::INTEGER,
                                                    \Doctrine\DBAL\Connection::PARAM_INT_ARRAY, ParameterType::INTEGER]);
        }
        else {
            $isAutumn = 1;
            if ($part === 'jaro') $isAutumn = 0;

            return $this->connection->fetchAll('SELECT assessor.name, (IFNULL(c.number_of_cards, 0 )/m.number_of_matches) AS cards_avg
                                                FROM(
                                                    (SELECT stat.assessor_id AS assessor_id, SUM(stat.number_of_matches) AS number_of_matches
                                                    FROM stat_assessor_matches AS stat
                                                    WHERE stat.league_id IN (?)
                                                    AND stat.season = ?
                                                    AND stat.is_autumn = ?
                                                    GROUP BY stat.assessor_id) m
                                                    LEFT JOIN
                                                    (SELECT stat.assessor_id AS assessor_id, SUM(stat.number_of_cards) AS number_of_cards
                                                    FROM stat_assessor_yellow AS stat
                                                    WHERE stat.league_id IN (?)
                                                    AND stat.season = ?
                                                    AND stat.is_autumn = ?
                                                    GROUP BY stat.assessor_id) c
                                                    ON m.assessor_id = c.assessor_id)
                                                JOIN assessor
                                                ON m.assessor_id = assessor.id',
                                                [$leaguesIds, $season, $isAutumn,
                                                    $leaguesIds, $season, $isAutumn],
                                                [\Doctrine\DBAL\Connection::PARAM_INT_ARRAY, ParameterType::INTEGER, ParameterType::INTEGER,
                                                    \Doctrine\DBAL\Connection::PARAM_INT_ARRAY, ParameterType::INTEGER, ParameterType::INTEGER]);
        }
    }

    private function getSeasonStatAssessorRed($leaguesIds, $season, $part)
    {
        if ($part === null) {
            return $this->connection->fetchAll('SELECT assessor.name, SUM(stat.number_of_cards) AS number_of_cards
                                                FROM stat_assessor_red AS stat
                                                JOIN assessor
                                                ON stat.assessor_id = assessor.id
                                                WHERE stat.league_id IN (?)
                                                AND stat.season = ?
                                                GROUP BY stat.assessor_id',
                                                [$leaguesIds, $season],
                                                [\Doctrine\DBAL\Connection::PARAM_INT_ARRAY, ParameterType::INTEGER]);
        }
        else {
            $isAutumn = 1;
            if ($part === 'jaro') $isAutumn = 0;

            return $this->connection->fetchAll('SELECT assessor.name, SUM(stat.number_of_cards) AS number_of_cards
                                                FROM stat_assessor_red AS stat
                                                JOIN assessor
                                                ON stat.assessor_id = assessor.id
                                                WHERE stat.league_id IN (?)
                                                AND stat.season = ?
                                                AND stat.is_autumn = ?
                                                GROUP BY stat.assessor_id',
                                                [$leaguesIds, $season, $isAutumn],
                                                [\Doctrine\DBAL\Connection::PARAM_INT_ARRAY, ParameterType::INTEGER, ParameterType::INTEGER]);
        }
    }

    private function getSeasonStatAssessorRedAvg($leaguesIds, $season, $part)
    {
        if ($part === null) {
            return $this->connection->fetchAll('SELECT assessor.name, (IFNULL(c.number_of_cards, 0 )/m.number_of_matches) AS cards_avg
                                                FROM(
                                                    (SELECT stat.assessor_id AS assessor_id, SUM(stat.number_of_matches) AS number_of_matches
                                                    FROM stat_assessor_matches AS stat
                                                    WHERE stat.league_id IN (?)
                                                    AND stat.season = ?
                                                    GROUP BY stat.assessor_id) m
                                                    LEFT JOIN
                                                    (SELECT stat.assessor_id AS assessor_id, SUM(stat.number_of_cards) AS number_of_cards
                                                    FROM stat_assessor_red AS stat
                                                    WHERE stat.league_id IN (?)
                                                    AND stat.season = ?
                                                    GROUP BY stat.assessor_id) c
                                                    ON m.assessor_id = c.assessor_id)
                                                JOIN assessor
                                                ON m.assessor_id = assessor.id',
                                                [$leaguesIds, $season,
                                                    $leaguesIds, $season],
                                                [\Doctrine\DBAL\Connection::PARAM_INT_ARRAY, ParameterType::INTEGER,
                                                    \Doctrine\DBAL\Connection::PARAM_INT_ARRAY, ParameterType::INTEGER]);
        }
        else {
            $isAutumn = 1;
            if ($part === 'jaro') $isAutumn = 0;

            return $this->connection->fetchAll('SELECT assessor.name, (IFNULL(c.number_of_cards, 0 )/m.number_of_matches) AS cards_avg
                                                FROM(
                                                    (SELECT stat.assessor_id AS assessor_id, SUM(stat.number_of_matches) AS number_of_matches
                                                    FROM stat_assessor_matches AS stat
                                                    WHERE stat.league_id IN (?)
                                                    AND stat.season = ?
                                                    AND stat.is_autumn = ?
                                                    GROUP BY stat.assessor_id) m
                                                    LEFT JOIN
                                                    (SELECT stat.assessor_id AS assessor_id, SUM(stat.number_of_cards) AS number_of_cards
                                                    FROM stat_assessor_red AS stat
                                                    WHERE stat.league_id IN (?)
                                                    AND stat.season = ?
                                                    AND stat.is_autumn = ?
                                                    GROUP BY stat.assessor_id) c
                                                    ON m.assessor_id = c.assessor_id)
                                                JOIN assessor
                                                ON m.assessor_id = assessor.id',
                                                [$leaguesIds, $season, $isAutumn,
                                                    $leaguesIds, $season, $isAutumn],
                                                [\Doctrine\DBAL\Connection::PARAM_INT_ARRAY, ParameterType::INTEGER, ParameterType::INTEGER,
                                                    \Doctrine\DBAL\Connection::PARAM_INT_ARRAY, ParameterType::INTEGER, ParameterType::INTEGER]);
        }
    }

    private function getSeasonStatAssessorTeam($leaguesIds, $season, $part)
    {
        if ($part === null) {
            return $this->connection->fetchAll('SELECT assessor.name AS assessor_name, team.short_name AS team_name, SUM(stat.number_of_matches) AS number_of_matches
                                                FROM stat_assessor_team AS stat
                                                JOIN assessor
                                                ON stat.assessor_id = assessor.id
                                                JOIN team
                                                ON stat.team_id = team.id
                                                WHERE stat.league_id IN (?)
                                                AND stat.season = ?
                                                GROUP BY stat.assessor_id, stat.team_id',
                                                [$leaguesIds, $season],
                                                [\Doctrine\DBAL\Connection::PARAM_INT_ARRAY, ParameterType::INTEGER]);
        }
        else {
            $isAutumn = 1;
            if ($part === 'jaro') $isAutumn = 0;

            return $this->connection->fetchAll('SELECT assessor.name AS assessor_name, team.short_name AS team_name, SUM(stat.number_of_matches) AS number_of_matches
                                                FROM stat_assessor_team AS stat
                                                JOIN assessor
                                                ON stat.assessor_id = assessor.id
                                                JOIN team
                                                ON stat.team_id = team.id
                                                WHERE stat.league_id IN (?)
                                                AND stat.season = ?
                                                AND stat.is_autumn = ?
                                                GROUP BY stat.assessor_id, stat.team_id',
                                                [$leaguesIds, $season, $isAutumn],
                                                [\Doctrine\DBAL\Connection::PARAM_INT_ARRAY, ParameterType::INTEGER, ParameterType::INTEGER]);
        }
    }

}
