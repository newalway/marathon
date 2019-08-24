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

use Doctrine\ORM\EntityRepository;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Ivory\CKEditorBundle\Form\Type\CKEditorType;

class AdminDiscountType extends AbstractType
{
    private $kernel;

    public function __construct($kernel)
    {
        $this->container = $kernel->getContainer();
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        // print_r($options['data']);

        $builder->add('discountCode', TextType::class, array(
                        'attr' => array('class'=>'form-control'),
                        'required' => false,
                        'constraints' => array(
                            new NotBlank(array('message' => 'Please enter discount_code')))
        ));


        $cons_discount_type = $this->container->getParameter('cons_discount_type');
        $builder->add('discountType', ChoiceType::class, array(
                        'choices' => $cons_discount_type,
                        'expanded' => false,
                        'multiple' => false,
                        'constraints' => array(
                            new NotBlank(array('message' => 'Enter a Discount type')))
        ));

        $builder->add('discountValue', NumberType::class, array(
                        'attr'      => array('class'=>'form-control'),
                        'required'  => true,
                        'constraints' => array(
                            new NotBlank(array('message' => 'Please enter value')))
        ));

        $cons_discount_applies_to = $this->container->getParameter('cons_discount_applies_to');
        $builder->add('appliesTo', ChoiceType::class, array(
                        'choices' => $cons_discount_applies_to,
                        'expanded' => true,
                        'multiple' => false,
                        'constraints' => array(
                            new NotBlank(array('message' => 'Enter this data')))
        ));

        $builder->add('onlyAppliesOnceItemPerProduct', CheckboxType::class, array(
                        'label'    => 'Only apply discount once item per product',
                        'required' => false,
        ));

        $cons_discount_minimum_requirement = $this->container->getParameter('cons_discount_minimum_requirement');
// print_r($cons_discount_minimum_requirement);
// exit;
        $builder->add('minimumRequirement', ChoiceType::class, array(
                        'choices' => $cons_discount_minimum_requirement,
                        'expanded' => true,
                        'multiple' => false,
                        'constraints' => array(
                            new NotBlank(array('message' => 'Enter this data')))
        ));
        $builder->add('minimumRequirementAmountValue', NumberType::class, array(
                        'attr'      => array('class'=>'form-control'),
                        'required'  => false,
        ));
        $builder->add('minimumRequirementQuantityValue', NumberType::class, array(
                        'attr' => array('class'=>'form-control'),
                        'required' => false,
        ));

        $builder->add('usageLimitDiscountTotal', CheckboxType::class, array(
                        'label'    => 'Limit number of times this discount can be used in total',
                        'required' => false,
        ));
        $builder->add('usageLimitDiscountTotalValue', TextType::class, array(
                        'attr' => array('class'=>'form-control'),
                        'required' => false,
        ));
        $builder->add('usageLimitDiscountOnePerCustomer', CheckboxType::class, array(
                        'label'    => 'Limit to one use per customer',
                        'required' => false,
        ));

        $builder->add('startDate', DateTimeType::class, array(
                        'required' => true,
                        'input'  => 'datetime',
                        'widget' => 'single_text',
                        'format' => 'YYYY-MM-dd HH:mm',
                        'attr' => array('class' => 'form-control'),
                        'constraints' => array(
                            new NotBlank(array('message' => 'Enter a start date')))
        ));
        $builder->add('isEndDate', CheckboxType::class, array(
                        'label'    => 'Set end date',
                        'required' => false,
        ));
        $builder->add('endDate', DateTimeType::class, array(
                        'required' => false,
                        'input'  => 'datetime',
                        'widget' => 'single_text',
                        'format' => 'YYYY-MM-dd HH:mm',
                        'attr' => array('class' => 'form-control')
        ));

        /*
        $builder->add('status', ChoiceType::class, array(
                        'choices' => array('Publish' => '1', 'Unpublish' => '0'),
                        'expanded' => true,
                        'multiple' => false,
                        'attr' => array('class' => 'form-control-static'),
                        'constraints' => array(
                            new NotBlank(array('message' => 'Enter a status')))
        ));
        */


        /*
        $builder->add('image', TextType::class, array(
                        'attr' => array('class' => 'form-control'),
                        'required' => false,
        ));

        $builder->add('short_desc', TextareaType::class, array(
                        'attr' => array('class'=>'form-control'),
                        'required' => false
        ));

        $builder->add('description', CKEditorType::class, array(
                        'attr' => array('class'=>'form-control'),
                        'required' => false
        ));

        $builder->add('filepath', TextType::class, array(
                        'attr' => array('class' => 'form-control'),
                        'required' => false,
        ));

        $builder->add('public_date', DateType::class, array(
                        'required' => true,
                        'input'  => 'datetime',
                        'widget' => 'single_text',
                        'attr' => array('class' => 'form-control-static'),
                        'constraints' => array(
                            new NotBlank(array('message' => 'Enter a publish date')))
        ));
        */



        $builder->add('save_and_add', SubmitType::class, array('attr' => array('class' => 'btn btn-primary')));
        $builder->add('save_and_edit', SubmitType::class, array('attr' => array('class' => 'btn btn-primary')));
        $builder->add('save', SubmitType::class, array('attr' => array('class' => 'btn btn-primary')));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ProjectBundle\Entity\Discount'
        ));
    }

}
