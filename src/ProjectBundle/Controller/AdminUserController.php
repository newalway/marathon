<?php

namespace ProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Doctrine\ORM\EntityManagerInterface;
use ProjectBundle\Entity\User;

use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Cookie;

use ProjectBundle\Form\Type\AdminUserType;
use ProjectBundle\Form\Type\AdminGenerateTokenType;
use ProjectBundle\Form\Type\AdminChangePasswordType;

use JMS\SecurityExtraBundle\Annotation\Secure;
use GuzzleHttp\Client;

class AdminUserController extends Controller
{
    const ACTION_NAME = 'Users';
    const ROUTER_INDEX = 'admin_user';
    const ROUTER_ADD = self::ROUTER_INDEX.'_new';
    const ROUTER_EDIT = self::ROUTER_INDEX.'_edit';
    const ROUTER_PREFIX = 'users';
    const ROUTER_CONTROLLER = 'AdminUser';

    /**
    * @Secure(roles="ROLE_ADMIN")
    */
    public function indexAction(Request $request)
    {
        $util = $this->container->get('utilities');
        $session = $request->getSession();
        try {
            $acctoken = $util->getAccessToken();
        } catch(\Exception $e) {
            return $this->redirectToRoute('admin_user_generate_token');
        }

        $repository = $this->getDoctrine()->getRepository(User::class);
        //QueryBuilder
        $datatable = $repository->getAllAdminUser()->getQuery()->getResult();

        //DQL
        //$datatable = $repository->getAllAdminUser();

        $util->setBackToUrl();
        return $this->render('ProjectBundle:'.self::ROUTER_CONTROLLER.':index.html.twig', array('datatable'=>$datatable));
    }

