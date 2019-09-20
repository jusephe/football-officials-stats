<?php

namespace App\Admin\Form;

use App\Admin\Entity\League;
use App\Admin\Functionality\GameFunctionality;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class AddedGamesFilterType extends AbstractType
{
    private $gameFunctionality;

    public function __construct(GameFunctionality $gameFunctionality)
    {
        $this->gameFunctionality = $gameFunctionality;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('league', EntityType::class, [
                'label' => 'Soutěž:',
                'class' => League::class,
                'choice_label' => 'fullName',
                'placeholder' => 'Všechny soutěže',
                'required' => false,
            ])
            ->add("season", ChoiceType::class, [
                "label" => "Sezóna:",
                "choices" => $this->gameFunctionality->getDistinctSeasons(),
                'choice_label' => function ($choice) {
                    $seasonEndYear = substr($choice+1, 2);
                    return "$choice/$seasonEndYear";  // pure season
                },
                'placeholder' => 'Všechny sezóny',
                'required' => false,
            ])
            ->add("round", ChoiceType::class, [
                "label" => "Kolo:",
                "choices" => $this->gameFunctionality->getDistinctRounds(),
                'choice_label' => function ($choice) {
                    return $choice;  // pure round
                },
                'placeholder' => 'Všechna kola',
                'required' => false,
            ]);
    }

}
