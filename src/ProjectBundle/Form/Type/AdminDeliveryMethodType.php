<?php

namespace ProjectBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;

use Symfony\Component\Validator\Constraints\NotBlank;

class AdminDeliveryMethodType extends AbstractType
{
    const DATE_OPTION = array(
        'Same day'  => 'today',
        'Next day'  => 'tomorrow',
        '+2 days'   => '+2 days',
        '+3 days'   => '+3 days',
        '+4 days'   => '+4 days',
        '+5 days'   => '+5 days',
        '+6 days'   => '+6 days',
        '+7 days'   => '+7 days'
    );
    const DAY_WORD_OPTION = array(
        'Sunday'    => 'Sunday',
        'Monday'    => 'Monday',
        'Tuesday'   => 'Tuesday',
        'Wednesday' => 'Wednesday',
        'Thursday'  => 'Thursday',
        'Friday'    => 'Friday',
        'Saturday'  => 'Saturday'
    );

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('place_order_time', TimeType::class, array(
            'input'  => 'datetime',
            'widget' => 'choice',
        ));

        $builder->add('before_set_date', ChoiceType::class, array(
            'choices'  => self::DATE_OPTION,
        ));

        $builder->add('after_set_date', ChoiceType::class, array(
            'choices'  => self::DATE_OPTION,
        ));

        $builder->add('non_delivery_date', ChoiceType::class, array(
            'choices'  => self::DAY_WORD_OPTION,
            'expanded' => true,
            'multiple' => true,
            'choice_translation_domain' => false //not translate
        ));

        $builder->add('status', ChoiceType::class, array(
            'choices' => array('Enable' => '1', 'Disable' => '0'),
            'expanded'  => true,
            'multiple'  => false,
            'constraints' => array(
                new NotBlank(array('message' => 'Enter a status')))
        ));

        $builder->add('save_and_add', SubmitType::class, array('attr' => array('class' => 'btn btn-primary')));
        $builder->add('save_and_edit', SubmitType::class, array('attr' => array('class' => 'btn btn-primary')));
        $builder->add('save', SubmitType::class, array('attr' => array('class' => 'btn btn-primary')));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ProjectBundle\Entity\DeliveryMethod',
        ));
    }
}
