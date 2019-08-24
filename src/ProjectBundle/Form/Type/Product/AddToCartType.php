<?php
  namespace ProjectBundle\Form\Type\Product;

  use Symfony\Component\Form\AbstractType;
  use Symfony\Component\Form\FormBuilderInterface;
  use Symfony\Component\Validator\Constraints\NotBlank;
  use Symfony\Component\Validator\Constraints\Email;
  use Symfony\Component\OptionsResolver\OptionsResolver;

  use Symfony\Component\Form\Extension\Core\Type\SubmitType;

  class AddToCartType extends AbstractType
  {
    public function buildForm(FormBuilderInterface $builder, array $options){

      $builder->add('save', SubmitType::class, array(
        'attr' => array('class' => 'btn btn-primary')
      ));

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'allow_extra_fields' => true,
        ));
    }

  }
?>
