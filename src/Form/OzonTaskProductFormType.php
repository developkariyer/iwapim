<?php

namespace App\Form;

use App\Form\Transformer\ProductTypeTransformer;
use Doctrine\DBAL\Exception;
use Random\RandomException;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OzonTaskProductFormType extends AbstractType
{
    /**
     * @throws RandomException
     * @throws Exception
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $taskId = $options['task_id'];
        $parentProductId = $options['parent_product_id'];
        $children = $options['children'];
        $selectedChildren = $options['selected_children'];

        $builder
            ->add('taskId', HiddenType::class, [
                'data' => $taskId,
            ])
            ->add('productId', HiddenType::class, [
                'data' => $parentProductId,
            ]);

        $selectedChildrenGroup = $builder->create('selectedChildren', null, [
            'compound' => true,
            'label' => false,
        ]);

        foreach ($children as $colorGroup) {
            foreach ($colorGroup as $child) {
                $selectedChildrenGroup->add((string)$child->getId(), ChoiceType::class, [
                    'choices' => array_merge(
                        [
                            '** Listeleme' => -1,
                            '* PIM Bilgilerini Kullan' => 0,
                        ],
                        array_combine(
                            array_map(fn($item) => mb_strimwidth($item->getKey(), 0, 190, '...'), $child->getListingItems()),
                            array_map(fn($item) => $item->getId(), $child->getListingItems())
                        )
                    ),
                    'label' => mb_strimwidth("{$child->getIWasku()} {$child->getKey()}", 0, 190, '...'),
                    'data' => $selectedChildren[$child->getId()] ?? -1,
                    'attr' => [
                        'class' => 'form-select form-select-sm',
                        'id' => "childSelect_{$taskId}_{$parentProductId}_{$child->getId()}",
                    ],
                    'required' => false,
                ]);
            }
        }

        $builder->add('productType', ChoiceType::class, [
            'choices' => [],
            'label' => 'Ürün Tipi',
            'attr' => [
                'class' => 'select2 form-select needs-initialization',
                'data-ajax-url' => '/ozontree',
                'data-placeholder' => 'Ürün Tipi Seçiniz...',
                'data-minimum-input-length' => 3,
                'id' => 'productType_{$taskId}_{$parentProductId}',
            ],
            'required' => false,
        ]);
        $builder->get('productType')->addModelTransformer(new ProductTypeTransformer());

        $builder->add($selectedChildrenGroup);

        $builder->add('submit', SubmitType::class, [
            'label' => 'Güncelle',
            'attr' => [
                'class' => 'btn btn-primary mt-3',
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null, // Or a DTO if you want to bind data to an object
            'task_id' => null,
            'parent_product_id' => null,
            'children' => [],
            'selected_children' => [],
        ]);
    }
}
