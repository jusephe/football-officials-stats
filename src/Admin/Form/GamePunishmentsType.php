<?php

namespace App\Admin\Form;

use App\Admin\Entity\Game;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GamePunishmentsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('redCards', CollectionType::class, [
                'label' => 'Vyloučení:',
                'entry_type' => PunishmentType::class,
                'entry_options' => [
                    'label' => false,
                    'game' => $options['data'],  // pass Game to child type - for restricting possible teams
                ],
                'allow_add' => true,
                'allow_delete' => true,
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
