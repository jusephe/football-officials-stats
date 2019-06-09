<?php

namespace App\Admin\Service;

use App\Admin\Entity\Game;
use Symfony\Component\DomCrawler\Crawler;

class GameBuilder
{
    private $ISFACRGameParser;

    public function __construct(ISFACRGameHtmlParser $ISFACRGameHtmlParser)
    {
        $this->ISFACRGameParser = $ISFACRGameHtmlParser;
    }

    public function createGameFromHtml($sourceCode)
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
