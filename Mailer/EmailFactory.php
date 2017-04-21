<?php

namespace Twigony\Bundle\FrameworkBundle\Mailer;

use Symfony\Component\Templating\EngineInterface;

class EmailFactory
{
    /**
     * @var EngineInterface
     */
    private $templateEngine;

    public function __construct(EngineInterface $templateEngine)
    {
        $this->templateEngine = $templateEngine;
    }

    /**
     * @param string       $emailTemplate Template to render
     * @param mixed        $data          Template data
     * @param string       $subject
     * @param string|array $from
     * @param string|array $to
     * @return \Swift_Message
     */
    public function createMail($emailTemplate, $data, $subject, $from, $to)
    {
        return \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($from)
            ->setTo($to)
            ->setBody(
                $this->templateEngine->render(
                    $emailTemplate,
                    $data
                ),
                'text/html'
            )
        ;
    }
}
