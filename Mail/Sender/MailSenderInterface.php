<?php

namespace App\Mail\Sender;

use App\Mail\Mime\TemplatedEmail;
use App\Entity\Interfaces\MailInterface;
use App\Mail\Exception\MailSenderException;

interface MailSenderInterface
{
    /**
     * @param MailInterface|TemplatedEmail $mail
     *
     * @return mixed
     *
     * @throws MailSenderException
     */
    public function send($mail);
}
