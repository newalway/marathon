<?php

namespace ProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use ProjectBundle\Entity\User;
use ProjectBundle\Entity\CustomerOrder;
use ProjectBundle\Entity\DeliveryAddress;

use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Cookie;

use ProjectBundle\Form\Type\B2bRegisterType;
use ProjectBundle\Form\Type\AdminGenerateTokenType;
use ProjectBundle\Form\Type\AdminChangePasswordType;

use JMS\SecurityExtraBundle\Annotation\Secure;
use GuzzleHttp\Client;

class AdminMemberB2BController extends Controller
{
    const ROUTER_CONTROLLER = 'AdminMemberB2B';
    const ROUTER_INDEX = 'admin_member_b2b';
    const ROUTER_ADD = self::ROUTER_INDEX.'_new';
    const ROUTER_EDIT = self::ROUTER_INDEX.'_edit';

    protected function prepare_query_data($request) {
        $arr_data = $request->query->get('search_member');
        $arr_query_data['q'] = trim($arr_data['q']);
        return $arr_query_data;
    }

    protected function getQuerySearchUser($arr_query_data)
    {
        $repository = $this->getDoctrine()->getRepository(User::class);
        $query = $repository->getAllMemberB2BByData($arr_query_data);
        return $query;
    }

    /**
    * @Secure(roles="ROLE_EDITOR")
    */
    public function indexAction(Request $request)
    {
        $util = $this->container->get('utilities');
        try {
            $acctoken = $util->getAccessToken();
        } catch(\Exception $e) {
            return $this->redirectToRoute('admin_user_generate_token');
        }

        $arr_query_data = $this->prepare_query_data($request);
        $query = $this->getQuerySearchUser($arr_query_data);
        $paginated = $util->setPaginatedOnPagerfanta($query);
        $util->setBackToUrl();
        return $this->render('ProjectBundle:'.self::ROUTER_CONTROLLER.':index.html.twig', array(
            'paginated' =>$paginated,
            'acctoken' => $acctoken,
        ));
    }

