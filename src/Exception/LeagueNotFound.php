<?php

namespace App\Exception;

use App\Entity\League;

class LeagueNotFound extends \Exception
{
    protected $league;

    public function __construct(League $league)
    {
        $this->league = $league;

        parent::__construct();
    }

    public function getLeague(): League
    {
        return $this->league;
    }

}
