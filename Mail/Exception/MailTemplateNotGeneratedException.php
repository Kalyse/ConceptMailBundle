<?php

namespace App\Mail\Exception;

/**
 * Class MailTemplateNotFound.
 */
class MailTemplateNotGeneratedException extends \Exception
{
    /**
     * MailTemplateNotGeneratedException constructor.
     */
    public function __construct(\Exception $exception)
    {
        parent::__construct(
            sprintf('Mail Template has not been generated(%s)', $exception->getMessage()),
            $exception->getCode(),
            $exception
        );
    }
}
