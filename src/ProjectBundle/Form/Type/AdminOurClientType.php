<?php

namespace ProjectBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

use ProjectBundle\Entity\OurClient;
use Doctrine\ORM\EntityRepository;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class AdminOurClientType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        // $builder->add('translations', 'A2lix\TranslationFormBundle\Form\Type\TranslationsType', array(
        //     'fields' => array(
        //         'title' => array(
        //             'field_type' => TextType::class,
        //             'label' => '* Title',
        //             'locale_options' => array(),
        //             'constraints' => array(
        //                 new NotBlank(array('message' => 'Please enter title')))
        //         ),
        //     ),
        //     'exclude_fields' => array('description')
        // ));

        $builder->add('translations', 'A2lix\TranslationFormBundle\Form\Type\TranslationsType', array(
            'fields' => array(
                'title' => array(
                    'field_type' => TextType::class,
                    'label' => '* Title',
                    'locale_options' => array(),
                    'constraints' => array(
                        new NotBlank(array('message' => 'Please enter title')))
                ),
            )
        ));

        $builder->add('image', TextType::class, array(
                        'attr' => array('class' => 'form-control'),
                        'required' => false,
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
