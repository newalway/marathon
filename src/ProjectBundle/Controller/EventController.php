<?php

namespace ProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use ProjectBundle\Entity\Event;
use ProjectBundle\Entity\EventCategory;
use ProjectBundle\Entity\Blog;

use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class EventController extends Controller
{
    public function indexAction(Request $request)
    {

        $util = $this->container->get('utilities');
        $session = $request->getSession();
        $repository = $this->getDoctrine()->getRepository(EventCategory::class);
        $event_category = $repository->findAllActiveData()->getQuery()->getResult();

        return $this->render('ProjectBundle:'.$this->container->getParameter('view_main').':_home_events.html.twig', array(
            'event_category' => $event_category
        ));
    }

    public function detailAction(Event $event)
    {
        $em = $this->getDoctrine();
        $data = $em->getRepository(Event::class)->findActiveDataByEvent(false,false,$event)->getQuery()->getSingleResult();

        if (!$data) { throw $this->createNotFoundException('No data found'); }
        // $data_image = $em->getRepository(BlogImage::class)->findBy(array('blog' => $request->get('id')), array('id' => 'ASC'));
        $recent_event  = $em->getRepository(Event::class)->getActiveDataByRecent(false,false,$event)->setMaxResults(5)->getQuery()->getResult();

        return $this->render('ProjectBundle:'.$this->container->getParameter('view_main').':event_detail.html.twig', array(
                'data'=>$data,
                'recent_event'=>$recent_event
        ));
    }

    public function categoryAction(EventCategory $eventCategory)
    {

        $util = $this->container->get('utilities');
        // $session = $request->getSession();
        $eventCategoryTitle = $eventCategory->getTitle();
        $repository = $this->getDoctrine()->getRepository(Event::class);
        $query = $repository->findActiveDataByCategory(false, false, $eventCategory);
        $paginated = $util->setPaginatedOnPagerfanta($query,10);

        return $this->render('ProjectBundle:'.$this->container->getParameter('view_main').':event_category.html.twig', array(
            'paginated' =>$paginated,
            'eventCategory'=> $eventCategory
        ));
    }
}
