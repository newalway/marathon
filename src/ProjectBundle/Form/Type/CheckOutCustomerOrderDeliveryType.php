<?php

namespace ProjectBundle\Form\Type;

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
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;



use Ivory\CKEditorBundle\Form\Type\CKEditorType;

class CheckOutCustomerOrderDeliveryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //$builder->add('addressType')->add('firstName')->add('lastName')->add('address')->add('phone')->add('district')->add('province')->add('country')->add('postCode')->add('taxPayerId')->add('companyName')->add('headOffice')->add('customerOrder');

        $builder->add('access_key', HiddenType::class, array(
                    'attr' => array('class'=>'form-control'),
                    'required' => false,
                  ));

        $builder->add('profile_id', HiddenType::class, array(
                    'attr' => array('class'=>'form-control'),
                    'required' => false,
                  ));

        $builder->add('transaction_uuid', HiddenType::class, array(
                    'attr' => array('class'=>'form-control'),
                    'required' => false,
                ));

        $builder->add('signed_field_names', HiddenType::class, array(
                    'attr' => array('class'=>'form-control'),
                    'required' => false,
                ));

        $builder->add('unsigned_field_names', HiddenType::class, array(
                    'attr' => array('class'=>'form-control'),
                    'required' => false,
                ));
        //* signed_date_time format 2018-10-02T04:08:17Z gmdate("Y-m-d\TH:i:s\Z") *//
        $builder->add('signed_date_time', HiddenType::class, array(
                    'attr' => array('class'=>'form-control'),
                    'required' => false,
                ));

        $builder->add('locale', HiddenType::class, array(
                    'attr' => array('class'=>'form-control'),
                    'required' => false,
                ));
        //* transaction_type value is authorization *//
        $builder->add('transaction_type', HiddenType::class, array(
                    'attr' => array('class'=>'form-control'),
                    'required' => false,
                ));
        //* reference_number is product number use for reference payment *//
        $builder->add('reference_number', HiddenType::class, array(
                    'attr' => array('class'=>'form-control'),
                    'required' => false,
                ));
        $builder->add('bill_to_forename', TextType::class, array(
                    'attr' => array('class'=>'form-control'),
                    'required' => false,
                ));
        $builder->add('bill_to_surname', TextType::class, array(
                    'attr' => array('class'=>'form-control'),
                    'required' => false,
                ));
        $builder->add('bill_to_email', TextType::class, array(
                    'attr' => array('class'=>'form-control'),
                    'required' => false,
                ));
        $builder->add('bill_to_address_line1', TextType::class, array(
                    'attr' => array('class'=>'form-control'),
                    'required' => false,
                ));

        $builder->add('bill_to_address_city', TextType::class, array(
                    'attr' => array('class'=>'form-control'),
                    'required' => false,
                ));
        $builder->add('bill_to_address_country', ChoiceType::class, array(
                    'required' => false,
                    'choices' => array('TH' => 'member.country.thailand',),
                    'attr' => array('class'=>'form-control'),
                    'data' =>'member.country.thailand',
                          // 'constraints' => array(
                          //     new NotBlank(array('message' => 'error.country')))
                ));
        $builder->add('bill_to_address_postal_code', TextType::class, array(
                    'attr' => array('class'=>'form-control'),
                    'required' => false,
                ));
        $builder->add('amount', HiddenType::class, array(
                    'attr' => array('class'=>'form-control'),
                    'required' => false,
                ));
        $builder->add('currency', HiddenType::class, array(
                    'attr' => array('class'=>'form-control'),
                    'required' => false,
                ));


    //---------------------------------------------------------------------------//
        $builder->add('title', TextType::class, array(
                  'attr' => array('class'=>'form-control'),
                  'required' => true,
                  'constraints' => array(
                      new NotBlank(array('message' => 'error.title')))
                    ));

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

        $builder->add('address', TextType::class, array(
                  'attr' => array('class'=>'form-control'),
                  'required' => false,
                  'constraints' => array(
                      new NotBlank(array('message' => 'error.address')))
                ));



        $builder->add('phone', TextType::class, array(
                  'required' => false,
                  'attr' => array('class'=>'form-control'),
                  'constraints' => array(
                      new NotBlank(array('message' => 'error.phone')))

        ));
        $builder->add('district', TextType::class, array(
                  'required' => false,
                  'attr' => array('class'=>'form-control'),
                  'constraints' => array(
                      new NotBlank(array('message' => 'error.district')))

        ));
        $builder->add('province', TextType::class, array(
                  'required' => false,
                  'attr' => array('class'=>'form-control'),
                  'constraints' => array(
                      new NotBlank(array('message' => 'error.province')))

        ));
        $builder->add('country', ChoiceType::class, array(
                  'required' => false,
                  'choices' => array('TH' => 'member.country.thailand',),
                  'attr' => array('class'=>'form-control'),
                  'data' =>'member.country.thailand',
                  // 'constraints' => array(
                  //     new NotBlank(array('message' => 'error.country')))

        ));
        $builder->add('postcode', TextType::class, array(
                  'required' => false,
                  'attr' => array('class'=>'form-control'),
                  'constraints' => array(
                      new NotBlank(array('message' => 'error.postcode')))

        ));
        $builder->add('taxpayer_id', TextType::class, array(
                  'required' => false,
                  'attr' => array('class'=>'form-control'),

        ));
        $builder->add('default_shipping', CheckboxType::class, array(
                        'label'    => 'Use as Default Variant',
                        'required' => false,
        ));
        $builder->add('default_tax', CheckboxType::class, array(
                        'label'    => 'Use as Default Variant',
                        'required' => false,
        ));
    }


}
