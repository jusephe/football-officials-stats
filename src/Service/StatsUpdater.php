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

        $this->connection->executeUpdate('DELETE FROM stat_ar1_matches');
        $this->connection->executeUpdate('INSERT INTO stat_ar1_matches (season, is_autumn, number_of_matches, league_id, official_id) 
                                          SELECT season, is_autumn, COUNT(id), league_id, ar1_official_id 
                                          FROM game 
                                          GROUP BY season, is_autumn, league_id, ar1_official_id');

        $this->connection->executeUpdate('DELETE FROM stat_ar2_matches');
        $this->connection->executeUpdate('INSERT INTO stat_ar2_matches (season, is_autumn, number_of_matches, league_id, official_id) 
                                          SELECT season, is_autumn, COUNT(id), league_id, ar2_official_id 
                                          FROM game 
                                          GROUP BY season, is_autumn, league_id, ar2_official_id');

        $this->connection->executeUpdate('DELETE FROM stat_ar1_and_ar2_matches');
        $this->connection->executeUpdate('INSERT INTO stat_ar1_and_ar2_matches (season, is_autumn, number_of_matches, league_id, official_id)
                                          SELECT season, is_autumn, COUNT(id), league_id, official_id 
                                          FROM (SELECT season, is_autumn, id, league_id, AR1_official_id AS official_id
                                                FROM game 
                                                UNION 
                                                SELECT season, is_autumn, id, league_id, AR2_official_id  AS official_id
                                                FROM game) t 
                                          GROUP BY season, is_autumn, league_id, official_id');

        $this->connection->executeUpdate('DELETE FROM stat_official_matches');
        $this->connection->executeUpdate('INSERT INTO stat_official_matches (season, is_autumn, number_of_matches, league_id, official_id)
                                          SELECT season, is_autumn, COUNT(id), league_id, official_id 
                                          FROM (SELECT season, is_autumn, id, league_id, referee_official_id AS official_id
                                                FROM game 
                                                UNION 
                                                SELECT season, is_autumn, id, league_id, AR1_official_id AS official_id
                                                FROM game 
                                                UNION 
                                                SELECT season, is_autumn, id, league_id, AR2_official_id  AS official_id
                                                FROM game) t 
                                          GROUP BY season, is_autumn, league_id, official_id');

        $this->connection->executeUpdate('DELETE FROM stat_referee_red');
        $this->connection->executeUpdate('INSERT INTO stat_referee_red (season, is_autumn, number_of_cards, league_id, official_id) 
                                          SELECT game.season, game.is_autumn, COUNT(red_card.id), game.league_id, game.referee_official_id 
                                          FROM game 
                                          INNER JOIN red_card 
                                          ON game.id = red_card.game_id
                                            AND red_card.minute IS NOT NULL
                                          GROUP BY game.season, game.is_autumn, game.league_id, game.referee_official_id');

        $this->connection->executeUpdate('DELETE FROM stat_referee_red_offence');
        $this->connection->executeUpdate('INSERT INTO stat_referee_red_offence (season, is_autumn, number_of_cards, league_id, official_id, offence_id) 
                                          SELECT game.season, game.is_autumn, COUNT(red_card.id), game.league_id, game.referee_official_id, red_card.offence_id 
                                          FROM game 
                                          INNER JOIN red_card 
                                          ON game.id = red_card.game_id
                                            AND red_card.minute IS NOT NULL
                                          GROUP BY game.season, game.is_autumn, game.league_id, game.referee_official_id, red_card.offence_id');

        $this->connection->executeUpdate('DELETE FROM stat_referee_yellow');
        $this->connection->executeUpdate('INSERT INTO stat_referee_yellow (season, is_autumn, number_of_cards, league_id, official_id) 
                                          SELECT game.season, game.is_autumn, COUNT(yellow_card.id), game.league_id, game.referee_official_id 
                                          FROM game 
                                          INNER JOIN yellow_card 
                                          ON game.id = yellow_card.game_id
                                          GROUP BY game.season, game.is_autumn, game.league_id, game.referee_official_id');

        $this->connection->executeUpdate('DELETE FROM stat_referee_yellow_first');
        $this->connection->executeUpdate('INSERT INTO stat_referee_yellow_first (game_id, season, is_autumn, minute, league_id, official_id) 
                                          SELECT game.id, game.season, game.is_autumn, MIN(yellow_card.minute), game.league_id, game.referee_official_id 
                                          FROM game 
                                          INNER JOIN yellow_card 
                                          ON game.id = yellow_card.game_id 
                                            AND yellow_card.minute REGEXP \'^[[:digit:]]+$\' 
                                          GROUP BY game.id');

        $this->connection->executeUpdate('DELETE FROM stat_referee_cards_minutes');
        $this->connection->executeUpdate('INSERT INTO stat_referee_cards_minutes (season, card_type, minute, number_of_cards, league_id, official_id) 
                                          SELECT game.season, \'yellow\', yellow_card.minute, COUNT(yellow_card.id), game.league_id, game.referee_official_id 
                                          FROM game 
                                          INNER JOIN yellow_card 
                                          ON game.id = yellow_card.game_id 
                                            AND yellow_card.minute REGEXP \'^[[:digit:]]+$\' 
                                          GROUP BY game.season, yellow_card.minute, game.league_id, game.referee_official_id');
        $this->connection->executeUpdate('INSERT INTO stat_referee_cards_minutes (season, card_type, minute, number_of_cards, league_id, official_id) 
                                          SELECT game.season, \'red\', red_card.minute, COUNT(red_card.id), game.league_id, game.referee_official_id 
                                          FROM game 
                                          INNER JOIN red_card 
                                          ON game.id = red_card.game_id 
                                            AND red_card.minute REGEXP \'^[[:digit:]]+$\' 
                                          GROUP BY game.season, red_card.minute, game.league_id, game.referee_official_id');

        $this->connection->executeUpdate('DELETE FROM stat_assessor_matches');
        $this->connection->executeUpdate('INSERT INTO stat_assessor_matches (season, is_autumn, number_of_matches, league_id, assessor_id) 
                                          SELECT season, is_autumn, COUNT(id), league_id, assessor_id 
                                          FROM game 
                                          GROUP BY season, is_autumn, league_id, assessor_id');

        $this->connection->executeUpdate('DELETE FROM stat_assessor_red');
        $this->connection->executeUpdate('INSERT INTO stat_assessor_red (season, is_autumn, number_of_cards, league_id, assessor_id) 
                                          SELECT game.season, game.is_autumn, COUNT(red_card.id), game.league_id, game.assessor_id 
                                          FROM game 
                                          INNER JOIN red_card 
                                          ON game.id = red_card.game_id
                                            AND red_card.minute IS NOT NULL
                                          GROUP BY game.season, game.is_autumn, game.league_id, game.assessor_id');

        $this->connection->executeUpdate('DELETE FROM stat_assessor_yellow');
        $this->connection->executeUpdate('INSERT INTO stat_assessor_yellow (season, is_autumn, number_of_cards, league_id, assessor_id) 
                                          SELECT game.season, game.is_autumn, COUNT(yellow_card.id), game.league_id, game.assessor_id 
                                          FROM game 
                                          INNER JOIN yellow_card 
                                          ON game.id = yellow_card.game_id
                                          GROUP BY game.season, game.is_autumn, game.league_id, game.assessor_id');












    }

}
