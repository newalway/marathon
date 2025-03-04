<?php

namespace ProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use ProjectBundle\Entity\Power;

use ProjectBundle\Form\Type\AdminPowerType;

use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Cookie;

use JMS\SecurityExtraBundle\Annotation\Secure;
use GuzzleHttp\Client;

class AdminPowerController extends Controller
{
	const ROUTER_INDEX = 'admin_power';
	const ROUTER_ADD = self::ROUTER_INDEX.'_new';
	const ROUTER_EDIT = self::ROUTER_INDEX.'_edit';
  const ROUTER_PREFIX = 'power';
	const ROUTER_CONTROLLER = 'AdminPower';

	protected function getQuerySearchData($arr_query_data)
	{
		$repository = $this->getDoctrine()->getRepository(Power::class);
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
		$paginated = $util->setPaginatedOnPagerfanta($query);

    $util->setBackToUrl();
    return $this->render('ProjectBundle:'.self::ROUTER_CONTROLLER.':index.html.twig', array(
			'paginated' =>$paginated
		));
  }

	/**
  * @Secure(roles="ROLE_EDITOR")
  */
  public function newAction(Request $request)
  {
		$util = $this->container->get('utilities');
		$util->setCkAuthorized();
		$form = $this->createForm(AdminPowerType::class, new Power());
    return $this->render('ProjectBundle:'.self::ROUTER_CONTROLLER.':new.html.twig', array(
			'form'=>$form->createView()
		));
  }

  /**
  * @Secure(roles="ROLE_EDITOR")
  */
  public function createAction(Request $request)
  {
    $util = $this->container->get('utilities');
    $em = $this->getDoctrine()->getManager();

		$data = new Power();
    $form = $this->createForm(AdminPowerType::class, $data);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
			$em->persist($data);
	    $em->flush();

			$util->setCreateNotice();
			$redirect_uri = $util->getRedirectUriSaveBtn($form, $data, self::ROUTER_INDEX, self::ROUTER_ADD, self::ROUTER_EDIT);
			return $this->redirect($redirect_uri);
    }
    return $this->render('ProjectBundle:'.self::ROUTER_CONTROLLER.':new.html.twig', array(
			'form'=>$form->createView()
		));
  }

	/**
  * @Secure(roles="ROLE_EDITOR")
  */
  public function editAction(Request $request)
  {
    $util = $this->container->get('utilities');
		$util->setCkAuthorized();
		$data = $this->getDoctrine()->getRepository(Power::class)->find($request->get('id'));
		if (!$data) { throw $this->createNotFoundException('No data found'); }

		$form = $this->createForm(AdminPowerType::class, $data);
    return $this->render('ProjectBundle:'.self::ROUTER_CONTROLLER.':edit.html.twig', array(
			'form'=>$form->createview()
		));
  }

  /**
  * @Secure(roles="ROLE_EDITOR")
  */
  public function updateAction(Request $request)
  {
    $util = $this->container->get('utilities');
    $em = $this->getDoctrine()->getManager();

		$data = $em->getRepository(Power::class)->find($request->get('id'));
		if (!$data) { throw $this->createNotFoundException('No data found'); }

    $form = $this->createForm(AdminPowerType::class, $data);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
			$em->flush();

			$util->setUpdateNotice();
			$redirect_uri = $util->getRedirectUriSaveBtn($form, $data, self::ROUTER_INDEX, self::ROUTER_ADD, self::ROUTER_EDIT);
			return $this->redirect($redirect_uri);
    }
    return $this->render('ProjectBundle:'.self::ROUTER_CONTROLLER.':edit.html.twig', array('form'=>$form->createview()));
  }

	/**
  * @Secure(roles="ROLE_EDITOR")
  */
  public function deleteAction(Request $request)
  {
    $util = $this->container->get('utilities');
		$em = $this->getDoctrine()->getManager();

    $data = $em->getRepository(Power::class)->find($request->get('id'));
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
				$data = $em->getRepository(Power::class)->find($data_id);
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

	/**
  * @Secure(roles="ROLE_EDITOR")
  */
	public function sortAction(Request $request)
	{
		$util = $this->container->get('utilities');
		$arr_query_data = $util->prepare_query_data($request);
		$datas = $this->getQuerySearchData($arr_query_data)->getQuery()->getResult();
		return $this->render('ProjectBundle:'.self::ROUTER_CONTROLLER.':sort.html.twig', array('datas' =>$datas));
	}

	/**
  * @Secure(roles="ROLE_EDITOR")
  */
	public function sort_prosessAction(Request $request)
	{
		$util = $this->container->get('utilities');
		$em = $this->getDoctrine()->getManager();

		$i=0;
		$sorted = $request->get('sort');
		if ($sorted) {
			foreach ($sorted as $data_id) {
				$data = $em->getRepository(Power::class)->find($data_id);
				if ($data) {
					$i=$i+1;
					$data->setPosition($i);
				}
			}
			try {
				$em->flush();
				$status='complete';
			} catch(\Exception $e) {
				$status='error';
			}
			return new Response($status);
		}
	}
}
