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

class RedType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('minute', IntegerType::class, [
                'label' => 'Minuta:',
            ])
            ->add('person', TextType::class, [
                'label' => 'Osoba:',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Popis:',
                'required' => false,
            ])
            ->add('offence', EntityType::class, [
                'label' => 'Důvod:',
                'class' => Offence::class,
                'choice_label' => 'fullName',
            ])
            ->add('team', EntityType::class, [
                'label' => 'Tým:',
                'class' => Team::class,
                'choice_label' => 'fullName',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => RedCard::class,
        ]);
    }

}
