<?php

namespace App\Mail\Template\Renderer;

use App\Mail\Mime\TemplatedEmail;

/**
 * Interface MailTemplateRendererInterface.
 */
interface MailTemplateRendererInterface
{
    /**
     * @param null $mode
     */
    public function getBody(array $variables = [], ?TemplatedEmail $email = null, $mode = null): string;

    public function getSubject(array $variables = []): string;
}
