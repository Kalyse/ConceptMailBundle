<?php

namespace App\Mail\Services;

use Throwable;
use App\Mail\Template\MailTemplate;
use JMS\Serializer\SerializerInterface;
use App\Entity\Interfaces\MailInterface;
use App\Mail\Template\VariableGenerator;
use Doctrine\ORM\EntityManagerInterface;
use App\Mail\Exception\MailTemplateNotFoundException;
use App\Mail\Exception\MailTemplateNotGeneratedException;

/**
 * Class MailManagerService.
 */
class MailTemplating implements MailTemplatingInterface
{
    private Mailer $mailService;

    private MailTemplate $mailTemplate;

    private SerializerInterface $serializer;

    private EntityManagerInterface $entityManager;

    private VariableGenerator $variableGenerator;

    /**
     * MailManagerService constructor.
     */
    public function __construct(MailTemplate $mailTemplate, Mailer $mailService, VariableGenerator $variableGenerator)
    {
        $this->mailService = $mailService;
        $this->mailTemplate = $mailTemplate;
        $this->variableGenerator = $variableGenerator;
    }

    /**
     * Create an entity Mail with the basic requierement for a mail from a MailTemplate name
     * Which will return the Mail entity.
     *
     * @param array|string $recipients
     * @param array|string $attachments
     * @param null         $baseTemplate
     *
     * @throws MailTemplateNotFoundException
     * @throws MailTemplateNotGeneratedException
     * @throws Throwable
     * @throws \JsonException
     */
    public function createEmail(
        string $templateCode,
        $recipients,
        array $variables = [],
        $attachments = [],
        $baseTemplate = null
    ): MailInterface {
        $template = $this->mailTemplate->getTemplate($templateCode);
        $context = $this->variableGenerator->generate($variables);

        return $this->mailService->createEmail(
            $template->getSubject($context),
            $template->getBody($context),
            $recipients,
            $attachments
        );
    }

    public function setBaseTemplate(?string $baseTemplate): void
    {
        $this->mailTemplate->setBaseTemplate($baseTemplate);
    }

    public function getVariables($variables): array
    {
        return [];
    }

    public function getMailService(): Mailer
    {
        return $this->mailService;
    }
}
