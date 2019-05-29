<?php

namespace App\Form;

use App\Entity\Offence;
use App\Entity\RedCard;
use App\Entity\Team;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PunishmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $game = $options['game'];

        $builder
            ->add('person', TextType::class, [
                'label' => 'Osoba:',
            ])
            ->add('team', EntityType::class, [
                'label' => 'Tým:',
                'class' => Team::class,
                'choices' => [$game->getHomeTeam(), $game->getAwayTeam()],
                'choice_label' => 'fullName',
                'placeholder' => 'Vyberte tým',
            ])
            ->add('offence', EntityType::class, [
                'label' => 'Důvod:',
                'class' => Offence::class,
                'choice_label' => 'shortName',
                'placeholder' => 'Vyberte důvod',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Popis:',
                'required' => false,
            ])
            ->add('weeks', IntegerType::class, [
                'label' => 'Týdny:',
                'required' => false,
            ])
            ->add('games', IntegerType::class, [
                'label' => 'Zápasy:',
                'required' => false,
            ])
            ->add('fine', IntegerType::class, [
                'label' => 'Pokuta:',
                'required' => false,
            ])
            ->add('fee', IntegerType::class, [
                'label' => 'Poplatek:',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => RedCard::class,
        ]);

        $resolver->setRequired(['game']);
    }

}
