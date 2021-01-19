<?php

namespace App\Mail\Exception;

/**
 * Class MailTemplateNotFound.
 */
class MailTemplateNotFoundException extends \Exception
{
    /**
     * MailTemplateNotFoundException constructor.
     */
    public function __construct()
    {
        parent::__construct('Mail Template not found');
    }
}
