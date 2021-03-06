<?php

namespace App\Admin\Form;

use App\Admin\Entity\Offence;
use App\Admin\Entity\RedCard;
use App\Admin\Entity\Team;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RedType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('minute', TextType::class, [
                'label' => 'Minuta:',
            ])
            ->add('person', TextType::class, [
                'label' => 'Osoba:',
            ])
            ->add('team', EntityType::class, [
                'label' => 'Tým:',
                'class' => Team::class,
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
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => RedCard::class,
        ]);
    }

}
