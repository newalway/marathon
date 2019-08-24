<?php

namespace ProjectBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;

use ProjectBundle\Entity\BankAccount;


class CustomerPaymentBankTransferType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

          $builder->add('orderNumber', TextType::Class, array(
                        'attr' => array(
                        'class'=>'form-control',
                        'readonly' => true ),
                        'required'=>false,
                        'constraints' => array(
                          new NotBlank(array('message' => 'error.order.no')),
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
          // $builder->add('email', TextType::Class, array(
          //               'required'=>false,
          //               'attr'=>array('class'=>'form-input'),
          //               'constraints' => array(new NotBlank(array('message' => 'error.email')),
          //                                      new Email(array('message' => 'error.email.wrong')))
          // ));
          $builder->add('phone', TextType::Class, array(
                        'required'=>false,
                        'attr' => array('class'=>'form-control'),
                        'constraints' => array(
                          new NotBlank(array('message' => 'error.mobile')),
                          new Regex(array('pattern' => "/^[0-9\-\(\)\/\+\#\s]*$/", 'message' => 'error.mobile.only.numbers')),
                          new Regex(array('pattern' => "/\d+/", 'message' => 'error.mobile.fill.valid')),
                          new Length(array('min' => 9, 'max' => 23, 'minMessage' => 'error.mobile.min9', 'maxMessage' => 'error.mobile.max23')))
          ));
          $builder->add('bankAccount', EntityType::Class, array(
                        'required'=>false,
                        'attr'=>array('class'=>'form-control'),
                        // query choices from this entity
                        'class' => BankAccount::class,
                        'query_builder' => function (EntityRepository $er) {
                          return $er->findAllActiveData();
                        },
                        'choice_label' => function($bankAccount, $key, $value) {
                            return $bankAccount->getTitle().', '.$bankAccount->getAccountName().', '.$bankAccount->getAccountNumber();
                        },
                        // use the User.username property as the visible option string
                        // 'choice_label' => 'title',
                        // 'choice_label' => 'translations['.$local.'].title',
                        // used to render a select box, check boxes or radios
                        'multiple' => false,
                        'expanded' => false,
                        'constraints' => array(new NotBlank(array('message' => 'error.bank')))
          ));

          $builder->add('amount', NumberType::Class, array(
                        'required'=>false,
                        'attr'=>array('class'=>'form-control'),
                        'constraints' => array(new NotBlank(array('message' => 'error.money')))
          ));

          $builder->add('dateTransfer',DatetimeType::Class, array(
                        'required'=>false,
                        'widget' => 'single_text',
                        'format' => 'dd/mm/yyyy',
                        'attr'=>array('class'=>'form-control dark', 'data-date-format'=>'dd/mm/yyyy'),
                        'constraints' => array(new NotBlank(array('message' => 'error.date')))
          ));

          // $builder->add('timeTransfer', TimeType::Class, array(
          //               'required'=>false,
          //               'attr'=>array('class'=>'form-control dark'),
          //               'constraints' => array(new NotBlank(array('message' => 'error.time')))
          // ));

          $builder->add('timeTransfer', TimeType::class, array(
                        'required'=>false,
                        'placeholder' => array(
                          'hour' => 'ชั่วโมง', 'minute' => 'นาที',
                        ),
                        'input'  => 'datetime',
                        'widget' => 'choice',
                        'attr'=>   array('class' => ''),
                        'constraints' => array(new NotBlank(array('message' => 'error.time')))
          ));

          $builder->add('attach_file', FileType::Class, array(
                        'required'=>false,
                        'attr'=>array('class'=>'', 'accept'=>'image/*'),
                        'constraints' => array(new NotBlank(array('message' => 'error.select_file')))
          ));
          // $builder->add('other', textarea, array(
          //               'required'=>false,
          //               'attr'=>array('class'=>'form-textarea','rows'=>'5')
          // ));
          $builder->add('orderSearch', TextType::Class, array(
                        'required'=>false,
                        'attr' => array('class'=>'form-control','placeholder'=>'payment.search'),
                        'mapped' => false
          ));

          $builder->add('save', SubmitType::Class, array('attr' => array('class' => 'btn btn-primary')));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ProjectBundle\Entity\CustomerPaymentBankTransfer'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'projectbundle_paymentbanktransfer';
    }


}
