<?php

namespace App\Site\Service;

use CMEN\GoogleChartsBundle\GoogleCharts\Charts\LineChart;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\PieChart;

class ChartFactory
{
    public static function createRedOffence($data)
    {
        $redOffenceChart = new PieChart();
        $redOffenceChart->getData()->setArrayToDataTable($data);
        $redOffenceChart->getOptions()->setPieSliceText('value');
        $redOffenceChart->getOptions()->getChartArea()->setWidth('90%');
        $redOffenceChart->getOptions()->setBackgroundColor('transparent');

        return $redOffenceChart;
    }

    public static function createCardsMinutes($data, $maxNumberOfCards)
    {
        $cardsMinutesChart = new LineChart();
        $cardsMinutesChart->getData()->setArrayToDataTable($data);
        $cardsMinutesChart->getOptions()->getChartArea()->setWidth('88%');
        $cardsMinutesChart->getOptions()->getChartArea()->setTop('10%');
        $cardsMinutesChart->getOptions()->getLegend()->setPosition('bottom');
        $cardsMinutesChart->getOptions()->getHAxis()->setTicks([5,10,15,20,25,30,35,40,45,50,55,60,65,70,75,80,85,90]);
        $cardsMinutesChart->getOptions()->getVAxis()->getGridlines()->setCount($maxNumberOfCards + 1 );
        $cardsMinutesChart->getOptions()->getVAxis()->getMinorGridlines()->setCount(0);
        $cardsMinutesChart->getOptions()->setColors(['gold', 'crimson']);
        $cardsMinutesChart->getOptions()->setBackgroundColor('transparent');
        $cardsMinutesChart->getOptions()->setLineWidth(3);

        return $cardsMinutesChart;
    }

}
