<?php

namespace App\Form;

use App\Entity\Assessor;
use App\Entity\Game;
use App\Entity\League;
use App\Entity\Official;
use App\Entity\Team;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GameType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('season', IntegerType::class, [
                'label' => 'Sezóna:',
            ])
            ->add('isAutumn', ChoiceType::class, [
                'choices' => [
                    'Podzim' => true,
                    'Jaro' => false,
                ],
                'expanded' => true,
                'label' => 'Část:',
            ])
            ->add('round', IntegerType::class, [
                'label' => 'Kolo:',
            ])
            ->add('league', EntityType::class, [
                'label' => 'Soutěž:',
                'class' => League::class,
                'choice_label' => 'fullName',
                'placeholder' => 'Vyberte soutěž',
            ])
            ->add('homeTeam', EntityType::class, [
                'label' => 'Domácí:',
                'class' => Team::class,
                'choice_label' => 'fullName',
                'placeholder' => 'Vyberte domácí tým',
            ])
            ->add('awayTeam', EntityType::class, [
                'label' => 'Hosté:',
                'class' => Team::class,
                'choice_label' => 'fullName',
                'placeholder' => 'Vyberte hostující tým',
            ])
            ->add('refereeOfficial', EntityType::class, [
                'label' => 'Rozhodčí:',
                'class' => Official::class,
                'choice_label' => function ($official) {
                    return $official->getNameWithId();
                },
                'placeholder' => 'Vyberte rozhodčího',
            ])
            ->add('ar1Official', EntityType::class, [
                'label' => 'AR1:',
                'class' => Official::class,
                'choice_label' => function ($official) {
                    return $official->getNameWithId();
                },
                'required' => false,
            ])
            ->add('ar2Official', EntityType::class, [
                'label' => 'AR2:',
                'class' => Official::class,
                'choice_label' => function ($official) {
                    return $official->getNameWithId();
                },
                'required' => false,
            ])
            ->add('assessor', EntityType::class, [
                'label' => 'Delegát:',
                'class' => Assessor::class,
                'choice_label' => function ($assessor) {
                    return $assessor->getNameWithId();
                },
                'required' => false,
            ])
            ->add('yellowCards', CollectionType::class, [
                'label' => 'Žluté karty:',
                'entry_type' => YellowType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'by_reference' => false,
            ])
            ->add('redCards', CollectionType::class, [
                'label' => 'Červené karty:',
                'entry_type' => RedType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'by_reference' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Game::class,
        ]);
    }

}
