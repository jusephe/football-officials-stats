<?php

namespace App\Admin\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Range;

class NominationListSeasonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('season', IntegerType::class, [
                'label' => 'Sezóna:',
                'help' => 'Uveďte rok, kdy sezóna začala. Např. pro sezónu 2019/20 vložte 2019.',
                'constraints' => [
                    new Range(['min' => 1950, 'max' => 2070]),
                ],
            ])
            ->add("partOfSeason", ChoiceType::class, [
                "label" => "Část:",
                "choices" => ['Jaro' => 'Jaro', 'Podzim' => 'Podzim'],
                'placeholder' => 'Vyberte část',
            ]);
    }

}
