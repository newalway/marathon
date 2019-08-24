<?php

namespace ProjectBundle\Controller\Api;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Doctrine\ORM\EntityManagerInterface;
use ProjectBundle\Entity\User;

use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use ProjectBundle\Form\Type\AdminUserType;
use ProjectBundle\Form\Type\AdminClientType;
use ProjectBundle\Form\Type\AdminChangePasswordType;

use FOS\RestBundle\Controller\Annotations;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class ApiUserController extends FOSRestController
{
	/**
   * List all public users.
   *
	 * @ApiDoc(
   *  resource=true,
   *  description="List all public users",
	 *   statusCodes = {
   *     200 = "Returned when successful"
   *   }
   * )
	 *
   * @Annotations\View()
   *
   * @param Request               $request      the request object
   *
   * @return array
   */
	public function getPublicUsersAction(Request $request)
  {
		return new JsonResponse([
			'status' => true,
			'time' => date('Y/m/d H:i:s')
		]);
	}

	/**
   * List all users.
   *
	 * @ApiDoc(
   *  resource=true,
   *  description="List all users",
	 *   statusCodes = {
   *     200 = "Returned when successful"
   *   }
   * )
	 *
   * @Annotations\View()
   *
   * @param Request               $request      the request object
   *
   * @return array
   */
	public function getUsersAction(Request $request)
  {
		return new JsonResponse([
			'status' => true,
			'time' => date('Y/m/d H:i:s')
		]);
	}

	/**
   * Creates a new user from the submitted data.
   *
   * @ApiDoc(
   *   resource = true,
   *   description = "Creates a new user from the submitted data.",
   *   input = "ProjectBundle\Entity\User",
   *   statusCodes = {
   *     200 = "Returned when successful",
   *     400 = "Returned when the form has errors"
   *   }
   * )
   *
   *
   * @param Request $request the request object
   *
   * @return FormTypeInterface|RouteRedirectView
   */
  public function postUsersAction(Request $request)
	{
      $data = json_decode($request->getContent(), true);
      $request->request->replace(is_array($data) ? $data : array());

			$userManager = $this->container->get('fos_user.user_manager');
      $user = $userManager->createUser();

			if( $request->request->get('admin_user') ){
				$param = $request->request->get('admin_user');
				$form = $this->createForm(AdminUserType::class, $user, array('csrf_protection' => false, 'allow_extra_fields'=>true));

			}elseif( $request->request->get('admin_client') ){
				$param = $request->request->get('admin_client');
				$form = $this->createForm(AdminClientType::class, $user, array('csrf_protection' => false, 'allow_extra_fields'=>true));
			}

      // $form = $this->container->get('form.factory')
      // ->createNamedBuilder(null, AdminUserType::class, $user, array('csrf_protection' => false, 'allow_extra_fields'=>true))
      // ->getForm();

      $form->submit($param);
      $data = $form->getData();
      $email = $data->getEmail();
      $plainpass = $data->getPlainPassword();

      if ($form->isValid()) {

          $user->setUsername($email);
          $user->setUsernameCanonical($email);
          $user->setEnabled(1);
          $userManager->updateUser($user, true);

          //remove role users
          //$user->removeRole('ROLE_USER');
          //$userManager->updateUser($user, true);

          return new JsonResponse([
            'success' => true,
            'time' => date('Y/m/d H:i:s')
          ]);

      } else {

        return new JsonResponse([
          'success' => false,
          'form' => 'Invalid submitted data',
          'time' => date('Y/m/d H:i:s')
        ]);
      }
  }

	/**
   * Update existing user from the submitted data.
   *
   * @ApiDoc(
   *   resource = true,
   *   description = "Update existing user from the submitted data.",
   *   input = "ProjectBundle\Entity\User",
   *   statusCodes = {
   *     200 = "Returned when successful",
   *     400 = "Returned when the form has errors",
   *     404 = "Returned when the users is not found"
   *   }
   * )
   *
   *
   * @param Request $request the request object
   * @param int     $id      the user id
   *
   * @return FormTypeInterface|RouteRedirectView
   *
   * @throws NotFoundHttpException when user not exist
   */
  public function putUsersAction(Request $request, $id)
  {
			$user = $this->getUser(); //not all user_data
			$user_roles = $user->getRoles();

			$util = $this->container->get('utilities');
      $data = json_decode($request->getContent(), true);
      $request->request->replace(is_array($data) ? $data : array());
			//$param = $request->request->get('admin_user');

      $userManager = $this->container->get('fos_user.user_manager');

			$data = $this->getDoctrine()->getRepository(User::class)->find($id);
			$roles = $data->getRoles();
      if(!$data){
        return new JsonResponse([
          'success' => false,
          'form' => 'This data doesn\'t exist',
          'time' => date('Y/m/d H:i:s')
        ]);
      }
      $current_email = $data->getEmail();

			if( $request->request->get('admin_user') ){
				$param = $request->request->get('admin_user');
				$form = $this->createForm(AdminUserType::class, $data, array('csrf_protection' => false, 'allow_extra_fields'=>true));

			}elseif( $request->request->get('admin_client') ){
				$param = $request->request->get('admin_client');
				$form = $this->createForm(AdminClientType::class, $data, array('csrf_protection' => false, 'allow_extra_fields'=>true));
			}

      // $form = $this->container->get('form.factory')
      // ->createNamedBuilder(null, AdminUserType::class, $data, array('csrf_protection' => false,'allow_extra_fields'=>true))
      // ->getForm();

      $form->submit($param);
      $form_data = $form->getData();
      $email = $form_data->getEmail();

      if($form->isValid()){

				// allow super_admin update role_super_admin
				if( in_array("ROLE_SUPER_ADMIN",$roles) && !in_array("ROLE_SUPER_ADMIN",$user_roles) ){
					return new JsonResponse([
            'success' => false,
            'time' => date('Y/m/d H:i:s')
          ]);
				}

        if($current_email != $email){
          // change email

					// delete token
					$util->deleteAccessAndRefreshToken($data);

          $data->setUsername($email);
          $data->setUsernameCanonical($email);
        }

        $userManager->updateUser($data, true);

        return new JsonResponse([
          'success' => true,
          'time' => date('Y/m/d H:i:s')
        ]);
      }else{
        return new JsonResponse([
          'success' => false,
          'form' => 'Invalid submitted data',
          'time' => date('Y/m/d H:i:s')
        ]);
      }
  }

	/**
   * Reset password.
   *
   * @ApiDoc(
   *   resource = true,
   *   description = "Reset password",
   *   input = "ProjectBundle\Entity\User",
   *   statusCodes = {
   *     200 = "Returned when successful",
   *     400 = "Returned when the form has errors",
   *     404 = "Returned when the users is not found"
   *   }
   * )
   *
   *
   * @param Request $request the request object
   * @param int     $id      the user id
   *
   * @return FormTypeInterface|RouteRedirectView
   *
   * @throws NotFoundHttpException when user not exist
   */
  public function putUsersChangepwdAction(Request $request, $id)
  {
			$user = $this->getUser(); //not all user_data
			$user_roles = $user->getRoles();

			$util = $this->container->get('utilities');
			$data = json_decode($request->getContent(), true);
			$request->request->replace(is_array($data) ? $data : array());
			$param = $request->request->get('admin_change_password');

      $userManager = $this->container->get('fos_user.user_manager');
			$data = $this->getDoctrine()->getRepository(User::class)->find($id);
			$roles = $data->getRoles();

      if(!$data){
        return new JsonResponse([
          'success' => false,
          'form' => 'This data doesn\'t exist',
          'time' => date('Y/m/d H:i:s')
        ]);
      }

			$form = $this->createForm(AdminChangePasswordType::class, $data, array('csrf_protection' => false, 'allow_extra_fields'=>true));
      // $form = $this->container->get('form.factory')
      // ->createNamedBuilder(null, AdminChangePasswordType::class, $data, array('csrf_protection' => false,'allow_extra_fields'=>true))
      // ->getForm();

			$form->submit($request->request->get('admin_change_password'));
      $form_data = $form->getData();
      $email = $form_data->getEmail();

      if($form->isValid()){

				// allow super_admin update role_super_admin
				if( in_array("ROLE_SUPER_ADMIN",$roles) && !in_array("ROLE_SUPER_ADMIN",$user_roles) ){
					return new JsonResponse([
            'success' => false,
            'time' => date('Y/m/d H:i:s')
          ]);
				}

        $userManager->updateUser($data, true);

        // delete token
				$util->deleteAccessAndRefreshToken($data);

        return new JsonResponse([
          'success' => true,
          'time' => date('Y/m/d H:i:s')
        ]);
      }else{
        return new JsonResponse([
          'success' => false,
          'form' => 'Invalid submitted data',
          'time' => date('Y/m/d H:i:s')
        ]);
      }
  }

	/**
   * Removes a user
   *
   * @ApiDoc(
   *   resource = true,
   *   description = "Removes a user.",
   *   statusCodes={
   *     204="Returned when successful",
   *     404={
   *       "Returned when the user is not found",
   *     }
   *   }
   * )
   *
   * @param Request $request the request object
   * @param int     $id      the user id
   *
   * @return RouteRedirectView
   *
   * @throws NotFoundHttpException when user not exist
   */
  public function deleteUsersAction($id)
	{
			$user = $this->getUser(); //not all user_data
			$user_roles = $user->getRoles();

			$em = $this->getDoctrine()->getManager();
			$util = $this->container->get('utilities');
			$data = $em->getRepository(User::class)->find($id);
			$roles = $data->getRoles();

      if ($data) {
				// allow super_admin update role_super_admin
				if( in_array("ROLE_SUPER_ADMIN",$roles) && !in_array("ROLE_SUPER_ADMIN",$user_roles) ){
					return new JsonResponse([
            'success' => false,
            'time' => date('Y/m/d H:i:s')
          ]);
				}

	      // delete token
				$util->deleteAccessAndRefreshToken($data);

				// delete user
				$em->remove($data);
		    $em->flush();

	      return new JsonResponse([
	        'success' => true,
	        'time' => date('Y/m/d H:i:s')
	      ]);

			}else{
				return new JsonResponse([
          'success' => false,
          'form' => 'This data doesn\'t exist',
          'time' => date('Y/m/d H:i:s')
        ]);
			}
  }

	/**
   * Group Removes a users
   *
   * @ApiDoc(
   *   resource = true,
   *   description = "Group Removes a users.",
   *   statusCodes={
   *     204="Returned when successful",
   *     404={
   *       "Returned when the user is not found",
   *     }
   *   }
   * )
   *
   * @param Request $request the request object
   * @param array     $data_ids      the user id
   *
   * @return RouteRedirectView
   *
   * @throws NotFoundHttpException when user not exist
   */
  public function deleteUsersGroupsDeletesAction(Request $request)
	{
		$user = $this->getUser(); //not all user_data
		$user_roles = $user->getRoles();

		$em = $this->getDoctrine()->getManager();
		$util = $this->container->get('utilities');
    $data_ids = json_decode($request->getContent(), true);
    if($data_ids){
      $flg_del = false;
      foreach($data_ids as $data_id){

				$data = $em->getRepository(User::class)->find($data_id);
				$roles = $data->getRoles();

				// allow super_admin update role_super_admin
				if( in_array("ROLE_SUPER_ADMIN",$roles) && !in_array("ROLE_SUPER_ADMIN",$user_roles) ){
          return new JsonResponse([
            'success' => false,
            'time' => date('Y/m/d H:i:s')
          ]);
        }

        if($data){
          try{
						// delete token
						$util->deleteAccessAndRefreshToken($data);

						// delete user
						$em->remove($data);

            $flg_del = true;
          }catch(\Exception $e){
            return new JsonResponse([
              'success' => false,
              'time' => date('Y/m/d H:i:s')
            ]);
          }
        }
      }

      if ($flg_del) {
				$em->flush();

        return new JsonResponse([
          'success' => true,
          'time' => date('Y/m/d H:i:s')
        ]);
      }
    }
    return new JsonResponse([
      'success' => false,
      'time' => date('Y/m/d H:i:s')
    ]);
  }

	/**
   * Group Enable users
   *
   * @ApiDoc(
   *   resource = true,
   *   description = "Group Enable users.",
   *   statusCodes={
   *     204="Returned when successful",
   *     404={
   *       "Returned when the user is not found",
   *     }
   *   }
   * )
   *
   * @param Request $request the request object
   * @param array     $data_ids      the user id
   *
   * @return RouteRedirectView
   *
   * @throws NotFoundHttpException when user not exist
   */
  public function postUsersGroupsEnablesAction(Request $request)
	{
		$user = $this->getUser(); //not all user_data
		$user_roles = $user->getRoles();

		$em = $this->getDoctrine()->getManager();
    $data_ids = json_decode($request->getContent(), true);
    if($data_ids){
      $flg_pub = false;
      foreach($data_ids as $data_id){
				$data = $em->getRepository(User::class)->find($data_id);
        $roles = $data->getRoles();

				// allow super_admin update role_super_admin
				if( in_array("ROLE_SUPER_ADMIN",$roles) && !in_array("ROLE_SUPER_ADMIN",$user_roles) ){
          return new JsonResponse([
            'success' => false,
            'time' => date('Y/m/d H:i:s')
          ]);
        }

        if($data){
          $data->setEnabled(1);
					$em->flush();
          $flg_pub = true;
        }
      }
      if ($flg_pub) {
        return new JsonResponse([
          'success' => true,
          'time' => date('Y/m/d H:i:s')
        ]);
      }
    }
    return new JsonResponse([
      'success' => false,
      'time' => date('Y/m/d H:i:s')
    ]);
  }

	/**
   * Group Disable users
   *
   * @ApiDoc(
   *   resource = true,
   *   description = "Group Disable users.",
   *   statusCodes={
   *     204="Returned when successful",
   *     404={
   *       "Returned when the user is not found",
   *     }
   *   }
   * )
   *
   * @param Request $request the request object
   * @param array     $data_ids      the user id
   *
   * @return RouteRedirectView
   *
   * @throws NotFoundHttpException when user not exist
   */
  public function postUsersGroupsDisablesAction(Request $request)
	{
		$user = $this->getUser(); //not all user_data
		$user_roles = $user->getRoles();

		$em = $this->getDoctrine()->getManager();
    $data_ids = json_decode($request->getContent(), true);
    if($data_ids){
      $flg_pub = false;
      foreach($data_ids as $data_id){
				$data = $em->getRepository(User::class)->find($data_id);
        $roles = $data->getRoles();

				// allow super_admin update role_super_admin
        if( in_array("ROLE_SUPER_ADMIN",$roles) && !in_array("ROLE_SUPER_ADMIN",$user_roles) ){
          return new JsonResponse([
            'success' => false,
            'time' => date('Y/m/d H:i:s')
          ]);
        }

        if($data){
          $data->setEnabled(0);
          $em->flush();
          $flg_pub = true;
        }
      }
      if ($flg_pub) {
        return new JsonResponse([
          'success' => true,
          'time' => date('Y/m/d H:i:s')
        ]);
      }
    }
    return new JsonResponse([
      'success' => false,
      'time' => date('Y/m/d H:i:s')
    ]);
  }
}
