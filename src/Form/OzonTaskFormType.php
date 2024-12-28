<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OzonTaskFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('marketplace', ChoiceType::class, [
                'label' => 'Ozon Pazaryeri',
                'choices' => $options['marketplaces'], // Dynamically pass marketplaces as options
                'placeholder' => 'Ozon Pazaryeri Seçiniz',
                'required' => true,
                'choice_label' => function ($marketplace) {
                    return $marketplace->getKey(); // Displayed value
                },
                'choice_value' => 'id', // Submitted value
                'attr' => ['class' => 'form-select'], // Bootstrap styling
            ])
            ->add('taskName', TextType::class, [
                'label' => 'Görev İsmi',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Göreve isim verin',
                    'class' => 'form-control', // Bootstrap styling
                ],
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Yeni Görev',
                'attr' => ['class' => 'btn btn-primary'], // Bootstrap button styling
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'marketplaces' => [], // Default empty, must be set in the controller
        ]);
    }
}