<?php

/*
 * This file is part of Twigony.
 *
 * © Timon F <dev@timonf.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Twigony\Bundle\FrameworkBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Templating\EngineInterface;

/**
 * Twigony's Security Controller to offer a login
 *
 * All controller actions can be used in the router definition without having any own controller.
 *
 * @author Timon F <dev@timonf.de>
 */
class SecurityController
{
     /**
      * @var TemplateEngine
      */
     private $templateEngine; 
    
    /**
     * @var AuthenticationUtils
     */
    private $authenticationUtils;

    public function __construct(EngineInterface $templateEngine, AuthenticationUtils $authenticationUtils)
    {
        $this->templateEngine = $templateEngine;
        $this->authenticationUtils = $authenticationUtils;
    }

    /**
     * Same behaviour as the code from the official Symfony documentation
     *
     * @link http://symfony.com/doc/current/security/form_login_setup.html
     * @param  string $template
     * @return Response
     */
    public function loginAction($template) : Response
    {
        $authenticationUtils = $this->authenticationUtils;

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return new Response($this->templateEngine->render($template, [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]));
    }
}
