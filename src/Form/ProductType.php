<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Product;
use App\Form\DataTransformer\CentimesTransformer;
use App\Form\Type\PriceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du produit',
                'attr' => [
                    'placeholder' => 'Tapez le nom du produit'
                ]
            ])
            ->add('price', MoneyType::class, [
                'label' => 'Prix du produit',
                'attr' => [
                    'placeholder' => 'Tapez le prix du produit en €'
                ],
                // Méthode 3 : conversion euros en centimes
                'divisor' => 100
            ])
            ->add('picture', UrlType::class, [
                'label' => 'Image du produit',
                'attr' => [
                    'placeholder' => 'Tapez une URL d\'image'
                ]
            ])
            ->add('shortDescription', TextareaType::class, [
                'label' => 'Description courte',
                'attr' => [
                    'placeholder' => 'Tapez une description assez courte mais parlante pour le visiteur'
                ]
            ])
            ->add('category', EntityType::class, [
                'label' => 'Catégorie',
                'placeholder' => '-- Choisir une catégorie --',
                'class' => Category::class,
                'choice_label' => function(Category $category) {
                    return $category->getName();
                }
            ])
        ;
        // Méthode 2 : Utilisation du Datatransformer
        // $builder->get('price')->addModelTransformer(new CentimesTransformer);

        // // Méthode 1 :DataTransformer pour convertir euros en centimes, cad transformer le prix en euro avant d'être affiché et en centimes avant d'être enregistré
        // $builder->get('price')->addModelTransformer(new CallbackTransformer(
        //     function($value) {
        //         // dd("Transformation: ", $value);
        //         if ($value === null) {
        //             return;
        //         }

        //         return $value / 100;
        //     },
        //     function($value) {
        //         if ($value === null) {
        //             return;
        //         }

        //         return $value * 100;
        //         // dd("ReverseTransformation: ", $value);
        //     }
        // ));

        // $builder->addEventListener(FormEvents::POST_SUBMIT, function(FormEvent $event) {
        //     $product = $event->getData();

        //     if ($product->getPrice() !== null) {
        //         $product->setPrice($product->getPrice() * 100);
        //     }
        // });

        // $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) {
        //     $form = $event->getForm();
        //     /** @var Product */
        //     $product = $event->getData();

        //     if ($product->getPrice() !== null) {
        //         $product->setPrice($product->getPrice() / 100);
        //     }

        //     // if ($product->getId() === null) {
        //     //     $form->add('category', EntityType::class, [
        //     //         'label' => 'Catégorie',
        //     //         'placeholder' => '-- Choisir une catégorie --',
        //     //         'class' => Category::class,
        //     //         'choice_label' => function(Category $category) {
        //     //             return $category->getName();
        //     //         }
        //     //     ]);
        //     // }
        // });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
