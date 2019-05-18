<?php

namespace App\Form;

use App\Entity\League;
use App\Functionality\LeagueFunctionality;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LeagueType extends AbstractType
{
    private $leagueFunctionality;

    public function __construct(LeagueFunctionality $leagueFunctionality)
    {
        $this->leagueFunctionality = $leagueFunctionality;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fullName', TextType::class, [
                'label' => 'Celý název:',
                'help' => 'Vyplňte celé jméno soutěže, tak jak je uváděno v oficiálních zápisech.',
            ])
            ->add("shortName", ChoiceType::class, [
                "label" => "Zkrácený název:",
                "choices" => $this->leagueFunctionality->getDistinctShortNames(),
                'placeholder' => 'Vyberte zkrácený název',
                'choice_label' => function ($choice) {
                    return $choice;  // short name
                },
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => League::class,
        ]);
    }

}
