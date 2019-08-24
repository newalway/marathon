<?php

namespace ProjectBundle\Controller\Api;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Doctrine\ORM\EntityManagerInterface;
use ProjectBundle\Entity\Hashtag;
use ProjectBundle\Entity\Product;

use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use FOS\RestBundle\Controller\Annotations;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class ApiHashTagController extends FOSRestController
{
	/**
   * List products tags.
   *
	 * @ApiDoc(
   *  resource=true,
   *  description="List products tags",
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
	public function getProductTagsAction(Request $request)
  {
		$user = $this->getUser(); //not all user_data

		$em = $this->getDoctrine()->getManager();
		$util = $this->container->get('utilities');
		$product_id = $request->query->get('product_id');

		$product = $this->getDoctrine()->getRepository(Product::class)->find($product_id);

		$arr_hash_tags = array();
		$current_hashtags = $product->getHashtags();
		if(!empty($current_hashtags)){
			foreach ($current_hashtags as $current_hashtag) {
				$tag = $current_hashtag->getTitle();
				array_push($arr_hash_tags, $tag);
			}
		}

		// $arr_hash_tags = ['takrawball','petanque'];

		return new JsonResponse([
			"data" => $arr_hash_tags
		]);
	}

	/**
   * List all tags.
   *
	 * @ApiDoc(
   *  resource=true,
   *  description="List all tags",
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
	public function getTagsAction(Request $request)
  {
		$user = $this->getUser(); //not all user_data

		$em = $this->getDoctrine()->getManager();
		$util = $this->container->get('utilities');
		$query = $request->query->get('query');

		$array_tag = array();
		$repository = $this->getDoctrine()->getRepository(Hashtag::class);
		$qb = $repository->createQueryBuilder('h');
		$qb->where($qb->expr()->andX(
           $qb->expr()->like('h.title', ':query')
        	))
        ->setParameter('query', '%'.$query.'%')
        ->orderBy('h.title', 'ASC');
    $hashtags = $qb->getQuery()->getResult();
		if($hashtags){
			foreach ($hashtags as $hashtag) {
				$tag = $hashtag->getTitle();
				$arr = array("text"=>$tag);
				array_push($array_tag, $arr);
			}
		}

		// $array_tag = [ ["text" => "takrawball"], ["text" => "petanque"] ];

		return new JsonResponse([
			"data" => $array_tag
		]);
	}

}
