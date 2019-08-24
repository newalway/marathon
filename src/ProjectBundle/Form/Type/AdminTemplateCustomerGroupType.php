<?php

namespace ProjectBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

use ProjectBundle\Entity\CustomerGroup;

use Ivory\CKEditorBundle\Form\Type\CKEditorType;

class AdminTemplateCustomerGroupType extends AbstractType
{
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
                    'label' => '* Name',
                    'locale_options' => array(),
                    'constraints' => array(
                        new NotBlank(array('message' => 'Please enter title')))
                ),
                'description' => array(
                    'field_type' => CKEditorType::class,
                    'label' => 'Content',
                    'locale_options' => array()
                ),
            ),
            // 'exclude_fields' => array('description')
        ));

        $builder->add('customerGroup', EntityType::class, array(
            'attr'=>array('class'=>''),
            'required' => true,
            // query choices from this entity
            'class' => CustomerGroup::class,
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

        $builder->add('save_and_add', SubmitType::class, array('attr' => array('class' => 'btn btn-primary')));
        $builder->add('save_and_edit', SubmitType::class, array('attr' => array('class' => 'btn btn-primary')));
        $builder->add('save', SubmitType::class, array('attr' => array('class' => 'btn btn-primary')));
    }

}
