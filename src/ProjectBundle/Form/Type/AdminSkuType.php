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

use ProjectBundle\Entity\Sku;
use Doctrine\ORM\EntityRepository;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Ivory\CKEditorBundle\Form\Type\CKEditorType;

class AdminSkuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('sku', TextType::class, array(
                        'attr'      => array('class' => ''),
                        'required'  => false,
        ));

        $builder->add('price', MoneyType::class, array(
                        'attr' => array('class'=>''),
                        'currency' => '',
                        'scale' => 2,
                        'required' => false,
                        // 'constraints' => array(
                        //     new NotBlank(array('message' => 'Please enter price')))
        ));

        $builder->add('compareAtPrice', MoneyType::class, array(
                        'attr'      => array('class'=>''),
                        'currency'  => '',
                        'scale'     => 2,
                        'required'  => false,
        ));

        $builder->add('inventoryPolicyStatus', ChoiceType::class, array(
                        'attr'      => array('ng-model'=> 'glob_inventory_policy_status'),
                        'choices'   => array("Don't track inventory" => '0', "Tracks this product's inventory" => '1'),
                        'expanded'  => false,
                        'multiple'  => false,
                        'required'  => true,
                        'constraints' => array(
                            new NotBlank(array('message' => 'Enter a inventory policy status')))
        ));

        $builder->add('inventoryQuantity', NumberType::class, array(
                        'attr'      => array('class' => ''),
                        'required'  => true,
        ));

        $builder->add('weight', NumberType::class, array(
                        'attr'      => array('class' => 'form-control-static'),
                        'required'  => false,
        ));

        $builder->add('weightUnit', ChoiceType::class, array(
                        'attr'      => array(),
                        'choices'   => array("g" => 'g', "kg" => 'kg', "lb" => 'lb'),
                        'expanded'  => false,
                        'multiple'  => false,
                        'required'  => true
        ));

        // $builder->add('image', TextType::class, array(
        //                 'attr'      => array('class' => ''),
        //                 'required'  => false,
        // ));

        $builder->add('image', HiddenType::class, array(
                        'required'  => false,
        ));

        $builder->add('defaultOption', CheckboxType::class, array(
                        'label'    => 'Use as Default Variant',
                        'required' => false,
        ));

        // $builder->add('defaultOption', ChoiceType::class, array(
        //                 'choices' => array('Available' => 1, 'Unavailable' => 0),
        //                 'expanded' => true,
        //                 'multiple' => false,
        //                 'label_attr' => array('class' => 'radio-inline'),
        // ));

        $builder->add('status', ChoiceType::class, array(
                        'choices' => array('Available' => '1', 'Unavailable' => '0'),
                        'expanded' => true,
                        'multiple' => false,
                        'label_attr' => array('class' => 'radio-inline'),
                        'constraints' => array(
                            new NotBlank(array('message' => 'Enter a status')))
        ));

        $builder->add('save', SubmitType::class, array('attr' => array('class' => 'btn btn-primary')));
    }


}
