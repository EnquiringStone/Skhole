<?php
/**
 * Created by PhpStorm.
 * User: EnquiringStone
 * Date: 08-Oct-16
 * Time: 16:56
 */

namespace AppBundle\Service\Ajax\Home;


use AppBundle\Enum\ContextEnum;
use AppBundle\Interfaces\AjaxInterface;
use AppBundle\Service\Ajax\Teach\CoursesAjaxService;
use AppBundle\Transformer\TransformManager;
use Doctrine\ORM\EntityManager;

class SearchAjaxService implements AjaxInterface
{

    /**
     * @var EntityManager
     */
    private $manager;
    /**
     * @var TransformManager
     */
    private $transformer;
    /**
     * @var CoursesAjaxService
     */
    private $coursesService;

    public function __construct(EntityManager $manager, TransformManager $transformer, CoursesAjaxService $coursesService)
    {
        $this->manager = $manager;
        $this->transformer = $transformer;
        $this->coursesService = $coursesService;
    }

    public function getMostViewedCourses($args)
    {
        $count = array_key_exists('count', $args) ? $args['count'] : 10;

        $courses = $this->manager->getRepository('AppBundle:Course\Courses')->findBy(
            array(
                'removed' => false,
                'stateId' => $this->manager->getRepository('AppBundle:Course\CourseStates')->findOneBy(array('stateCode' => 'OK'))->getId(),
                'isUndesirable' => false),
            array('views' => 'DESC'),
            $count);

        $html = $this->transformer->getTransformerByName('CoursesTransformer')->transformToAjaxResponse($courses, ContextEnum::SEARCH_CONTEXT);
        $modalHtml = $this->coursesService->getReviewModals(array('limit' => $count, 'sortAttribute' => 'views', 'sortValue' => 'DESC', 'context' => ContextEnum::PUBLIC_CONTEXT))['html'];

        return array('html' => $html, 'totalFound' => sizeof($courses), 'modalHtml' => $modalHtml);
    }

    /**
     * Returns an unique code that is used to determine which implementation
     * of this interface should be used for the ajax call
     * @return string
     */
    public function getUniqueCode()
    {
        //Search Ajax Service
       return 'SAS2';
    }

    /**
     * Returns a list of public methods defined within its class that
     * can be accessed from an ajax call
     * @return array
     */
    public function getSubscribedMethods()
    {
        return array('getMostViewedCourses');
    }
}