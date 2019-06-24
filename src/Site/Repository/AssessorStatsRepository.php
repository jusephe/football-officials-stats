<?php

namespace App\Site\Repository;

use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\ParameterType;

class AssessorStatsRepository
{
    private $connection;

    public function __construct(Connection $DBALconnection)
    {
        $this->connection = $DBALconnection;
    }

    public function getAssessorStats($id, $seasons, $leagues)
    {
        $stats = array();

        $stats['AssessorMatches'] = $this->transformQuantityStat($this->getStatAssessorMatches($id, $leagues));

        $stats['AssessorYellow'] = $this->transformQuantityStat($this->getStatAssessorYellow($id, $leagues));

        $yellowAvg = $this->getStatAssessorYellowAvg($id, $leagues);
        $yellowAvgSeason = $this->getStatAssessorYellowAvgTotalSeason($id, $leagues);
        $stats['AssessorYellowAvg'] = $this->transformAvgStat($yellowAvg, $yellowAvgSeason);
        $stats['AssessorYellowAvgTotals'] = $this->getStatAssessorYellowAvgTotals($id, $leagues);

        $stats['AssessorRed'] = $this->transformQuantityStat($this->getStatAssessorRed($id, $leagues));

        $redAvg = $this->getStatAssessorRedAvg($id, $leagues);
        $redAvgSeason = $this->getStatAssessorRedAvgTotalSeason($id, $leagues);
        $stats['AssessorRedAvg'] = $this->transformAvgStat($redAvg, $redAvgSeason);
        $stats['AssessorRedAvgTotals'] = $this->getStatAssessorRedAvgTotals($id, $leagues);

        $stats['AssessorTeam'] = $this->transformInteractionStat($this->getStatAssessorTeam($id, $seasons));

        return $stats;
    }


    // transforms given array with stat data to format that can be use in tables
    private function transformQuantityStat($statArray)
    {
        $newStatArray = array();
        foreach ($statArray as $row) {
            $newStatArray[$row['season']][$row['league_name']] = $row['number'];
        }
        // sum for each entity
        foreach ($newStatArray as &$array) {
            $sum = 0;
            foreach ($array as $item) {
                $sum += intval($item);
            }
            $array['total'] = $sum;
        }

        return $newStatArray;
    }

    private function getStatAssessorMatches($id, $leagues)
    {
        return $this->connection->fetchAll('SELECT stat.season, league.short_name AS league_name, SUM(stat.number_of_matches) AS `number`
                                            FROM stat_assessor_matches AS stat
                                            JOIN league
                                            ON stat.league_id = league.id
                                            WHERE stat.assessor_id = ?
                                            AND league.short_name IN (?)
                                            GROUP BY league.short_name,
                                                     stat.season',
            [$id, $leagues],
            [ParameterType::STRING, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY]);
    }


    private function getStatAssessorYellow($id, $leagues)
    {
        return $this->connection->fetchAll('SELECT stat.season, league.short_name AS league_name, SUM(stat.number_of_cards) AS `number`
                                            FROM stat_assessor_yellow AS stat
                                            JOIN league
                                            ON stat.league_id = league.id
                                            WHERE stat.assessor_id = ?
                                            AND league.short_name IN (?)
                                            GROUP BY league.short_name,
                                                     stat.season',
            [$id, $leagues],
            [ParameterType::STRING, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY]);
    }


    // transforms given array with stat data to format that can be use in tables
    private function transformAvgStat($avg, $avgSeason)
    {
        $newStatArray = array();
        foreach ($avg as $row) {
            $newStatArray[$row['season']][$row['league_name']] = $row['number'];
        }

        // add season total avg to every season
        foreach ($avgSeason as $array) {
            $newStatArray[$array['season']]['total'] = $array['total'];
        }

        return $newStatArray;
    }

    private function getStatAssessorYellowAvg($id, $leagues)
    {
        return $this->connection->fetchAll('SELECT m.season, m.league_name, (IFNULL(c.cards, 0 )/m.matches) AS `number`
                                            FROM	
                                                (SELECT stat.season AS season, league.short_name AS league_name, SUM(stat.number_of_matches) AS matches
                                                FROM stat_assessor_matches AS stat
                                                JOIN league
                                                ON stat.league_id = league.id
                                                WHERE stat.assessor_id = ?
                                                AND league.short_name IN (?)
                                                GROUP BY league.short_name,
                                                stat.season) m
                                                LEFT JOIN
                                                (SELECT stat.season AS season, league.short_name AS league_name, SUM(stat.number_of_cards) AS cards
                                                FROM stat_assessor_yellow AS stat
                                                JOIN league
                                                ON stat.league_id = league.id
                                                WHERE stat.assessor_id = ?
                                                AND league.short_name IN (?)
                                                GROUP BY league.short_name,
                                                stat.season) c
                                                ON m.season = c.season
                                                AND m.league_name = c.league_name',
            [$id, $leagues, $id, $leagues],
            [ParameterType::STRING, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY,
                ParameterType::STRING, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY]);
    }

