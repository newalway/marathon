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
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class AdminUserType extends AbstractType
{
    protected $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /*
    //pass option entity_manager to form
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('entity_manager');
    }
    */

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $this->tokenStorage->getToken()->getUser();
        $loggedin_user_roles = $user->getRoles();

        $admin_roles = array('ADMIN' => 'ROLE_ADMIN', 'EDITOR' => 'ROLE_EDITOR');
        $super_admin_roles = array('SUPER_ADMIN' => 'ROLE_SUPER_ADMIN', 'ADMIN' => 'ROLE_ADMIN', 'EDITOR' => 'ROLE_EDITOR');
        if ( in_array("ROLE_SUPER_ADMIN", $options['data']->getRoles()) || in_array("ROLE_SUPER_ADMIN", $loggedin_user_roles) ){
          $roles_options = $super_admin_roles;
        }else{
          $roles_options = $admin_roles;
        }

        //get option entity_manager
        //$em = $options['entity_manager'];


        $builder->add('email', TextType::class, array(
                     'attr' => array('validate'=>'email',
                                     'placeholder'=>'Email',
                                     'class'=>'form-control',
                                     'errortext'=>'Please enter a valid email address in the form username@domain.com'),
                     'required' => true,
                     'constraints' => array(
                         new NotBlank(array('message' => 'Please enter a valid email address in the form username@domain.com')),
                         new Email(array('message' => 'The email address might be wrong')))
                   ));

      $builder->add('plainPassword', RepeatedType::class, array(
                      'attr' => array('class' => 'preview-able'),
                      'required' => true,
                      'type' => PasswordType::class,
                      'options' => array('translation_domain' => 'FOSUserBundle'),
                      'first_options' => array('label' => 'form.password'),
                      'second_options' => array('label' => 'form.password_confirmation'),
                      'invalid_message' => 'fos_user.password.mismatch'
      ));

      $builder->add('roles', ChoiceType::class,array(
                    'choices' => $roles_options,
                    'expanded'  => true,
                    'multiple'  => true,
                    'constraints' => array(
                        new NotBlank(array('message' => 'Please enter roles')))
      ));

      $builder->add('save_and_add', SubmitType::class, array('attr' => array('class' => 'btn btn-primary')));
      $builder->add('save', SubmitType::class, array('attr' => array('class' => 'btn btn-primary')));
    }
}
