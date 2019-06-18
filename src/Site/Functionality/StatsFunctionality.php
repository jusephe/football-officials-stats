<?php

namespace App\Site\Functionality;

use Doctrine\DBAL\Driver\Connection;

class StatsFunctionality
{
    private $connection;

    public function __construct(Connection $DBALconnection)
    {
        $this->connection = $DBALconnection;
    }

    // indicates which seasons and their parts are in our stats tables for given league level name
    // returns array of [season, isAutumn]
    public function getSeasonsWithPart($league) {
        return $this->connection->fetchAll('SELECT DISTINCT stat_referee_matches.season, stat_referee_matches.is_autumn
                                            FROM stat_referee_matches 
                                            JOIN league 
                                            ON stat_referee_matches.league_id = league.id
                                            WHERE league.short_name = ?',
                                            [$league]);
    }

}
