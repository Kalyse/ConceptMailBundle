<?php

namespace App\Mail\Template;

use Twig\Environment;
use Psr\Log\LoggerInterface;
use App\Mail\Template\Renderer\MailTemplateRenderer;
use App\Mail\Exception\MailTemplateNotFoundException;
use App\Mail\Provider\Template\MailTemplateProviderInterface;

/**
 * Class Template.
 */
class MailTemplate
{
    private MailTemplateProviderInterface $mailProvider;

    private Environment $twig;

    private LoggerInterface $logger;

    private ?string $baseTemplate = null;

    private VariableGenerator $variableGenerator;

    /**
     * Template constructor.
     */
    public function __construct(
        MailTemplateProviderInterface $mailProvider,
        Environment $twig,
        LoggerInterface $logger,
        VariableGenerator $variableGenerator
    ) {
        $this->mailProvider = $mailProvider;
        $this->twig = $twig;
        $this->logger = $logger;
        $this->variableGenerator = $variableGenerator;
    }

    /**
     * Return the rendered mail body from MailTemplate code.
     *
     * @param $code
     *
     * @throws MailTemplateNotFoundException
     */
    public function getTemplate(string $code): MailTemplateRenderer
    {
        $mailTemplate = $this->mailProvider->findOneTemplateByCode($code);

        return new MailTemplateRenderer($this->twig, $mailTemplate, $this->logger, $this->variableGenerator, $this->baseTemplate);
    }

    public function setBaseTemplate(string $baseTemplate = null): void
    {
        $this->baseTemplate = $baseTemplate;
    }
}
