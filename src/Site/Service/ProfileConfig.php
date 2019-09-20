<?php

namespace App\Site\Service;

class ProfileConfig
{
    // which seasons display in interaction stats tables
    public function getSeasons()
    {
        $currentYear = date('Y');
        $currentMonth = date('n');
        if($currentMonth < 8) {
            $seasons = [$currentYear-4, $currentYear-3, $currentYear-2, $currentYear-1];
        }
        else $seasons = [$currentYear-3, $currentYear-2, $currentYear-1, $currentYear];

        return $seasons;
    }

    // which leagues display in basic stats tables
    public function getLeagues()
    {
        return ['Přebor', '1.A třída'];
    }

}
