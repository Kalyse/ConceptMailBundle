<?php

namespace App\Mail\Sender;

use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mailer\Mailer;
use App\Entity\Interfaces\MailInterface;
use App\Mail\Exception\MailSenderException;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use App\Mail\Provider\Mail\MailProviderInterface;
use App\Mail\Exception\MailerSenderEmptyException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

/**
 * Class MailSenderService.
 */
class MailSender implements MailSenderInterface
{
    /**
     * @var Mailer
     */
    private MailerInterface $mailer;

    private MailProviderInterface $mailEntityProvider;

    /**
     * MailSenderService constructor.
     */
    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @throws MailerSenderEmptyException
     */
    public function initEmail(MailInterface $mail): Email
    {
        $message = (new Email())
            ->subject($mail->getSubject())
            ->to(...$mail->getRecipient())
            ->htmlTemplate()
            ->html($mail->getBody(), 'text/html')
            ->cc(...$mail->getRecipientCopy())
            ->bcc(...$mail->getRecipientHiddenCopy());

        if (empty($mail->getSenderEmail())) {
            throw new MailerSenderEmptyException($mail);
        }

        $senderAlias = ! empty($mail->getSenderAlias()) ? $mail->getSenderAlias() : null;
        $message->from(new Address($mail->getSenderEmail(), $senderAlias));

        foreach ($mail->getAttachments() as $attachement) {
            $message->attachFromPath($attachement);
        }

        return $message;
    }

    /**
     * @param MailInterface|TemplatedEmail $mail
     *
     * @throws MailSenderException
     * @throws MailerSenderEmptyException
     * @throws TransportExceptionInterface
     */
    public function send($mail)
    {
        // We can assume it's already a TempaltedEmail
        $message = $mail;
        if ($mail instanceof MailInterface) {
            $message = $this->initEmail($mail);
        }

        $this->mailer->send($message);
    }
}
