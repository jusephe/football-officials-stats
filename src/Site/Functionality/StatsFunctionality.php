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

    public function getSeasonsWithPart($league) {
        return $this->connection->fetchAll('SELECT DISTINCT stat_referee_matches.season, stat_referee_matches.is_autumn
                                            FROM stat_referee_matches 
                                            LEFT JOIN league 
                                            ON stat_referee_matches.league_id = league.id
                                            WHERE league.short_name = ?',
                                            [$league]);
    }

}
