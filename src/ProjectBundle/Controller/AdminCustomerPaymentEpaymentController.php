<?php

namespace ProjectBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use ProjectBundle\Entity\CustomerPaymentEpayment;

use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Cookie;

use JMS\SecurityExtraBundle\Annotation\Secure;
use GuzzleHttp\Client;

class AdminCustomerPaymentEpaymentController extends Controller
{
    const ROUTER_CONTROLLER = 'AdminCustomerPaymentEpayment';
    /**
  * @Secure(roles="ROLE_EDITOR")
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

    $arr_query_data = $util->prepare_query_data($request);
    $repository = $this->getDoctrine()->getRepository(CustomerPaymentEpayment::class);
    $query = $repository->findAllData($arr_query_data);
    $paginated = $util->setPaginatedOnPagerfanta($query);


    $util->setBackToUrl();
    return $this->render('ProjectBundle:'.self::ROUTER_CONTROLLER.':index.html.twig', array(
      'paginated' =>$paginated
    ));
  }
}
