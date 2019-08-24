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

use ProjectBundle\Entity\LayoutShop;
use Doctrine\ORM\EntityRepository;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use A2lix\TranslationFormBundle\Form\Type\TranslationsType;


class AdminLayoutShopType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('translations', TranslationsType::class, array(
            'fields' => array(
                'content' => array(
                    'field_type' => CKEditorType::class,
                    'label' => 'Content',
                    'locale_options' => array()
                    // 'constraints' => array(
                    //     new NotBlank(array('message' => 'Please enter content')))
                ),
                'linkUrl' => array(
                    'field_type' => TextType::class,
                    'label' => 'Link URL',
                    'locale_options' => array(),
                    'attr' => array('placeholder'=>'http://www.marathon.co.th/product')
                    // 'constraints' => array(
                    //     new NotBlank(array('message' => 'Please enter link URL')))
                ),
                'linkTitle' => array(
                    'field_type' => TextType::class,
                    'label' => 'Button',
                    'locale_options' => array(),
                    'attr' => array('placeholder'=>'Shop Now')
                    // 'constraints' => array(
                    //     new NotBlank(array('message' => 'Please enter button')))
                )
            )
        ));

        $builder->add('image', TextType::class, array(
                        'attr' => array('class' => 'form-control'),
                        'required' => false,
                        // 'constraints' => array(
                        //     new NotBlank(array('message' => 'Enter a image')))
        ));

        $builder->add('publish_date', DateType::class, array(
                        'required' => true,
                        'input'  => 'datetime',
                        'widget' => 'single_text',
                        'attr' => array('class' => 'form-control-static'),
                        'constraints' => array(
                            new NotBlank(array('message' => 'Enter a publish date')))
        ));

        $builder->add('content_position', ChoiceType::class, array(
                        'choices' => array('Left' => 'L', 'Right' => 'R'),
                        'expanded' => true,
                        'multiple' => false,
                        'attr' => array('class' => 'form-control-static'),
                        'constraints' => array(
                            new NotBlank(array('message' => 'Enter a content position')))
        ));

        $builder->add('button_position', ChoiceType::class, array(
                        'choices' => array('Left' => 'L', 'Right' => 'R'),
                        'expanded' => true,
                        'multiple' => false,
                        'attr' => array('class' => 'form-control-static'),
                        'constraints' => array(
                            new NotBlank(array('message' => 'Enter a button position')))
        ));

        $builder->add('status', ChoiceType::class, array(
                        'choices' => array('Publish' => '1', 'Unpublish' => '0'),
                        'expanded' => true,
                        'multiple' => false,
                        'attr' => array('class' => 'form-control-static'),
                        'constraints' => array(
                            new NotBlank(array('message' => 'Enter a status')))
        ));

        $builder->add('save_and_add', SubmitType::class, array('attr' => array('class' => 'btn btn-primary')));
        $builder->add('save_and_edit', SubmitType::class, array('attr' => array('class' => 'btn btn-primary')));
        $builder->add('save', SubmitType::class, array('attr' => array('class' => 'btn btn-primary')));
    }

}
