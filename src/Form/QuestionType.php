<?php

namespace App\Form;

use App\Entity\Question;
//use Doctrine\DBAL\Types\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) 
    {
        $builder
            ->add('title' ,TextType::class)
            ->add('content' ,TextareaType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)  
    {
        $resolver->setDefaults([
            // Configure your form options here
             'data_class' => Question::class
        ]);
    }
}
