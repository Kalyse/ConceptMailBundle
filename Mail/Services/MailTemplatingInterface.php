<?php

namespace App\Mail\Services;

interface MailTemplatingInterface
{
    /**
     * @param string $templateCode $templateCode
     * @param        $recipients
     * @param array  $attachments
     *
     * @return mixed
     */
    public function createEmail(string $templateCode, $recipients, array $variables = [], $attachments = []);
}
