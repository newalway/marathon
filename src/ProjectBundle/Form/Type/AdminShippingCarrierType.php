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

use Symfony\Component\Validator\Constraints\NotBlank;


class AdminShippingCarrierType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('name', TextType::class, array(
            'attr' => array(
                'class'=>'form-control',
                'errortext'=>'Enter a name'
            ),
            'required' => true,
            'constraints' => array(
                new NotBlank(array('message' => 'Enter a name')),
            )
        ));

        $builder->add('tracking_url', TextareaType::class, array(
            'attr' => array('class'=>'form-control'),
            'required' => true,
            'constraints' => array(
                new NotBlank(array('message' => 'Enter a tracking url')),
            )
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
            'data_class' => 'ProjectBundle\Entity\ShippingCarrier',
            'name'       => 'shipping_carrier',
        ));
    }
}
