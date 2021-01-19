<?php

namespace App\Mail\Services;

use App\Entity\Mail;
use App\Mail\Mime\TemplatedEmail;
use App\Entity\Interfaces\MailInterface;
use App\Mail\Provider\Mail\MailProviderInterface;

/**
 * Class MailService.
 */
class Mailer
{
    private MailProviderInterface $mailProvider;

    private MailBuilder $mailBuilder;

    /**
     * MailService constructor.
     */
    public function __construct(MailProviderInterface $mailProvider, MailBuilder $mailBuilder)
    {
        $this->mailProvider = $mailProvider;
        $this->mailBuilder = $mailBuilder;
    }

    /**
     * Create an entity Mail with the basic requierement for a mail.
     *
     * @param              $subject
     * @param              $body
     * @param array|string $recipients
     * @param array        $attachements
     */
    public function createEmail($subject, $body, $recipients, $attachements = []): Mail
    {
        return $this->mailBuilder->createEmailEntity($subject, $body, $recipients, $attachements);
    }

    public function createTemplatedEmail(string $code, TemplatedEmail $mail, array $variables, ?array $attachments = []): TemplatedEmail
    {
        return $this->mailBuilder->createEmailMessage($code, $mail, $variables, $attachments);
    }

    /**
     * Save mails.
     *
     * @param MailInterface|TemplatedEmail $mails
     */
    public function send($mails): void
    {
        $this->mailProvider->save($mails);
    }
}
