<?php

namespace App\Mail\Exception;

use App\Entity\Interfaces\MailInterface;

/**
 * Class MailerSenderEmptyException.
 */
class MailerSenderEmptyException extends MailSenderException
{
    /**
     * Mail Identifier.
     *
     * @var MailInterface
     */
    protected ?MailInterface $mail;

    /**
     * MailerSenderEmptyException constructor.
     *
     * @param string $message
     */
    public function __construct(MailInterface $mail = null, $message = 'The Mailer has an empty sender_email')
    {
        $this->mail = $mail;
        parent::__construct($message);
    }
}
