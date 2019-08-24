<?php

namespace ProjectBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;

class ProfileType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$bd_this_year = date('Y');
		$bd_start_year = 1920;

		// remove username field.
		$builder->remove('username');  // we use email as the username
		$builder->remove('current_password');  // for remove current password

		$builder->add('first_name', TextType::class, array(
			'attr' => array(),
			'required' => true,
			'constraints' => array(
				new NotBlank(array('message' => 'fos_user.first_name.blank')))
		));

		$builder->add('last_name', TextType::class, array(
			'attr' => array(),
			'required' => true,
			'constraints' => array(
				new NotBlank(array('message' => 'fos_user.last_name.blank')))
		));

		$builder->add('company_name', TextType::class, array(
			'attr' => array(),
			'required' => false,
		));

		$builder->add('gender', ChoiceType::class, array(
			'required' => true,
			'choices' => array('member.personal.male' => 'M', 'member.personal.female' => 'F'),
			'expanded'  => true,
			'multiple'  => false,
			// 'constraints' => array(
			// 	new NotBlank(array('message' => 'fos_user.gender.blank')),)
		));

		$builder->add('birth_date', DateType::class, array(
			'required' => false,
			'widget' => 'choice',
			'years' => range($bd_this_year, $bd_start_year),
			//'format' => 'dd/MM/yyyy',
			'attr' => array('class'=>''),
			/*'constraints' => array( new NotBlank(array('message' => 'Can\'t be blank')) )*/
		));

		$builder->add('phone_number', TextType::class, array(
			'required' => false,
			'attr' => array('class'=>''),
			'constraints' => array(
				// new NotBlank(array('message' => 'error.phone')),
				new Regex(array('pattern' => "/^[0-9\-\(\)\/\+\#\s]*$/", 'message' => 'error.mobile.only.numbers')),
				new Regex(array('pattern' => "/\d+/", 'message' => 'error.mobile.fill.valid')),
				new Length(array('min' => 9, 'max' => 23, 'minMessage' => 'error.mobile.min9', 'maxMessage' => 'error.mobile.max23')))
		));

		$builder->add('update', SubmitType::class, array('attr' => array('class' => '')));
	}

    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\ProfileFormType';
    }

		public function getBlockPrefix()
    {
        return 'app_user_profile';
    }

		// For Symfony 2.x
    public function getName()
    {
        return $this->getBlockPrefix();
    }

}
