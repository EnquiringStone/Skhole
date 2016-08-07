<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\EventListener;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

/**
 * When visiting the homepage, this listener redirects the user to the most
 * appropriate localized version according to the browser settings.
 *
 * See http://symfony.com/doc/current/components/http_kernel/introduction.html#the-kernel-request-event
 *
 * @author Oleg Voronkovich <oleg-voronkovich@yandex.ru>
 */
class LocaleListener
{
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * List of supported locales.
     *
     * @var string[]
     */
    private $locales = array();

    /**
     * @var string
     */
    private $defaultLocale = '';
    /**
     * @var Session
     */
    private $session;

    /**
     * Constructor.
     *
     * @param UrlGeneratorInterface $urlGenerator
     * @param Session $session
     * @param string $locales Supported locales separated by '|'
     * @param string|null $defaultLocale
     */
    public function __construct(UrlGeneratorInterface $urlGenerator, Session $session, $locales, $defaultLocale = null)
    {
        $this->urlGenerator = $urlGenerator;

        $this->locales = explode('|', trim($locales));
        if (empty($this->locales))
        {
            throw new \UnexpectedValueException('The list of supported locales must not be empty.');
        }

        $this->defaultLocale = $defaultLocale ?: $this->locales[0];

        if (!in_array($this->defaultLocale, $this->locales))
        {
            throw new \UnexpectedValueException(sprintf('The default locale ("%s") must be one of "%s".', $this->defaultLocale, $locales));
        }

        // Add the default locale at the first position of the array,
        // because Symfony\HttpFoundation\Request::getPreferredLanguage
        // returns the first element when no an appropriate language is found
        array_unshift($this->locales, $this->defaultLocale);
        $this->session = $session;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if(!$this->session->isStarted()) $this->session->start();

        if(!$this->session->has('acceptedCookies')) $this->session->set('acceptedCookies', 0);

        $request->setSession($this->session);


        // Ignore sub-requests and all URLs but the homepage
        if ($request->isXmlHttpRequest() || '/' !== $request->getPathInfo())
        {
            return;
        }

        $preferredLanguage = $request->getPreferredLanguage($this->locales);

        $login = false;
        if(array_key_exists('login', $request->query->all()))
        {
            $login = $request->query->get('login');
        }

        $response = new RedirectResponse($this->urlGenerator->generate('app_home_dashboard_page', array('_locale' => $preferredLanguage, 'login' => $login)));
        $event->setResponse($response);
    }
}