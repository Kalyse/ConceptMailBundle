<?php

namespace App\Mail\Tests\Services;

use App\Mail\Services\Mailer;
use PHPUnit\Framework\TestCase;
use App\Mail\Template\MailTemplate;
use App\Mail\Services\MailTemplating;
use PHPUnit\Framework\MockObject\MockObject;

class MailTemplatingTest extends TestCase
{
    /**
     * @var MailTemplating
     */
    private $mailTemplating;
    /**
     * @var MailTemplate|MockObject
     */
    private $mailTemplate;
    /**
     * @var Mailer|MockObject
     */
    private $mailer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mailTemplate = $this->createMock(MailTemplate::class);
        $this->mailer = $this->createMock(Mailer::class);
        $this->mailTemplating = new MailTemplating($this->mailTemplate, $this->mailer);
    }

    public function testMailService()
    {
        $mailService = $this->mailTemplating->getMailService();
        $this->assertNotNull($mailService);
        $this->assertInstanceOf(Mailer::class, $mailService);
    }
}
