<?php

namespace App\Form;

use App\Entity\NominationList;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditNominationListType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('year', IntegerType::class, [
                'label' => 'Rok:',
                'disabled' => true,
            ])
            ->add("partOfSeason", ChoiceType::class, [
                "label" => "Část:",
                "choices" => ['Jaro' => 'Jaro', 'Podzim' => 'Podzim'],
                'disabled' => true,
            ])
            ->add('leagueLevelName', TextType::class, [
                'label' => 'Listina:',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => NominationList::class,
        ]);
    }

}
