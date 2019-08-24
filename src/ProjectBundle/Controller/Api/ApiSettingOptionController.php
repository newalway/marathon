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

use ProjectBundle\Entity\SettingOption;

class ApiSettingOptionController extends FOSRestController
{
    /**
    * List all setting_option.
    *
    * @ApiDoc(
    *  resource=true,
    *  description="List all setting_option",
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
    public function getSettingOptionsAction(Request $request)
    {
        // $datas = SettingOptionQuery::create()->find();
        // $arr = $datas->toArray();
        $arr = array();
        $response = new JsonResponse();
        $response->setEncodingOptions(JSON_NUMERIC_CHECK);
        $response->setData(array(
            'datas'  => $arr,
            'time' => date('Y/m/d H:i:s')
        ));
        // $response->setMaxAge(300);
        // $response->setSharedMaxAge(300);
        return $response;
    }

    /**
    * Update existing setting_option from the submitted data.
    *
    * @ApiDoc(
    *   resource = true,
    *   description = "Update existing setting_option from the submitted data.",
    *   input = "FOS\UserBundle\Propel\User",
    *   statusCodes = {
    *     200 = "Returned when successful",
    *     400 = "Returned when the form has errors",
    *     404 = "Returned when the setting_option is not found"
    *   }
    * )
    *
    *
    * @param Request $request the request object
    * @param int     $id      the setting_option id
    *
    * @return FormTypeInterface|RouteRedirectView
    *
    * @throws NotFoundHttpException when user not exist
    */
    public function putSettingOptionsUpdateAction(Request $request)
    {
        $util = $this->container->get('utilities');
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(SettingOption::class);

        $data = json_decode($request->getContent(), true);
        $request->request->replace(is_array($data) ? $data : array());

        $setting_options = $this->getDoctrine()->getRepository(SettingOption::class)->findAll();
        if(empty($setting_options)) {
            return new JsonResponse([
                'success' => false,
                'form' => 'This data doesn\'t exist',
                'time' => date('Y/m/d H:i:s')
            ]);
        }

        if($request->get('data')){
            foreach ($request->get('data') as $key => $value) {
                // $setting_option = $em->getRepository(SettingOption::class)->findOneBy(array('optionName'=>$key));

                $setting_option = $repository->findOneByOptionName($key);
                $setting_option->setOptionValue($value);

                //try save data param
                // if($key=='fos_resetting_email_message'){
                //     $arr_value = array('{email}','{first_name}','{last_name}','{confirmation_url}');
                //     $setting_option->setParam($arr_value);
                // }

                $em->flush();
            }

            return new JsonResponse([
                'success' => true,
                'time' => date('Y/m/d H:i:s')
            ]);
        }
    }

}
