<?php

namespace ProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use ProjectBundle\Form\Type\AdminHolidayType;
use ProjectBundle\Entity\Holiday;

use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Cookie;

use JMS\SecurityExtraBundle\Annotation\Secure;
use GuzzleHttp\Client;

class AdminHolidayController extends Controller
{
    const ROUTER_INDEX = 'admin_holiday';
    const ROUTER_CONTROLLER = 'AdminHoliday';
    const ROUTER_ADD = self::ROUTER_INDEX.'_new';
    const ROUTER_EDIT = self::ROUTER_INDEX.'_edit';

    protected function getQuerySearchData($arr_query_data)
    {
        $repository = $this->getDoctrine()->getRepository(Holiday::class);
        $query = $repository->findAllData($arr_query_data);
        return $query;
    }

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
        $query = $this->getQuerySearchData($arr_query_data);

// $a = $query->getQuery()->getArrayResult();
// print_r($a);
// exit;

        $paginated = $util->setPaginatedOnPagerfanta($query);

        $util->setBackToUrl();
        return $this->render('ProjectBundle:'.self::ROUTER_CONTROLLER.':index.html.twig', array('paginated' =>$paginated));
    }

    /**
	* @Secure(roles="ROLE_EDITOR")
	*/
	public function newAction(Request $request)
	{
        $em = $this->getDoctrine()->getManager();
		$util = $this->container->get('utilities');
		$util->setCkAuthorized();
		$acctoken = $util->getAccessToken();

        $data = new Holiday();
		$form = $this->createForm(AdminHolidayType::class, $data, array('allow_extra_fields'=>true));
		// set default startDate
		// $form->get('startDate')->setData(new \DateTime);

        $form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
            $form_data = $request->get('admin_holiday');
            $is_created = false;
            $obj_form_data = $form->getData();
            $holiday_title = $obj_form_data->getTitle();
            $holiday_date = $form_data['holidayDate'];

            $arr_date = $util->getArrayDateRange($holiday_date);
            if($arr_date['start'] && $arr_date['end']){
                $objStartDate = new \DateTime($arr_date['start']);
                $objEndDate = new \DateTime($arr_date['end']);
                if($objStartDate!=$objEndDate){
                    //date range
                    do {
                        //validate unique data
                        $count_holiday = $em->getRepository(Holiday::class)->findOneByHolidayDate($objStartDate);
                        if($count_holiday){
                            $this->get('session')->getFlashBag()->add('warning', $objStartDate->format('d/m/Y').' already exists ');
                            $objStartDate->modify('+1 day');
                            continue;
                        }else{
                            $holiday = new Holiday();
                            $holiday->setTitle($holiday_title);
                            $holiday->setHolidayDate($objStartDate);
                            $holiday->setStatus(1);
                            $em->persist($holiday);
                    		$em->flush();

                            $objStartDate->modify('+1 day');
                            $is_created = true;
                        }
                    } while ($objStartDate <= $objEndDate);

                }else{
                    //one day
                    $count_holiday = $em->getRepository(Holiday::class)->findOneByHolidayDate($objStartDate);
                    if($count_holiday){
                        $this->get('session')->getFlashBag()->add('warning', $objStartDate->format('d/m/Y').' already exists ');
                    }else{
                        $holiday = new Holiday();
                        $holiday->setTitle($holiday_title);
                        $holiday->setHolidayDate($objStartDate);
                        $holiday->setStatus(1);
                        $em->persist($holiday);
                        $em->flush();
                        $is_created = true;
                    }
                }
            }
            if($is_created){
                $util->setCreateNotice();
            }

			$redirect_uri = $util->getRedirectUriSaveBtn($form, $data, self::ROUTER_INDEX, self::ROUTER_ADD, self::ROUTER_EDIT);
			return $this->redirect($redirect_uri);
        }

		return $this->render('ProjectBundle:'.self::ROUTER_CONTROLLER.':new.html.twig', array(
			'form'=>$form->createView(),
			'acctoken'=>$acctoken
		));
	}

    /**
	* @Secure(roles="ROLE_EDITOR")
	*/
	public function editAction(Request $request)
	{
        $em = $this->getDoctrine()->getManager();
		$util = $this->container->get('utilities');
		$util->setCkAuthorized();
		$acctoken = $util->getAccessToken();

        $data = $this->getDoctrine()->getRepository(Holiday::class)->find($request->get('id'));
		if (!$data) { throw $this->createNotFoundException('No data found'); }

        $form = $this->createForm(AdminHolidayType::class, $data, array('allow_extra_fields'=>true));
        $form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
            $form_data = $request->get('admin_holiday');
            $obj_form_data = $form->getData();
            $holiday_title = $obj_form_data->getTitle();
            $holiday_date = $form_data['holidayDate'];
            $arr_date = $util->getArrayDateRange($holiday_date);
            if($arr_date['start']){
                $objStartDate = new \DateTime($arr_date['start']);
                $qb = $em->getRepository(Holiday::class)->createQueryBuilder('h')
                    ->where('h.id != :data_id')
                    ->andWhere('h.holidayDate = :holiday_date')
                    ->setParameters(array('data_id' => $request->get('id'), 'holiday_date' => $objStartDate));
                $count_holiday = $qb->getQuery()->getResult();
                if($count_holiday){
                    $this->get('session')->getFlashBag()->add('warning', $objStartDate->format('d/m/Y').' already exists ');
                }else{
                    $data->setTitle($holiday_title);
                    $data->setHolidayDate($objStartDate);
                    $data->setStatus(1);
                    $em->flush();
                    $util->setUpdateNotice();
                }
            }

			$redirect_uri = $util->getRedirectUriSaveBtn($form, $data, self::ROUTER_INDEX, self::ROUTER_ADD, self::ROUTER_EDIT);
			return $this->redirect($redirect_uri);
        }

		return $this->render('ProjectBundle:'.self::ROUTER_CONTROLLER.':edit.html.twig', array(
			'form'=>$form->createview(),
			'acctoken'=>$acctoken
		));
	}

    /**
	* @Secure(roles="ROLE_EDITOR")
	*/
	public function deleteAction(Request $request)
	{
		$util = $this->container->get('utilities');
		$em = $this->getDoctrine()->getManager();

		$data = $em->getRepository(Holiday::class)->find($request->get('id'));
		if (!$data) { throw $this->createNotFoundException('No data found'); }

		try {
			$em->remove($data);
			$em->flush();
			$util->setRemoveNotice();
		} catch(\Doctrine\DBAL\DBALException $e) {
			$util->setCustomeFlashMessage('warning', $msg="Can't delete ".$data->getTitle());
		}

		return $this->redirect($util->getBackToUrl(self::ROUTER_INDEX));
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
				$data = $em->getRepository(Holiday::class)->find($data_id);
				if ($data) {
					try {
						$em->remove($data);
						$em->flush();
						$flg_del = true;
					} catch(\Doctrine\DBAL\DBALException $e) {
						$util->setCustomeFlashMessage('warning', $msg="Can't delete ".$data->getTitle());
					}
				}
			}
			if ($flg_del) {
				$util->setRemoveNotice();
			}
		}
		return $this->redirect($util->getBackToUrl(self::ROUTER_INDEX));
	}

}
