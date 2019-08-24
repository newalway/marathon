<?php
  namespace ProjectBundle\Form\Type;
  use Symfony\Component\Form\AbstractType;
  use Symfony\Component\Form\FormBuilderInterface;
  use Symfony\Component\Validator\Constraints\NotBlank;
  use Symfony\Component\Validator\Constraints\Email;

  use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
  use Symfony\Component\Form\Extension\Core\Type\PasswordType;
  use Symfony\Component\Form\Extension\Core\Type\SubmitType;

  class AdminChangePasswordType extends AbstractType
  {
    public function buildForm(FormBuilderInterface $builder, array $options){

      $builder->add('plainPassword', RepeatedType::class, array(
                      'attr' => array('class' => 'preview-able'),
                      'required' => true,
                      'type' => PasswordType::class,
                      'options' => array('translation_domain' => 'FOSUserBundle'),
                      'first_options' => array('label' => 'form.password'),
                      'second_options' => array('label' => 'form.password_confirmation'),
                      'invalid_message' => 'fos_user.password.mismatch'
      ));

      $builder->add('save', SubmitType::class, array('attr' => array('class' => 'btn btn-primary')));
    }
    public function getName(){
      return 'admin_change_password';
    }
  }
?>
