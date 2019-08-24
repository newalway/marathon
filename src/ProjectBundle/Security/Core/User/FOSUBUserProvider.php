<?php
namespace ProjectBundle\Security\Core\User;

use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider as BaseClass;
use HWI\Bundle\OAuthBundle\Security\Core\Exception\AccountNotLinkedException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

use FOS\UserBundle\Model\UserManagerInterface;
use ProjectBundle\Utils\Utilities;
use ProjectBundle\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;

class FOSUBUserProvider extends BaseClass
{
    private $kernel;
    private $utilities;
    private $router;

    /**
     * Constructor.
     *
     * @comment_out param UserManagerInterface $userManager fOSUB user provider
     * @comment_out param array                $properties  property mapping
     */
    public function __construct(UserManagerInterface $userManager, array $properties, $kernel, $utilities, Router $router)
    {
        $this->userManager = $userManager;
        $this->properties = array_merge($this->properties, $properties);
        $this->container = $kernel->getContainer();
        $this->utilities = $utilities;
        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $username = $response->getUsername();
        $userEmail = $response->getEmail();
        $userRealName = $response->getRealName();
        $userFirstName = $response->getFirstName();
        $userLastName = $response->getLastName();

        $user = $this->userManager->findUserBy(array($this->getProperty($response) => $username));
        //when the user is registrating
        if (null === $user) {
            $service = $response->getResourceOwner()->getName();
            $setter = 'set'.ucfirst($service);
            $setter_id = $setter.'Id';
            $setter_token = $setter.'AccessToken';

            //check email is registrating
            $check_user = $this->userManager->findUserBy(array('email'=> $userEmail));
            if (null === $check_user) {

                // create new user here
                $user = $this->userManager->createUser();
                $user->$setter_id($username);
                $user->$setter_token($response->getAccessToken());
                //I have set all requested data with the user's username
                //modify here with relevant data
                $user->setUsername($userEmail);
                $user->setEmail($userEmail);
                $user->setPassword($username);
                $user->setEnabled(true);

                $user->setOauth(1);
                $user->setServiceName($service);
                $user->setServiceEmail($userEmail);
                $user->setFirstName($userFirstName);
                $user->setLastName($userLastName);

                //set role
                $roles = array('ROLE_CUSTOMER');
                $user->setRoles($roles);

                $this->userManager->updateUser($user);

                //get user scope
                // $scope = $this->container->getparameter('access_token_customer_scope');
                //set oauth token
                // $this->utilities->setAccessToken($username, $username, $scope);

                return $user;

            }else{
                // the email is already used
                return $check_user; //if disable redirect to register
            }
        }
        //if user exists - go with the HWIOAuth way
        $user = parent::loadUserByOAuthUserResponse($response);
        $serviceName = $response->getResourceOwner()->getName();
        $setter = 'set' . ucfirst($serviceName) . 'AccessToken';
        //update access token
        $user->$setter($response->getAccessToken());
        return $user;
    }

    /**
     * {@inheritDoc}
     */
    public function connect(UserInterface $user, UserResponseInterface $response)
    {
        $property = $this->getProperty($response);
        $username = $response->getUsername();
        $userEmail = $response->getEmail();
        $userRealName = $response->getRealName();
        $userFirstName = $response->getFirstName();
        $userLastName = $response->getLastName();

        //check exists email
        $em = $this->container->get('doctrine')->getEntityManager();
        $qb = $em->getRepository(User::class)->createQueryBuilder('u');
        $qb->where('u.id != :user_id')
            ->andWhere('u.email = :email')
            ->setParameters(array('user_id' => $user->getId(), 'email' => $userEmail));
        $chk_email = $qb->getQuery()->getResult();
        if(count($chk_email)>0){
            //already exists email
            $this->container->get('session')->getFlashBag()->add('error-social-connect', 'The account or email is already used');
            return false;
            // return new RedirectResponse($this->router->generate('member_social_connections'));
        }


        //on connect - get the access token and the user ID
        $service = $response->getResourceOwner()->getName();
        $setter = 'set'.ucfirst($service);
        $setter_id = $setter.'Id';
        $setter_token = $setter.'AccessToken';
        //we "disconnect" previously connected users
        if (null !== $previousUser = $this->userManager->findUserBy(array($property => $username))) {
            $previousUser->$setter_id(null);
            $previousUser->$setter_token(null);
            $this->userManager->updateUser($previousUser);
        }

        //remove other connected
        if($service=='google'){
            $user->setFacebookId(null);
            $user->setFacebookAccessToken(null);
        }elseif($service=='facebook'){
            $user->setGoogleId(null);
            $user->setGoogleAccessToken(null);
        }

        //we connect current user
        $user->$setter_id($username);
        $user->$setter_token($response->getAccessToken());
        $user->setUsername($userEmail);
        $user->setEmail($userEmail);
        //$user->setPassword($username);

        $user->setOauth(1);
        $user->setServiceName($service);
        $user->setServiceEmail($userEmail);
        $user->setFirstName($userFirstName);
        $user->setLastName($userLastName);
        $this->userManager->updateUser($user);

        //set flash success
        $this->container->get('session')->getFlashBag()->add('social-connect-success', 1);
    }

}
