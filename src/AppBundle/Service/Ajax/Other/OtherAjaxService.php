<?php
/**
 * Created by PhpStorm.
 * User: EnquiringStone
 * Date: 06-Aug-16
 * Time: 18:03
 */

namespace AppBundle\Service\Ajax\Other;


use AppBundle\Authorization\AuthorizationService;
use AppBundle\Exception\FrontEndException;
use AppBundle\Interfaces\AjaxInterface;
use AppBundle\Validator\Validator;
use Doctrine\ORM\EntityManager;

class OtherAjaxService implements AjaxInterface
{
    /**
     * @var \Twig_Environment
     */
    private $environment;
    /**
     * @var AuthorizationService
     */
    private $authorizationService;
    /**
     * @var EntityManager
     */
    private $manager;
    /**
     * @var \Swift_Mailer
     */
    private $mailer;
    /**
     * @var Validator
     */
    private $validator;
    /**
     * @var
     */
    private $emailTo;

    function __construct(\Twig_Environment $environment, AuthorizationService $authorizationService, EntityManager $manager,
                            \Swift_Mailer $mailer, Validator $validator, $emailTo)
    {
        $this->environment = $environment;
        $this->authorizationService = $authorizationService;
        $this->manager = $manager;
        $this->mailer = $mailer;
        $this->validator = $validator;
        $this->emailTo = $emailTo;
    }

    public function sendContactEmail($args)
    {
        $this->validator->validate($args, 'Contact');

        $subject = $this->manager->getRepository('AppBundle:EmailSubjects')->find($args['subject']);
        if($subject == null) throw new FrontEndException('contact.subject.invalid', 'ajaxerrors');

        $name = $args['firstName'] . ' ' . $args['surname'];

        $user = null;
        if($this->authorizationService->isAuthorized()) $user = $this->authorizationService->getAuthorizedUserOrThrowException();

        $message = \Swift_Message::newInstance()
            ->setSubject($subject->getSubject())
            ->setFrom($args['email'])
            ->setTo($this->emailTo)
            ->setBody(
                $this->environment->render('mail/contact.mail.html.twig', array('user' => $user, 'name' => $name, 'message' => $args['message'])),
                'text/html'
            );

        $this->mailer->send($message);

        return array('html' => $this->environment->render(':ajax/other:contact.finish.html.twig'));
    }

    /**
     * Returns an unique code that is used to determine which implementation
     * of this interface should be used for the ajax call
     * @return string
     */
    public function getUniqueCode()
    {
        //OtherAjaxService 1
        return 'OAS1';
    }

    /**
     * Returns a list of public methods defined within its class that
     * can be accessed from an ajax call
     * @return array
     */
    public function getSubscribedMethods()
    {
        return array('sendContactEmail');
    }
}