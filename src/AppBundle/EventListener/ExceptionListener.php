<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 01-Dec-15
 * Time: 22:38
 */

namespace AppBundle\EventListener;


use AppBundle\Exception\CourseRemovedException;
use AppBundle\Exception\DelayException;
use AppBundle\Exception\FrontEndException;
use Monolog\Logger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Translation\Translator;

class ExceptionListener implements EventSubscriberInterface
{
    private $translator;
    /**
     * @var \Twig_Environment
     */
    private $environment;
    /**
     * @var Logger
     */
    private $logger;

    public function __construct(Translator $translator, \Twig_Environment $environment, Logger $logger)
    {
        $this->translator = $translator;
        $this->environment = $environment;
        $this->logger = $logger;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        $this->logger->error('error occurred', array('message' => $exception->getMessage(), 'trace' => $exception->getTraceAsString()));
        if($event->getRequest()->isXmlHttpRequest())
        {
            if($exception instanceof FrontEndException)
            {
                $message = $this->translator->trans($exception->getTranslationCode(), $exception->getParams(), $exception->getTranslationDomain());
            }
            elseif ($exception instanceof AccessDeniedException)
            {
                $message = $this->translator->trans('authorization.error', array(), 'ajaxerrors');
            }
            else
            {
                $message = $this->translator->trans('general.error', array(), 'ajaxerrors');
            }

            $html = $this->environment->render(':errors:ajax.error.message.modal.html.twig', array('errorMessage' => $message, 'exception' => $exception));

            $response = new Response(json_encode(array('html' => $html), 400));
            $response->headers->set('Content-Type', 'application/json');
            $event->setResponse($response);
        }
        elseif($event->getRequest()->getPathInfo() != '/') //ignore all exceptions on the base
        {
            $session = $event->getRequest()->getSession();
            if($session != null && $session->has('locale')) $event->getRequest()->setLocale($session->get('locale'));
            $params = array('exception' => $exception, 'locale' => $event->getRequest()->getLocale(), 'menu' => '', 'subMenu' => '', 'context' => 'exception');
            if ($exception instanceof AccessDeniedException || $exception instanceof \Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException || $exception instanceof \Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException || $exception instanceof \Symfony\Component\Finder\Exception\AccessDeniedException)
            {
                $event->setResponse($this->getNormalResponse($this->environment->render(':exceptions:access.denied.exception.html.twig', $params)));
            }
            elseif ($exception instanceof NotFoundHttpException)
            {
                $event->setResponse($this->getNormalResponse($this->environment->render(':exceptions:page.not.found.exception.html.twig', $params)));
            }
            elseif ($exception instanceof DelayException)
            {
                $event->setResponse($this->getNormalResponse($this->environment->render(':exceptions:delay.exception.html.twig', $params)));
            }
            elseif ($exception instanceof CourseRemovedException)
            {
                $event->setResponse($this->getNormalResponse($this->environment->render(':exceptions:course.removed.exception.html.twig', $params)));
            }
            else
            {
                $event->setResponse($this->getNormalResponse($this->environment->render(':exceptions:default.exception.html.twig', $params)));
            }
        }
        return;
    }

    public static function getSubscribedEvents()
    {
        return array(KernelEvents::EXCEPTION => array(array('onKernelException', 5)));
    }

    /**
     * @param $html
     * @return Response
     */
    private function getNormalResponse($html)
    {
        $response = new Response($html, 200);
        $response->headers->set('Content-Type', 'text/html');
        return $response;
    }
}