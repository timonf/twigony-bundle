<?php

namespace Twigony\Bundle\FrameworkBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Templating\EngineInterface;
use Twigony\Bundle\FrameworkBundle\Form\AutomaticFormBuilder;
use Twigony\Bundle\FrameworkBundle\Mailer\EmailFactory;

/**
 * Twigony's SwiftMailer Controller to send form data as email
 *
 * All controller actions can be used in the router definition without having any own controller.
 *
 * @author Timon F <dev@timonf.de>
 */
class SwiftMailerController
{
    use CacheTrait;

    /**
     * @var AutomaticFormBuilder
     */
    private $automaticFormBuilder;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var EngineInterface
     */
    private $templateEngine;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var EmailFactory
     */
    private $emailFactory;

    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(
        AutomaticFormBuilder $automaticFormBuilder,
        EngineInterface $templateEngine,
        RouterInterface $router,
        FormFactoryInterface $formFactory,
        Session $session,
        EmailFactory $emailFactory,
        \Swift_Mailer $swiftMailer,
        EntityManagerInterface $entityManager = null
    ) {
        $this->automaticFormBuilder = $automaticFormBuilder;
        $this->templateEngine = $templateEngine;
        $this->router = $router;
        $this->formFactory = $formFactory;
        $this->session = $session;
        $this->emailFactory = $emailFactory;
        $this->mailer = $swiftMailer;
        $this->entityManager = $entityManager;
    }

    /**
     * Sending mails… (WIP)
     *
     * @param Request $request
     * @param string  $template Template path and file name
     * @param string  $entity   Full class name of entity to create form from
     * @param array   $options  Additional configuration options. Following options are possible:
     *                          - "form_class" (optional) -> Custom form. Otherwise form will be created for you
     *                          - "to" -> Receiver of email
     *                          - "subject" -> Subject of email
     *                          - "from" -> Sender of email
     *                          - "template" -> Template of email
     * @return Response
     */
    public function emailAction(Request $request, $template, $entity, $options)
    {
        $form = $this->handleForm($options, $entity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->sendMail($options, $form->getData());

            if (array_key_exists('persist', $options)) {
                $this->entityManager->persist($form->getData());
                $this->entityManager->flush();
            }

            $this->applyFlashMessage($options);

            if (array_key_exists('redirect', $options)) {
                return new RedirectResponse($this->router->generate($options['redirect']), 301);
            }
        }

        $response = new Response($this->templateEngine->render($template, [
            'options' => $options,
            'form' => $form->createView(),
        ]));

        $this->applyCacheOptions($response, $options);

        return $response;
    }

    /**
     * @param array $options given options (MUST provide keys "subject", "to" and "emailTemplate"
     * @param mixed $data    data to add to the email template
     * @return \Swift_Message
     */
    protected function sendMail($options, $data)
    {
        if (!array_key_exists('subject', $options)) {
            throw new \InvalidArgumentException(sprintf('Please provide a "subject" to send an email.'));
        }

        if (!array_key_exists('to', $options)) {
            throw new \InvalidArgumentException(sprintf('Please provide a recipient ("to") to send an email.'));
        }

        if (!array_key_exists('emailTemplate', $options)) {
            throw new \InvalidArgumentException(sprintf('Please provide a "emailTemplate" to send an email.'));
        }

        $subject  = $options['subject'];
        $to       = $options['to'];
        $from     = array_key_exists('from', $options) ? $options['from'] : $to;
        $as       = array_key_exists('as', $options) ? $options['as'] : 'entity';
        $template = $options['emailTemplate'];

        $message = $this->emailFactory->createMail($template, [
            $as => $data,
            $options = array_merge($options, [
                'from' => $from,
                'as'   => $as,
            ])
        ], $subject, $from, $to);

        $this->mailer->send($message);

        return $message;
    }

    /**
     * @param array  $options Options array, defined in routing files
     * @param object $entity  Instance of an given entity - or "null"
     * @return FormInterface
     */
    protected function handleForm($options, $entity)
    {
        if (null === $entity) {
            throw new NotFoundHttpException('Entity not found.');
        }

        if (array_key_exists('form_class', $options)) {
            return $this->formFactory->create($options['form_class']);
        } else {
            return $this->automaticFormBuilder->buildFormByClass($entity);
        }
    }

    /**
     * @param array  $options Options array, defined in routing files
     * @param string $type
     */
    protected function applyFlashMessage($options, $type = 'notice')
    {
        if (array_key_exists('flash', $options)) {
            $this->session->getFlashBag()->add($type, $options['flash']);
        }
    }
}
