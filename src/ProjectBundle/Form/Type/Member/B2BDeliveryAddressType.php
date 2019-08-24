<?php

namespace ProjectBundle\Form\Type\Member;

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
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;

use Doctrine\ORM\EntityRepository;
use ProjectBundle\Entity\CountryCode;

use Ivory\CKEditorBundle\Form\Type\CKEditorType;

class B2BDeliveryAddressType extends DeliveryAddressType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add('taxpayer_id', TextType::class, array(
                  'required' => false,
                  'attr' => array('class'=>'form-control'),
                  'constraints' => array(
                      new Regex(array('pattern' => "/^[\d]{13}$/", 'message' => 'error.taxid.invalid')),
                  )
        ));

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ProjectBundle\Entity\DeliveryAddress'
        ));
    }

}
