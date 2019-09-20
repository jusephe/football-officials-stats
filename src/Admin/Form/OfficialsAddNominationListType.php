<?php

namespace App\Admin\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Valid;

class OfficialsAddNominationListType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $newLists = $options['newLists'];

        $builder
            ->add('nominationLists', CollectionType::class, [
                'label' => false,
                'entry_type' => AddNominationListType::class,
                'data' => $newLists,
                'entry_options' => ['label' => false],
                'constraints' => [
                    new Valid(),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('newLists');
        $resolver->setAllowedTypes('newLists', 'array');
    }

}
