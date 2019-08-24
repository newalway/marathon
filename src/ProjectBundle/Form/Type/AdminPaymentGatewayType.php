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

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class AdminPaymentGatewayType extends AbstractType
{
    const PAYMENT_GATEWAY_OPTION = array(
        // 'Cash on delivery'              => 'COD',
        'Bank transfer'                 => 'BT',
        'Credit card'                   => 'CRDT',
        'Request for Quotations (B2B)'  => 'RFQ'
    );

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('gateway', ChoiceType::class, array(
            'choices'  => self::PAYMENT_GATEWAY_OPTION,
            'expanded' => true,
            'multiple' => true,
            'choice_translation_domain' => false //not translate
        ));

        $builder->add('save_and_add', SubmitType::class, array('attr' => array('class' => 'btn btn-primary')));
        $builder->add('save_and_edit', SubmitType::class, array('attr' => array('class' => 'btn btn-primary')));
        $builder->add('save', SubmitType::class, array('attr' => array('class' => 'btn btn-primary')));
    }
}
