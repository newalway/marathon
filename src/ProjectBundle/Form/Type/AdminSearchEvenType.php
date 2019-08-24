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

use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use ProjectBundle\Entity\EventCategory;
use Doctrine\ORM\EntityRepository;

class AdminSearchEvenType extends AbstractType
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

        $builder->add('q', TextType::class, array(
            'required' => false,
        ));

        $builder->add('status', ChoiceType::class, array(
            'choices' => array('Publish' => '1', 'Unpublish' => '0'),
            'expanded' => true,
            'multiple' => false,
            'attr' => array('class' => 'form-control-static'),
        ));

        $builder->add('eventCategory', EntityType::class, array(
            'attr'=>array('class'=>'form-control'),
            'label_attr' => array('class' => ''),
            'required' => false,
            // query choices from this entity
            'class' => EventCategory::class,
            'query_builder' => function (EntityRepository $er) {
                return $er->findAllData();
            },
            'choice_attr' => function($choiceValue, $key, $value) {
                // adds a class like attending_yes, attending_no, etc
                return ['class' => 'template_customer_group_item'];
                // return ['class' => 'attending_'.strtolower($key)];
                // return ['class' => 'level_'.$choiceValue->getLvl()];
            },
            // use the User.username property as the visible option string
            'choice_label' => 'translations['.$local.'].title',
            // 'choice_label' => 'title',
            // used to render a select box, check boxes or radios
            'multiple' => false,
            'expanded' => false, //false for select multiple
        ));


        $builder->add('search', SubmitType::class, array('attr' => array( 'class' => 'btn btn-primary') ));

        $builder->setMethod('GET');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection' => false,
        ));
    }

}
