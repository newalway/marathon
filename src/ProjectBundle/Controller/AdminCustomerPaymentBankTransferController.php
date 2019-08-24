<?php

namespace ProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use ProjectBundle\Entity\CustomerPaymentBankTransfer;

use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

use JMS\SecurityExtraBundle\Annotation\Secure;
use GuzzleHttp\Client;

class AdminCustomerPaymentBankTransferController extends Controller
{
    const ROUTER_CONTROLLER = 'AdminCustomerPaymentBankTransfer';

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
        $repository = $this->getDoctrine()->getRepository(CustomerPaymentBankTransfer::class);
        $query = $repository->findAllDataJoin($arr_query_data);
        $paginated = $util->setPaginatedOnPagerfanta($query);

        $util->setBackToUrl();
        return $this->render('ProjectBundle:'.self::ROUTER_CONTROLLER.':index.html.twig', array(
            'paginated' =>$paginated
        ));
    }

    /**
    * @Secure(roles="ROLE_EDITOR")
    */
    public function downloadAction(Request $request)
    {
        $util = $this->container->get('utilities');
        $util->setCkAuthorized();
        $id = $request->get('id');

        $data = $this->getDoctrine()->getRepository(CustomerPaymentBankTransfer::class)->find($id);
        if (!$data) {
            throw $this->createNotFoundException('This data doesn\'t exist');
        }
        $attach_file = $this->container->getParameter('files_upload_bank_transfer').$data->getAttachFile();
        try{
            $file_name = $data->getAttachFile();
            $response = new Response();
            $response->headers->set('Content-type', mime_content_type($attach_file));
            $response->setContent(file_get_contents($attach_file));
            $response->headers->set('Content-Disposition', sprintf('attachment; filename="%s"', $file_name ));
            return $response;
        }
        catch(\Exception $e){
            throw $this->createNotFoundException('This file is already deleted');
        }
    }
    /**
    * @Secure(roles="ROLE_EDITOR")
    */
    public function viewAction(CustomerPaymentBankTransfer $customer_bank_transfer,Request $request)
    {
        $publicResourcesFolderPath = $this->container->getParameter('files_upload_bank_transfer');
        $filename = $customer_bank_transfer->getAttachFile();


        // This should return the file to the browser as response
        $response = new BinaryFileResponse($publicResourcesFolderPath.$filename);
         return $response;
    }


}
