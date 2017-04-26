<?php

/*
 * This file is part of Twigony.
 *
 * © Timon F <dev@timonf.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Twigony\Bundle\FrameworkBundle\EventListener;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class ControllerListener
{
    /**
     * @var AuthorizationCheckerInterface
     */
    protected $authorizationChecker;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->authorizationChecker = $authorizationChecker;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function onKernelRequest(KernelEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $options = $event->getRequest()->attributes->get('options', []);

        if (!array_key_exists('roles', $options)) {
            return;
        }

        $roles = $options['roles'];

        if (is_scalar($roles)) {
            $roles = [$roles];
        }

        if (!$this->isGrantedByRoles($roles)) {
            throw new AccessDeniedHttpException(
                sprintf('Current user does not have any of the required roles ("%s")', implode(', ', $roles))
            );
        }
    }

    protected function isGrantedByRoles($roles)
    {
        foreach ($roles as $role) {
            if ($this->authorizationChecker->isGranted($role)) {
                return true;
            }
        }

        return false;
    }
}
