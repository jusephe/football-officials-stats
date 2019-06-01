<?php

namespace App\Form;

use App\Entity\Official;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OfficialNominatonListsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nominationLists', CollectionType::class, [
                'label' => false,
                'entry_type' => EditNominationListType::class,
                'entry_options' => [
                    'label' => false,
                ],
                'by_reference' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Official::class,
        ]);
    }

}
