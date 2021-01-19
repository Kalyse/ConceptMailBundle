<?php

namespace App\Mail\Services;

use App\Entity\Interfaces\MailInterface;

interface MailerInterface
{
    /**
     * @param $subject
     * @param $body
     * @param $recipients
     * @param $attachements
     */
    public function createEmail($subject, $body, $recipients, $attachements): MailInterface;

    public function getMail(): MailInterface;

    /**
     * @param $mails
     */
    public function save($mails);
}
