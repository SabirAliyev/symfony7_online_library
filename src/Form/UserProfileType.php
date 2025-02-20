<?php

namespace App\Form;

use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'FullName',
                'required' => false,
                'constraints' => [
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Your nam must be at least 2 characters long.',
                        'max' => 50
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email Address',
                'required' => false,
            ])
            ->add('password', PasswordType::class, [
                'mapped' => false,
                'attr' =>['autocomplete' => 'new-password'],
                'required' => false
            ])
            ->add('profilePicture', FileType::class, [
                'label' => 'Profile Picture',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new Image([
                        'maxSize' => '2M',
                        'mimeTypesMessage' => 'Please upload a valid image file (JPEG, PNG, etc).'
                    ]),
                ]
            ])
        ;
    }
}