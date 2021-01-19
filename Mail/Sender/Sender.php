<?php

namespace App\Mail\Sender;

use Psr\Log\LoggerInterface;
use App\Entity\Interfaces\MailInterface;
use App\Mail\Exception\MailSenderException;
use App\Mail\Provider\Mail\MailProviderInterface;

/**
 * Class Sender.
 */
class Sender
{
    private MailSenderInterface $mailSender;

    private LoggerInterface $logger;

    private MailProviderInterface $mailEntityProvider;

    /**
     * Sender constructor.
     */
    public function __construct(
        MailSenderInterface $mailSender,
        MailProviderInterface $mailEntityProvider,
        LoggerInterface $logger
    ) {
        $this->mailSender = $mailSender;
        $this->logger = $logger;
        $this->mailEntityProvider = $mailEntityProvider;
    }

    /**
     * Send all mail.
     */
    public function sendAll(): void
    {
        try {
            $mails = $this->mailEntityProvider->findAllMail();
        } catch (\Exception $exception) {
            //It should be never reach except if doctrine fails to get mails from the database
            $this->logger->critical('Impossible to get mails from database', ['message' => $exception->getMessage()]);

            return;
        }

        /** @var MailInterface $mail */
        foreach ($mails as $mail) {
            $this->sendOne($mail);
        }

        $this->mailEntityProvider->save($mails);
    }

    /**
     * Send one mail.
     */
    public function sendOne(MailInterface $mail): void
    {
        try {
            $this->mailSender->send($mail);
            $mail->updateSentDate();
            $this->logger->info('The mail has been sent', $this->getLogData($mail));
        } catch (MailSenderException $exception) {
            $this->logger->error($exception->getMessage(), [
                $this->getLogData($mail),
            ]);
            $mail->setSentError(true);
        }
    }

    public function getLogData(MailInterface $mail): array
    {
        return [
            'recipients' => $mail->getRecipient(),
            'recipientsCopy' => $mail->getRecipientCopy(),
            'recipentsHiddenCopy' => $mail->getRecipientHiddenCopy(),
            'subject' => $mail->getSubject(),
            'id' => $mail->getId(),
        ];
    }
}
