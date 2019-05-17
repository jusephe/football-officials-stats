<?php

namespace App\Form;

use App\Entity\League;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LeagueType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fullName', TextType::class, [
                'label' => 'Celý název:',
                'help' => 'Vyplňte celé jméno soutěže, tak jak je uváděno v oficiálních zápisech.',
            ])
            ->add('shortName', TextType::class, [
                'label' => 'Zkrácený název:',
                'help' => 'Vyplňte zkrácené jméno soutěže (např. Přebor), dejte pozor ať je úplně stejné jako u jiných soutěží stejné úrovně!',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => League::class,
        ]);
    }

}
