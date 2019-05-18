<?php

namespace App\Form;

use App\Entity\Team;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TeamType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('clubId', TextType::class, [
                'label' => 'ID klubu:',
                'help' => 'Vyplňte ID klubu, tak jak je uváděno v oficiálních zápisech před jménem týmu.',
            ])
            ->add('fullName', TextType::class, [
                'label' => 'Celý název:',
                'help' => 'Vyplňte celý název týmu, tak jak je uváděn v oficiálních zápisech.',
            ])
            ->add('shortName', TextType::class, [
                'label' => 'Zkrácený název:',
                'help' => 'Vyplňte zkrácený název týmu, tak jak bude uváděn na webu pro návštěvníky.',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Team::class,
        ]);
    }

}