    /**
    * @Secure(roles="ROLE_ADMIN")
    */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $userManager = $this->container->get('fos_user.user_manager');
        $user = $userManager->createUser();
        //pass option entity_manager to form
        $form = $this->createForm(AdminUserType::class, $user, array('csrf_protection' => true,'allow_extra_fields'=>true, /* 'entity_manager' => $em */));
        return $this->render('ProjectBundle:'.self::ROUTER_CONTROLLER.':new.html.twig', array('form'=>$form->createView()));
    }

    /**
    * @Secure(roles="ROLE_ADMIN")
    */
    public function createAction(Request $request)
    {
        $util = $this->container->get('utilities');
        $em = $this->getDoctrine()->getManager();
        $userManager = $this->container->get('fos_user.user_manager');
        $user = $userManager->createUser();

        $form = $this->createForm(AdminUserType::class, $user, array('csrf_protection' => true,'allow_extra_fields'=>true));
        $form->handleRequest($request);

        $data = $form->getData();
        $email = $data->getEmail();
        $plainpass = $data->getPlainPassword();
        $chk_email = $em->getRepository(User::class)->findByEmailCanonical($email);
        if(count($chk_email)>0){
            $form->get('email')->addError(new FormError('The email is already used'));//already exists email
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $data->getEmail();
            $plainpass = $data->getPlainPassword();
            $acctoken = $util->getAccessToken();

            $response= $util->call(
                'POST',
                self::ROUTER_PREFIX,
                $acctoken,
                json_encode($request->request->all())
            );

            $status = json_decode($response->getBody());
            if($status->success){
                //set oauth token
                $scope = $this->container->getparameter('access_token_admin_scope');
                $util->setAccessToken($email, $plainpass, $scope);

                $util->setCreateNotice();
                $redirect_uri = $util->getRedirectUriSaveBtn($form, $data, self::ROUTER_INDEX, self::ROUTER_ADD, false);
                return $this->redirect($redirect_uri);
            }
        }
        return $this->render('ProjectBundle:'.self::ROUTER_CONTROLLER.':new.html.twig', array('form'=>$form->createView()));
    }

    /**
    * @Secure(roles="ROLE_ADMIN")
    */
    public function editAction(Request $request)
    {
        $util = $this->container->get('utilities');
        $user = $this->getDoctrine()->getRepository(User::class)->find($request->get('id'));
        if(!$user){
            throw $this->createNotFoundException('This User not found in the database');
        }
        $user_roles = $user->getRoles();
        $user_active = $this->get('security.token_storage')->getToken()->getUser();
        $user_roles_active = $user_active->getRoles();
        if( (in_array("ROLE_SUPER_ADMIN",$user_roles)) && (in_array("ROLE_SUPER_ADMIN",$user_roles_active)==0) ){
            throw $this->createAccessDeniedException('You cannot access this page!');
        }
        $form = $this->createForm(AdminUserType::class, $user, array('csrf_protection' => true, 'allow_extra_fields'=>true));
        return $this->render('ProjectBundle:'.self::ROUTER_CONTROLLER.':edit.html.twig', array('form'=>$form->createview()));
    }

    /**
    * @Secure(roles="ROLE_ADMIN")
    */
    public function updateAction(Request $request)
    {
        $logged_in_user = $this->getUser();
        $logged_in_user_id = $logged_in_user->getId();

        $util = $this->container->get('utilities');
        $em = $this->getDoctrine()->getManager();
        $user = $this->getDoctrine()->getRepository(User::class)->find($request->get('id'));
        if(!$user){
            throw $this->createNotFoundException('This User not found in the database');
        }
        $current_email = $user->getEmail();
        $user_roles = $user->getRoles();
        $user_active = $this->get('security.token_storage')->getToken()->getUser();
        $user_roles_active = $user_active->getRoles();
        if( (in_array("ROLE_SUPER_ADMIN",$user_roles)) && (in_array("ROLE_SUPER_ADMIN",$user_roles_active)==0) ){
            throw $this->createAccessDeniedException('You cannot access this page!');
        }

        $userManager = $this->container->get('fos_user.user_manager');
        $form = $this->createForm(AdminUserType::class, $user, array('csrf_protection' => true, 'allow_extra_fields'=>true));
        $form->handleRequest($request);

        $data = $form->getData();
        $email = $data->getEmail();
        $qb = $em->getRepository(User::class)->createQueryBuilder('u');
        $qb->where('u.id != :user_id')
        ->andWhere('u.email = :email')
        ->setParameters(array('user_id' => $user->getId(), 'email' => $email));
        $chk_email = $qb->getQuery()->getResult();
        if(count($chk_email)>0){
            $form->get('email')->addError(new FormError('The email is already used'));//already exists email
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $id = $data->getId();
            $acctoken = $util->getAccessToken();
            $response= $util->call(
                'PUT',
                self::ROUTER_PREFIX.'/'.$id,
                $acctoken,
                json_encode($request->request->all())
            );
            $status = json_decode($response->getBody());
            if($status->success){

                if(($logged_in_user_id==$id) && ($current_email!=$email)){
                    // change email, remove token
                    $session = $request->getSession();
                    $session->remove('token');
                }

                $util->setUpdateNotice();
                $redirect_uri = $util->getRedirectUriSaveBtn($form, $data, self::ROUTER_INDEX, self::ROUTER_ADD, false);
                return $this->redirect($redirect_uri);
            }
        }
        return $this->render('ProjectBundle:'.self::ROUTER_CONTROLLER.':edit.html.twig', array('form'=>$form->createview()));
    }

    /**
    * @Secure(roles="ROLE_ADMIN")
    */
    public function change_passwordAction(Request $request)
    {
        $util = $this->container->get('utilities');
        $user = $this->getDoctrine()->getRepository(User::class)->find($request->get('id'));
        if(!$user){
            throw $this->createNotFoundException('This User not found in the database');
        }
        $roles = $user->getRoles();
        $user_active = $this->get('security.token_storage')->getToken()->getUser();
        $roles_active = $user_active->getRoles();
        if(!in_array("ROLE_SUPER_ADMIN",$roles_active) && in_array("ROLE_SUPER_ADMIN",$roles)){
            throw $this->createAccessDeniedException('You don\'t have permission to access');
        }
        $form = $this->createForm(AdminChangePasswordType::class, $user, array('csrf_protection' => true,'allow_extra_fields'=>true));
        return $this->render('ProjectBundle:'.self::ROUTER_CONTROLLER.':change_password.html.twig', array('form'=>$form->createview()));
    }

    /**
    * @Secure(roles="ROLE_ADMIN")
    */
    public function change_password_updateAction(Request $request)
    {
        $util = $this->container->get('utilities');
        $user = $this->getDoctrine()->getRepository(User::class)->find($request->get('id'));
        if(!$user){
            throw $this->createNotFoundException('This User not found in the database');
        }
        $roles = $user->getRoles();
        $user_active = $this->get('security.token_storage')->getToken()->getUser();
        $roles_active = $user_active->getRoles();
        if(!in_array("ROLE_SUPER_ADMIN",$roles_active) && in_array("ROLE_SUPER_ADMIN",$roles)){
            throw $this->createAccessDeniedException('You don\'t have permission to access');
        }

        $userManager = $this->container->get('fos_user.user_manager');
        $form = $this->createForm(AdminChangePasswordType::class, $user, array('csrf_protection' => true,'allow_extra_fields'=>true));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $email = $data->getEmail();
            $plainpass = $data->getPlainPassword();
            $id = $data->getId();

            $acctoken = $util->getAccessToken();

            $response= $util->call(
                'PUT',
                self::ROUTER_PREFIX.'/'.$id.'/changepwd',
                $acctoken,
                json_encode($request->request->all())
            );
            $status = json_decode($response->getBody());
            if($status->success){
                // update token
                //set oauth token
                $scope = $this->container->getparameter('access_token_admin_scope');
                $util->setAccessToken($email, $plainpass, $scope);

                //set new session if change own password
                $user_active = $this->get('security.token_storage')->getToken()->getUser();
                if($user_active->getId() == $id){
                    $session = $request->getSession();
                    $session->remove('token');
                    $util->getAccessToken();
                }

                $this->get('session')->getFlashBag()->add('notice', 'Your changes were saved');
                return $this->redirect($util->getBackToUrl(self::ROUTER_INDEX));
            }
        }

        return $this->render('ProjectBundle:'.self::ROUTER_CONTROLLER.':change_password.html.twig', array('form'=>$form->createview()));
    }

    /**
    * @Secure(roles="ROLE_ADMIN")
    */
    public function deleteAction(Request $request)
    {
        $util = $this->container->get('utilities');
        $data = $this->getDoctrine()->getRepository(User::class)->find($request->get('id'));
        if (!$data) {
            throw $this->createNotFoundException('This data doesn\'t exist');
        }
        $roles = $data->getRoles();
        $user_active = $this->get('security.token_storage')->getToken()->getUser();
        $roles_active = $user_active->getRoles();
        if(!in_array("ROLE_SUPER_ADMIN",$roles_active) && in_array("ROLE_SUPER_ADMIN",$roles)){
            throw $this->createAccessDeniedException('You don\'t have permission to access');
        }

        $acctoken = $util->getAccessToken();
        $response= $util->call(
            'DELETE',
            self::ROUTER_PREFIX.'/'.$request->get('id'),
            $acctoken,
            json_encode($request->request->all())
        );
        $status = json_decode($response->getBody());
        if($status->success){
            $this->get('session')->getFlashBag()->add('notice', 'Data deleted');
        }
        return $this->redirect($util->getBackToUrl(self::ROUTER_INDEX));
    }

    /**
    * @Secure(roles="ROLE_ADMIN")
    */
    public function group_deleteAction(Request $request)
    {
        $util = $this->container->get('utilities');
        $acctoken = $util->getAccessToken();
        $response= $util->call(
            'DELETE',
            self::ROUTER_PREFIX.'/groups/deletes',
            $acctoken,
            json_encode($request->get('data_ids'))
        );
        $status = json_decode($response->getBody());
        if($status->success){
            $this->get('session')->getFlashBag()->add('notice', 'Deleted ');
        }
        return $this->redirect($util->getBackToUrl(self::ROUTER_INDEX));
    }

    /**
    * @Secure(roles="ROLE_ADMIN")
    */
    public function group_enableAction(Request $request)
    {
        $util = $this->container->get('utilities');
        $acctoken = $util->getAccessToken();
        $response= $util->call(
            'POST',
            self::ROUTER_PREFIX.'/groups/enables',
            $acctoken,
            json_encode($request->get('data_ids'))
        );
        $status = json_decode($response->getBody());
        if($status->success){
            $this->get('session')->getFlashBag()->add('notice', 'Active ');
        }
        return $this->redirect($util->getBackToUrl(self::ROUTER_INDEX));
    }

    /**
    * @Secure(roles="ROLE_ADMIN")
    */
    public function group_disableAction(Request $request)
    {
        $util = $this->container->get('utilities');
        $acctoken = $util->getAccessToken();
        $response= $util->call(
            'POST',
            self::ROUTER_PREFIX.'/groups/disables',
            $acctoken,
            json_encode($request->get('data_ids'))
        );
        $status = json_decode($response->getBody());
        if($status->success){
            $this->get('session')->getFlashBag()->add('notice', 'Inactive');
        }
        return $this->redirect($util->getBackToUrl(self::ROUTER_INDEX));
    }

    /**
    * @Secure(roles="ROLE_USER")
    */
    public function generate_tokenAction(Request $request)
    {
        $util = $this->container->get('utilities');
        $user = $this->get('security.token_storage')->getToken()->getUser();
        if(!$user){
            throw $this->createNotFoundException('This User not found in the database');
        }
        $form = $this->createForm(AdminGenerateTokenType::class, $user, array('csrf_protection' => true,'allow_extra_fields'=>true));
        $util->setBackToUrl();
        return $this->render('ProjectBundle:'.self::ROUTER_CONTROLLER.':generate_token.html.twig', array('form'=>$form->createview()));
    }

    /**
    * @Secure(roles="ROLE_USER")
    */
    public function generate_token_updateAction(Request $request)
    {
        $util = $this->container->get('utilities');
        $user = $this->get('security.token_storage')->getToken()->getUser();
        if(!$user){
            throw $this->createNotFoundException('This User not found in the database');
        }

        $form = $this->createForm(AdminGenerateTokenType::class, $user, array('csrf_protection' => true,'allow_extra_fields'=>true));
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $arr_data =  $request->get('admin_generate_token');
            $current_passowrd = $arr_data['current_password'];
            if(!$current_passowrd){
                $form->get('current_password')->addError(new FormError('Required'));
            }

            if($form->isValid()){

                $userManager = $this->container->get('fos_user.user_manager');
                $data = $form->getData();

                $encoder = $this->container->get('security.encoder_factory')->getEncoder($user);
                $pwd_encoded = $encoder->encodePassword($current_passowrd, $user->getSalt());
                if($user->getPassword() != $pwd_encoded) {
                    $form->get('current_password')->addError(new FormError('Incorrect Password'));
                }

                if($form->isValid()){
                    $email = $user->getEmail();
                    //update token
                    //set oauth token
                    $scope = $this->container->getparameter('access_token_admin_scope');
                    $util->setAccessToken($email, $current_passowrd, $scope);
                    //reset session access token
                    $token = $util->getAccessTokenFromDB();

                    return $this->redirectToRoute('admin');
                }
            }
        }
        return $this->render('ProjectBundle:'.self::ROUTER_CONTROLLER.':generate_token.html.twig', array('form'=>$form->createview()));
    }
}
