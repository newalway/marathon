<?php

namespace ProjectBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class B2bRegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
      $bd_this_year = date('Y');
      $bd_start_year = 1920;

      // remove username field.
      $builder->remove('username');  // we use email as the username

      $builder->add('first_name', TextType::class, array(
                    'attr' => array('class'=>'form-control'),
                    'required' => true,
                    'constraints' => array(
                        new NotBlank(array('message' => 'error.firstname')))
      ));

      $builder->add('last_name', TextType::class, array(
                    'attr' => array('class'=>'form-control'),
                    'required' => true,
                    'constraints' => array(
                        new NotBlank(array('message' => 'error.lastname')))
      ));

      $builder->add('company_name', TextType::class, array(
                    'attr' => array('class'=>'form-control'),
                    'required' => true,
                    'constraints' => array(
                        new NotBlank(array('message' => 'error.companyname')))
      ));

      $builder->add('email', TextType::class, array(
                    'attr' => array('class'=>'form-control'),
                    'required' => true,
                    'constraints' => array(
                        new NotBlank(array('message' => 'error.email')),
                        new Email(array('message' => 'error.email.wrong')))
      ));

      $builder->add('phone_number', TextType::class, array(
                    'required' => false,
                    'attr' => array('class'=>'form-control'),
                    'constraints' => array(
                        new NotBlank(array('message' => 'error.phone')),
                        new Regex(array('pattern' => "/^[0-9\-\(\)\/\+\#\s]*$/", 'message' => 'error.mobile.only.numbers')),
                        new Regex(array('pattern' => "/\d+/", 'message' => 'error.mobile.fill.valid')),
                        new Length(array('min' => 9, 'max' => 23, 'minMessage' => 'error.mobile.min9', 'maxMessage' => 'error.mobile.max23')))
      ));

      if(!$options['data']->getId()){
          // disable the repeated password
          $builder->add('plainPassword', PasswordType::class, array(
                        'attr' => array('class'=>'form-control'),
                        'label' => 'form.password',
                        'translation_domain' => 'FOSUserBundle',
                        'constraints' => array(
                            new NotBlank(array('message' => 'error.password')))
                        //'constraints' => new UserPassword($constraintsOptions),
          ));
      }

      /*
      $builder->add('enabled', ChoiceType::class, array(
                      'choices' => array('Active' => '1', 'Inactive' => '0'),
                      'expanded' => true,
                      'multiple' => false,
                      'attr' => array('class' => 'form-control-static'),
      ));
      */

      $builder->add('save_and_add', SubmitType::class, array('attr' => array('class' => 'btn btn-primary')));
      $builder->add('save', SubmitType::class, array('attr' => array('class' => 'btn btn-primary')));

    }

    // protected $options = array(
    //     'data_class' => 'ProjectBundle\Entity\User',
    //     'name'       => 'user',
    // );

    public function getParent()
	{
		return 'FOS\UserBundle\Form\Type\RegistrationFormType';
	}

	public function getBlockPrefix()
	{
		return 'b2b_registration';
	}

		// For Symfony 2.x
    public function getName()
    {
        return $this->getBlockPrefix();
    }

}
