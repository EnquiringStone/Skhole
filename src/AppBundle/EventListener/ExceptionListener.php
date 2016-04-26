<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 01-Dec-15
 * Time: 22:38
 */

namespace AppBundle\EventListener;


use AppBundle\Exception\FrontEndException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Translation\Translator;

class ExceptionListener implements EventSubscriberInterface
{
    private $translator;
    /**
     * @var \Twig_Environment
     */
    private $environment;

    public function __construct(Translator $translator, \Twig_Environment $environment)
    {
        $this->translator = $translator;
        $this->environment = $environment;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        if($exception instanceof FrontEndException)
        {
            $message = $this->translator->trans($exception->getTranslationCode(), $exception->getParams(), $exception->getTranslationDomain());
            $html = $this->environment->render(':errors:ajax.error.message.modal.html.twig', array('errorMessage' => $message));

            $response = new Response(json_encode(array('html' => $html), 400));
            $response->headers->set('Content-Type', 'application/json');
            $event->setResponse($response);
        }
        return;
    }

    public static function getSubscribedEvents()
    {
        return array(KernelEvents::EXCEPTION => array(array('onKernelException', 5)));
    }
}