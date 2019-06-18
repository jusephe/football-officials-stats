<?php

namespace App\Site\Service;

use App\Site\Functionality\StatsFunctionality;

class SeasonsListMaker
{
    private $statsFunctionality;

    public function __construct(StatsFunctionality $statsFunctionality)
    {
        $this->statsFunctionality = $statsFunctionality;
    }

    // returns ordered array of [season, autumn, spring]
    public function createSeasonsList($league)
    {
        $partsOfSeasons = $this->statsFunctionality->getSeasonsWithPart($league);

        $seasonsWithParts = array();

        // transform [season, isAutumn] to [season, autumn, spring]
        foreach ($partsOfSeasons as $item) {
            $season = $item['season'];
            if ( !array_key_exists($season, $seasonsWithParts) ) {
                if ($item['is_autumn']) {
                    $seasonsWithParts[$season] = ['season' => $season, 'autumn' => true, 'spring' => false];
                }
                else $seasonsWithParts[$season] = ['season' => $season, 'autumn' => false, 'spring' => true];
            }
            else {
                if ($item['is_autumn']) {
                    $seasonsWithParts[$season]['autumn'] = true;
                }
                else $seas1onsWithParts[$season]['spring'] = true;
            }
        }

        krsort($seasonsWithParts); // newest seasons will be on top

        return $seasonsWithParts;
    }
}
