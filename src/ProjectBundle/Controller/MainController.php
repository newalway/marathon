<?php

namespace ProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Doctrine\ORM\EntityManagerInterface;
use ProjectBundle\Entity\User;
use ProjectBundle\Entity\Blog;
use ProjectBundle\Entity\BlogImage;
use ProjectBundle\Entity\Portfolio;
use ProjectBundle\Entity\PortfolioImage;
use ProjectBundle\Entity\BrandType;
use ProjectBundle\Entity\Brand;
use ProjectBundle\Entity\Product;
use ProjectBundle\Entity\Promotion;
use ProjectBundle\Entity\PromotionDownloadCounter;
use ProjectBundle\Entity\Banner;
use ProjectBundle\Entity\Contact;
use ProjectBundle\Entity\SettingOption;
use ProjectBundle\Entity\Authentication;
use ProjectBundle\Entity\Pages;
use ProjectBundle\Entity\Showroom;
use ProjectBundle\Entity\OurClient;
use ProjectBundle\Entity\CustomerPaymentBankTransfer;
use ProjectBundle\Entity\CustomerOrder;
use ProjectBundle\Entity\CustomerOrderDelivery;
use ProjectBundle\Entity\BankAccount;
use ProjectBundle\Entity\CustomerOrderItem;
use ProjectBundle\Entity\Subscriber;
use ProjectBundle\Entity\TrackingNumber;
use ProjectBundle\Entity\LayoutShop;
use ProjectBundle\Entity\ProductCategory;

use ProjectBundle\Form\Type\B2bRegisterType;
use ProjectBundle\Form\Type\CustomerPaymentBankTransferType;
use ProjectBundle\Form\Type\ContactType;
use ProjectBundle\Form\Type\SubscriberType;
use ProjectBundle\Form\Type\PaymentBankTransferType;

use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\File\MimeType\FileinfoMimeTypeGuesser;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;

use JMS\SecurityExtraBundle\Annotation\Secure;
use GuzzleHttp\Client;

class MainController extends Controller
{
    public function _footerAction(Request $request)
    {
        $em = $this->getDoctrine();
        $news_recents  = $em->getRepository(Blog::class)->findAllActiveData()->setMaxResults(3)->getQuery()->getResult();
        $setting_option_repo = $em->getRepository(SettingOption::class);
		$social_networks = $setting_option_repo->getDataByGroup('social_network', 'Social Network Link')->getQuery()->getResult();
        return $this->render('ProjectBundle:'.$this->container->getParameter('view_main').':_footer.html.twig', array(
            'news_recents'=>$news_recents,
            'social_networks'=>$social_networks
        ));
    }

    public function indexAction(Request $request)
    {
        $util = $this->container->get('utilities');
        $product_util = $this->container->get('app.product');
        $locale = $request->getLocale();
        $session = $request->getSession();
        $repository = $this->getDoctrine()->getRepository(Product::class);

        $product_cat_repo = $this->getDoctrine()->getRepository(ProductCategory::class);
		$product_category_root_id = $this->container->getParameter('product_category_root_id');
        $product_cat_query = $product_cat_repo->findHighlightDataByRootId($product_category_root_id)->getQuery();
        $product_categories = $product_cat_query->getResult();

        $blogs = $this->getDoctrine()->getRepository(Blog::class)->findAllActiveData()->setMaxResults(4)->getQuery()->getResult();
        $banners = $this->getDoctrine()->getRepository(Banner::class)->findAllActiveData($request->getLocale())->getQuery()->getResult();
        $our_clients = $this->getDoctrine()->getRepository(OurClient::class)->findAllActiveData($request->getLocale())->getQuery()->getResult();
        $item_best_seller = $this->getDoctrine()->getRepository(Product::class)->findPublishProductBestSeller()->getQuery()->getResult();
        $subscriber_form = $this->createForm(SubscriberType::class, new Subscriber());
        $layout_shops = $this->getDoctrine()->getRepository(LayoutShop::class)->findAllActiveData()->getQuery()->getResult();

        // not used
        // $limitPages = $this->container->getParameter('max_per_page_latest_product');
        // $limitPerPage = (isset($data['limitPerPage'])) ? $data['limitPerPage'] : $limitPages;
        // $query = $repository->findAllActiveData(array(),$locale);
        // $paginated = $util->setPaginatedOnPagerfanta($query,$limitPerPage);
        // $brands = $this->getDoctrine()->getRepository(Brand::class)->findAllActiveByProduct();

        /* not used
        $limitBestSeller = $this->container->getParameter('max_best_seller_product');
        $item_best_seller = $this->getDoctrine()->getRepository(Product::class)->findPublishProductBestSeller()->setMaxResults($limitBestSeller)->getQuery()->getResult();
        //$item_best_seller = $this->getDoctrine()->getRepository(CustomerOrderItem::class)->findItemBestSeller()->setMaxResults($limitBestSeller)->getQuery()->getResult();
        */

        return $this->render('ProjectBundle:'.$this->container->getParameter('view_main').':index.html.twig', array(
            'product_categories'=>$product_categories,
            'blogs'=>$blogs,
            'banners'=>$banners,
            'our_clients'=>$our_clients,
            'item_best_seller'=>$item_best_seller,
            'subscriber_form'=>$subscriber_form->createView(),
            'layout_shops'=>$layout_shops,
            // 'paginated'=>$paginated,
            // 'brands'=>$brands,
        ));
    }

