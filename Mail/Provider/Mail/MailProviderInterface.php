<?php

namespace App\Mail\Provider\Mail;

use App\Mail\Mime\TemplatedEmail;
use App\Entity\Interfaces\MailInterface;

interface MailProviderInterface
{
    /**
     * @param MailInterface|TemplatedEmail $mail
     */
    public function save($mail);

    /**
     * @return MailInterface[]|null
     */
    public function findAllMail();
}
