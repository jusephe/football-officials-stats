<?php

namespace App\Admin\Form;

use App\Admin\Entity\Official;
use App\Admin\Repository\OfficialRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class NominationListOfficialType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('official', EntityType::class, [
                'label' => 'Rozhodčí:',
                'class' => Official::class,
                'query_builder' => function (OfficialRepository $or) {
                    return $or->createQueryBuilder('o')
                        ->orderBy('o.name', 'ASC');
                },
                'choice_label' => function ($official) {
                    return $official->getNameWithId();
                },
                'placeholder' => 'Vyberte rozhodčího',
            ]);
    }

}
