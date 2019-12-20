<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title',TextType::class,[
                'label' => 'form.title',
            ])
            ->add('content', TextareaType::class,[
                'label' => 'form.content',
                ])
            ->add('author',TextType::class,[
                'required' => false,
                'label' => 'form.author',
            ])
            ->add('nbViews',IntegerType::class,[
                'label' => 'form.nbViews',
            ])
            ->add('published', CheckboxType::class, [
                'required' => false,
                'attr' => ['class' => 'custom-control-input'],
                'label_attr' => ['class' => 'custom-control-label '],
                'label' => 'form.published',
                ])
            ->add('categories',EntityType::class, [
                'class' => Category::class,
                'multiple' => true,
                'label' => 'form.categories',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
