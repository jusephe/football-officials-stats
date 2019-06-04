<?php

namespace App\Form;

use App\Entity\Post;
use App\Functionality\LeagueFunctionality;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostType extends AbstractType
{
    private $leagueFunctionality;

    public function __construct(LeagueFunctionality $leagueFunctionality)
    {
        $this->leagueFunctionality = $leagueFunctionality;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Nadpis:',
            ])
            ->add('contentsMd', TextareaType::class, [
                'label' => 'Obsah:',
                'required' => false,
            ]);;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }

}
