<?php

namespace App\Service;

use Doctrine\DBAL\Driver\Connection;

class StatsUpdater
{
    private $connection;

    public function __construct(Connection $DBALconnection)
    {
        $this->connection = $DBALconnection;
    }

    public function updateAllStats()
    {
        $this->connection->executeUpdate('DELETE FROM stat_referee_matches');
        $this->connection->executeUpdate('INSERT INTO stat_referee_matches (season, is_autumn, number_of_matches, league_id, official_id) 
                                          SELECT season, is_autumn, COUNT(id), league_id, referee_official_id 
                                          FROM game 
                                          GROUP BY season, is_autumn, league_id, referee_official_id');


    }

}
