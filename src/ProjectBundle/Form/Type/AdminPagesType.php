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

use ProjectBundle\Entity\Pages;
use Doctrine\ORM\EntityRepository;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Ivory\CKEditorBundle\Form\Type\CKEditorType;

class AdminPagesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('translations', 'A2lix\TranslationFormBundle\Form\Type\TranslationsType', array(
            'fields' => array(
                'title' => array(
                    'field_type' => TextType::class,
                    'label' => 'Title',
                    'locale_options' => array(
                        //'th' => array('label' => 'ไทย')
                        //'th' => array('display' => false)
                    )
                ),
                'content' => array(
                    'field_type' => CKEditorType::class,
                    'label' => 'Content',
                    'locale_options' => array(
                        //'th' => array('label' => 'ไทย')
                        //'th' => array('display' => false)
                    )
                )
            ),
            'label_attr'=>array('class'=>'hide')
            //'exclude_fields' => array('details')
        ));

        $builder->add('status', ChoiceType::class, array(
                        'choices' => array('Publish' => '1', 'Unpublish' => '0'),
                        'expanded' => true,
                        'multiple' => false,
                        'label_attr' => array('class' => 'radio-inline'),
                        'constraints' => array(
                            new NotBlank(array('message' => 'Enter a status')))
        ));

        $builder->add('save', SubmitType::class, array('attr' => array('class' => 'btn btn-primary')));
    }

}
