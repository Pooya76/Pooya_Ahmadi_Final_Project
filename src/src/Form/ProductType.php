<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Contracts\Translation\TranslatorInterface;

class ProductType extends AbstractType
{
    private $translator;
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $categories=$options['categories'];
        $nameLabel = $this->translator->trans('product.name');
        $rateLabel = $this->translator->trans('product.rate');
        $priceLabel = $this->translator->trans('product.price');
        $categoryLabel = $this->translator->trans('product.categories');
        $imageLabel = $this->translator->trans('product.image');
        $builder
            ->setAttribute('class', 'form-group')
            ->add('name', TextType::class, ['label_format' => $nameLabel])->setAttribute('class', 'form-control')
            ->add('rate', RangeType::class, [
                'attr' => [
                    'min' => 1,
                    'max' => 10
                ],
                'label_format' => $rateLabel
            ])
            ->add('availability' , CheckboxType::class, ['required' => false, 'label_format' => '%name%'])
            ->add('price', MoneyType::class, [
                'currency' => 'IRR',
                'label_format' => $priceLabel
            ])

            ->add('categories', ChoiceType::class, array(
                'mapped'=>false,
                'label_format' => $categoryLabel,
                'choices'=> array(

                    'categories'=>$categories,
                ),
                'choice_label' => function(?Category $category) {
                    return $category ? $category : '';
                },
                'multiple'  => true,
                'expanded' => true,
                'required'=>false,
            ))
            ->add('picture', FileType::class, [
                'label_format' => $imageLabel,
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '10240k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid Image file',
                    ])
                ],
            ])
            ->add('save', SubmitType::class, ['label_format' => '%name%'])
        ;

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
            'categories'=>null,
        ]);
    }
}
