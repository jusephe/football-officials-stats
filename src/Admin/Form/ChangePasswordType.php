<?php

namespace App\Admin\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('currentPassword', PasswordType::class, [
                'constraints' => [
                    new UserPassword(),
                ],
                'label' => 'Současné heslo:',
                'attr' => [
                    'autocomplete' => 'new-password',
                ],
            ])
            ->add('newPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'min' => 7,
                        'max' => 128,
                    ]),
                ],
                'first_options' => [
                    'label' => 'Nové heslo:',
                    'help' => 'Minimální délka hesla je 7 znaků.',
                ],
                'second_options' => [
                    'label' => 'Nové heslo znovu:',
                ],
                'invalid_message' => 'Hesla se musí shodovat.',
            ]);
    }

}
