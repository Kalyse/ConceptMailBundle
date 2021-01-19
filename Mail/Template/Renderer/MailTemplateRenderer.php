<?php

namespace App\Mail\Template\Renderer;

use Throwable;
use Twig\Environment;
use Twig\Error\Error;
use Twig\Error\LoaderError;
use Twig\Error\SyntaxError;
use Psr\Log\LoggerInterface;
use App\Mail\Mime\TemplatedEmail;
use App\Mail\Template\VariableGenerator;
use App\Entity\Interfaces\MailTemplateInterface;
use App\Mail\Exception\MailTemplateNotGeneratedException;

/**
 * Class MailTemplateRender.
 */
class MailTemplateRenderer implements MailTemplateRendererInterface
{
    private Environment $twig;

    private MailTemplateInterface $mailTemplate;

    private LoggerInterface $logger;

    private ?string $baseTemplate;

    private VariableGenerator $variableGenerator;

    /**
     * MailTemplateRender constructor.
     */
    public function __construct(
        Environment $twig,
        MailTemplateInterface $mailTemplate,
        LoggerInterface $logger,
        VariableGenerator $variableGenerator,
        string $baseTemplate = null
    ) {
        $this->twig = $twig;
        $this->mailTemplate = $mailTemplate;
        $this->logger = $logger;
        $this->baseTemplate = $baseTemplate;
        $this->variableGenerator = $variableGenerator;
    }

    /**
     * @param null $mode Used for things like EMBED_MODE
     *
     * @throws MailTemplateNotGeneratedException
     * @throws \JsonException
     */
    public function getBody(array $variables = [], ?TemplatedEmail $email = null, $mode = null): string
    {
        $context = $this->variableGenerator->generate($variables, $email);

        try {
//            if (null !== $this->baseTemplate) {
//                $baseTemplate = $this->twig->load($this->baseTemplate);
//                $bodyContent = $this->renderTemplate($variables);
//
//                return $baseTemplate->render(['content' => $bodyContent, 'vars' => $variables]);
//            }
            return $this->renderTemplate($context);
        } catch (Error $e) {
            $this->logger->error(sprintf('Impossible to generate Mail Body template with error %s', $e->getMessage()), $context);
            throw new MailTemplateNotGeneratedException($e);
        }
    }

    /**
     * @throws MailTemplateNotGeneratedException
     * @throws Throwable
     */
    public function getSubject(array $variables = []): string
    {
        try {
            $template = $this->twig->createTemplate($this->mailTemplate->getMailSubject());

            return $template->render($variables);
        } catch (Error $e) {
            $this->logger->error('Impossible to generate Mail Subject template', $variables);
            throw new MailTemplateNotGeneratedException($e);
        }
    }

    /**
     * @throws LoaderError
     * @throws SyntaxError
     */
    private function renderTemplate(array $variables = []): string
    {
        $variables['__baseLayout'] = $this->baseTemplate;
        if (0 === strpos($this->mailTemplate->getMailBody(), '@') && '.html.twig' === substr($this->mailTemplate->getMailBody(), -strlen('.html.twig'))) {
            $template = $this->mailTemplate->getMailBody();

            return $this->twig->render($template, $variables);
        }

        return $this->twig->createTemplate($this->mailTemplate->getMailBody())->render($variables);
    }
}
