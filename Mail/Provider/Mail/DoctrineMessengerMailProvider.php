<?php

namespace App\Mail\Provider\Mail;

use App\Entity\Mail;
use App\Mail\Mime\TemplatedEmail;
use App\Entity\Interfaces\MailInterface;
use App\Mail\Sender\MailSenderInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Mail\Exception\MailerSenderEmptyException;

/**
 * Class MailProviderDoctrine.
 */
class DoctrineMessengerMailProvider implements MailProviderInterface
{
    private EntityManagerInterface $entityManager;

    private MailSenderInterface $sender;

    /**
     * MailProviderDoctrine constructor.
     */
    public function __construct(EntityManagerInterface $entityManager, MailSenderInterface $sender)
    {
        $this->entityManager = $entityManager;
        $this->sender = $sender;
    }

    /**
     * Save all mails to the database and flush to Messenger.
     *
     * @param TemplatedEmail|MailInterface $mail
     *
     * @throws MailerSenderEmptyException
     * @throws \Exception
     */
    public function save($mail): void
    {
        if ($mail instanceof MailInterface) {
            if (empty($mail->getSenderEmail())) {
                throw new MailerSenderEmptyException($mail);
            }

            $this->entityManager->persist($mail);
            $this->sender->send($mail);
            //Flush all mails
            $this->entityManager->getUnitOfWork()->commit($mail);
        } elseif ($mail instanceof TemplatedEmail) {
            $email = $this->getModel($mail);
            $this->entityManager->persist($email);
            $this->entityManager->getUnitOfWork()->commit($email);
            $this->sender->send($mail);
        } else {
            throw new \RuntimeException('We did not handle a message in this request');
        }
    }

    public function getModel(TemplatedEmail $email): Mail
    {
        $model = new Mail($email->getSubject(), $email->getHtmlBody(), $email->getTo());
        foreach ($email->getFrom() as $from) {
            $model->setSenderAlias($from->getName());
            $model->setSenderEmail($from->getAddress());
        }
        foreach ($email->getReplyTo() as $replyTo) {
            $model->setReplyToEmail($replyTo->getEncodedAddress());
        }
        // $model->setAttachments($email->getAttachments()-);

        return $model;
    }

    /**
     * @return MailInterface[]|null
     */
    public function findAllMail(): ?array
    {
        return $this->entityManager
            ->getRepository(Mail::class)
            ->findBySentDate();
    }
}