    public function subscriber_createAction(Request $request)
    {
        $util = $this->container->get('utilities');
        $em = $this->getDoctrine()->getManager();

        $data = new Subscriber();
        $form = $this->createForm(SubscriberType::class, $data);
        $form->submit($request->request->all());

        $form_data = $form->getData();
        $email = $form_data->getEmail();
        $chk_email = $em->getRepository(Subscriber::class)->findByEmail($email);
        if(count($chk_email)>0){
          $form->get('email')->addError(new FormError('The email is already subscribe'));
        }

        if($form->isSubmitted() && $form->isValid()){

            $em->persist($data);
            $em->flush();

            $response['success'] = true;
            $response['message'] = $this->get('translator')->trans('subscribe.thanks');
            return new JsonResponse($response);
        }else{
            // $errors = $this->getFormErrorMessage($form);
            $errors = $util->getFormErrorMessage($form);
            $response['success'] = false;
            $response['message'] = '';
            $response['errors'] = $errors;
            return new JsonResponse($response);
        }
    }

    public function about_usAction(Request $request)
    {
        $data = $this->getDoctrine()->getRepository(Pages::class)->getActiveDataByName('about_us',$request->getLocale());
        if (!$data) { throw $this->createNotFoundException('No data found'); }
        return $this->render('ProjectBundle:'.$this->container->getParameter('view_main').':about_us.html.twig', array(
            'data'=>$data
        ));
    }

    public function serviceAction(Request $request)
    {
        $data = $this->getDoctrine()->getRepository(Pages::class)->getActiveDataByName('service',$request->getLocale());
        if (!$data) { throw $this->createNotFoundException('No data found'); }
        return $this->render('ProjectBundle:'.$this->container->getParameter('view_main').':service.html.twig', array(
            'data'=>$data
        ));
    }

    public function how_to_buyAction(Request $request)
    {
        $data = $this->getDoctrine()->getRepository(Pages::class)->getActiveDataByName('how_to_buy',$request->getLocale());
        if (!$data) { throw $this->createNotFoundException('No data found'); }
        return $this->render('ProjectBundle:'.$this->container->getParameter('view_main').':how_to_buy.html.twig', array(
            'data'=>$data
        ));
    }

    public function shipping_deliveryAction(Request $request)
    {
        $data = $this->getDoctrine()->getRepository(Pages::class)->getActiveDataByName('shipping_delivery',$request->getLocale());
        if (!$data) { throw $this->createNotFoundException('No data found'); }
        return $this->render('ProjectBundle:'.$this->container->getParameter('view_main').':shipping_delivery.html.twig', array(
            'data'=>$data
        ));
    }

    public function terms_conditionsAction(Request $request)
    {
        $data = $this->getDoctrine()->getRepository(Pages::class)->getActiveDataByName('terms_conditions',$request->getLocale());
        if (!$data) { throw $this->createNotFoundException('No data found'); }
        return $this->render('ProjectBundle:'.$this->container->getParameter('view_main').':terms_conditions.html.twig', array(
            'data'=>$data
        ));
    }

    public function privacy_policyAction(Request $request)
    {
        $data = $this->getDoctrine()->getRepository(Pages::class)->getActiveDataByName('privacy_policy',$request->getLocale());
        if (!$data) { throw $this->createNotFoundException('No data found'); }
        return $this->render('ProjectBundle:'.$this->container->getParameter('view_main').':privacy_policy.html.twig', array(
            'data'=>$data
        ));
    }

