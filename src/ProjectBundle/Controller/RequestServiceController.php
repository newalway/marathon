<?php

namespace ProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use ProjectBundle\Entity\RequestService;

use ProjectBundle\Form\Type\RequestServiceType;


use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use Doctrine\ORM\EntityManagerInterface;

class RequestServiceController extends Controller
{
    public function request_serviceAction(Request $request)
    {
        $util = $this->container->get('utilities');
        $request_service = new RequestService();
        $form = $this->createForm(RequestServiceType::class,$request_service);

        // $em = $this->getDoctrine()->getManager();
        // $form->handleRequest($request);
        // $data = $form->getData();

        // dump($form->isSubmitted());
        // exit;

        // if ($form->isSubmitted() && $form->isValid()) {
        //     $em->persist($data);
        //     $em->flush();
        //
        //     $this->get('session')->getFlashBag()->add('notice',$this->get('translator')->trans('request_service.success'));
        //
        //     return $this->redirect($this->generateUrl('request_service'));
        // }

        return $this->render('ProjectBundle:'.$this->container->getParameter('view_main').':request_service.html.twig', array(
            'form' =>$form->createView(),
        ));
    }

    public function request_service_createAction(Request $request)
    {
        $util = $this->container->get('utilities');
        $requestService = new RequestService();
        $form = $this->createForm(RequestServiceType::class, $requestService);
        $form->submit($request->request->all());
        $data = $form->getData();
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($data);
            $em->flush();

            // $subject = 'You have a new message(s) in your contact';
            $subject = $this->get('translator')->trans('request_service.success');

            $urls = $this->generateUrl('admin_request_service_view',array('id'=>$data->getId()), UrlGeneratorInterface::ABSOLUTE_URL);
            $response = $util->sendmail_request_service($urls,$subject,$data);
            return new JsonResponse($response);
        }else{
            // $errors = $this->getFormErrorMessage($form);
            $errors = $util->getFormErrorMessage($form);
            $response['errors'] = $errors;
            $response['success'] = false;
            return new JsonResponse($response);
        }
    }

}
