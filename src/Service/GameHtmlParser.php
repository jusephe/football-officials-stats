<?php

namespace App\Service;

use App\Entity\Game;
use Symfony\Component\DomCrawler\Crawler;

class GameHtmlParser
{
    private $ISFACRGameParser;

    public function __construct(ISFACRGameHtmlParser $ISFACRGameHtmlParser)
    {
        $this->ISFACRGameParser = $ISFACRGameHtmlParser;
    }

    public function createGame($sourceCode)
    {
        $game = new Game();

        $crawler = new Crawler($sourceCode);
        $title = trim($crawler->filter('title')->text());

        switch ($title) {
            case 'IS FAČR':
                $this->ISFACRGameParser->parseHtml($crawler, $game);
        break;
            default:
                throw new \InvalidArgumentException('Zdroj nebyl rozpoznán! Kontaktujte správce systému.');
        }

        return $game;
    }

}
