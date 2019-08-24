<?php

namespace ProjectBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpFoundation\RequestStack;

use ProjectBundle\Entity\ProductCategory;
use ProjectBundle\Entity\Equipment;
use ProjectBundle\Entity\Brand;
use ProjectBundle\Entity\Power;
use ProjectBundle\Entity\PowerTranslation;
use ProjectBundle\Entity\CustomerGroup;
use ProjectBundle\Entity\AgeGroup;
use ProjectBundle\Entity\Muscle;
use ProjectBundle\Entity\Showroom;
use Doctrine\ORM\EntityRepository;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Ivory\CKEditorBundle\Form\Type\CKEditorType;

class AdminProductType extends AbstractType
{
    private $kernel;
    protected $container;
    protected $request_stack;

    public function __construct($kernel, RequestStack $request_stack)
    {
        $this->request_stack = $request_stack;
        $this->container = $kernel->getContainer();
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $local = $this->request_stack->getCurrentRequest()->getLocale();

        $builder->add('translations', 'A2lix\TranslationFormBundle\Form\Type\TranslationsType', array(
            'fields' => array(
                'title' => array(
                    'field_type' => TextType::class,
                    'label' => '* Title',
                    'locale_options' => array(),
                    'constraints' => array(
                        new NotBlank(array('message' => 'Please enter title')))
                ),
                'shortDesc' => array(
                    'field_type' => TextareaType::class,
                    'label' => 'Short Description',
                    'locale_options' => array()
                ),
                'description' => array(
                    'field_type' => CKEditorType::class,
                    'label' => 'Description',
                    'locale_options' => array()
                ),
            ),
            //'exclude_fields' => array('description')
        ));

        $builder->add('price', MoneyType::class, array(
                        'attr'      => array('ng-model'=> 'glob_price', 'class'=>''),
                        'currency'  => '',
                        'scale'     => 2,
                        'required'  => true,
                        'constraints' => array(
                            new NotBlank(array('message' => 'Please enter price')),
                            new Range(array('min' => 0, 'max' => 99999999.99, 'minMessage' => 'error.product.price_min', 'maxMessage' => 'error.product.price_max')))
        ));

        $builder->add('compareAtPrice', MoneyType::class, array(
                        'attr'      => array('ng-model'=> 'glob_compare_at_price', 'class'=>''),
                        'currency'  => '',
                        'scale'     => 2,
                        'required'  => false,
                        'constraints' => array(
                            new Range(array('min' => 0, 'max' => 99999999.99, 'minMessage' => 'error.product.price_min', 'maxMessage' => 'error.product.price_max')))
        ));

        $builder->add('sku', TextType::class, array(
                        'attr'      => array('ng-model'=> 'glob_sku', 'class' => ''),
                        'required'  => false,
        ));

        $builder->add('inventoryPolicyStatus', ChoiceType::class, array(
                        'attr'      => array('ng-model'=> 'glob_inventory_policy_status'),
                        // 'attr'      => array('ng-model'=> 'glob_inventory_policy_status', 'ng-change' => 'changedInventoryPolicyStatus(glob_inventory_policy_status)'),
                        'choices'   => array("Don't track inventory" => '0', "Tracks this product's inventory" => '1'),
                        'expanded'  => false,
                        'multiple'  => false,
                        'required'  => true,
                        'constraints' => array(
                            new NotBlank(array('message' => 'Enter a inventory policy status')))
        ));

        $builder->add('inventoryQuantity', NumberType::class, array(
                        'attr'      => array('ng-model'=> 'glob_inventory_quantity', 'class' => ''),
                        'required'  => false,
                        'constraints' => array(
                            new Range(array('min' => 0, 'max' => 99999999, 'minMessage' => 'error.product.inventory_min', 'maxMessage' => 'error.product.inventory_max')))
        ));

        $builder->add('weight', NumberType::class, array(
                        'attr'      => array('class' => 'form-control-static'),
                        'required'  => false,
                        'constraints' => array(
                            new Range(array('min' => 1, 'max' => 99999, 'minMessage' => 'error.product.weight_min', 'maxMessage' => 'error.product.weight_max')))
        ));

        $builder->add('weightUnit', ChoiceType::class, array(
                        'attr'      => array(),
                        'choices'   => array("g" => 'g', "kg" => 'kg', "lb" => 'lb'),
                        'expanded'  => false,
                        'multiple'  => false,
                        'required'  => true
        ));

        $builder->add('image', HiddenType::class, array(
                        'required'  => false,
        ));

        $builder->add('userWeight', TextType::class, array(
                        'attr'      => array('class' => ''),
                        'required'  => false,
        ));

        $builder->add('productCategories', EntityType::class, array(
            'attr'=>array('class'=>''),
            'required' => false,
            // query choices from this entity
            'class' => ProductCategory::class,
            'query_builder' => function (EntityRepository $er) {
              $product_category_root_id = $this->container->getParameter('product_category_root_id');
              return $er->findDataByRootId($product_category_root_id);
            },
            'choice_attr' => function($choiceValue, $key, $value) {
                // adds a class like attending_yes, attending_no, etc
                // return ['class' => 'attending_'.strtolower($key)];
                return ['class' => 'level_'.$choiceValue->getLvl()];
            },
            // use the User.username property as the visible option string
            'choice_label' => function ($product_category) {
                return $product_category->getTitle();
            },
            // 'choice_label' => 'translations['.$local.'].title',
            // 'choice_label' => 'title',
            // used to render a select box, check boxes or radios
            'multiple' => true,
            'expanded' => true, //false for select multiple
        ));

        /* relation one to many
        $builder->add('productCategory', EntityType::class, array(
            'attr'=>array('class' => ''),
            'label_attr' => array('class' => ''),
            'required' => true,
            // query choices from this entity
            'class' => ProductCategory::class,
            'query_builder' => function (EntityRepository $er) {
                $product_category_root_id = $this->container->getParameter('product_category_root_id');
                return $er->findDataByRootId($product_category_root_id);
            },
            // use the User.username property as the visible option string
            'choice_label' => function ($product_category) {
                return $product_category->getTitle();
            },
            'choice_attr' => function($choiceValue, $key, $value) {
                return ['class' => 'level_'.$choiceValue->getLvl()];
            },
            // used to render a select box, check boxes or radios
            'multiple' => false,
            'expanded' => false,
        ));
        */

        $builder->add('brand', EntityType::class, array(
            'attr'=>array('class' => ''),
            'label_attr' => array('class' => ''),
            'required' => true,
            // query choices from this entity
            'class' => Brand::class,
            'query_builder' => function (EntityRepository $er) {
              return $er->findAllData();
            },
            // use the User.username property as the visible option string
            'choice_label' => function ($brand) {
              return $brand->getTitle();
            },
            // 'choice_attr' => function($choiceValue, $key, $value) {
            //     return ['class' => 'attending_'.strtolower($key)];
            // },
            // used to render a select box, check boxes or radios
            'multiple' => false,
            'expanded' => false,
        ));

        /*
        $builder->add('equipment', EntityType::class, array(
            'attr'=>array('class'=>''),
            'required' => false,
            // query choices from this entity
            'class' => Equipment::class,
            'query_builder' => function (EntityRepository $er) {
              return $er->findAllData();
            },
            // use the User.username property as the visible option string
            'choice_label' => 'translations['.$local.'].title',
            // 'choice_label' => 'title',
            // used to render a select box, check boxes or radios
            'multiple' => false,
            'expanded' => false,
        ));
        */

        $builder->add('power', EntityType::class, array(
            'attr'=>array('class'=>''),
            'required' => false,
            // query choices from this entity
            'class' => Power::class,
            'query_builder' => function (EntityRepository $er) {
              return $er->findAllData();
            },
            // use the User.username property as the visible option string
            'choice_label' => 'translations['.$local.'].title',
            // 'choice_label' => 'title',
            // used to render a select box, check boxes or radios
            'multiple' => false,
            'expanded' => false,
        ));

        $builder->add('customer_groups', EntityType::class, array(
            'attr'=>array('class'=>''),
            'label_attr' => array('class' => ''),
            'required' => true,
            // query choices from this entity
            'class' => CustomerGroup::class,
            'query_builder' => function (EntityRepository $er) {
              return $er->findAllData();
            },
            'choice_attr' => function($choiceValue, $key, $value) {
                // adds a class like attending_yes, attending_no, etc
                // return ['class' => 'attending_'.strtolower($key)];
                return ['class' => 'customer_check_item'];
            },
            // use the User.username property as the visible option string
            'choice_label' => 'translations['.$local.'].title',
            // 'choice_label' => 'title',
            // used to render a select box, check boxes or radios
            'multiple' => true,
            'expanded' => true,
        ));

        $builder->add('age_groups', EntityType::class, array(
            'attr'=>array('class'=>''),
            'label_attr' => array('class' => ''),
            // 'label_attr' => array('class' => 'checkbox-inline'),
            'required' => true,
            // query choices from this entity
            'class' => AgeGroup::class,
            'query_builder' => function (EntityRepository $er) {
              return $er->findAllData();
            },
            'choice_attr' => function($choiceValue, $key, $value) {
                // adds a class like attending_yes, attending_no, etc
                // return ['class' => 'attending_'.strtolower($key)];
                return ['class' => 'age_group_check_item'];
            },
            // use the User.username property as the visible option string
            'choice_label' => 'translations['.$local.'].title',
            // 'choice_label' => 'title',
            // used to render a select box, check boxes or radios
            'multiple' => true,
            'expanded' => true,
        ));

        $builder->add('muscles', EntityType::class, array(
            'attr'=>array('class'=>''),
            'label_attr' => array('class' => ''),
            'required' => true,
            // query choices from this entity
            'class' => Muscle::class,
            'query_builder' => function (EntityRepository $er) {
              return $er->findAllData();
            },
            'choice_attr' => function($choiceValue, $key, $value) {
                // adds a class like attending_yes, attending_no, etc
                // return ['class' => 'attending_'.strtolower($key)];
                return ['class' => 'muscle_check_item'];
            },
            // use the User.username property as the visible option string
            'choice_label' => 'translations['.$local.'].title',
            // 'choice_label' => 'title',
            // used to render a select box, check boxes or radios
            'multiple' => true,
            'expanded' => true,
        ));

        $builder->add('showrooms', EntityType::class, array(
            'attr'=>array('class'=>''),
            'label_attr' => array('class' => ''),
            'required' => false,
            // query choices from this entity
            'class' => Showroom::class,
            'query_builder' => function (EntityRepository $er) {
              return $er->findAllData();
            },
            'choice_attr' => function($choiceValue, $key, $value) {
                // adds a class like attending_yes, attending_no, etc
                // return ['class' => 'attending_'.strtolower($key)];
                return ['class' => 'showroom_check_item'];
            },
            // use the User.username property as the visible option string
            'choice_label' => 'translations['.$local.'].title',
            // 'choice_label' => 'title',
            // used to render a select box, check boxes or radios
            'multiple' => true,
            'expanded' => true, //false for select multiple
        ));

        $builder->add('isNew', CheckboxType::class, array(
                        'label'    => 'Show a New icon on the site',
                        'required' => false,
        ));

        $builder->add('status', ChoiceType::class, array(
                        'choices' => array('Available' => '1', 'Unavailable' => '0'),
                        'expanded' => true,
                        'multiple' => false,
                        'label_attr' => array('class' => 'radio-inline'),
                        'constraints' => array(
                            new NotBlank(array('message' => 'Enter a status')))
        ));

        $builder->add('publishDate', DateTimeType::class, array(
                        'required' => true,
                        'input'  => 'datetime',
                        'widget' => 'single_text',
                        'format' => 'YYYY-MM-dd HH:mm',
                        'attr' => array('class' => 'form-control-static'),
                        'constraints' => array(
                            new NotBlank(array('message' => 'Enter a publish date')))
        ));

        $builder->add('isBestSeller', CheckboxType::class, array(
                        'label'    => 'Best selling product',
                        'required' => false,
        ));

        $builder->add('save_and_add', SubmitType::class, array('attr' => array('class' => 'btn btn-primary')));
        $builder->add('save_and_edit', SubmitType::class, array('attr' => array('class' => 'btn btn-primary')));
        $builder->add('save', SubmitType::class, array('attr' => array('class' => 'btn btn-primary')));
    }

}
