<?php
  namespace ProjectBundle\Form\Type;
  use Symfony\Component\Form\AbstractType;
  use Symfony\Component\Form\FormBuilderInterface;
  use Symfony\Component\Validator\Constraints\NotBlank;
  use Symfony\Component\Validator\Constraints\Email;

  use Symfony\Component\Form\Extension\Core\Type\SubmitType;
  use Symfony\Component\Form\Extension\Core\Type\TextType;
  use Symfony\Component\Form\Extension\Core\Type\HiddenType;
  use Symfony\Component\Form\Extension\Core\Type\TextareaType;

  class ContactType extends AbstractType{
    public function buildForm(FormBuilderInterface $builder, array $options){

      $builder->add('name',TextType::class, array(
        'required'=>false,
        'attr'=>array('class'=>'form-control'),
        'constraints' => array(new NotBlank(array('message' => 'error.name')))
      ));
      $builder->add('telephone_number',TextType::class, array(
        'required'=>false,
        'attr'=>array('class'=>'form-control'),
        'constraints' => array(new NotBlank(array('message' => 'error.phone')))
      ));

      $builder->add('email',TextType::class, array(
        'required'=>false,
        'attr'=>array('class'=>'form-control'),
        'constraints' => array(new NotBlank(array('message' => 'error.email')),
                               new Email(array('message' => 'error.email.wrong')))
      ));

      $builder->add('message',TextareaType::class, array(
        'required'=>false,
        'attr'=>array(
          'class'=>'form-control',
          'rows'=>'3'
        ),
        'constraints' => array(new NotBlank(array('message' => 'error.message')))
      ));

      $builder->add('save',SubmitType::class, array(
        'attr' => array('class' => 'btn btn-primary')
      ));

    }
    public function getName(){
      return 'contact';
    }
  }
?>
