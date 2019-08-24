<?php

namespace ProjectBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class AdminBankAccountType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', TextType::Class, array(
                      'attr' => array(
                      'class'=>'form-control',
                      'errortext'=>'Enter a title'),
                      'required' => false,
                      'constraints' => array(
                        new NotBlank(array('message' => 'Enter a title')),
                      )
        ));
        $builder->add('branch', TextType::Class, array(
                      'attr' => array(
                      'class'=>'form-control',
                      'errortext'=>'Enter a branch'),
                      'required' => false,
                      'constraints' => array(
                        new NotBlank(array('message' => 'Enter a branch')),
                      )
        ));
        $builder->add('image', TextType::Class, array(
                      'attr' => array('class' => 'form-control'),
                      'required' => false,
                      'constraints' => array(
                        new NotBlank(array('message' => 'Select your image logo')),
                      )
        ));
        $builder->add('account_number', TextType::Class, array(
                      'attr' => array(
                      'class'=>'form-control',
                      'errortext'=>'Enter a account number'),
                      'required' => false,
                      'constraints' => array(
                        new NotBlank(array('message' => 'Enter a account number')),
                      )
        ));
        $builder->add('account_name', TextType::Class, array(
                      'attr' => array(
                        'class'=>'form-control',
                        'errortext'=>'Enter a account name'),
                      'required' => false,
                      'constraints' => array(
                        new NotBlank(array('message' => 'Enter a account name')),
                      )
        ));
        $builder->add('status', ChoiceType::Class,array(
                      'choices' => array('Enable' => '1', 'Disable' => '0'),
                      'expanded'  => true,
                      'multiple'  => false,
                      'data' => '1',
                      'constraints' => array(
                          new NotBlank(array('message' => 'Enter a status')))
        ));

        $builder->add('save', SubmitType::Class, array('attr' => array('class' => 'btn btn-primary')));
        $builder->add('save_and_edit', SubmitType::Class, array('attr' => array('class' => 'btn btn-primary')));
        $builder->add('save_and_add', SubmitType::Class, array('attr' => array('class' => 'btn btn-primary')));


    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ProjectBundle\Entity\BankAccount'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'projectbundle_bankaccount';
    }


}
