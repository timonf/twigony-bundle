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

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Templating\EngineInterface;
use Twigony\Bundle\FrameworkBundle\Form\AutomaticFormBuilder;

/**
 * Twigony's Template Controller for static pages
 *
 * All controller actions can be used in the router definition without having any own controller.
 *
 * @author Timon F <dev@timonf.de>
 */
class TemplateController
{
    use CacheTrait;

    /**
     * @var EngineInterface
     */
    private $templateEngine;

    public function __construct(EngineInterface $templateEngine)
    {
        $this->templateEngine = $templateEngine;
    }

    /**
     * Displays static pages. You can use an optional placeholder to reuse the route easily.
     *
     * @param string  $template Template path and file name. You can use the "page" parameter here. So you can
     *                          easily put all templates in a single place:
     * @param string  $page     Name of the page (e. g. "about", "imprint", "terms", "privacy").
     * @param array   $options  Additional configuration options. You can access them in Twig via {{ options.* }}.
     * @return Response
     */
    public function templateAction($template, string $page = null, $options = []) : Response
    {
        $template = str_replace('{page}', $page, $template);

        if (!$this->templateEngine->exists($template)) {
            throw new NotFoundHttpException(sprintf('Template "%s" could not be found.', $template));
        }

        $response = new Response($this->templateEngine->render($template, [
            'options' => $options,
            'page' => $page,
        ]));

        $this->applyCacheOptions($response, $options);

        return $response;
    }
}
