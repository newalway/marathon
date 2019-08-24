<?php

namespace ProjectBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class RequestServiceType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('requestTitle', ChoiceType::class, array(
            'choices' => array('request_service.title' => '0'),
            'attr' => array('class'=>'form-control'),
        ));
        // $builder->add('requestTitle', HiddenType::Class, array(
        //               'required'=>false,
        //               'attr' => array('class'=>'form-control'),
        //               'constraints' => array(
        //                 new NotBlank(array('message' => 'error.firstname')),
        //               )
        // ));

        $builder->add('productTitle', TextType::Class, array(
                      'required'=>false,
                      'attr' => array('class'=>'form-control'),
                      'constraints' => array(
                        new NotBlank(array('message' => 'error.product.name')),
                      )
        ));

        $builder->add('productModel', TextType::Class, array(
                      'required'=>false,
                      'attr' => array('class'=>'form-control'),
                      // 'constraints' => array(
                      //   new NotBlank(array('message' => 'error.product.medel')),
                      // )
        ));

        $builder->add('productTextType', TextType::Class, array(
                      'required'=>false,
                      'attr' => array('class'=>'form-control'),
                      // 'constraints' => array(
                      //   new NotBlank(array('message' => 'error.product.medel')),
                      // )
        ));

        $builder->add('productSerialNumber', TextType::Class, array(
                      'required'=>false,
                      'attr' => array('class'=>'form-control'),
                      // 'constraints' => array(
                      //   new NotBlank(array('message' => 'error.firstname')),
                      // )
        ));

        $builder->add('productWarrantyNumber', TextType::Class, array(
                      'required'=>false,
                      'attr' => array('class'=>'form-control'),
                      // 'constraints' => array(
                      //   new NotBlank(array('message' => 'error.product.medel')),
                      // )
        ));

        $builder->add('requestDetail', TextareaType::Class, array(
                      'required'=>false,
                      'attr' => array('class'=>'form-control','style'=>'height:150px'),
                      'constraints' => array(
                        new NotBlank(array('message' => 'error.description')),
                      )
        ));

        $builder->add('firstName', TextType::Class, array(
                      'required'=>false,
                      'attr' => array('class'=>'form-control'),
                      'constraints' => array(
                        new NotBlank(array('message' => 'error.firstname')),
                      )
        ));
        $builder->add('lastName', TextType::Class, array(
                      'required'=>false,
                      'attr' => array('class'=>'form-control'),
                      'constraints' => array(
                        new NotBlank(array('message' => 'error.lastname')),
                      )
        ));

        $builder->add('phone', TextType::Class, array(
                      'required'=>false,
                      'attr' => array('class'=>'form-control'),
                      'constraints' => array(
                        new NotBlank(array('message' => 'error.mobile')),
                        new Regex(array('pattern' => "/^[0-9\-\(\)\/\+\#\s]*$/", 'message' => 'error.mobile.only.numbers')),
                        new Regex(array('pattern' => "/\d+/", 'message' => 'error.mobile.fill.valid')),
                        new Length(array('min' => 9, 'max' => 23, 'minMessage' => 'error.mobile.min9', 'maxMessage' => 'error.mobile.max23')))
        ));

        $builder->add('email',TextType::class, array(
            'required'=>false,
            'attr'=>array('class'=>'form-control'),
            'constraints' => array(
                //new NotBlank(array('message' => 'error.email')),
                new Email(array('message' => 'error.email.wrong'))
            )
        ));

        $builder->add('address', TextType::Class, array(
                      'required'=>false,
                      'attr' => array('class'=>'form-control'),
                      'constraints' => array(
                        new NotBlank(array('message' => 'error.address')),
                      )
        ));

        $builder->add('subDistrict', TextType::class, array(
                  'required' => false,
                  'attr' => array('class'=>'form-control'),
                  'constraints' => array(
                      new NotBlank(array('message' => 'error.subDistrict')))
        ));

        $builder->add('district', TextType::Class, array(
                      'required'=>false,
                      'attr' => array('class'=>'form-control'),
                      'constraints' => array(
                        new NotBlank(array('message' => 'error.district')),
                      )
        ));



        // $builder->add('subDistrict', TextType::Class, array(
        //               'required'=>false,
        //               'attr' => array('class'=>'form-control'),
        //               'constraints' => array(
        //                 new NotBlank(array('message' => 'error.firstname')),
        //               )
        // ));

        $builder->add('province', TextType::Class, array(
                      'required'=>false,
                      'attr' => array('class'=>'form-control'),
                      'constraints' => array(
                        new NotBlank(array('message' => 'error.province')),
                      )
        ));

        $builder->add('postCode', TextType::Class, array(
                      'required'=>false,
                      'attr' => array('class'=>'form-control'),
                      'constraints' => array(
                        new NotBlank(array('message' => 'error.zip')),
                      )
        ));


    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ProjectBundle\Entity\RequestService'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'projectbundle_requestservice';
    }


}
