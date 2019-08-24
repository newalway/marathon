<?php

namespace ProjectBundle\Form\Type\Member;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;

use Doctrine\ORM\EntityRepository;
use ProjectBundle\Entity\CountryCode;

use Ivory\CKEditorBundle\Form\Type\CKEditorType;

class DeliveryAddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /*
        $builder->add('title', TextType::class, array(
                  'attr' => array('class'=>'form-control'),
                  'required' => true,
                  'constraints' => array(
                      new NotBlank(array('message' => 'error.title')))
                    ));
        */

        $builder->add('first_name', TextType::class, array(
                  'attr' => array('class'=>'form-control'),
                  'required' => true,
                  'constraints' => array(
                      new NotBlank(array('message' => 'error.firstname')))
                    ));

        $builder->add('last_name', TextType::class, array(
                  'attr' => array('class'=>'form-control'),
                  'required' => true,
                  'constraints' => array(
                      new NotBlank(array('message' => 'error.lastname')))
                    ));

        $builder->add('company_name', TextType::class, array(
                  'attr' => array('class'=>'form-control'),
                  'required' => false,
                  // 'constraints' => array(
                  //     new NotBlank(array('message' => 'error.companyname')))
                    ));

        $builder->add('address', TextType::class, array(
                  'attr' => array('class'=>'form-control'),
                  'required' => false,
                  'constraints' => array(
                      new NotBlank(array('message' => 'error.address')),
                      new Length(array('max' => 50,'maxMessage' => 'error.address.max50')))
                ));

        $builder->add('phone', TextType::class, array(
                    'required' => false,
                    'attr' => array('class'=>'form-control'),
                    'constraints' => array(
                        new NotBlank(array('message' => 'error.phone')),
                        new Regex(array('pattern' => "/^[0-9\-\(\)\/\+\#\s]*$/", 'message' => 'error.mobile.only.numbers')),
                        new Regex(array('pattern' => "/\d+/", 'message' => 'error.mobile.fill.valid')),
                        new Length(array('min' => 9, 'max' => 23, 'minMessage' => 'error.mobile.min9', 'maxMessage' => 'error.mobile.max23')))
        ));

        // $builder->add('phone', TextType::class, array(
        //           'required' => false,
        //           'attr' => array('class'=>'form-control'),
        //
        // ));

        $builder->add('district', TextType::class, array(
                  'required' => false,
                  'attr' => array('class'=>'form-control'),
                  'constraints' => array(
                      new NotBlank(array('message' => 'error.subDistrict')))
        ));

        $builder->add('amphure', TextType::class, array(
                  'required' => false,
                  'attr' => array('class'=>'form-control'),
                  'constraints' => array(
                      new NotBlank(array('message' => 'error.amphure')))
        ));

        $builder->add('province', TextType::class, array(
                'required' => false,
                'attr' => array('class'=>'form-control'),
                'constraints' => array(
                    new NotBlank(array('message' => 'error.province')))
        ));

        // $builder->add('country', ChoiceType::class, array(
        //           'required' => false,
        //           'choices' => array('member.country.thailand' => 'member.country.thailand',),
        //           'attr' => array('class'=>'form-control'),
        //           'data' =>'member.country.thailand',
        //           // 'constraints' => array(
        //           //     new NotBlank(array('message' => 'error.country')))
        // ));

        $builder->add('countryCode', EntityType::class, array(
            'attr' => array(),
            'required' => true,
            // query choices from this entity
            'class' => CountryCode::class,
            'query_builder' => function (EntityRepository $er) {
              return $er->findAllData();
            },
            // use the User.username property as the visible option string
            'choice_label' => 'country',
            // 'choice_label' => 'translations['.$local.'].title',
            // used to render a select box, check boxes or radios
            'multiple' => false,
            'expanded' => false,
            'constraints' => array(
                new NotBlank(array('message' => 'error.country'))
            )
        ));

        $builder->add('postcode', TextType::class, array(
                  'required' => false,
                  'attr' => array('class'=>'form-control'),
                  'constraints' => array(
                      new NotBlank(array('message' => 'error.postcode')),
                      new Regex(array('pattern' => "/^[0-9]+$/", 'message' => 'error.postcode.fill.valid')),
                  )
        ));
        $builder->add('taxpayer_id', TextType::class, array(
                  'required' => false,
                  'attr' => array('class'=>'form-control'),
                  'constraints' => array(
                      // new NotBlank(array('message' => 'error.taxid')),
                      new Regex(array('pattern' => "/^[\d]{13}$/", 'message' => 'error.taxid.invalid')),
                  )
        ));
        $builder->add('default_shipping', CheckboxType::class, array(
                        'label'    => 'Use as Default Variant',
                        'required' => false,
        ));
        $builder->add('default_tax', CheckboxType::class, array(
                        'label'    => 'Use as Default Variant',
                        'required' => false,
        ));

        $builder->add('latitude', TextType::class, array(
                        'attr' => array('class' => 'form-control'),
                        'required' => false,
                        'constraints' => array(
                            new NotBlank(array('message' => 'error.latitude')))
        ));
        $builder->add('longitude', TextType::class, array(
                        'attr' => array('class' => 'form-control'),
                        'required' => false,
                        'constraints' => array(
                            new NotBlank(array('message' => 'error.longitude')))
        ));
        $builder->add('placeId', TextType::class, array(
                        'attr' => array('class' => 'form-control'),
                        'required' => false
        ));

        $builder->add('update', SubmitType::class, array('attr' => array('class' => '')));
        $builder->add('save', SubmitType::class, array('attr' => array('class' => '')));

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ProjectBundle\Entity\DeliveryAddress'
        ));
    }

}
