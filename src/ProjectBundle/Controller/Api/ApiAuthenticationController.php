<?php

namespace ProjectBundle\Controller\Api;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\Security\Core\SecurityContext;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use ProjectBundle\Entity\Authentication;

class ApiAuthenticationController extends FOSRestController
{
	/**
   * Update existing authentication from the submitted data.
   *
   * @ApiDoc(
   *   resource = true,
   *   description = "Update existing authentication from the submitted data.",
   *   input = "FOS\UserBundle\Propel\User",
   *   statusCodes = {
   *     200 = "Returned when successful",
   *     400 = "Returned when the form has errors",
   *     404 = "Returned when the authentication is not found"
   *   }
   * )
   *
   *
   * @param Request $request the request object
   * @param int     $id      the authentication id
   *
   * @return FormTypeInterface|RouteRedirectView
   *
   * @throws NotFoundHttpException when user not exist
   */
    public function putAuthenticationsUpdateAction(Request $request)
    {
        $util = $this->container->get('utilities');
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(Authentication::class);

        $data = json_decode($request->getContent(), true);
        $request->request->replace(is_array($data) ? $data : array());

        $authentications = $this->getDoctrine()->getRepository(Authentication::class)->findAll();
        if(empty($authentications)) {
            return new JsonResponse([
            'success' => false,
            'form' => 'This data doesn\'t exist',
            'time' => date('Y/m/d H:i:s')
            ]);
        }

        if($request->get('data')){
            foreach ($request->get('data') as $key => $value) {
                $authentication = $repository->findOneByName($key);
                $authentication->setValue($value);
                $em->flush();
            }
            return new JsonResponse([
                'success' => true,
                'time' => date('Y/m/d H:i:s')
            ]);
        }

    }
}
