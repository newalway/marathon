<?php

namespace ProjectBundle\EventListener;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Event\FormEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Listener responsible to change the redirection at the end of the password resetting
 */
class PasswordResettingListener implements EventSubscriberInterface
{
    private $router;

    public function __construct(UrlGeneratorInterface $router)
	{
        $this->router = $router;
    }

	/**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FOSUserEvents::RESETTING_RESET_SUCCESS => 'onPasswordResettingSuccess',
        ];
    }

    public function onPasswordResettingSuccess(FormEvent $event)
	{
        $url = $this->router->generate('default_target_password_resetting');
        $event->setResponse(new RedirectResponse($url));
    }
}
