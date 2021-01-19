<?php

namespace App\Mail\Provider\Template;

use App\Entity\Interfaces\MailTemplateInterface;
use App\Mail\Exception\MailTemplateNotFoundException;

interface MailTemplateProviderInterface
{
    /**
     * @param $code
     *
     * @return MailTemplateInterface
     *
     * @throws MailTemplateNotFoundException
     */
    public function findOneTemplateByCode($code);
}
