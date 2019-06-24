<?php

namespace App\Site\Repository;

use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\ParameterType;

class OfficialStatsRepository
{
    private $connection;

    public function __construct(Connection $DBALconnection)
    {
        $this->connection = $DBALconnection;
    }

    public function getOfficialStats($id, $seasons, $leagues)
    {
        $stats = array();

        $stats['RefereeMatches'] = $this->transformQuantityStat($this->getStatRefereeMatches($id, $leagues));
        $stats['Ar1Matches'] = $this->transformQuantityStat($this->getStatAr1Matches($id, $leagues));
        $stats['Ar2Matches'] = $this->transformQuantityStat($this->getStatAr2Matches($id, $leagues));
        $stats['Ar1AndAr2Matches'] = $this->transformQuantityStat($this->getStatAr1AndAr2Matches($id, $leagues));
        $stats['OfficialMatches'] = $this->transformQuantityStat($this->getStatOfficialMatches($id, $leagues));

        $stats['RefereeYellow'] = $this->transformQuantityStat($this->getStatRefereeYellow($id, $leagues));

        $yellowAvg = $this->getStatRefereeYellowAvg($id, $leagues);
        $yellowAvgSeason = $this->getStatRefereeYellowAvgTotalSeason($id, $leagues);
        $stats['RefereeYellowAvg'] = $this->transformAvgStat($yellowAvg, $yellowAvgSeason);
        $stats['RefereeYellowAvgTotals'] = $this->getStatRefereeYellowAvgTotals($id, $leagues);

        $yellowFirst = $this->getStatRefereeYellowFirst($id, $leagues);
        $yellowFirstSeason = $this->getStatRefereeYellowFirstTotalSeason($id, $leagues);
        $stats['RefereeYellowFirst'] = $this->transformAvgStat($yellowFirst, $yellowFirstSeason);
        $stats['RefereeYellowFirstTotals'] = $this->getStatRefereeYellowFirstTotals($id, $leagues);

        $stats['RefereeRed'] = $this->transformQuantityStat($this->getStatRefereeRed($id, $leagues));

        $redAvg = $this->getStatRefereeRedAvg($id, $leagues);
        $redAvgSeason = $this->getStatRefereeRedAvgTotalSeason($id, $leagues);
        $stats['RefereeRedAvg'] = $this->transformAvgStat($redAvg, $redAvgSeason);
        $stats['RefereeRedAvgTotals'] = $this->getStatRefereeRedAvgTotals($id, $leagues);

        $stats['RefereeRedOffence'] = $this->transformStatRefereeRedOffence($this->getStatRefereeRedOffence($id));

        $stats['RefereeCardsMinutes'] = $this->transformStatRefereeCardsMinutes($this->getStatRefereeCardsMinutes($id));
        $stats['RefereeCardsMinutesMaxNumberOfCards'] = $this->getStatRefereeCardsMinutesMaxNumberOfCards($id);

        $stats['RefereeAr'] = $this->transformInteractionStat($this->getStatRefereeAr($id, $seasons));
        $stats['OfficialOfficial'] = $this->transformInteractionStat($this->getStatOfficialOfficial($id, $seasons));
        $stats['RefereeAssessor'] = $this->transformInteractionStat($this->getStatRefereeAssessor($id, $seasons));
        $stats['OfficialAssessor'] = $this->transformInteractionStat($this->getStatOfficialAssessor($id, $seasons));
        $stats['OfficialTeam'] = $this->transformInteractionStat($this->getStatOfficialTeam($id, $seasons));
        $stats['OfficialHomeTeam'] = $this->transformInteractionStat($this->getStatOfficialHomeTeam($id, $seasons));

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

    private function getStatRefereeMatches($id, $leagues)
    {
        return $this->connection->fetchAll('SELECT stat.season, league.short_name AS league_name, SUM(stat.number_of_matches) AS `number`
                                            FROM stat_referee_matches AS stat
                                            JOIN league
                                            ON stat.league_id = league.id
                                            WHERE stat.official_id = ?
                                            AND league.short_name IN (?)
                                            GROUP BY league.short_name,
                                            stat.season',
            [$id, $leagues],
            [ParameterType::STRING, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY]);
    }

    private function getStatAr1Matches($id, $leagues)
    {
        return $this->connection->fetchAll('SELECT stat.season, league.short_name AS league_name, SUM(stat.number_of_matches) AS `number`
                                            FROM stat_ar1_matches AS stat
                                            JOIN league
                                            ON stat.league_id = league.id
                                            WHERE stat.official_id = ?
                                            AND league.short_name IN (?)
                                            GROUP BY league.short_name,
                                            stat.season',
            [$id, $leagues],
            [ParameterType::STRING, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY]);
    }

    private function getStatAr2Matches($id, $leagues)
    {
        return $this->connection->fetchAll('SELECT stat.season, league.short_name AS league_name, SUM(stat.number_of_matches) AS `number`
                                            FROM stat_ar2_matches AS stat
                                            JOIN league
                                            ON stat.league_id = league.id
                                            WHERE stat.official_id = ?
                                            AND league.short_name IN (?)
                                            GROUP BY league.short_name,
                                            stat.season',
            [$id, $leagues],
            [ParameterType::STRING, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY]);
    }

    private function getStatAr1AndAr2Matches($id, $leagues)
    {
        return $this->connection->fetchAll('SELECT stat.season, league.short_name AS league_name, SUM(stat.number_of_matches) AS `number`
                                            FROM stat_ar1_and_ar2_matches AS stat
                                            JOIN league
                                            ON stat.league_id = league.id
                                            WHERE stat.official_id = ?
                                            AND league.short_name IN (?)
                                            GROUP BY league.short_name,
                                            stat.season',
            [$id, $leagues],
            [ParameterType::STRING, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY]);
    }

    private function getStatOfficialMatches($id, $leagues)
    {
        return $this->connection->fetchAll('SELECT stat.season, league.short_name AS league_name, SUM(stat.number_of_matches) AS `number`
                                            FROM stat_official_matches AS stat
                                            JOIN league
                                            ON stat.league_id = league.id
                                            WHERE stat.official_id = ?
                                            AND league.short_name IN (?)
                                            GROUP BY league.short_name,
                                            stat.season',
            [$id, $leagues],
            [ParameterType::STRING, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY]);
    }

    private function getStatRefereeYellow($id, $leagues)
    {
        return $this->connection->fetchAll('SELECT stat.season, league.short_name AS league_name, SUM(stat.number_of_cards) AS `number`
                                            FROM stat_referee_yellow AS stat
                                            JOIN league
                                            ON stat.league_id = league.id
                                            WHERE stat.official_id = ?
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

    private function getStatRefereeYellowAvg($id, $leagues)
    {
        return $this->connection->fetchAll('SELECT m.season, m.league_name, (IFNULL(c.cards, 0 )/m.matches) AS `number`
                                            FROM	
                                                (SELECT stat.season AS season, league.short_name AS league_name, SUM(stat.number_of_matches) AS matches
                                                FROM stat_referee_matches AS stat
                                                JOIN league
                                                ON stat.league_id = league.id
                                                WHERE stat.official_id = ?
                                                AND league.short_name IN (?)
                                                GROUP BY league.short_name,
                                                stat.season) m
                                                LEFT JOIN
                                                (SELECT stat.season AS season, league.short_name AS league_name, SUM(stat.number_of_cards) AS cards
                                                FROM stat_referee_yellow AS stat
                                                JOIN league
                                                ON stat.league_id = league.id
                                                WHERE stat.official_id = ?
                                                AND league.short_name IN (?)
                                                GROUP BY league.short_name,
                                                stat.season) c
                                                ON m.season = c.season
                                                AND m.league_name = c.league_name',
            [$id, $leagues, $id, $leagues],
            [ParameterType::STRING, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY,
                ParameterType::STRING, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY]);
    }

    private function getStatRefereeYellowAvgTotalSeason($id, $leagues)
    {
        return $this->connection->fetchAll('SELECT m.season, (IFNULL(c.cards, 0 )/m.matches) AS total
                                            FROM
                                                (SELECT stat.season AS season, SUM(stat.number_of_matches) AS matches
                                                FROM stat_referee_matches AS stat
                                                JOIN league
                                                ON stat.league_id = league.id
                                                WHERE stat.official_id = ?
                                                AND league.short_name IN (?)
                                                GROUP BY stat.season) m
                                                LEFT JOIN
                                                (SELECT stat.season AS season, SUM(stat.number_of_cards) AS cards
                                                FROM stat_referee_yellow AS stat
                                                JOIN league
                                                ON stat.league_id = league.id
                                                WHERE stat.official_id = ?
                                                AND league.short_name IN (?)
                                                GROUP BY stat.season) c
                                                ON m.season = c.season',
            [$id, $leagues, $id, $leagues],
            [ParameterType::STRING, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY,
                ParameterType::STRING, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY]);
    }

    private function getStatRefereeYellowAvgTotals($id, $leagues)
    {
        $totalArray = array();

        foreach ($leagues as $league) {
            $totalArray[$league] = $this->getStatRefereeYellowAvgTotalLeague($id, $league);
        }

        $totalArray['total'] = $this->getStatRefereeYellowAvgTotal($id, $leagues);

        return $totalArray;
    }

    private function getStatRefereeYellowAvgTotalLeague($id, $league)
    {
        return $this->connection->fetchColumn('SELECT (IFNULL(c.cards, 0)/m.matches) AS total
                                               FROM
                                                    (SELECT stat.official_id AS official_id, SUM(stat.number_of_matches) AS matches
                                                    FROM stat_referee_matches AS stat
                                                    JOIN league
                                                    ON stat.league_id = league.id
                                                    WHERE stat.official_id = ?
                                                    AND league.short_name = ?
                                                    GROUP BY league.short_name) m
                                                    LEFT JOIN
                                                    (SELECT stat.official_id AS official_id, SUM(stat.number_of_cards) AS cards
                                                    FROM stat_referee_yellow AS stat
                                                    JOIN league
                                                    ON stat.league_id = league.id
                                                    WHERE stat.official_id = ?
                                                    AND league.short_name = ?
                                                    GROUP BY league.short_name) c
                                                    ON m.official_id = c.official_id',
            [$id, $league, $id, $league],
            0);
    }

    private function getStatRefereeYellowAvgTotal($id, $leagues)
    {
        return $this->connection->fetchColumn('SELECT (IFNULL(c.cards, 0)/m.matches) AS total
                                               FROM
                                                    (SELECT stat.official_id AS official_id, SUM(stat.number_of_matches) AS matches
                                                    FROM stat_referee_matches AS stat
                                                    JOIN league
                                                    ON stat.league_id = league.id
                                                    WHERE stat.official_id = ?
                                                    AND league.short_name IN (?)
                                                    GROUP BY stat.official_id) m
                                                    LEFT JOIN
                                                    (SELECT stat.official_id AS official_id, SUM(stat.number_of_cards) AS cards
                                                    FROM stat_referee_yellow AS stat
                                                    JOIN league
                                                    ON stat.league_id = league.id
                                                    WHERE stat.official_id = ?
                                                    AND league.short_name IN (?)
                                                    GROUP BY stat.official_id) c
                                                    ON m.official_id = c.official_id',
            [$id, $leagues, $id, $leagues],
            0,
            [ParameterType::STRING, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY,
                ParameterType::STRING, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY]
        );
    }


    private function getStatRefereeYellowFirst($id, $leagues)
    {
        return $this->connection->fetchAll('SELECT stat.season, league.short_name AS league_name, AVG(stat.minute) AS `number`
                                            FROM stat_referee_yellow_first AS stat
                                            JOIN league
                                            ON stat.league_id = league.id
                                            WHERE stat.official_id = ?
                                            AND league.short_name IN (?)
                                            GROUP BY league.short_name,
                                            stat.season',
            [$id, $leagues],
            [ParameterType::STRING, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY]);
    }

    private function getStatRefereeYellowFirstTotalSeason($id, $leagues)
    {
        return $this->connection->fetchAll('SELECT stat.season AS season, AVG(stat.minute) AS total
                                            FROM stat_referee_yellow_first AS stat
                                            JOIN league
                                            ON stat.league_id = league.id
                                            WHERE stat.official_id = ?
                                            AND league.short_name IN (?)
                                            GROUP BY stat.season',
            [$id, $leagues],
            [ParameterType::STRING, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY]);
    }

    private function getStatRefereeYellowFirstTotals($id, $leagues)
    {
        $totalArray = array();

        foreach ($leagues as $league) {
            $totalArray[$league] = $this->getStatRefereeYellowFirstTotalLeague($id, $league);
        }

        $totalArray['total'] = $this->getStatRefereeYellowFirstTotal($id, $leagues);

        return $totalArray;
    }

    private function getStatRefereeYellowFirstTotalLeague($id, $league)
    {
        return $this->connection->fetchColumn('SELECT AVG(stat.minute) AS total
                                                FROM stat_referee_yellow_first AS stat
                                                JOIN league
                                                ON stat.league_id = league.id
                                                WHERE stat.official_id = ?
                                                AND league.short_name = ?
                                                GROUP BY league.short_name',
            [$id, $league],
            0);
    }

    private function getStatRefereeYellowFirstTotal($id, $leagues)
    {
        return $this->connection->fetchColumn('SELECT AVG(stat.minute) AS total
                                                FROM stat_referee_yellow_first AS stat
                                                JOIN league
                                                ON stat.league_id = league.id
                                                WHERE stat.official_id = ?
                                                AND league.short_name IN (?)
                                                GROUP BY stat.official_id',
            [$id, $leagues],
            0,
            [ParameterType::STRING, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY]);
    }


    private function getStatRefereeRed($id, $leagues)
    {
        return $this->connection->fetchAll('SELECT stat.season, league.short_name AS league_name, SUM(stat.number_of_cards) AS `number`
                                            FROM stat_referee_red AS stat
                                            JOIN league
                                            ON stat.league_id = league.id
                                            WHERE stat.official_id = ?
                                            AND league.short_name IN (?)
                                            GROUP BY league.short_name,
                                            stat.season',
            [$id, $leagues],
            [ParameterType::STRING, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY]);
    }

    private function getStatRefereeRedAvg($id, $leagues)
    {
        return $this->connection->fetchAll('SELECT m.season, m.league_name, (IFNULL(c.cards, 0 )/m.matches) AS `number`
                                            FROM	
                                                (SELECT stat.season AS season, league.short_name AS league_name, SUM(stat.number_of_matches) AS matches
                                                FROM stat_referee_matches AS stat
                                                JOIN league
                                                ON stat.league_id = league.id
                                                WHERE stat.official_id = ?
                                                AND league.short_name IN (?)
                                                GROUP BY league.short_name,
                                                stat.season) m
                                                LEFT JOIN
                                                (SELECT stat.season AS season, league.short_name AS league_name, SUM(stat.number_of_cards) AS cards
                                                FROM stat_referee_red AS stat
                                                JOIN league
                                                ON stat.league_id = league.id
                                                WHERE stat.official_id = ?
                                                AND league.short_name IN (?)
                                                GROUP BY league.short_name,
                                                stat.season) c
                                                ON m.season = c.season
                                                AND m.league_name = c.league_name',
            [$id, $leagues, $id, $leagues],
            [ParameterType::STRING, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY,
                ParameterType::STRING, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY]);
    }

    private function getStatRefereeRedAvgTotalSeason($id, $leagues)
    {
        return $this->connection->fetchAll('SELECT m.season, (IFNULL(c.cards, 0 )/m.matches) AS total
                                            FROM
                                                (SELECT stat.season AS season, SUM(stat.number_of_matches) AS matches
                                                FROM stat_referee_matches AS stat
                                                JOIN league
                                                ON stat.league_id = league.id
                                                WHERE stat.official_id = ?
                                                AND league.short_name IN (?)
                                                GROUP BY stat.season) m
                                                LEFT JOIN
                                                (SELECT stat.season AS season, SUM(stat.number_of_cards) AS cards
                                                FROM stat_referee_red AS stat
                                                JOIN league
                                                ON stat.league_id = league.id
                                                WHERE stat.official_id = ?
                                                AND league.short_name IN (?)
                                                GROUP BY stat.season) c
                                                ON m.season = c.season',
            [$id, $leagues, $id, $leagues],
            [ParameterType::STRING, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY,
                ParameterType::STRING, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY]);
    }

    private function getStatRefereeRedAvgTotals($id, $leagues)
    {
        $totalArray = array();

        foreach ($leagues as $league) {
            $totalArray[$league] = $this->getStatRefereeRedAvgTotalLeague($id, $league);
        }

        $totalArray['total'] = $this->getStatRefereeRedAvgTotal($id, $leagues);

        return $totalArray;
    }

    private function getStatRefereeRedAvgTotalLeague($id, $league)
    {
        return $this->connection->fetchColumn('SELECT (IFNULL(c.cards, 0)/m.matches) AS total
                                               FROM
                                                    (SELECT stat.official_id AS official_id, SUM(stat.number_of_matches) AS matches
                                                    FROM stat_referee_matches AS stat
                                                    JOIN league
                                                    ON stat.league_id = league.id
                                                    WHERE stat.official_id = ?
                                                    AND league.short_name = ?
                                                    GROUP BY league.short_name) m
                                                    LEFT JOIN
                                                    (SELECT stat.official_id AS official_id, SUM(stat.number_of_cards) AS cards
                                                    FROM stat_referee_red AS stat
                                                    JOIN league
                                                    ON stat.league_id = league.id
                                                    WHERE stat.official_id = ?
                                                    AND league.short_name = ?
                                                    GROUP BY league.short_name) c
                                                    ON m.official_id = c.official_id',
            [$id, $league, $id, $league],
            0);
    }

    private function getStatRefereeRedAvgTotal($id, $leagues)
    {
        return $this->connection->fetchColumn('SELECT (IFNULL(c.cards, 0)/m.matches) AS total
                                               FROM
                                                    (SELECT stat.official_id AS official_id, SUM(stat.number_of_matches) AS matches
                                                    FROM stat_referee_matches AS stat
                                                    JOIN league
                                                    ON stat.league_id = league.id
                                                    WHERE stat.official_id = ?
                                                    AND league.short_name IN (?)
                                                    GROUP BY stat.official_id) m
                                                    LEFT JOIN
                                                    (SELECT stat.official_id AS official_id, SUM(stat.number_of_cards) AS cards
                                                    FROM stat_referee_red AS stat
                                                    JOIN league
                                                    ON stat.league_id = league.id
                                                    WHERE stat.official_id = ?
                                                    AND league.short_name IN (?)
                                                    GROUP BY stat.official_id) c
                                                    ON m.official_id = c.official_id',
            [$id, $leagues, $id, $leagues],
            0,
            [ParameterType::STRING, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY,
                ParameterType::STRING, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY]);
    }

    private function getStatRefereeRedOffence($id)
    {
        return $this->connection->fetchAll('SELECT offence.short_name, SUM(stat.number_of_cards) AS number_of_cards
                                            FROM stat_referee_red_offence AS stat
                                            JOIN offence
                                            ON stat.offence_id = offence.id
                                            WHERE stat.official_id = ?
                                            GROUP BY stat.offence_id
                                            ORDER BY number_of_cards DESC',
            [$id]);
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


    private function getStatRefereeCardsMinutes($id)
    {
        return $this->connection->fetchAll('SELECT stat.minute, stat.card_type, stat.number_of_cards
                                            FROM stat_referee_cards_minutes AS stat
                                            WHERE stat.official_id = ?',
            [$id]);
    }

    // transforms to array which can be use as source for chart with CMENGoogleChartsBundle
    private function transformStatRefereeCardsMinutes($statArray)
    {
        $newStatArray[0] = ['Minuta', 'ŽK', 'ČK'];
        for ($i = 1; $i <= 90; $i++) {
            $newStatArray[$i] = [$i, 0, 0];
        }

        foreach ($statArray as $row) {
            if ($row['card_type'] === 'red') {
                $newStatArray[$row['minute']][2] = (int)$row['number_of_cards'];
            }
            else $newStatArray[$row['minute']][1] = (int)$row['number_of_cards'];
        }

        return $newStatArray;
    }

    // get max number of cards in some minute for given referee in stat table referee_cards_minutes
    private function getStatRefereeCardsMinutesMaxNumberOfCards($id)
    {
        return $this->connection->fetchColumn('SELECT MAX(number_of_cards)
                                               FROM stat_referee_cards_minutes AS stat
                                               WHERE stat.official_id = ?',
            [$id],
            0);
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

    private function getStatRefereeAr($id, $seasons)
    {
        return $this->connection->fetchAll('SELECT ar.name AS entity_name, stat.season, SUM(stat.number_of_matches) AS number_of_matches
                                            FROM stat_referee_ar AS stat
                                            JOIN official AS ar
                                            ON stat.ar_official_id = ar.id
                                            WHERE stat.referee_official_id = ?
                                            AND stat.season IN (?)
                                            GROUP BY stat.season, 
                                                     stat.ar_official_id',
            [$id, $seasons],
            [ParameterType::INTEGER, \Doctrine\DBAL\Connection::PARAM_INT_ARRAY]);
    }

    private function getStatOfficialOfficial($id, $seasons)
    {
        return $this->connection->fetchAll('SELECT official.name AS entity_name, stat.season, SUM(stat.number_of_matches) AS number_of_matches
                                            FROM stat_official_official AS stat
                                            JOIN official
                                            ON stat.official_id2 = official.id
                                            WHERE stat.official_id1 = ?
                                            AND stat.season IN (?)
                                            GROUP BY stat.season, 
                                                     stat.official_id2
                                            UNION
                                            SELECT official.name AS entity_name, stat.season, SUM(stat.number_of_matches) AS number_of_matches
                                            FROM stat_official_official AS stat
                                            JOIN official
                                            ON stat.official_id1 = official.id
                                            WHERE stat.official_id2 = ?
                                            AND stat.season IN (?)
                                            GROUP BY stat.season, 
                                                     stat.official_id1
                                                     ',
            [$id, $seasons, $id, $seasons],
            [ParameterType::INTEGER, \Doctrine\DBAL\Connection::PARAM_INT_ARRAY, ParameterType::INTEGER, \Doctrine\DBAL\Connection::PARAM_INT_ARRAY]);
    }

    private function getStatRefereeAssessor($id, $seasons)
    {
        return $this->connection->fetchAll('SELECT assessor.name AS entity_name, stat.season, SUM(stat.number_of_matches) AS number_of_matches
                                            FROM stat_referee_assessor AS stat
                                            JOIN assessor
                                            ON stat.assessor_id = assessor.id
                                            WHERE stat.official_id = ?
                                            AND stat.season IN (?)
                                            GROUP BY stat.season, 
                                                     stat.assessor_id',
            [$id, $seasons],
            [ParameterType::INTEGER, \Doctrine\DBAL\Connection::PARAM_INT_ARRAY]);
    }

    private function getStatOfficialAssessor($id, $seasons)
    {
        return $this->connection->fetchAll('SELECT assessor.name AS entity_name, stat.season, SUM(stat.number_of_matches) AS number_of_matches
                                            FROM stat_official_assessor AS stat
                                            JOIN assessor
                                            ON stat.assessor_id = assessor.id
                                            WHERE stat.official_id = ?
                                            AND stat.season IN (?)
                                            GROUP BY stat.season, 
                                                     stat.assessor_id',
            [$id, $seasons],
            [ParameterType::INTEGER, \Doctrine\DBAL\Connection::PARAM_INT_ARRAY]);
    }

    private function getStatOfficialTeam($id, $seasons)
    {
        return $this->connection->fetchAll('SELECT team.short_name AS entity_name, stat.season, SUM(stat.number_of_matches) AS number_of_matches
                                            FROM stat_official_team AS stat
                                            JOIN team
                                            ON stat.team_id = team.id
                                            WHERE stat.official_id = ?
                                            AND stat.season IN (?)
                                            GROUP BY stat.season, 
                                                     stat.team_id',
            [$id, $seasons],
            [ParameterType::INTEGER, \Doctrine\DBAL\Connection::PARAM_INT_ARRAY]);
    }

    private function getStatOfficialHomeTeam($id, $seasons)
    {
        return $this->connection->fetchAll('SELECT team.short_name AS entity_name, stat.season, SUM(stat.number_of_matches) AS number_of_matches
                                            FROM stat_official_home_team AS stat
                                            JOIN team
                                            ON stat.team_id = team.id
                                            WHERE stat.official_id = ?
                                            AND stat.season IN (?)
                                            GROUP BY stat.season, 
                                                     stat.team_id',
            [$id, $seasons],
            [ParameterType::INTEGER, \Doctrine\DBAL\Connection::PARAM_INT_ARRAY]);
    }

}
