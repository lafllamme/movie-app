<?php

namespace App\Form;

use App\Entity\Movie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MovieFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'attr' => [
                    'class' => 'bg-transparent block border-b-2 w-full h-20 text-6xl outline-none',
                    'placeholder' => 'Enter title...',
                ],
                'label' => false,
                'required' => false
            ])
            ->add('releaseYear', IntegerType::class, [
                'attr' => [
                    'class' => 'bg-transparent block border-b-2 w-full h-20 text-6xl outline-none mt-10',
                    'placeholder' => 'Enter release year',
                ],
                'label' => false,
                'required' => false

            ])
            ->add('description', TextareaType::class, [
                'attr' => [
                    'class' => 'py-20 bg-transparent block border-b-2 w-full h-60 text-xl outline-none',
                    'placeholder' => 'Enter description...',
                ],
                'label' => false,
                'required' => false

            ])
            ->add('imagePath', FileType::class, array(
                'attr' => [
                    'class' => 'py-10 shadow appearance-none border border-blue-500 rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline'
                ],
                'required' => false,
                'data_class' => null,
                'label' => false,

            ))
            // ->add('imagePath', FileType::class, [
            //     'attr' => [
            //         'class' => 'py-10 shadow appearance-none border border-blue-500 rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline',
            //     ],
            //     'label' => false
            // ]);

            // ->add('actors')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Movie::class,
        ]);
    }
}
