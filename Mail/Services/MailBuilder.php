<?php

namespace App\Mail\Services;

use JsonException;
use App\Entity\Mail;
use App\Mail\Mime\TemplatedEmail;
use App\Mail\Template\MailTemplate;
use Symfony\Component\Mime\Address;
use App\Mail\Exception\MailTemplateNotFoundException;
use App\Mail\Exception\MailTemplateNotGeneratedException;

/**
 * Class MailBuilder.
 */
class MailBuilder
{
    private string $mailAddressFrom;

    private string $mailAliasFrom;

    private string $mailReplyTo;

    private MailTemplate $mailTemplate;

    public function __construct(string $mailAddressFrom, string $mailAliasFrom, MailTemplate $mailTemplate)
    {
        $this->mailAddressFrom = $mailAddressFrom;
        $this->mailAliasFrom = $mailAliasFrom;
        $this->mailTemplate = $mailTemplate;
    }

    /**
     * Create an entity Mail with the basic requirement for a mail.
     * Which will return the Mail entity.
     *
     * @param $subject
     * @param $body
     * @param $recipients
     */
    public function createEmailEntity(string $subject, string $body, array $recipients, array $attachments = []): Mail
    {
        if (! is_array($attachments)) {
            $attachments = [$attachments];
        }

        $mail = new Mail($subject, $body, $recipients);

        $mail->setSenderEmail($this->mailAddressFrom);

        if (! empty($this->mailAliasFrom)) {
            $mail->setSenderAlias($this->mailAliasFrom);
        }

        if (! empty($this->mailReplyTo)) {
            $mail->setReplyToEmail($this->mailReplyTo);
        }

        $mail->setAttachments($attachments);

        return $mail;
    }

    /**
     * @throws JsonException
     * @throws MailTemplateNotFoundException
     * @throws MailTemplateNotGeneratedException
     */
    public function createEmailMessage(string $code, TemplatedEmail $message, array $variables, ?array $attachments = []): TemplatedEmail
    {
        $template = $this->mailTemplate->getTemplate($code);

        // template->getSubject($variables)

        $message->html($template->getBody($variables, $message), 'text/html');

        $message->from(new Address($this->mailAddressFrom, $this->mailAliasFrom));
        foreach ($attachments as $attachement) {
            $message->attachFromPath($attachement);
        }

        return $message;
    }

    public function setMailAddressFrom(string $mailAddressFrom = ''): void
    {
        $this->mailAddressFrom = $mailAddressFrom;
    }

    public function setMailAliasFrom(string $mailAliasFrom = ''): void
    {
        $this->mailAliasFrom = $mailAliasFrom;
    }

    public function setMailReplyTo(string $mailReplyTo = ''): void
    {
        $this->mailReplyTo = $mailReplyTo;
    }
}
