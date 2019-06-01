<?php

namespace App\Form;

use App\Entity\NominationList;
use App\Entity\Official;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddNominationListType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('official', EntityType::class, [
                'label' => 'Rozhodčí:',
                'class' => Official::class,
                'choice_label' => function ($official) {
                    return $official->getNameWithId();
                },
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