    private function getStatAssessorYellowAvgTotalSeason($id, $leagues)
    {
        return $this->connection->fetchAll('SELECT m.season, (IFNULL(c.cards, 0 )/m.matches) AS total
                                            FROM
                                                (SELECT stat.season AS season, SUM(stat.number_of_matches) AS matches
                                                FROM stat_assessor_matches AS stat
                                                JOIN league
                                                ON stat.league_id = league.id
                                                WHERE stat.assessor_id = ?
                                                AND league.short_name IN (?)
                                                GROUP BY stat.season) m
                                                LEFT JOIN
                                                (SELECT stat.season AS season, SUM(stat.number_of_cards) AS cards
                                                FROM stat_assessor_yellow AS stat
                                                JOIN league
                                                ON stat.league_id = league.id
                                                WHERE stat.assessor_id = ?
                                                AND league.short_name IN (?)
                                                GROUP BY stat.season) c
                                                ON m.season = c.season',
            [$id, $leagues, $id, $leagues],
            [ParameterType::STRING, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY,
                ParameterType::STRING, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY]);
    }

    private function getStatAssessorYellowAvgTotals($id, $leagues)
    {
        $totalArray = array();

        foreach ($leagues as $league) {
            $totalArray[$league] = $this->getStatAssessorYellowAvgTotalLeague($id, $league);
        }

        $totalArray['total'] = $this->getStatAssessorYellowAvgTotal($id, $leagues);

        return $totalArray;
    }

    private function getStatAssessorYellowAvgTotalLeague($id, $league)
    {
        return $this->connection->fetchColumn('SELECT (IFNULL(c.cards, 0)/m.matches) AS total
                                               FROM
                                                    (SELECT stat.assessor_id AS assessor_id, SUM(stat.number_of_matches) AS matches
                                                    FROM stat_assessor_matches AS stat
                                                    JOIN league
                                                    ON stat.league_id = league.id
                                                    WHERE stat.assessor_id = ?
                                                    AND league.short_name = ?
                                                    GROUP BY league.short_name) m
                                                    LEFT JOIN
                                                    (SELECT stat.assessor_id AS assessor_id, SUM(stat.number_of_cards) AS cards
                                                    FROM stat_assessor_yellow AS stat
                                                    JOIN league
                                                    ON stat.league_id = league.id
                                                    WHERE stat.assessor_id = ?
                                                    AND league.short_name = ?
                                                    GROUP BY league.short_name) c
                                                    ON m.assessor_id = c.assessor_id',
            [$id, $league, $id, $league],
            0);
    }

    private function getStatAssessorYellowAvgTotal($id, $leagues)
    {
        return $this->connection->fetchColumn('SELECT (IFNULL(c.cards, 0)/m.matches) AS total
                                               FROM
                                                    (SELECT stat.assessor_id AS assessor_id, SUM(stat.number_of_matches) AS matches
                                                    FROM stat_assessor_matches AS stat
                                                    JOIN league
                                                    ON stat.league_id = league.id
                                                    WHERE stat.assessor_id = ?
                                                    AND league.short_name IN (?)
                                                    GROUP BY stat.assessor_id) m
                                                    LEFT JOIN
                                                    (SELECT stat.assessor_id AS assessor_id, SUM(stat.number_of_cards) AS cards
                                                    FROM stat_assessor_yellow AS stat
                                                    JOIN league
                                                    ON stat.league_id = league.id
                                                    WHERE stat.assessor_id = ?
                                                    AND league.short_name IN (?)
                                                    GROUP BY stat.assessor_id) c
                                                    ON m.assessor_id = c.assessor_id',
            [$id, $leagues, $id, $leagues],
            0,
            [ParameterType::STRING, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY,
                ParameterType::STRING, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY]
        );
    }


    private function getStatAssessorRed($id, $leagues)
    {
        return $this->connection->fetchAll('SELECT stat.season, league.short_name AS league_name, SUM(stat.number_of_cards) AS `number`
                                            FROM stat_assessor_red AS stat
                                            JOIN league
                                            ON stat.league_id = league.id
                                            WHERE stat.assessor_id = ?
                                            AND league.short_name IN (?)
                                            GROUP BY league.short_name,
                                                     stat.season',
            [$id, $leagues],
            [ParameterType::STRING, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY]);
    }


    private function getStatAssessorRedAvg($id, $leagues)
    {
        return $this->connection->fetchAll('SELECT m.season, m.league_name, (IFNULL(c.cards, 0 )/m.matches) AS `number`
                                            FROM	
                                                (SELECT stat.season AS season, league.short_name AS league_name, SUM(stat.number_of_matches) AS matches
                                                FROM stat_assessor_matches AS stat
                                                JOIN league
                                                ON stat.league_id = league.id
                                                WHERE stat.assessor_id = ?
                                                AND league.short_name IN (?)
                                                GROUP BY league.short_name,
                                                         stat.season) m
                                                LEFT JOIN
                                                (SELECT stat.season AS season, league.short_name AS league_name, SUM(stat.number_of_cards) AS cards
                                                FROM stat_assessor_red AS stat
                                                JOIN league
                                                ON stat.league_id = league.id
                                                WHERE stat.assessor_id = ?
                                                AND league.short_name IN (?)
                                                GROUP BY league.short_name,
                                                         stat.season) c
                                                ON m.season = c.season
                                                AND m.league_name = c.league_name',
            [$id, $leagues, $id, $leagues],
            [ParameterType::STRING, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY,
                ParameterType::STRING, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY]);
    }

