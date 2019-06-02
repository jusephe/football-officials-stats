<?php

namespace App\Form;

use App\Entity\Official;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OfficialType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', TextType::class, [
                'label' => 'ID:',
                'help' => 'Vyplňte ID rozhodčího, tak jak je uváděno v oficiálních zápisech za jeho jménem.',
            ])
            ->add('name', TextType::class, [
                'label' => 'Příjmení a jméno:',
                'help' => 'Vyplňte nejprve příjmení a za něj jméno rozhodčího, tak jak bude uváděno na webu pro návštěvníky. 
                            Nemusí se shodovat se jménem uváděným v zápisech.',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Official::class,
        ]);
    }

}
