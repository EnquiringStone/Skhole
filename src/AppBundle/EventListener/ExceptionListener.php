<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 01-Dec-15
 * Time: 22:38
 */

namespace AppBundle\EventListener;


use AppBundle\Exception\TranslatableException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Translation\Translator;

class ExceptionListener implements EventSubscriberInterface
{
    private $translator;

    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        if($exception instanceof TranslatableException && $event->getRequest()->isXmlHttpRequest())
        {
            $message = $this->translator->trans($exception->getTranslationCode(), $exception->getParams());
            $response = new Response(json_encode($message, 412));
            $response->headers->set('Content-Type', 'application/json');
            $event->setResponse($response);
        }
        return;
    }

    public static function getSubscribedEvents()
    {
        return array(KernelEvents::EXCEPTION => array(array('onKernelException', 17)));
    }
}