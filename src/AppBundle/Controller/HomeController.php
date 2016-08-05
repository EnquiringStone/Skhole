<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 30-Nov-15
 * Time: 21:43
 */
namespace AppBundle\Controller;

use AppBundle\Doctrine\SearchQuery;
use Doctrine\Common\Collections\Criteria;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends Controller {
    private $acceptedHomePages = array('dashboard', 'badges', 'statistics', 'profile', 'settings', 'messages');

    /**
     * @Route("/{_locale}/home/", name="app_home_dashboard_page")
     */
    public function dashboardAction(Request $request)
    {
        $login = false;
        if(array_key_exists('login', $request->query->all())) {
            $login = $request->query->get('login');
        }
        return $this->render(':home/dashboard:dashboard.html.twig', array(
            'login' => $login,
            'courses' => $this->getRandomCourses(10),
            'courseCollection' => $this->getCourseCollectionsForUser()
        ));
    }

    /**
     * @Route("/{_locale}/home/{page}", name="app_home_dashboard_pages_page")
     */
    public function dashboardPagesAction($page, Request $request)
    {
        if(!in_array($page, $this->acceptedHomePages))
            $page = 'dashboard';

        $function = 'create'.ucfirst($page).'Page';
        return $this->$function($request);
    }

//  Dashboard Pages

    public function createDashboardPage(Request $request)
    {
        return $this->render(':home/dashboard:dashboard.html.twig');
    }

    public function createProfilePage(Request $request)
    {
        $education = $this->getDoctrine()->getRepository('AppBundle:Education\Educations')->findOneBy(array('userId' => $this->getUser()->getId()));
        $createdCourses = $this->getDoctrine()->getRepository('AppBundle:Course\Courses')->getCountByUserId($this->getUser()->getId());
        return $this->render(':home/dashboard:profile.html.twig', array('education' => $education, 'createdCourses' => $createdCourses));
    }

    public function createMessagesPage(Request $request)
    {
        $offset = $request->query->get('offset');
        if($offset == null)
            $offset = 0;

        $limit = $this->getParameter('standard_query_limit');
        $maxPagination = $this->getParameter('standard_pagination_max');

        $repository = $this->getDoctrine()->getRepository('AppBundle:Messages');

        $messages = $repository->findBy(array('userId' => $this->getUser()->getId()), array('sendDateTime' => 'DESC'), $limit, $offset);
        $total = $repository->getCountByUserId($this->getUser()->getId());
        return $this->render(':home/dashboard:messages.html.twig',
            array(  'messages' => $messages,
                    'limit' => $limit,
                    'offset' => $offset,
                    'total' => $total,
                    'maxPages' => $maxPagination
            ));
    }

//  EndPages


    /**
     * @Route("/{_locale}/search/", name="app_search_page")
     */
    public function searchAction(Request $request)
    {
        $requestParams = $request->query->all();
        $search = null;
        if(array_key_exists('search', $requestParams))
            $search = $requestParams['search'];

        $languages = $this->getDoctrine()->getRepository('AppBundle:Course\CourseLanguages')->findAll();
        $levels = $this->getDoctrine()->getRepository('AppBundle:Course\CourseLevels')->findAll();

        return $this->render(':home/search:search.html.twig', array('languages' => $languages, 'levels' => $levels, 'search' => $search));
    }

    /**
     * @Route("/{_locale}/home/getting-started/", name="app_home_getting_started_page")
     */
    public function gettingStartedAction(Request $request)
    {
        return $this->render('getting.started.html.twig');
    }

    /**
     * @Route("/{_locale}/ajax/", name="app_home_ajax_page")
     */
    public function otherAction(Request $request)
    {
        //Don't do shit
    }

    private function getRandomCourses($amount)
    {
        $criteria = array('removed' => false, 'state' => $this->getDoctrine()->getRepository('AppBundle:Course\CourseStates')->findOneBy(array('stateCode' => 'OK')), 'isUndesirable' => false);

        $repo = $this->getDoctrine()->getRepository('AppBundle:Course\Courses');

        $totalCourses = $repo->getCountByCriteria($criteria);

        if($totalCourses <= $amount)
            return $repo->findBy($criteria);

        $offset = rand(0, $totalCourses - $amount);

        return $repo->findBy($criteria, null, $amount, $offset);
    }

    private function getCourseCollectionsForUser()
    {
        if(!$this->isGranted('ROLE_USER')) return array();

        $collectionItems = $this->getDoctrine()->getRepository('AppBundle:Course\CourseCollectionItems')->findBy(array('userId' => $this->getUser()->getId()));

        $friendlyArray = array();
        foreach ($collectionItems as $item)
        {
            $friendlyArray[] = $item->getCourseId();
        }
        return $friendlyArray;
    }
}