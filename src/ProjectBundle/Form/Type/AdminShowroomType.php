<?php

namespace ProjectBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Ivory\CKEditorBundle\Form\Type\CKEditorType;

class AdminShowroomType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('translations', 'A2lix\TranslationFormBundle\Form\Type\TranslationsType', array(
            'fields' => array(
                'title' => array(
                    'field_type' => TextType::class,
                    'label' => '* Title',
                    'locale_options' => array(),
                    'constraints' => array(
                        new NotBlank(array('message' => 'Please enter title')))
                ),
            ),
            'exclude_fields' => array('description')
        ));

        $builder->add('phone', TextType::class, array(
                        'attr' => array('class' => 'form-control'),
                        'required' => false,
        ));

        $builder->add('fax', TextType::class, array(
                        'attr' => array('class' => 'form-control'),
                        'required' => false,
        ));

        $builder->add('mobile', TextType::class, array(
                        'attr' => array('class' => 'form-control'),
                        'required' => false,
        ));

        $builder->add('latitude', TextType::class, array(
                        'attr' => array('class' => 'form-control'),
                        'required' => false,
                        'constraints' => array(
                            new NotBlank(array('message' => 'Please enter latitude')))
        ));

        $builder->add('longitude', TextType::class, array(
                        'attr' => array('class' => 'form-control'),
                        'required' => false,
                        'constraints' => array(
                            new NotBlank(array('message' => 'Please enter latitude')))
        ));

        $builder->add('placeId', TextType::class, array(
                        'attr' => array('class' => 'form-control'),
                        'required' => false
        ));

        $builder->add('status', ChoiceType::class, array(
                        'choices' => array('Publish' => '1', 'Unpublish' => '0'),
                        'expanded' => true,
                        'multiple' => false,
                        'label_attr' => array('class' => 'radio-inline'),
                        'constraints' => array(
                            new NotBlank(array('message' => 'Enter a status')))
        ));

        $builder->add('save_and_add', SubmitType::class, array('attr' => array('class' => 'btn btn-primary')));
        $builder->add('save_and_edit', SubmitType::class, array('attr' => array('class' => 'btn btn-primary')));
        $builder->add('save', SubmitType::class, array('attr' => array('class' => 'btn btn-primary')));
    }

}
