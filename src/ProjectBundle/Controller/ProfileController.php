<?php

namespace ProjectBundle\Controller;

use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Form\Factory\FactoryInterface;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\UserBundle\Controller\ProfileController as BaseController;
use ProjectBundle\Form\Type\B2bRegisterType;

class ProfileController extends BaseController
{
    /**
    * Show the user.
    */
    public function showAction()
    {
        $request = $this->get('request_stack')->getCurrentRequest();
        $session = $request->getSession();

        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        return $this->render('@FOSUser/Profile/show.html.twig', array(
            'user' => $user,
        ));
    }

    /**
    * Edit the user.
    *
    * @param Request $request
    *
    * @return Response
    */
    public function editAction(Request $request)
    {
        $util = $this->container->get('utilities');
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        $current_email = $user->getEmail();
        $arr_fos_user_profile_form = $request->request->get('fos_user_profile_form');

        /** @var $dispatcher EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $auth_checker = $this->get('security.authorization_checker');
        if( $auth_checker->isGranted('ROLE_CLIENT')){
            //b2b customer
            $form = $this->createForm(B2bRegisterType::class, $user);
        }else{
            /** @var $formFactory FactoryInterface */
            $formFactory = $this->get('fos_user.profile.form.factory');
            $form = $formFactory->createForm();
        }

        $form->setData($user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //get form data
            $data = $form->getData();
            $email = $data->getEmail();
            //get current_password if exist
            $plainpass = false;
            if( isset($arr_fos_user_profile_form['current_password']) ){
                $plainpass = $arr_fos_user_profile_form['current_password']; //form current_password not mapped to the model
            }

            /** @var $userManager UserManagerInterface */
            $userManager = $this->get('fos_user.user_manager');

            $event = new FormEvent($form, $request);
            $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_SUCCESS, $event);

            $userManager->updateUser($user);

            if($current_email!=$email){
                // delete token
                $util->deleteAccessAndRefreshToken($user);

                // change email, remove token
                $session = $request->getSession();
                $session->remove('token');

                if($plainpass){
                    //get user scope
                    $user_roles = $user->getRoles();
                    if( in_array("ROLE_CLIENT",$user_roles) ){
                        $scope = $this->container->getparameter('access_token_client_scope');
                    }else{
                        $scope = $this->container->getparameter('access_token_customer_scope');
                    }

                    //set oauth token
                    $util->setAccessToken($email, $plainpass, $scope);
                    //get access toekn and set to session
                    $util->getAccessToken();
                }
            }

            if (null === $response = $event->getResponse()) {
                $url = $this->generateUrl('fos_user_profile_show');
                $response = new RedirectResponse($url);
            }

            $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

            return $response;
        }

        return $this->render('@FOSUser/Profile/edit.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}
