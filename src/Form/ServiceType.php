<?php

namespace App\Form;

use App\Entity\Service;
use App\Entity\ServiceCategory;
use DateTime;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ServiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom',
                'attr'  => [
                    'placeholder' => 'Nom',
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr'  => [
                    'placeholder' => 'Description',
                    'rows'        => 3,
                ],
            ])
            ->add('price', MoneyType::class, [
                'label'   => 'Prix',
                'attr'    => [
                    'placeholder' => 'Prix',
                ],
                'divisor' => 100,
            ])
            ->add('date', DateType::class, [
                'label' => false,
                'data'  => new DateTime('now'),
            ])
            ->add('status', ChoiceType::class, [
                'label'   => 'Statut',
                'choices' => [
                    'Disponible'   => true,
                    'Indisponible' => false,
                ],
            ])
            ->add('mainPicture', TextType::class, [
                'label'       => 'Image principale',
                'attr'        => [
                    'placeholder' => 'Image',
                ],
                'constraints' => new NotBlank(['message' => 'L\'image du service est obligatoire !']),
            ])
            ->add('category', EntityType::class, [
                'placeholder'  => '-- Sélectionner votre catégorie --',
                'label'        => 'Catégorie',
                'multiple'     => false,
                'class'        => ServiceCategory::class,
                'expanded'     => false,
                'choice_label' => function (ServiceCategory $category) {
                    return strtolower($category->getName());
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Service::class,
        ]);
    }
}
