<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 27-Feb-16
 * Time: 19:50
 */

namespace AppBundle\Service\Ajax\Teach;


use AppBundle\Authorization\AuthorizationService;
use AppBundle\Entity\Course\CourseAnnouncements;
use AppBundle\Entity\Course\CourseAnswers;
use AppBundle\Entity\Course\CourseCards;
use AppBundle\Entity\Course\CoursePages;
use AppBundle\Entity\Course\CourseQuestions;
use AppBundle\Entity\Course\CourseResources;
use AppBundle\Entity\Course\Courses;
use AppBundle\Entity\Course\CourseSchedules;
use AppBundle\Entity\Providers;
use AppBundle\Entity\Tags;
use AppBundle\Entity\Teachers;
use AppBundle\Enum\ContextEnum;
use AppBundle\Exception\FrontEndException;
use AppBundle\Interfaces\AjaxInterface;
use AppBundle\Transformer\TransformManager;
use AppBundle\Util\FileHelper;
use AppBundle\Util\SecurityHelper;
use AppBundle\Util\TransformerHelper;
use AppBundle\Util\ValidatorHelper;
use AppBundle\Validator\Validator;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class CreateCourseAjaxService implements AjaxInterface
{
    /**
     * @var EntityManager
     */
    private $manager;
    /**
     * @var Validator
     */
    private $validator;
    /**
     * @var TransformManager
     */
    private $transformer;
    /**
     * @var Router
     */
    private $router;
    /**
     * @var \Twig_Environment
     */
    private $environment;
    /**
     * @var AuthorizationService
     */
    private $authorizationService;

    public function __construct(EntityManager $manager, Validator $validator, TransformManager $transformer,
                                Router $router, \Twig_Environment $environment, AuthorizationService $authorizationService)
    {
        $this->manager = $manager;
        $this->validator = $validator;
        $this->transformer = $transformer;
        $this->router = $router;
        $this->environment = $environment;
        $this->authorizationService = $authorizationService;
    }

    public function saveCourseInformationValues($args)
    {
        $this->idSpecified($args);
        $entity = $this->manager->getRepository('AppBundle:Course\Courses')->find($args['id']);
        if($entity == null) throw new EntityNotFoundException();
        $this->hasEditRights($entity);

        unset($args['id']);

        foreach($entity->getTags() as $tag)
        {
            $entity->removeTag($tag);
        }

        $existingTags = array();
        $checkTags = array();

        if($args['tags'] != null)
        {
            $stringTags = explode(',', $args['tags']);
            foreach($stringTags as $stringTag)
            {
                $tagEntity = $this->manager->getRepository('AppBundle:Tags')->findOneBy(array('tag' => strtolower($stringTag)));
                if($tagEntity != null)
                {
                    $existingTags[$tagEntity->getId()] = $tagEntity;
                } else
                {
                    $checkTags[] = $stringTag;
                }
            }
            $args['tags'] = $checkTags;
        } else {
            unset($args['tags']);
        }

        if($args['level'] == '')
        {
            if($entity->getLevel() != null)
                $entity->setLevel(null);

            unset($args['level']);
        }

        $this->validator->validate($args, 'courses');

        foreach($existingTags as $tag)
        {
            if(!$entity->getTags()->contains($tag))
            {
                $entity->addTag($tag);
            }
        }
        foreach($checkTags as $tag)
        {
            $object = new Tags();
            $object->setTag(strtolower($tag));
            $entity->addTag($object);

            $this->manager->persist($object);
        }

        $entity->setName($args['name']);
        $entity->setDescription($args['description']);
        $entity->setLanguage($this->manager->getRepository('AppBundle:Course\CourseLanguages')->findOneBy(array('languageCode' => $args['language'])));
        if(array_key_exists('level', $args))
            $entity->setLevel($this->manager->getRepository('AppBundle:Course\CourseLevels')->findOneBy(array('levelShort' => $args['level'])));
        $entity->setDifficulty(intval($args['difficulty']));
        $entity->setEstimatedDuration(intval($args['estimatedDuration']));

        $this->manager->persist($entity);
        $this->manager->flush();
    }

    public function saveCardIntroduction($args)
    {
        $this->idSpecified($args);
        $entity = $this->manager->getRepository('AppBundle:Course\Courses')->find($args['id']);
        if($entity == null) throw new EntityNotFoundException();
        $this->hasEditRights($entity);
        unset($args['id']);

        if($args['description'] == '' && $args['youtubeUrl'] == '')
            throw new FrontEndException('course.edit.card.youtube.url.or.description', 'ajaxerrors');

        if($args['youtubeUrl'] == '')
            unset($args['youtubeUrl']);

        $this->validator->validate($args, 'CourseCards');

        $card = $entity->getCourseCard();
        if($card == null) {
            $card = new CourseCards();
            $card->setCourse($entity);
        }

        $card->setDescription($args['description']);
        $card->setName($args['name']);
        if(array_key_exists('youtubeUrl', $args))
        {
            $card->setYoutubeUrl($args['youtubeUrl']);
            $card->setYoutubeEmbedUrl(TransformerHelper::createEmbeddedYoutubeUrl($args['youtubeUrl']));
        }
        else
        {
            $card->setYoutubeUrl(null);
            $card->setYoutubeEmbedUrl(null);
        }

        $this->manager->persist($card);
        $this->manager->flush();
    }

    public function saveCardProvider($args)
    {
        $this->idSpecified($args);
        $entity = $this->manager->getRepository('AppBundle:Course\Courses')->find($args['id']);
        if($entity == null) throw new EntityNotFoundException();
        $this->hasEditRights($entity);
        unset($args['id']);

        $this->validator->validate($args, 'CourseCards');

        $card = $entity->getCourseCard();
        if($card == null)
        {
            $card = new CourseCards();
            $card->setCourse($entity);
        }

        $card->setPriorKnowledge($args['priorKnowledge']);

        $this->manager->persist($card);
        $this->manager->flush();
    }

    public function saveSchedule($args)
    {
        $this->idSpecified($args);
        $entity = $this->manager->getRepository('AppBundle:Course\Courses')->find($args['id']);
        if($entity == null) throw new EntityNotFoundException();
        $this->hasEditRights($entity);
        unset($args['id']);

        $this->validator->validate($args, 'CourseSchedules');

        $schedule = $entity->getCourseSchedule();
        if($schedule == null)
        {
            $schedule = new CourseSchedules();
            $schedule->setCourse($entity);
            $schedule->setIsUndesirable(false);
        }
        $schedule->setSchedule($args['schedule']);

        $this->manager->persist($schedule);
        $this->manager->flush();
    }

    public function saveTeacher($args)
    {
        $this->idSpecified($args);
        $entity = $this->manager->getRepository('AppBundle:Course\Courses')->find($args['id']);
        if($entity == null) throw new EntityNotFoundException();
        $user = $this->hasEditRights($entity);
        unset($args['id']);

        $this->validator->validate($args, 'CourseTeachers');

        if($entity->getCourseCard() == null)
        {
            $courseCard = new CourseCards();
            $courseCard->setCourse($entity);
            $entity->setCourseCard($courseCard);
            $this->manager->persist($courseCard);
        }
        $isNew = false;
        if($args['teacherId'] != null && intval($args['teacherId']) > 0)
        {
            $teacher = $this->manager->getRepository('AppBundle:Teachers')->find($args['teacherId']);
            if($teacher == null)
                throw new \Exception('Teacher not found');
            $this->hasEditRights($teacher);
        }
        else
        {
            $teacher = new Teachers();
            $teacher->setIsUndesirable(false);
            $teacher->setInsertUser($user);
            $entity->getCourseCard()->addTeacher($teacher);
            $isNew = true;
        }

        $teacher->setDescription($args['description']);
        $teacher->setName($args['name']);
        if(!($args['pictureUrl'] == '' || $args['pictureUrl'] == null))
        {
            $teacher->setPictureUrl($args['pictureUrl']);
        }
        $this->manager->persist($teacher);

        $this->manager->flush();

        if($isNew)
            return array('html' => $this->transformer->getTransformerByName('CourseTeachersTransformer')->transformToAjaxResponse(array($teacher), ContextEnum::SELF_CONTEXT));

        return array('id' => $teacher->getId());
    }

    public function removeCourseTeachers($args)
    {
        $this->idSpecified($args);
        $entity = $this->manager->getRepository('AppBundle:Course\Courses')->find($args['id']);
        if($entity == null) throw new EntityNotFoundException();
        $this->hasEditRights($entity);
        unset($args['id']);

        foreach($args['ids'] as $id)
        {
            $teacher = $this->manager->getRepository('AppBundle:Teachers')->find($id);
            if($teacher != null)
            {
                $this->hasEditRights($teacher);
                if($entity->getCourseCard() == null)
                    throw new \Exception('No course card defined');

                $picture = $teacher->getPictureUrl();

                $fileHelper = new FileHelper();
                $absolute = $fileHelper->getWebDir() . $picture;
                unlink($absolute);

                $entity->getCourseCard()->removeTeacher($teacher);
                $this->manager->remove($teacher);
            }
        }
        $this->manager->flush();
    }

    public function saveCourseAnnouncement($args)
    {
        $this->idSpecified($args);
        $entity = $this->manager->getRepository('AppBundle:Course\Courses')->find($args['id']);
        if($entity == null) throw new EntityNotFoundException();
        $user = $this->hasEditRights($entity);
        unset($args['id']);

        $this->validator->validate($args, 'CourseAnnouncements');

        $announcement = new CourseAnnouncements();
        $announcement->setCourse($entity);
        $announcement->setContents($args['contents']);
        $announcement->setTitle($args['title']);
        $announcement->setIsUndesirable(false);

        if($args['teacherId'] > 0)
        {
            $teacher = $this->manager->getRepository('AppBundle:Teachers')->find($args['teacherId']);
            if($teacher == null) throw new EntityNotFoundException();
            if($teacher->getUserInsertedId() != $user->getId())
                throw new AccessDeniedException();

            $announcement->setTeacher($teacher);
        }

        $this->manager->persist($announcement);
        $entity->addCourseAnnouncement($announcement);
        $this->manager->flush();

        return array('html' =>
            $this->transformer->getTransformerByName('CourseAnnouncementsTransformer')->transformToAjaxResponse(array($announcement), ContextEnum::SELF_CONTEXT));
    }

    public function removeCourseAnnouncement($args)
    {
        $this->idSpecified($args);
        $entity = $this->manager->getRepository('AppBundle:Course\Courses')->find($args['id']);
        if($entity == null) throw new EntityNotFoundException();
        $this->hasEditRights($entity);
        unset($args['id']);

        foreach($args['ids'] as $id)
        {
            $announcement = $this->manager->getRepository('AppBundle:Course\CourseAnnouncements')->find($id);
            if($announcement != null)
            {
                $this->hasEditRights($announcement);
                $entity->removeCourseAnnouncement($announcement);
                $this->manager->remove($announcement);
            }
        }
        $this->manager->flush();
    }

    public function removeCourseProviders($args)
    {
        $this->idSpecified($args);
        $entity = $this->manager->getRepository('AppBundle:Course\Courses')->find($args['id']);
        if($entity == null) throw new EntityNotFoundException();
        $this->hasEditRights($entity);
        unset($args['id']);

        if($entity->getCourseCard() == null)
            throw new \Exception('No course card defined');

        foreach($args['ids'] as $id)
        {
            $provider = $this->manager->getRepository('AppBundle:Providers')->find($id);
            if($provider != null)
            {
                $this->hasEditRights($provider);
                $entity->getCourseCard()->removeProvider($provider);
                $this->manager->remove($provider);
            }
        }
        $this->manager->flush();
    }

    public function saveProvider($args)
    {
        $this->idSpecified($args);
        $entity = $this->manager->getRepository('AppBundle:Course\Courses')->find($args['id']);
        if($entity == null) throw new EntityNotFoundException();
        $user = $this->hasEditRights($entity);
        unset($args['id']);

        $this->validator->validate($args, 'CourseProviders');

        if($entity->getCourseCard() == null)
        {
            $courseCard = new CourseCards();
            $courseCard->setCourse($entity);
            $entity->setCourseCard($courseCard);
            $this->manager->persist($courseCard);
        }
        $isNew = false;
        if($args['providerId'] != null && intval($args['providerId']) > 0)
        {
            $provider = $this->manager->getRepository('AppBundle:Providers')->find($args['providerId']);
            if($provider == null)
                throw new \Exception('Provider not found');
            $this->hasEditRights($provider);
        } else {
            $provider = new Providers();
            $provider->setInsertUser($user);
            $entity->getCourseCard()->addProvider($provider);
            $isNew = true;
        }

        $provider->setDescription($args['description']);
        $provider->setName($args['name']);

        $this->manager->persist($provider);

        $this->manager->flush();

        if($isNew)
            return array('html' => $this->transformer->getTransformerByName('CourseProvidersTransformer')->transformToAjaxResponse(array($provider), ContextEnum::SELF_CONTEXT));

        return array('id' => $provider->getId());
    }

    public function removeCourses($args)
    {
        $this->idSpecified($args);
        $entity = $this->manager->getRepository('AppBundle:Course\Courses')->find($args['id']);
        if($entity == null) throw new EntityNotFoundException();
        $this->hasEditRights($entity);
        unset($args['id']);

        foreach($args['ids'] as $id)
        {
            $course = $this->manager->getRepository('AppBundle:Course\Courses')->find($id);
            if($course != null)
            {
                $this->hasEditRights($course);
                $course->setRemoved(true);
            }
        }
        $this->manager->flush();
    }

    public function saveCourseInstruction($args)
    {
        $this->idSpecified($args);
        $repo = $this->manager->getRepository('AppBundle:Course\Courses');
        $entity = $repo->find($args['id']);
        if($entity == null) throw new EntityNotFoundException();
        $this->hasEditRights($entity);
        unset($args['id']);

        $type = $args['type'];
        unset($args['type']);

        $pageId = null;
        if(array_key_exists('pageId', $args))
        {
            $pageId = $args['pageId'];
            unset($args['pageId']);
        }
        $this->validator->validate($args, 'CoursePages', array('type' => $type));

        if($type == 'video-text' && $args['youtubeUrl'] == '' && $args['contents'] == '')
            throw new FrontEndException('course.edit.card.youtube.url.or.description', 'ajaxerrors');

        if($pageId != null)
        {
            $page = $this->manager->getRepository('AppBundle:Course\CoursePages')->find($pageId);
            if($page->getCourseId() != $entity->getId())
                throw new AccessDeniedException();
        }
        else
            $page = new CoursePages();

        if($type == 'video-text' && $args['youtubeUrl'] != '')
        {
            $page->setYoutubeEmbedUrl(TransformerHelper::createEmbeddedYoutubeUrl($args['youtubeUrl']));
            $page->setYoutubeUrl($args['youtubeUrl']);
        }
        $page->setContents($args['contents']);
        $page->setTitle($args['title']);

        if($pageId == null)
        {
            $page->setIsUndesirable(false);
            $page->setCourse($entity);
            $page->setPageType($this->manager->getRepository('AppBundle:Course\CoursePageTypes')->findOneBy(array('type' => $type)));
            $page->setPageOrder($repo->getHighestOrder($entity->getId()) + 1);
            $entity->addCoursePage($page);
        }

        $this->manager->persist($page);

        $this->manager->flush();
    }

    public function updatePageOrder($args)
    {
        $this->idSpecified($args);
        $repo = $this->manager->getRepository('AppBundle:Course\Courses');
        $entity = $repo->find($args['id']);
        if($entity == null) throw new EntityNotFoundException();
        $this->hasEditRights($entity);
        unset($args['id']);

        $newPage = $this->manager->getRepository('AppBundle:Course\CoursePages')->find($args['pageId']);
        if($newPage == null) throw new EntityNotFoundException();
        if($newPage->getCourseId() != $entity->getId())
            throw new AccessDeniedException();
        if($newPage->getPageOrder() == $args['order']) return;

        $allPages = $this->manager->getRepository('AppBundle:Course\CoursePages')->findBy(array('courseId' => $entity->getId()), array('pageOrder' => 'ASC'));

        $currentOrder = $newPage->getPageOrder();
        $givenOrder = $args['order'];
        $difference = $givenOrder - $currentOrder;
        $i = 0;
        $targetReached = false;

        if($difference < 0)
        {
            $difference = abs($difference);
            $allPages = $this->manager->getRepository('AppBundle:Course\CoursePages')->findBy(array('courseId' => $entity->getId()), array('pageOrder' => 'DESC'));
            foreach($allPages as $page)
            {
                if(!$targetReached && $page->getPageOrder() == $currentOrder)
                    $targetReached = true;
                elseif($targetReached)
                {
                    if($i < $difference)
                    {
                        $page->setPageOrder($page->getPageOrder() + 1);
                        $i ++;
                    }
                }
            }
        }
        else
        {
            foreach($allPages as $page)
            {
                if(!$targetReached && $page->getPageOrder() == $currentOrder)
                    $targetReached = true;
                elseif($targetReached)
                {
                    if($i < $difference)
                    {
                        $page->setPageOrder($page->getPageOrder() - 1);
                        $i ++;
                    }
                }
            }
        }
        $newPage->setPageOrder($args['order']);

        $this->manager->flush();

        $this->refreshOrder($entity);
    }

    public function removePages($args)
    {
        $this->idSpecified($args);
        $entity = $this->manager->getRepository('AppBundle:Course\Courses')->find($args['id']);
        if($entity == null) throw new EntityNotFoundException();
        $this->hasEditRights($entity);
        unset($args['id']);

        foreach($args['ids'] as $id)
        {
            $page = $this->manager->getRepository('AppBundle:Course\CoursePages')->find($id);
            if($page != null)
            {
                if($page->getCourseId() != $entity->getId())
                    throw new AccessDeniedException();

                $entity->removeCoursePage($page);
                $this->manager->remove($page);
            }
        }
        $this->manager->flush();

        $this->refreshOrder($entity);
    }

    public function saveMultipleChoice($args)
    {
        $this->idSpecified($args);
        $courseRepo = $this->manager->getRepository('AppBundle:Course\Courses');
        $entity = $courseRepo->find($args['id']);
        if($entity == null) throw new EntityNotFoundException();
        $this->hasEditRights($entity);
        unset($args['id']);
        if($entity->getState()->getStateCode() == 'OK') throw new FrontEndException('course.create.is.published.create.exercise', 'ajaxerrors');

        $pageRepo = $this->manager->getRepository('AppBundle:Course\CoursePages');
        $questionRepo = $this->manager->getRepository('AppBundle:Course\CourseQuestions');
        $newPage = false;

        if($args['pageId'] > 0)
            $page = $pageRepo->find($args['pageId']);
        else
        {
            $page = new CoursePages();
            $page->setTitle(array_key_exists('pageTitle', $args) ? $args['pageTitle'] : '');
            $page->setPageOrder($courseRepo->getHighestOrder($entity->getId()) + 1);
            $page->setPageType($this->manager->getRepository('AppBundle:Course\CoursePageTypes')->findOneBy(array('type' => 'exercise')));
            $page->setCourse($entity);
            $page->setIsUndesirable(false);
            $this->manager->persist($page);
            $entity->addCoursePage($page);
            $newPage = true;
        }

        if(!$newPage && ($page->getCourseId() != $entity->getId() || $page->getPageType()->getType() != 'exercise'))
            throw new AccessDeniedException();

        if(sizeof($args['answers']) < 2)
            throw new FrontEndException('course.edit.answer.too.few', 'ajaxerrors');

        $this->validator->validate(array('question' => $args['question']), 'CourseQuestions');

        if($page->getQuestions()->count() > 10)
            throw new FrontEndException('course.edit.exercise.too.many', 'ajaxerrors');

        if($args['questionId'] > 0)
        {
            $question = $questionRepo->find($args['questionId']);
            if($question->getCoursePageId() != $page->getId())
                throw new AccessDeniedException();
        }
        else
        {
            $question = new CourseQuestions();
            $question->setCoursePage($page);
            $question->setQuestionOrder($pageRepo->getQuestionHighestOrder($page->getId()) + 1);
            $question->setQuestionType($this->manager->getRepository('AppBundle:Course\CourseQuestionTypes')->findOneBy(array('type' => 'multiple-choice')));
            $question->setQuestion('');
            $this->manager->persist($question);
        }
        $question->setQuestion($args['question']);
        $page->addQuestion($question);

        $hasNoCorrectAnswers = true;
        foreach($args['answers'] as $answer)
        {
            $this->validator->validate($answer, 'CourseAnswers');
            if($answer['answerId'] > 0)
            {
                $courseAnswer = $this->manager->getRepository('AppBundle:Course\CourseAnswers')->find($answer['answerId']);
                if($courseAnswer->getCourseQuestionId() != $question->getId())
                    throw new AccessDeniedException();
            }
            else
            {
                $courseAnswer = new CourseAnswers();
                $courseAnswer->setAnswerOrder($questionRepo->getAnswerHighestOrder($question->getId()) + 1);
                $courseAnswer->setCourseQuestion($question);
                $this->manager->persist($courseAnswer);
            }
            $courseAnswer->setAnswer($answer['answer']);
            $courseAnswer->setIsCorrect($answer['correct']);

            $question->addCourseAnswer($courseAnswer);

            if((bool) $answer['correct'])
                $hasNoCorrectAnswers = false;
        }

        if($hasNoCorrectAnswers)
            throw new FrontEndException('course.edit.answer.no.correct', 'ajaxerrors');

        $this->manager->flush();

        $html = $this->transformer->getTransformerByName('CourseQuestionsTransformer')->transformToAjaxResponse($page->getQuestions(), ContextEnum::SELF_CONTEXT);
        return array('html' => $html, 'pageId' => $page->getId());
    }

    function addCustomExercisePage($args)
    {
        $this->idSpecified($args);
        $courseRepo = $this->manager->getRepository('AppBundle:Course\Courses');
        $entity = $courseRepo->find($args['id']);
        if($entity == null) throw new EntityNotFoundException();
        $this->hasEditRights($entity);
        unset($args['id']);

        if($entity->getState()->getStateCode() == 'OK') throw new FrontEndException('course.create.is.published.create.exercise', 'ajaxerrors');

        $this->validator->validate($args, 'CourseExercise');

        $page = new CoursePages();

        $page->setCourse($entity);
        $page->setPageType($this->manager->getRepository('AppBundle:Course\CoursePageTypes')->findOneBy(array('type' => 'exercise')));
        $page->setTitle($args['title']);
        $page->setPageOrder($courseRepo->getHighestOrder($entity->getId()) + 1);
        $page->setIsUndesirable(false);

        $this->manager->persist($page);
        $this->manager->flush();

        $url = $this->router->generate('app_teach_edit_course_page_page', array('id' => $entity->getId(), 'pageType' => 'custom',
            'name' => 'questions', 'pageId' => $page->getId()));

        return array('url' => $url);
    }

    function loadQuestion($args)
    {
        $this->idSpecified($args);
        $courseRepo = $this->manager->getRepository('AppBundle:Course\Courses');
        $entity = $courseRepo->find($args['id']);
        if($entity == null) throw new EntityNotFoundException();
        $this->hasEditRights($entity);
        unset($args['id']);

        $page = $this->manager->getRepository('AppBundle:Course\CoursePages')->find($args['pageId']);
        if($page == null) throw new EntityNotFoundException();
        if($page->getCourseId() != $entity->getId()) throw new AccessDeniedException();

        if($page->getQuestions()->count() == 10 && $args['questionId'] <= 0)
            throw new FrontEndException('course.edit.exercise.too.many', 'ajaxerrors');


        $questionParam = array();
        if($args['questionId'] > 0)
        {
            $question = $this->manager->getRepository('AppBundle:Course\CourseQuestions')->find($args['questionId']);
            if($question == null) throw new EntityNotFoundException();
            if($question->getCoursePage()->getCourseId() != $entity->getId()) throw new AccessDeniedException();
            $questionParam = array('question' => $question);
        }

        $type = $args['type'];
        $html = '';
        if($type == 'multiple-choice')
        {
            $html = $this->environment->render(':ajax/create-courses/questions:questions.add.multiple.choice.html.twig', $questionParam);
        }
        else if($type == 'open-question')
        {
            $html = $this->environment->render(':ajax/create-courses/questions:questions.add.open.question.html.twig', $questionParam);
        }
        else if($type == 'overview')
        {
            $html = $this->environment->render(':ajax/create-courses/questions:question.order.overview.html.twig',
                array('course' => $entity, 'exercise' => $page));
        }
        if($html == '')
            throw new \Exception('type '.$type.' not supported');

        return array('html' => $html);
    }

    function saveCustomQuestion($args)
    {
        $this->idSpecified($args);
        $courseRepo = $this->manager->getRepository('AppBundle:Course\Courses');
        $entity = $courseRepo->find($args['id']);
        if($entity == null) throw new EntityNotFoundException();
        $this->hasEditRights($entity);
        unset($args['id']);
        if($entity->getState()->getStateCode() == 'OK') throw new FrontEndException('course.create.is.published.create.exercise', 'ajaxerrors');

        $pageRepo = $this->manager->getRepository('AppBundle:Course\CoursePages');
        $questionRepo = $this->manager->getRepository('AppBundle:Course\CourseQuestions');

        $questionDetails = array('question' => $args['question'], 'answerIndication' => null);
        if(array_key_exists('answerIndication', $args))
            $questionDetails['answerIndication'] = $args['answerIndication'];

        $this->validator->validate($questionDetails, 'CourseQuestions');

        $page = $pageRepo->find($args['pageId']);
        if($page == null) throw new EntityNotFoundException();
        if($page->getCourseId() != $entity->getId()) throw new AccessDeniedException();
        if($page->getQuestions()->count() == 10 && $args['questionId'] <= 0) throw new FrontEndException('course.edit.exercise.too.many', 'ajaxerrors');

        if($args['questionId'] > 0)
        {
            $question = $questionRepo->find($args['questionId']);
            if($question == null) throw new EntityNotFoundException();
            if($question->getCoursePageId() != $page->getId()) throw new AccessDeniedException();
        }
        else
        {
            $question = new CourseQuestions();
            $question->setQuestionType($this->manager->getRepository('AppBundle:Course\CourseQuestionTypes')->findOneBy(array('type' => $args['type'])));
            $question->setCoursePage($page);
            $question->setQuestionOrder($pageRepo->getQuestionHighestOrder($page->getId()) + 1);
            $page->addQuestion($question);
        }
        $question->setQuestion($questionDetails['question']);
        $question->setAnswerIndication($questionDetails['answerIndication']);
        $this->manager->persist($question);
        $this->manager->flush();

        if($question->getQuestionType()->getType() == 'multiple-choice')
            $this->saveCustomAnswers($args['answers'], $question, $args['questionId'] <= 0);

        return array('html' => $this->environment->render(':ajax/create-courses/questions:question.order.overview.html.twig', array('exercise' => $page, 'course' => $entity)));
    }

    function saveCustomAnswers($answers, CourseQuestions $question, $isNew)
    {
        $noCorrectAnswers = true;
        $highestOrder = $this->manager->getRepository('AppBundle:Course\CourseQuestions')->getAnswerHighestOrder($question->getId());
        foreach($answers as $answer)
        {
            try
            {
                $this->validator->validate($answer, 'CourseAnswers');
                if($answer['answerId'] > 0)
                {
                    $answerEntity = $this->manager->getRepository('AppBundle:Course\CourseAnswers')->find($answer['answerId']);
                    if($answerEntity == null)
                        throw new EntityNotFoundException();

                    if($answerEntity->getCourseQuestionId() != $question->getId())
                        throw new AccessDeniedException();
                }
                else
                {
                    $highestOrder++;
                    $answerEntity = new CourseAnswers();
                    $answerEntity->setCourseQuestion($question);
                    $answerEntity->setAnswerOrder($highestOrder);
                    $question->addCourseAnswer($answerEntity);
                }
                $answerEntity->setAnswer($answer['answer']);
                $answerEntity->setIsCorrect($answer['correct'] == 'true');
                $this->manager->persist($answerEntity);

                if($answer['correct'] == 'true') {
                    $noCorrectAnswers = false;
                }
            }
            catch (\Exception $e)
            {
                if ($isNew)
                {
                    $this->manager->remove($question);
                    $this->manager->flush();
                }
                throw $e;
            }
        }
        if($noCorrectAnswers)
        {
            if($isNew)
            {
                $this->manager->remove($question);
                $this->manager->flush();
            }
            throw new FrontEndException('course.edit.answer.no.correct', 'ajaxerrors');
        }
        $this->manager->flush();
    }

    function removeCustomQuestions($args)
    {
        $this->idSpecified($args);
        $courseRepo = $this->manager->getRepository('AppBundle:Course\Courses');
        $entity = $courseRepo->find($args['id']);
        if($entity == null) throw new EntityNotFoundException();
        $this->hasEditRights($entity);
        unset($args['id']);

        $page = $this->manager->getRepository('AppBundle:Course\CoursePages')->find($args['pageId']);
        if($page == null) throw new EntityNotFoundException();
        if($page->getCourseId() != $entity->getId()) throw new AccessDeniedException();

        foreach($args['ids'] as $id)
        {
            if($id > 0)
            {
                $question = $this->manager->getRepository('AppBundle:Course\CourseQuestions')->find($id);
                if($question == null) throw new EntityNotFoundException();
                if($question->getCoursePageId() != $page->getId())
                    throw new AccessDeniedException();
                $this->manager->remove($question);
            }
        }
        $this->manager->flush();

        $this->refreshQuestionOrder($page);

        return array('html' => $this->environment->render(':ajax/create-courses/questions:question.order.overview.html.twig', array('exercise' => $page, 'course' => $entity)));
    }

    function updateQuestionOrder($args)
    {
        $this->idSpecified($args);
        $repo = $this->manager->getRepository('AppBundle:Course\Courses');
        $entity = $repo->find($args['id']);
        if($entity == null) throw new EntityNotFoundException();
        $this->hasEditRights($entity);
        unset($args['id']);

        $newQuestion = $this->manager->getRepository('AppBundle:Course\CourseQuestions')->find($args['questionId']);
        if($newQuestion == null) throw new EntityNotFoundException();
        if($newQuestion->getCoursePage()->getCourseId() != $entity->getId())
            throw new AccessDeniedException();

        if($newQuestion->getQuestionOrder() == $args['order']) return;

        $allQuestions = $this->manager->getRepository('AppBundle:Course\CourseQuestions')->findBy(array('coursePageId' => $newQuestion->getCoursePage()->getId()), array('questionOrder' => 'ASC'));

        $currentOrder = $newQuestion->getQuestionOrder();
        $givenOrder = $args['order'];
        $difference = $givenOrder - $currentOrder;
        $i = 0;
        $targetReached = false;

        if($difference < 0)
        {
            $difference = abs($difference);
            $allQuestions = $this->manager->getRepository('AppBundle:Course\CourseQuestions')->findBy(array('coursePageId' => $newQuestion->getCoursePage()->getId()), array('questionOrder' => 'DESC'));
            foreach($allQuestions as $question)
            {
                if(!$targetReached && $question->getQuestionOrder() == $currentOrder)
                    $targetReached = true;
                elseif($targetReached)
                {
                    if($i < $difference)
                    {
                        $question->setQuestionOrder($question->getQuestionOrder() + 1);
                        $i ++;
                    }
                }
            }
        }
        else
        {
            foreach($allQuestions as $question)
            {
                if(!$targetReached && $question->getQuestionOrder() == $currentOrder)
                    $targetReached = true;
                elseif($targetReached)
                {
                    if($i < $difference)
                    {
                        $question->setQuestionOrder($question->getQuestionOrder() - 1);
                        $i ++;
                    }
                }
            }
        }
        $newQuestion->setQuestionOrder($args['order']);

        $this->manager->flush();

        $this->refreshQuestionOrder($newQuestion->getCoursePage());
    }

    function removeExtraAnswer($args)
    {
        $this->idSpecified($args);
        $repo = $this->manager->getRepository('AppBundle:Course\Courses');
        $entity = $repo->find($args['id']);
        if($entity == null) throw new EntityNotFoundException();
        $this->hasEditRights($entity);
        unset($args['id']);

        $page = $this->manager->getRepository('AppBundle:Course\CoursePages')->find($args['pageId']);
        if($page == null) throw new EntityNotFoundException();
        if($page->getCourseId() != $entity->getId()) throw new AccessDeniedException();

        $answer = $this->manager->getRepository('AppBundle:Course\CourseAnswers')->find($args['answerId']);
        if($answer == null) throw new EntityNotFoundException();
        if($answer->getCourseQuestion()->getCoursePageId() != $page->getId())
            throw new AccessDeniedException();

        $this->manager->remove($answer);
        $this->manager->flush();

        $this->refreshAnswerOrder($answer->getCourseQuestion());
    }

    function publishCourse($args)
    {
        $this->idSpecified($args);
        $repo = $this->manager->getRepository('AppBundle:Course\Courses');
        $entity = $repo->find($args['id']);
        if($entity == null) throw new EntityNotFoundException();
        $this->hasEditRights($entity);
        unset($args['id']);

        if($entity->canPublish())
        {
            foreach($entity->getCoursePages() as $page)
            {
                if($page instanceof CoursePages)
                {
                    if($page->getPageType()->getType() == 'exercise')
                    {
                        if($page->getQuestions()->count() == 0)
                            throw new FrontEndException('course.edit.publish.no.questions', 'ajaxerrors', array('%1' => $page->getTitle()));
                    }
                }
            }
            $entity->setStateChanged(new \DateTime());
            $entity->setPublishedDateTime(new \DateTime());
            $entity->setState($this->manager->getRepository('AppBundle:Course\CourseStates')->findOneBy(array('stateCode' => 'OK')));
            $this->manager->flush();

            return;
        }
        throw new FrontEndException('course.edit.publish', 'ajaxerrors');
    }

    function saveCourseResources($args)
    {
        $this->idSpecified($args);
        $repo = $this->manager->getRepository('AppBundle:Course\Courses');
        $entity = $repo->find($args['id']);
        if($entity == null) throw new EntityNotFoundException();
        $this->hasEditRights($entity);
        unset($args['id']);

        $this->validator->validate($args, 'CourseResources');

        $resources = $entity->getCourseResources();
        if($resources == null)
        {
            $resources = new CourseResources();
            $resources->setCourse($entity);
            $resources->setIsUndesirable(false);
            $this->manager->persist($resources);
        }

        if(array_key_exists('dropboxUrl', $args) && !ValidatorHelper::isStringNullOrEmpty($args['dropboxUrl']))
            $resources->setDropboxUrl($args['dropboxUrl']);

        if(array_key_exists('googleDriveUrl', $args) && !ValidatorHelper::isStringNullOrEmpty($args['googleDriveUrl']))
            $resources->setGoogleDriveUrl($args['googleDriveUrl']);

        $this->manager->flush();
    }

    function removeResourceUrl($args)
    {
        $this->idSpecified($args);
        $repo = $this->manager->getRepository('AppBundle:Course\Courses');
        $entity = $repo->find($args['id']);
        if($entity == null) throw new EntityNotFoundException();
        $this->hasEditRights($entity);
        unset($args['id']);

        $resource = $entity->getCourseResources();

        if($resource != null)
        {
            if($args['type'] == 'dropbox')
                $resource->setDropboxUrl(null);

            if($args['type'] == 'google')
                $resource->setGoogleDriveUrl(null);

            $this->manager->flush();
        }
    }

    function addResourceLink($args)
    {
        $this->idSpecified($args);

        $arguments = array();
        $arguments['id'] = $args['id'];

        if($args['type'] == 'google')
            $arguments['googleDriveUrl'] = $args['resourceUrl'];

        if($args['type'] == 'dropbox')
            $arguments['dropboxUrl'] = $args['resourceUrl'];

        $this->saveCourseResources($arguments);
    }

    function removeTeacherPicture($args)
    {
        if (!array_key_exists('teacherId', $args)) return;

        $teacher = $this->manager->getRepository('AppBundle:Teachers')->find($args['teacherId']);
        if ($teacher == null) return;

        $this->hasEditRights($teacher);
        $picture = $teacher->getPictureUrl();

        if (ValidatorHelper::isStringNullOrEmpty($picture)) return;

        $teacher->setPictureUrl(null);
        $this->manager->flush();

        $fileHelper = new FileHelper();
        $absoluteUrl = $fileHelper->getWebDir() . $picture;
        if (is_file($absoluteUrl))
            unlink($absoluteUrl);
    }

    /**
     * Returns an unique code that is used to determine which implementation
     * of this interface should be used for the ajax call
     *
     * @return string
     */
    public function getUniqueCode()
    {
        //Create Course Ajax Service
        return 'CCAS1';
    }

    /**
     * Returns a list of public methods defined within its class that
     * can be accessed from an ajax call
     *
     * @return array
     */
    public function getSubscribedMethods()
    {
        return array('saveCourseInformationValues', 'saveCardIntroduction', 'saveCardProvider', 'saveSchedule', 'saveTeacher',
            'removeCourseTeachers', 'saveCourseAnnouncement', 'removeCourseAnnouncement', 'removeCourseProviders', 'saveProvider',
            'removeCourses', 'saveCourseInstruction', 'updatePageOrder', 'removePages', 'saveMultipleChoice', 'addCustomExercisePage',
            'loadQuestion', 'saveCustomQuestion', 'removeCustomQuestions', 'updateQuestionOrder', 'removeExtraAnswer', 'publishCourse',
            'saveCourseResources', 'removeResourceUrl', 'addResourceLink', 'removeTeacherPicture');
    }

    private function idSpecified($args)
    {
        if(!array_key_exists('id', $args))
            throw new \Exception('Id should be specified');
    }

    /**
     * @param $entity
     * @return \AppBundle\Entity\User
     */
    private function hasEditRights($entity)
    {
        $user = $this->authorizationService->getAuthorizedUserOrThrowException();
        if(SecurityHelper::hasEditRights($entity, 'userInsertedId', $user->getId()))
            return $user;

        throw new AccessDeniedException();
    }

    private function refreshOrder(Courses $course)
    {
        $this->hasEditRights($course);

        $pages = $this->manager->getRepository('AppBundle:Course\CoursePages')->findBy(array('courseId' => $course->getId()), array('pageOrder' => 'ASC'));
        $i = 1;
        foreach($pages as $page)
        {
            $page->setPageOrder($i);
            $i ++;
        }
        $this->manager->flush();
    }

    private function refreshQuestionOrder(CoursePages $page)
    {
        $questions = $this->manager->getRepository('AppBundle:Course\CourseQuestions')->findBy(array('coursePageId' => $page->getId()), array('questionOrder' => 'ASC'));
        $i = 1;
        foreach($questions as $question)
        {
            $question->setQuestionOrder($i);
            $i ++;
        }
        $this->manager->flush();
    }

    private function refreshAnswerOrder(CourseQuestions $question)
    {
        $answers = $this->manager->getRepository('AppBundle:Course\CourseAnswers')->findBy(array('courseQuestionId' => $question->getId()), array('answerOrder' => 'ASC'));
        $i = 1;
        foreach($answers as $answer)
        {
            $answer->setAnswerOrder($i);
            $i ++;
        }
        $this->manager->flush();
    }
}