    /**
    * @Secure(roles="ROLE_EDITOR")
    */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $userManager = $this->container->get('fos_user.user_manager');
        $user = $userManager->createUser();
        //pass option entity_manager to form
        $form = $this->createForm(B2bRegisterType::class, $user, array('csrf_protection' => true,'allow_extra_fields'=>true, /* 'entity_manager' => $em */));
        return $this->render('ProjectBundle:'.self::ROUTER_CONTROLLER.':new.html.twig', array('form'=>$form->createView()));
    }

    /**
    * @Secure(roles="ROLE_EDITOR")
    */
    public function createAction(Request $request)
    {
        $util = $this->container->get('utilities');
        $em = $this->getDoctrine()->getManager();
        $userManager = $this->container->get('fos_user.user_manager');
        $user = $userManager->createUser();

        $form = $this->createForm(B2bRegisterType::class, $user, array('csrf_protection' => true,'allow_extra_fields'=>true));
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
            $roles = array('ROLE_CLIENT');

            $user->setUsername($email);
            $user->setUsernameCanonical($email);
            $user->setRoles($roles);
            $user->setIsSetPassword(1);
            $user->setEnabled(1);
            $userManager->updateUser($user, true);

            //set oauth token
            $scope = $this->container->getparameter('access_token_client_scope');
            $util->setAccessToken($email, $plainpass, $scope);

            $util->setCreateNotice();
            $redirect_uri = $util->getRedirectUriSaveBtn($form, $data, self::ROUTER_INDEX, self::ROUTER_ADD, false);
            return $this->redirect($redirect_uri);

        }
        return $this->render('ProjectBundle:'.self::ROUTER_CONTROLLER.':new.html.twig', array('form'=>$form->createView()));
    }

    /**
    * @Secure(roles="ROLE_EDITOR")
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
        if( (in_array("ROLE_SUPER_ADMIN",$user_roles)) ){
            throw $this->createAccessDeniedException('You cannot access this page!');
        }
        $form = $this->createForm(B2bRegisterType::class, $user, array('csrf_protection' => true, 'allow_extra_fields'=>true));
        return $this->render('ProjectBundle:'.self::ROUTER_CONTROLLER.':edit.html.twig', array('form'=>$form->createview()));
    }

    /**
    * @Secure(roles="ROLE_EDITOR")
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
        if( (in_array("ROLE_SUPER_ADMIN",$user_roles)) ){
            throw $this->createAccessDeniedException('You cannot access this page!');
        }

        $userManager = $this->container->get('fos_user.user_manager');
        $form = $this->createForm(B2bRegisterType::class, $user, array('csrf_protection' => true, 'allow_extra_fields'=>true));
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

            if($current_email != $email){
                // change email
                // delete token
                $util->deleteAccessAndRefreshToken($data);
                $data->setUsername($email);
                $data->setUsernameCanonical($email);
            }

            $userManager->updateUser($data, true);
            $id = $data->getId();
            if(($logged_in_user_id==$id) && ($current_email!=$email)){
                // change email, remove token
                $session = $request->getSession();
                $session->remove('token');
            }

            $util->setUpdateNotice();
            $redirect_uri = $util->getRedirectUriSaveBtn($form, $data, self::ROUTER_INDEX, self::ROUTER_ADD, false);
            return $this->redirect($redirect_uri);
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
        if( in_array("ROLE_SUPER_ADMIN", $roles)){
            throw $this->createAccessDeniedException('You don\'t have permission to access');
        }
        $form = $this->createForm(AdminChangePasswordType::class, $user, array('csrf_protection' => true,'allow_extra_fields'=>true));
        return $this->render('ProjectBundle:'.self::ROUTER_CONTROLLER.':change_password.html.twig', array(
            'form'=>$form->createview(),
            'user'=>$user
        ));
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
        if( in_array("ROLE_SUPER_ADMIN", $roles)){
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

            $userManager->updateUser($user, true);

            // delete token
    		$util->deleteAccessAndRefreshToken($user);

            // $response= $util->call(
            //     'PUT',
            //     self::ROUTER_PREFIX.'/'.$id.'/changepwd',
            //     $acctoken,
            //     json_encode($request->request->all())
            // );
            //
            // $status = json_decode($response->getBody());
            // if($status->success){

                // update token
                //set oauth token
                $scope = $this->container->getparameter('access_token_client_scope');
                $util->setAccessToken($email, $plainpass, $scope);

                $this->get('session')->getFlashBag()->add('notice', 'Your changes were saved');
                return $this->redirect($util->getBackToUrl(self::ROUTER_INDEX));

            // }
        }

        return $this->render('ProjectBundle:'.self::ROUTER_CONTROLLER.':change_password.html.twig', array(
            'form'=>$form->createview(),
            'user'=>$user
        ));
    }

    /**
    * @Secure(roles="ROLE_EDITOR")
    */
    public function viewAction(Request $request)
    {
        $util = $this->container->get('utilities');
        $util->setCkAuthorized();
        $user_id = $request->get('id');
        $em = $this->getDoctrine()->getManager();
        $repository = $this->getDoctrine()->getRepository(User::class);
        $member = $repository->getMemberB2BById($user_id)->getQuery()->getSingleResult();

        $order = $em->getRepository(CustomerOrder::Class)->findCustomerOrderHasItemByUser($member)->getQuery();

        $default_shipping_address = $em->getRepository(DeliveryAddress::Class)->findDefaultShippingAddressById($member)->getQuery()->getOneOrNullResult();
        $default_billing_address = $em->getRepository(DeliveryAddress::Class)->findDefaultBillingAddressById($member)->getQuery()->getOneOrNullResult();

        $paginated = $util->setPaginatedOnPagerfanta($order);

        return $this->render('ProjectBundle:'.self::ROUTER_CONTROLLER.':view.html.twig', array(
            'member' =>$member,
            'default_shipping_address' => $default_shipping_address,
            'default_billing_address' => $default_billing_address,
            'paginated' => $paginated
        ));
    }

    /**
    * @Secure(roles="ROLE_EDITOR")
    */
    public function group_export_excelAction(request $request)
    {
        $util = $this->container->get('utilities');
        $arr_query_data = $this->prepare_query_data($request);
        $em = $this->getDoctrine()->getManager();
        $members = $em->getRepository(User::class)->getAllMemberB2BByData($arr_query_data)->getQuery()->getResult();
        // $members = $this->getAllMemberByData($arr_query_data)->getQuery()->getResult();

        //$members = $this->getQuerySearchUser($arr_query_data)->find();

        $export_excel = $this->container->get('exportexcel');
        $export_excel->getExcelObjectMember();
        $export_excel->exportMemberData($members);


        $name_post_fix = date('dMy');
        $file_name = 'Member_B2B_'.$name_post_fix.'.xls';
        $export_excel->saveOutputExcel($file_name);
        $response =  $export_excel->streamOutputExcel($file_name);

        //headers
        // $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        // $response->headers->set('Content-Disposition', 'attachment;filename="'.$file_name.'"');
        // $response->headers->set('Pragma', 'public');
        // $response->headers->set('Cache-Control', 'maxage=1');
        return $response;
    }

    /**
    * @Secure(roles="ROLE_EDITOR")
    */
    public function group_deleteAction(Request $request)
    {
        $util = $this->container->get('utilities');
        $em = $this->getDoctrine()->getManager();
        $data_ids = $request->get('data_ids');
        if ($data_ids) {
            $flg_del = false;
            foreach ($data_ids as $data_id) {
                $data = $em->getRepository(User::class)->find($data_id);
                $roles = $data->getRoles();

                // allow super_admin update role_super_admin
                if( in_array("ROLE_SUPER_ADMIN", $roles) ){
                    throw $this->createAccessDeniedException('You cannot access this page!');
                }

                if ($data) {
                    try {
                        // delete token
                        $util->deleteAccessAndRefreshToken($data);

                        // delete user
                        $em->remove($data);
                        $em->flush();
                        $flg_del = true;
                    } catch(\Doctrine\DBAL\DBALException $e) {
                        // print_r($e->getMessage()); exit;
                        $util->setCustomeFlashMessage('warning', $msg="Can't delete ".$data->getEmail());
                    }
                }
            }
            if ($flg_del) {
                $util->setRemoveNotice();
            }
        }
        return $this->redirect($util->getBackToUrl(self::ROUTER_INDEX));
    }

    /**
    * @Secure(roles="ROLE_EDITOR")
    */
    public function group_enableAction(Request $request)
    {
        $util = $this->container->get('utilities');
        $em = $this->getDoctrine()->getManager();
        $data_ids = $request->get('data_ids');
        if ($data_ids) {
            $flg_pub = false;
            foreach ($data_ids as $data_id) {
                $data = $em->getRepository(User::class)->find($data_id);
                $roles = $data->getRoles();

                // allow super_admin update role_super_admin
                if( in_array("ROLE_SUPER_ADMIN", $roles) ){
                    throw $this->createAccessDeniedException('You cannot access this page!');
                }

                if ($data) {
                    $data->setEnabled(1);
                    $flg_pub = true;
                }
            }
            try {
                $em->flush();
            } catch(\Exception $e) {
                $util->setCustomeFlashMessage('warning', $msg="Can't enable ");
            }
            if($flg_pub){
                $util->setCustomeFlashMessage('notice', $msg="Published ");
            }
        }
        return $this->redirect($util->getBackToUrl(self::ROUTER_INDEX));
    }

    /**
    * @Secure(roles="ROLE_EDITOR")
    */
    public function group_disableAction(Request $request)
    {
        $util = $this->container->get('utilities');
        $em = $this->getDoctrine()->getManager();
        $data_ids = $request->get('data_ids');
        if($data_ids){
            $flg_pub = false;
            foreach($data_ids as $data_id){
                $data = $em->getRepository(User::class)->find($data_id);
                $roles = $data->getRoles();

                // allow super_admin update role_super_admin
                if( in_array("ROLE_SUPER_ADMIN", $roles) ){
                    throw $this->createAccessDeniedException('You cannot access this page!');
                }

                if($data){
                    $data->setEnabled(0);
                    $flg_pub = true;
                }
            }
            try {
                $em->flush();
            } catch(\Exception $e) {
                $util->setCustomeFlashMessage('warning', $msg="Can't disable ");
            }
            if ($flg_pub) {
                $util->setCustomeFlashMessage('notice', $msg="Unpublished ");
            }
        }
        return $this->redirect($util->getBackToUrl(self::ROUTER_INDEX));
    }

}