    private function getStatAssessorRedAvgTotalSeason($id, $leagues)
    {
        return $this->connection->fetchAll('SELECT m.season, (IFNULL(c.cards, 0 )/m.matches) AS total
                                            FROM
                                                (SELECT stat.season AS season, SUM(stat.number_of_matches) AS matches
                                                FROM stat_assessor_matches AS stat
                                                JOIN league
                                                ON stat.league_id = league.id
                                                WHERE stat.assessor_id = ?
                                                AND league.short_name IN (?)
                                                GROUP BY stat.season) m
                                                LEFT JOIN
                                                (SELECT stat.season AS season, SUM(stat.number_of_cards) AS cards
                                                FROM stat_assessor_red AS stat
                                                JOIN league
                                                ON stat.league_id = league.id
                                                WHERE stat.assessor_id = ?
                                                AND league.short_name IN (?)
                                                GROUP BY stat.season) c
                                                ON m.season = c.season',
            [$id, $leagues, $id, $leagues],
            [ParameterType::STRING, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY,
                ParameterType::STRING, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY]);
    }

    private function getStatAssessorRedAvgTotals($id, $leagues)
    {
        $totalArray = array();

        foreach ($leagues as $league) {
            $totalArray[$league] = $this->getStatAssessorRedAvgTotalLeague($id, $league);
        }

        $totalArray['total'] = $this->getStatAssessorRedAvgTotal($id, $leagues);

        return $totalArray;
    }

    private function getStatAssessorRedAvgTotalLeague($id, $league)
    {
        return $this->connection->fetchColumn('SELECT (IFNULL(c.cards, 0)/m.matches) AS total
                                               FROM
                                                    (SELECT stat.assessor_id AS assessor_id, SUM(stat.number_of_matches) AS matches
                                                    FROM stat_assessor_matches AS stat
                                                    JOIN league
                                                    ON stat.league_id = league.id
                                                    WHERE stat.assessor_id = ?
                                                    AND league.short_name = ?
                                                    GROUP BY league.short_name) m
                                                    LEFT JOIN
                                                    (SELECT stat.assessor_id AS assessor_id, SUM(stat.number_of_cards) AS cards
                                                    FROM stat_assessor_red AS stat
                                                    JOIN league
                                                    ON stat.league_id = league.id
                                                    WHERE stat.assessor_id = ?
                                                    AND league.short_name = ?
                                                    GROUP BY league.short_name) c
                                                    ON m.assessor_id = c.assessor_id',
            [$id, $league, $id, $league],
            0);
    }

    private function getStatAssessorRedAvgTotal($id, $leagues)
    {
        return $this->connection->fetchColumn('SELECT (IFNULL(c.cards, 0)/m.matches) AS total
                                               FROM
                                                    (SELECT stat.assessor_id AS assessor_id, SUM(stat.number_of_matches) AS matches
                                                    FROM stat_assessor_matches AS stat
                                                    JOIN league
                                                    ON stat.league_id = league.id
                                                    WHERE stat.assessor_id = ?
                                                    AND league.short_name IN (?)
                                                    GROUP BY stat.assessor_id) m
                                                    LEFT JOIN
                                                    (SELECT stat.assessor_id AS assessor_id, SUM(stat.number_of_cards) AS cards
                                                    FROM stat_assessor_red AS stat
                                                    JOIN league
                                                    ON stat.league_id = league.id
                                                    WHERE stat.assessor_id = ?
                                                    AND league.short_name IN (?)
                                                    GROUP BY stat.assessor_id) c
                                                    ON m.assessor_id = c.assessor_id',
            [$id, $leagues, $id, $leagues],
            0,
            [ParameterType::STRING, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY,
                ParameterType::STRING, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY]);
    }


    // transforms given array with stat data to format that can be use in tables
    private function transformInteractionStat($statArray)
    {
        $newStatArray = array();
        foreach ($statArray as $row) {
            $newStatArray[$row['entity_name']][$row['season']] = $row['number_of_matches'];
        }
        // sum for each entity
        foreach ($newStatArray as &$array) {
            $sum = 0;
            foreach ($array as $item) {
                $sum += intval($item);
            }
            $array['total'] = $sum;
        }

        return $newStatArray;
    }

    private function getStatAssessorTeam($id, $seasons)
    {
        return $this->connection->fetchAll('SELECT team.short_name AS entity_name, stat.season, SUM(stat.number_of_matches) AS number_of_matches
                                            FROM stat_assessor_team AS stat
                                            JOIN team
                                            ON stat.team_id = team.id
                                            WHERE stat.assessor_id = ?
                                            AND stat.season IN (?)
                                            GROUP BY stat.season, 
                                                     stat.team_id',
            [$id, $seasons],
            [ParameterType::INTEGER, \Doctrine\DBAL\Connection::PARAM_INT_ARRAY]);
    }

}
