<?php
  namespace ProjectBundle\Form\Type;
  use Symfony\Component\Form\AbstractType;
  use Symfony\Component\Form\FormBuilderInterface;
  use Symfony\Component\Validator\Constraints\NotBlank;
  use Symfony\Component\Validator\Constraints\Email;

  use Symfony\Component\Form\Extension\Core\Type\SubmitType;
  use Symfony\Component\Form\Extension\Core\Type\PasswordType;

  class MemberGenerateTokenType extends AbstractType{
    public function buildForm(FormBuilderInterface $builder, array $options){

      $builder->add('current_password', PasswordType::class, array(
                      'required' => false,
                      'attr' => array('class'=>'preview-able'),
                      'mapped' => false,
                      'constraints' => array(
                        new NotBlank(array('message' => 'error.password.blank')),
                      )
      ));

      $builder->add('save', SubmitType::class, array(
        'attr' => array('class' => 'btn btn-primary')
      ));

    }
    public function getName(){
      return 'member_generate_token';
    }
  }
?>
