<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 02-Dec-15
 * Time: 19:35
 */

namespace AppBundle\EventListener;

use AppBundle\Interfaces\AjaxInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class AjaxListener implements EventSubscriberInterface
{

    private $ajaxServices = array();
    private $ajaxConfig;

    public function __construct(array $ajaxConfig)
    {
        //Get all ajax services
        foreach(func_get_args() as $service)
        {
            if($service instanceof AjaxInterface) $this->ajaxServices[$service->getUniqueCode()] = $service;
        }
        $this->ajaxConfig = $ajaxConfig;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $params = $request->isMethod('GET') ? $request->query->all() : $request->request->all();

        if($request->isXmlHttpRequest() && array_key_exists($this->getIdentifier(), $params))
        {
            $validData = $this->validate($request, $params);
            $methodParams = $this->getMethodParams($params);

            $returnValues = call_user_func_array(array($this->ajaxServices[$validData['name']], $validData['method']), array($methodParams));

            if($returnValues != null && is_array($returnValues))
            {
                $returnValues = json_encode($returnValues);
            } else {
                $returnValues = json_encode(array('success' => true));
            }
            $response = new Response($returnValues, 200, array('Content-Type', 'application/json'));
            return $event->setResponse($response);
        }
    }

    public static function getSubscribedEvents()
    {
        return array(KernelEvents::REQUEST => array(array('onKernelRequest', 15)));
    }

    private function validate(Request $request, array $params)
    {
        //TODO Frontend exceptions and translations
        if(!array_key_exists($this->getIdentifier(), $params)) {
            throw new \Exception('exceptions.ajax.no.key', $request->getLocale());
        }

        if(!array_key_exists($params[$this->getIdentifier()], $this->ajaxServices)) {
            throw new \Exception('exceptions.ajax.missing.key', $request->getLocale(), array('AjaxKey' => $params[$this->getIdentifier()]));
        }

        if(!array_key_exists($this->getMethod(), $params)){
            throw new \Exception('exceptions.ajax.missing.method', $request->getLocale());
        }

        $service = $this->ajaxServices[$params[$this->getIdentifier()]];
        if($service instanceof AjaxInterface)
        {
            if(!in_array($params[$this->getMethod()], $service->getSubscribedMethods())){
                throw new \Exception('exceptions.ajax.no.method', $request->getLocale(), array('MethodName' => $params[$this->getMethod()], 'ClassCode' => $params[$this->getIdentifier()]));
            }
        }
        else
        {
            throw new \Exception('exceptions.ajax.no.interface', $request->getLocale());
        }

        return array('name' => $params[$this->getIdentifier()], 'method' => $params[$this->getMethod()]);
    }

    private function getMethodParams(array $requestParams)
    {
        $methodParams = $requestParams;
        unset($methodParams[$this->getMethod()]);
        unset($methodParams[$this->getIdentifier()]);
        return $methodParams;
    }

    private function getIdentifier()
    {
        return $this->ajaxConfig['identifier'];
    }

    private function getMethod()
    {
        return $this->ajaxConfig['method'];
    }
}