    public function contactAction(Request $request)
    {
        $em = $this->getDoctrine();
        $util = $this->container->get('utilities');

        $form = $this->createForm(ContactType::class, new Contact());
        $google_maps_api_key = $util->getGoogleMapsApiKey();

        return $this->render('ProjectBundle:'.$this->container->getParameter('view_main').':contact.html.twig', array(
            'form'=>$form->createView(),
            'google_maps_api_key'=>$google_maps_api_key
        ));
    }

    public function contact_createAction(Request $request)
    {
        $util = $this->container->get('utilities');
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->submit($request->request->all());
        $data = $form->getData();
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($contact);
            $em->flush();

            $subject = 'You have a new message(s) in your contact';
            $urls = $this->generateUrl('admin_contact_view',array('id'=>$data->getId()), UrlGeneratorInterface::ABSOLUTE_URL);
            $response = $this->sendmail($urls,$subject,$data);
            return new JsonResponse($response);
        }else{
            // $errors = $this->getFormErrorMessage($form);
            $errors = $util->getFormErrorMessage($form);
            $response['errors'] = $errors;
            $response['success'] = false;
            return new JsonResponse($response);
        }
    }

    public function blogAction(Request $request)
    {
        $util = $this->container->get('utilities');
        $session = $request->getSession();
        $repository = $this->getDoctrine()->getRepository(Blog::class);
        $query = $repository->findAllActiveData();
        $paginated = $util->setPaginatedOnPagerfanta($query,10);
        return $this->render('ProjectBundle:'.$this->container->getParameter('view_main').':blog.html.twig', array(
            'paginated' =>$paginated
        ));
    }

    public function blog_detailAction(Request $request)
    {
        $session = $request->getSession();
        $em = $this->getDoctrine();
        $data = $em->getRepository(Blog::class)->getActiveDataById($request->get('id'))->getQuery()->getSingleResult();
        if (!$data) { throw $this->createNotFoundException('No data found'); }
        $data_image = $em->getRepository(BlogImage::class)->findBy(array('blog' => $request->get('id')), array('id' => 'ASC'));
        $recent_news  = $em->getRepository(Blog::class)->getActiveDataByRecent($request->get('id'))->setMaxResults(5)->getQuery()->getResult();
        return $this->render('ProjectBundle:'.$this->container->getParameter('view_main').':blog_detail.html.twig', array(
            'data'=>$data,'data_image'=>$data_image,'recent_news'=>$recent_news
        ));
    }

    public function portfolioAction(Request $request)
    {
        $util = $this->container->get('utilities');
        $session = $request->getSession();
        $repository = $this->getDoctrine()->getRepository(Portfolio::class);
        $query = $repository->findAllActiveData();
        $paginated = $util->setPaginatedOnPagerfanta($query,10);
        // dump($paginated->getCurrentpageresults());
        // exit;
        return $this->render('ProjectBundle:'.$this->container->getParameter('view_main').':portfolio.html.twig', array(
            'paginated' =>$paginated
        ));
    }

    public function portfolio_detailAction(Request $request)
    {
        $session = $request->getSession();
        $em = $this->getDoctrine();
        $data = $em->getRepository(Portfolio::class)->getActiveDataById($request->get('id'))->getQuery()->getSingleResult();
        if (!$data) { throw $this->createNotFoundException('No data found'); }
        $data_image = $em->getRepository(PortfolioImage::class)->findBy(array('portfolio' => $request->get('id')), array('id' => 'ASC'));
        $recent_port  = $em->getRepository(Portfolio::class)->getActiveDataByRecent($request->get('id'))->setMaxResults(5)->getQuery()->getResult();
        return $this->render('ProjectBundle:'.$this->container->getParameter('view_main').':portfolio_detail.html.twig', array(
            'data'=>$data,'data_image'=>$data_image,'recent_port'=>$recent_port
        ));
    }

    public function showroomAction(Request $request)
    {
        $em = $this->getDoctrine();
        $util = $this->container->get('utilities');
        $session = $request->getSession();

        // $coordinates = $util->getCookieGeocodeCoordinates();
        // if($coordinates['lat'] && $coordinates['lng']){
        //     $query = $em->getRepository(Showroom::class)->getFastestDistanceByLatLng($coordinates['lat'], $coordinates['lng'], $request->getLocale());
        // }else{
        //     $query = $em->getRepository(Showroom::class)->findAllActiveData($request->getLocale());
        // }
        // $query = $em->getRepository(Showroom::class)->findAllActiveData($request->getLocale());
        // $paginated = $util->setPaginatedOnPagerfanta($query, 12);

        $cookie_coordinates = $util->getCookieCoordinates();
        $google_maps_api_key = $util->getGoogleMapsApiKey();
        $geolocation_api_key = $util->getGeolocationApiKey();
        return $this->render('ProjectBundle:'.$this->container->getParameter('view_main').':showroom.html.twig', array(
            // 'paginated' => $paginated,
            'google_maps_api_key'=> $google_maps_api_key,
            'geolocation_api_key'=>$geolocation_api_key,
            'cookie_coordinates'=>$cookie_coordinates
        ));
    }

    public function find_showroom_distanceAction(Request $request)
    {
        $em = $this->getDoctrine();
        $util = $this->container->get('utilities');

        $coordinates['lat'] = $request->get('lat');
		$coordinates['lng'] = $request->get('lng');
        $set_cookie = $request->get('set_cookie');
		$arr_showrooms = array();

        if($coordinates['lat'] && $coordinates['lng']){
            if($set_cookie){
                $util->setCookieCoordinates($coordinates);
            }
            //get showroom with lat, lng
            $rs = $em->getRepository(Showroom::class)->getFastestDistanceByLatLng($coordinates['lat'], $coordinates['lng'], $request->getLocale())->getQuery()->getResult();
            if($rs){
                foreach ($rs as $key => $arr_showroom) {
                    $showroom = $arr_showroom[0];
                    $data_result = $util->setArrayShowroomDataFromObj($showroom);
                    $data_result['distance'] = $arr_showroom['distance'];
                    array_push($arr_showrooms, $data_result);
                }
            }

        }else{
            //get showroom without lat, lng
            $rs = $em->getRepository(Showroom::class)->findAllActiveData($request->getLocale())->getQuery()->getResult();
            if($rs){
                foreach ($rs as $showroom) {
                    $data_result = $util->setArrayShowroomDataFromObj($showroom);
                    $data_result['distance'] = '';
                    array_push($arr_showrooms, $data_result);
                }
            }
        }

        $json_response = new JsonResponse();
		$json_response->setEncodingOptions(JSON_NUMERIC_CHECK);
	    $json_response->setData(array(
			'arr_showrooms' => $arr_showrooms,
			'user_lat' => $coordinates['lat'],
			'user_lng' => $coordinates['lng'],
            'set_cookie' => $set_cookie,
			'time' => date('Y/m/d H:i:s')
	    ));
		return $json_response;
    }

    public function searchAction(Request $request)
    {
        $session = $request->getSession();
        return $this->render('ProjectBundle:'.$this->container->getParameter('view_main').':search.html.twig', array());
    }

    public function search_apiAction(Request $request)
    {
        $session = $request->getSession();

        $arr_data = array();
        $arr_result = array();
        $arr_query_data = array();
        $all_result = 0;

        if($request->get('q')){
            $arr_query_data['q'] = $request->get('q');
            $em = $this->getDoctrine();

            $product = $em->getRepository(Product::class)->findAllActiveData($arr_query_data)->getQuery()->getResult(2);
            $arr_data['product'] = $product;
            $arr_result['product'] = count($product);

            $blog = $em->getRepository(Blog::class)->findAllActiveData($arr_query_data)->getQuery()->getResult(2);
            $arr_data['blog'] = $blog;
            $arr_result['blog'] = count($blog);

            foreach ($arr_result as $key => $value) {
                $all_result = $all_result + $value;
            }
        }

        $response = new JsonResponse();
        $response->setData(array(
            'data'  => $arr_data,
            'result' => $arr_result,
            'all_result' => $all_result,
            'time' => date('Y/m/d H:i:s'),
        ));
        $response->setSharedMaxAge(600);
        return $response;
    }

    /**
    * @Secure(roles="ROLE_CLIENT, ROLE_CUSTOMER, ROLE_USER, ROLE_EDITOR, ROLE_ADMIN")
    */
    public function default_target_loginAction(Request $request)
    {
        $user = $this->getUser();
        $session = $request->getSession();
        $auth_checker = $this->get('security.authorization_checker');
        $util = $this->container->get('utilities');

        $util->destroySessionAfterLogin($request);

        if( $auth_checker->isGranted('ROLE_EDITOR') || $auth_checker->isGranted('ROLE_ADMIN') ){
            return $this->redirect($this->generateUrl('admin'));

        }elseif( $auth_checker->isGranted('ROLE_CLIENT') || $auth_checker->isGranted('ROLE_CUSTOMER') ){
            //check valid token for only customer is_set_password
            $is_set_pwd = $user->getIsSetPassword();
            if($is_set_pwd){
                try {
                    $acctoken = $util->getAccessToken();
                    //check token expire here
                } catch(\Exception $e) {
                	//no access token or expire redirect to generate_token
                    return $this->redirectToRoute('member_generate_token');
                }
            }

            if($auth_checker->isGranted('ROLE_CLIENT')){
                return $this->redirect($this->generateUrl('fos_user_profile_show'));
            }else{
                return $this->redirect($this->generateUrl('fos_user_profile_show'));
            }

        }else{
            return $this->redirect($this->generateUrl('homepage'));
        }
    }



    public function default_target_logoutAction(Request $request)
    {
        $util = $this->container->get('utilities');

        $util->destroySessionAfterLogout($request);
        return $this->redirect($this->generateUrl('homepage'));
    }

    /**
    * @Secure(roles="ROLE_ADMIN, ROLE_EDITOR, ROLE_CUSTOMER, ROLE_CLIENT")
    */
    public function default_target_password_resettingAction(Request $request)
    {
        $util = $this->container->get('utilities');
        $session = $request->getSession();
        $user = $this->getUser();

        if($session->has('tmp_password_resetting')){
            //get password
            $new_pwd = $session->get('tmp_password_resetting');
            //get email
            $email = $user->getEmail();

            //get user scope
            $user_roles = $user->getRoles();
            if( in_array("ROLE_CLIENT",$user_roles) ){
                $scope = $this->container->getparameter('access_token_client_scope');
            }else{
                $scope = $this->container->getparameter('access_token_customer_scope');
            }

            //set oauth token
            $util->setAccessToken($email, $new_pwd, $scope);
            //reset session access token
            $token = $util->getAccessTokenFromDB();

            //remove session tmp_password_resetting
            $session->remove('tmp_password_resetting');
        }
        //return $this->redirect($this->generateUrl('fos_user_profile_show'));
        return $this->redirect($this->generateUrl('default_target_login'));
    }

    public function b2b_registerAction(Request $request)
    {
        $session = $request->getSession();
        $user = $this->getUser();
        if($user){
          throw $this->createNotFoundException('You are not permitted to use that link to directly access that page');
        }

        $userManager = $this->container->get('fos_user.user_manager');
        $user = $userManager->createUser();
        $form = $this->createForm(B2bRegisterType::class, $user);
        return $this->render('ProjectBundle:'.$this->container->getParameter('view_main').':b2b_register.html.twig', array('form' =>$form->createView()));
    }

    public function b2b_register_createAction(Request $request)
    {
        $util = $this->container->get('utilities');
        $em = $this->getDoctrine()->getManager();
        $session = $request->getSession();
        $user = $this->getUser();
        if($user){
            throw $this->createNotFoundException('You are not permitted to use that link to directly access that page');
        }

        $userManager = $this->container->get('fos_user.user_manager');
        $user = $userManager->createUser();

        $form = $this->createForm(B2bRegisterType::class, $user);
        $form->handleRequest($request);
        $datas = $form->getData();
        $email = $datas->getEmail();

        $chk_email = $em->getRepository(User::class)->findByEmailCanonical($email);
        if(count($chk_email)>0){
            $form->get('email')->addError(new FormError('The email is already used'));//already exists email
        }

        if ($form->isSubmitted() && $form->isValid())
        {
            $plainpass = $datas->getPlainPassword();
            $roles = array('ROLE_CLIENT');

            // we set username in user entity
            // $user->setUsername($email);
            // $user->setUsernameCanonical($email);
            $user->setRoles($roles);
            $user->setIsSetPassword(1);

            $setting_repo = $em->getRepository(SettingOption::class);
            $setting_option_email = $setting_repo->findOneByOptionName('b2b_quotation_mail_address');
            $b2b_quotation_mail_address = $util->explodeStrToArrayEmail($setting_option_email->getOptionValue());

            $confirmationEnabled = $this->container->getParameter('fos_user.registration.confirmation.enabled');
            if($confirmationEnabled){
                // register with email comfirmation

                // default mailer mail to customer
                // $mailer = $this->container->get('fos_user.mailer');

                // custom mailer mail to admin
                $mailer = $this->container->get('app.custom_fos_user_mailer');

                $tokenGenerator = $this->container->get('fos_user.util.token_generator');
                //save confirmation token
                $user->setConfirmationToken($tokenGenerator->generateToken());
                //send confirmation email
                $mailer->sendConfirmationEmailMessage($user, $b2b_quotation_mail_address);
                //save user data
                $userManager->updateUser($user);
                //set oauth token
                $scope = $this->container->getparameter('access_token_client_scope');
                $util->setAccessToken($email, $plainpass, $scope);
                //send mail to user for approve
                $this->get('session')->set('fos_user_send_confirmation_email/email', $user->getEmail());

                // default mailer mail to customer
                // $route = 'fos_user_registration_check_email';
                // $this->get('session')->getFlashBag()->add('fos_user_success', 'registration.flash.user_created');
                // return $this->redirect($this->generateUrl('fos_user_registration_check_email'));

                // custom mailer mail to admin
                return $this->redirect($this->generateUrl('b2b_register_complete'));

            }else{
                // register with non comfirmation
                //enable customer status
                $user->setEnabled(1);
                //save user data
                $userManager->updateUser($user, true);
                //set oauth token
                $scope = $this->container->getparameter('access_token_client_scope');
                $util->setAccessToken($email, $plainpass, $scope);
                return $this->redirect($this->generateUrl('b2b_register_complete'));
            }

        }
        return $this->render('ProjectBundle:'.$this->container->getParameter('view_main').':b2b_register.html.twig', array('form'=>$form->createview()));
    }

    public function b2b_register_completeAction(Request $request)
    {
        // $locale = $request->getLocale();
        // $translated = $this->get('translator')->trans('customer.registration');
        $em = $this->getDoctrine()->getManager();
        $setting_repo = $em->getRepository(SettingOption::class);
        $setting_option_email = $setting_repo->findOneByOptionName('b2b_quotation_mail_address');
        $b2b_quotation_mail_address = $setting_option_email->getOptionValue();

        $session = $request->getSession();
        $flashBag = $this->get('session')->getFlashBag();
        foreach ($session->getFlashBag()->get('login_at_checkout', array()) as $val){
            if($val){
                //change flash login_at_checkout to register_at_checkout
                $flashBag->add('register_at_checkout', true);
            }
        }
        return $this->render('ProjectBundle:'.$this->container->getParameter('view_main').':b2b_register_complete.html.twig', array(
            'b2b_quotation_mail_address' => $b2b_quotation_mail_address,
        ));
    }

    protected function sendmail($urls,$subject,$data)
    {
        $em = $this->getDoctrine()->getManager()->getRepository(SettingOption::class);
        //Recipients
        $setting_option_email = $em->findOneByOptionName('contact_mail_address');
        $arr_contact_mail_address = explode(",", $setting_option_email->getOptionValue());
        //Sender name
        $setting_option_name = $em->findOneByOptionName('contact_mail_name');
        $contact_mail_name = $setting_option_name->getOptionValue();
        //Default email
        $sender_mail_address = $this->container->getParameter('default_sender_mail_address') ;

        $message = (new \Swift_Message($subject))
        ->setFrom(array($sender_mail_address => $contact_mail_name))
        ->setTo($arr_contact_mail_address)
        ->setBody(
            $this->renderView(
                'ProjectBundle:'.$this->container->getParameter('view_main').':_email.html.twig',
                array('urls'=> $urls,'subject'=>$subject,'data'=>$data)
            ),
            'text/html'
        );

        try{
            $this->get('mailer')->send($message);
            $response['success'] = true;
            $response['message'] = $this->get('translator')->trans('contact.send.thank');
        }catch(\Exception $e){
            #Do nothing
            $response['success'] = false;
            $response['message'] = $this->get('translator')->trans('contact.cannot.send');
        }

        return $response;
    }

    public function promotionAction(Request $request)
    {
        $util = $this->container->get('utilities');
        $session = $request->getSession();
        $repository = $this->getDoctrine()->getRepository(Promotion::class);
        $query = $repository->findAllActiveData();
        $paginated = $util->setPaginatedOnPagerfanta($query,10);
        return $this->render('ProjectBundle:'.$this->container->getParameter('view_main').':promotion.html.twig', array(
            'paginated' =>$paginated
        ));
    }

    public function promotion_detailAction(Promotion $promotion,Request $request)
    {
        $session = $request->getSession();
        $em = $this->getDoctrine();

        $data_promotion = $em->getRepository(Promotion::class)->getActiveDataById($promotion)->getQuery()->getOneOrNullResult();
        if($data_promotion){
            $product_has_promotion = $this->getDoctrine()->getRepository(Product::class)->getActiveDataProductsByPromotionId($promotion)->getQuery();

            $util = $this->container->get('utilities');
            $limitPages = $this->container->getParameter('max_per_page_latest_product');
            $paginated = $util->setPaginatedOnPagerfanta($product_has_promotion,$limitPages);

            return $this->render('ProjectBundle:'.$this->container->getParameter('view_main').':promotion_detail.html.twig', array(
                'paginated'=>$paginated,
                'promotion'=>$data_promotion
            ));

        }else{
            return $this->redirect($this->generateUrl('promotion'));
        }
    }

    public function promotion_download_createAction(Request $request)
    {
        $util = $this->container->get('utilities');
		$em = $this->getDoctrine()->getManager();

        $id = $request->get('id');
		$data = $em->getRepository(Promotion::class)->find($request->get('id'));
		if (!$data) { throw $this->createNotFoundException('No data found'); }

        //save counter download
        $ip_address = $request->getClientIp();

        $counter = $data->getPromotionDownloadCounter()->count();
		$data->setDownloadCount($counter);

        $current_browser = $util->getBrowserCap();
		$dc = new PromotionDownloadCounter();
	    $dc->setIpAddress($ip_address);
	    $dc->setBrowserName($current_browser['browser_name_pattern']);
	    $dc->setPlatform($current_browser['platform']);
        $dc->setPlatformVersion($current_browser['platform_version']);
		$dc->setBrowser($current_browser['browser']);
		$dc->setVersion($current_browser['version']);
		$dc->setPromotion($data);

        $record = $util->getGeoLite2City();
		if(!empty($record)){
			$dc->setCountryCode($record->country->isoCode);
			$dc->setCountryName($record->country->name);
			$dc->setCityName($record->city->name);
			$dc->setPostalCode($record->postal->code);
			$dc->setLocationLatitude($record->location->latitude);
			$dc->setLocationLongitude($record->location->longitude);
		}

        $em->persist($dc);
	    $em->flush();

        return new JsonResponse([
			'success' => true,
            'time' => date('Y/m/d H:i:s')
		]);
    }

    public function promotion_download_contentAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');
		$data = $em->getRepository(Promotion::class)->find($request->get('id'));
		if (!$data) { throw $this->createNotFoundException('No data found'); }

        $publicResourcesFolderPath = $this->container->getParameter('web_path');
        $filename_path = $data->getFilepath();
        $filename = basename($filename_path);

        // This should return the file to the browser as response
		$response = new BinaryFileResponse($publicResourcesFolderPath.$filename_path);

        // To generate a file download, you need the mimetype of the file
		$mimeTypeGuesser = new FileinfoMimeTypeGuesser();

        // Set the mimetype with the guesser or manually
		if($mimeTypeGuesser->isSupported()){
			// Guess the mimetype of the file according to the extension of the file
			$response->headers->set('Content-Type', $mimeTypeGuesser->guess($publicResourcesFolderPath.$filename_path));
		}else{
			// Set the mimetype of the file manually, in this case for a text file is text/plain
			$response->headers->set('Content-Type', 'text/plain');
		}

		// Set content disposition inline of the file
		$response->setContentDisposition(
			ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename
		);

		return $response;
    }

    public function trackAction(Request $request)
    {
        $em = $this->getDoctrine();
        if($request->get('no')){
            $customerOrder = $em->getRepository(CustomerOrder::Class)->findCustomerOrderHasItemByOrderNumber($request->get('no'))->getQuery()->getResult();
            if($customerOrder){
                $status = 1;
            }else{
                $status = 0;
            }
        }else {
            $customerOrder = "";
            $status = 3;
        }

        $bankAccount = $em->getRepository(BankAccount::Class)->findAllActiveData()->getQuery()->getResult();
        $payment_bank_transfer = $em->getRepository(CustomerPaymentBankTransfer::class)->findCustomerPaymentBankTransferByOrder($customerOrder)->getQuery()->getResult();
        $arr_tracking_numbers = $em->getRepository(TrackingNumber::class)->findSelectDataByOrder($customerOrder)->getQuery()->getArrayResult();

        return $this->render('ProjectBundle:'.$this->container->getParameter('view_main').':track.html.twig', array(
            'customerOrder'=>$customerOrder,
            'status'=>$status,
            'bankAccount'=>$bankAccount,
            'payment_bank_transfer'=>$payment_bank_transfer,
            'arr_tracking_numbers'=>$arr_tracking_numbers
        ));
    }

    //***** old version is in Pages
    // public function portfolioAction(Request $request)
    // {
    //     $data = $this->getDoctrine()->getRepository(Pages::class)->getActiveDataByName('portfolio',$request->getLocale());
    //     if (!$data) { throw $this->createNotFoundException('No data found'); }
    //     return $this->render('ProjectBundle:'.$this->container->getParameter('view_main').':portfolio.html.twig', array(
    //         'data'=>$data
    //     ));
    // }

    public function confirm_paymentAction(Request $request)
    {
        $util = $this->container->get('utilities');
        $payment_bank_transfer = new CustomerPaymentBankTransfer();
        $form = $this->createForm(CustomerPaymentBankTransferType::class,$payment_bank_transfer);

        $now_date = new \DateTime();
        $form->get('timeTransfer')->setData($now_date);

        $em = $this->getDoctrine()->getManager();
        $form->handleRequest($request);
        $data = $form->getData();

        // $bankAccount =  new BankAccount();
        // $bankAccountObj = $em->getRepository(BankAccount::Class)->findOneById(8);
        // dump($bankAccountObj[0]->getId());
        // exit;

        if ($form->isSubmitted() && $form->isValid()) {

            $customerOrderObj = $em->getRepository(CustomerOrder::Class)->findCustomerOrderHasItemByOrderNumber($data->getOrderNumber())->getQuery()->getResult();

            $file = $form['attach_file']->getData();
            // dump($imageEn->getImage());
            // $file =	$file->getClientOriginalName();
            $extension = $file->guessExtension();
            $date = date("Y-m-d");
            $fileName = $date.rand(1, 99999).'.'.$extension;
            $file->move($this->container->getParameter('files_upload_bank_transfer'),$fileName);

            $payment_bank_transfer->setAttachFile($fileName);
            // $payment_bank_transfer->setBankAccount($bankAccountObj);
            $payment_bank_transfer->setCustomerOrder($customerOrderObj[0]);
            //dump($data);
            $em->persist($payment_bank_transfer);
            $em->flush();

            $util->sendMailPaymentBankTransfer($payment_bank_transfer);

            $this->get('session')->getFlashBag()->add('notice', 'success');
            return $this->redirect($this->generateUrl('confirm_payment'));
        }

        return $this->render('ProjectBundle:'.$this->container->getParameter('view_main').':confirm_payment.html.twig', array(
            'form' =>$form->createView(),
        ));
    }

    public function search_paymentDataAction(Request $request){
        $payment_bank_transfer = new CustomerPaymentBankTransfer();
        //$form = $this->createForm(CustomerPaymentBankTransferType::class,$payment_bank_transfer);
        //$em = $this->getDoctrine()->getManager();
        // $form->handleRequest($request);

        if (isset($_REQUEST['orderId'])){
            $ordid = $request->get('orderId');
            $em = $this->getDoctrine()->getManager();
            $customerOrderObj = $em->getRepository(CustomerOrder::Class)->checkCustomerHasOrderById($ordid)->getQuery()->getOneOrNullResult();
            if (isset($customerOrderObj)){
            $customerOrderAddressObj = $em->getRepository(CustomerOrderDelivery::Class)->findCustomerOrderDeliveryByOrder($customerOrderObj,1)->getQuery()->getOneOrNullResult();
            $data  = array('firstname'=>$customerOrderAddressObj->getFirstName(),
                            'lastname'=>$customerOrderAddressObj->getLastName(),
                            'phone'=>$customerOrderAddressObj->getPhone(),
                            'orderNumber' =>$customerOrderObj->getOrderNumber(),
                            'totalPrice' =>$customerOrderObj->getTotalPrice()
            );

            $json_response = new JsonResponse();
    		// $json_response->setEncodingOptions(JSON_NUMERIC_CHECK);
            $json_response->setData($data);
            return $json_response;
            }else{
                return new JsonResponse(false);
            }
        }
    }

    public function _setting_webAction(Request $request, $title, $is_translate=false)
    {
        $local = $request->getLocale();
        $option_name = $title;
        if($is_translate){
            $option_name = $title.'_'.$local;
        }

        $em = $this->getDoctrine()->getManager()->getRepository(SettingOption::class);
        $setting_option_web = $em->findOneByOptionName($option_name);
        if($setting_option_web){
            $val = $setting_option_web->getOptionValue();
        }else{
            $val = '';
        }
        return new Response($val);
    }


}
