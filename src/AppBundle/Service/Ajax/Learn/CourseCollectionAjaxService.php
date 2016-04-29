<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 29-Apr-16
 * Time: 19:41
 */

namespace AppBundle\Service\Ajax\Learn;


use AppBundle\Authorization\AuthorizationService;
use AppBundle\Entity\Course\CourseCollectionItems;
use AppBundle\Exception\FrontEndException;
use AppBundle\Interfaces\AjaxInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class CourseCollectionAjaxService implements AjaxInterface
{

    /**
     * @var EntityManager
     */
    private $manager;
    /**
     * @var AuthorizationService
     */
    private $authorizationService;

    function __construct(EntityManager $manager, AuthorizationService $authorizationService)
    {
        $this->manager = $manager;
        $this->authorizationService = $authorizationService;
    }

    public function addToCollection($args)
    {
        $user = $this->authorizationService->getAuthorizedUserOrThrowException();
        $courseId = $args['courseId'];

        $courseItem = $this->manager->getRepository('AppBundle:Course\CourseCollectionItems')->findOneBy(array('userId' => $user->getId(), 'courseId' => $courseId));

        if($courseItem != null)
            throw new FrontEndException('course.collection.item.already.exists', 'ajaxerrors');

        $course = $this->manager->getRepository('AppBundle:Course\Courses')->find($courseId);

        if($course == null)
            throw new FrontEndException('course.collection.invalid.course.id', 'ajaxerrors');

        if($course->getRemoved())
            throw new FrontEndException('course.collection.course.removed', 'ajaxerrors');

        if($course->getState()->getState() != 'Finished')
            throw new FrontEndException('course.collection.course.not.published', 'ajaxerrors');

        $courseItem = new CourseCollectionItems();
        $courseItem->setCourse($course);
        $courseItem->setUser($user);
        $courseItem->setInsertDateTime(new \DateTime());

        $this->manager->persist($courseItem);
        $this->manager->flush();
    }

    public function removeCollectionItem($args)
    {
        $item = $this->manager->getRepository('AppBundle:Course\CourseCollectionItems')->find($args['id']);
        if($item == null)
            throw new EntityNotFoundException();

        if($this->authorizationService->canEditEntity($item))
        {
            $courseId = $item->getCourseId();
            $this->manager->remove($item);
            $this->manager->flush();

            return array('courseId' => $courseId);
        }
        throw new AccessDeniedException();
    }

    /**
     * Returns an unique code that is used to determine which implementation
     * of this interface should be used for the ajax call
     *
     * @return string
     */
    public function getUniqueCode()
    {
        //Course COllection Ajax Service
        return 'CCOAS1';
    }

    /**
     * Returns a list of public methods defined within its class that
     * can be accessed from an ajax call
     *
     * @return array
     */
    public function getSubscribedMethods()
    {
        return array('addToCollection', 'removeCollectionItem');
    }
}