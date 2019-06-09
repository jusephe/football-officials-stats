<?php

namespace App\Admin\Exception;

use App\Admin\Entity\Team;

class TeamNotFound extends \Exception
{
    protected $team;

    public function __construct(Team $team)
    {
        $this->team = $team;

        parent::__construct();
    }

    public function getTeam(): Team
    {
        return $this->team;
    }

}
