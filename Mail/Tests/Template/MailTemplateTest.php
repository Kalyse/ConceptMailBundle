<?php

namespace App\Mail\Tests\Template;

use Twig\Environment;
use Psr\Log\LoggerInterface;
use PHPUnit\Framework\TestCase;
use App\Mail\Template\MailTemplate;
use App\Mail\Template\MailTemplateRenderer;
use PHPUnit\Framework\MockObject\MockObject;
use App\Mail\Provider\Template\MailTemplateProviderInterface;

class MailTemplateTest extends TestCase
{
    /**
     * @var MailTemplate
     */
    private $mailTemplate;

    /**
     * @var MailTemplateProviderInterface | MockObject
     */
    private $mailTemplateProvider;

    /**
     * @var \Twig_Environment | MockObject
     */
    private $twig;

    /**
     * @var LoggerInterface | MockObject
     */
    private $logger;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mailTemplateProvider = $this->createMock(MailTemplateProviderInterface::class);
        $this->twig = $this->createMock(Environment::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->mailTemplate = new MailTemplate($this->mailTemplateProvider, $this->twig, $this->logger);
    }

    public function testGetTemplate()
    {
        $mailTemplate = new \App\Mail\Entity\MailTemplate();

        $this->mailTemplateProvider
            ->expects($this->once())
            ->method('findOneTemplateByCode')
            ->with('test')
            ->willReturn($mailTemplate);

        $template = $this->mailTemplate->getTemplate('test');
        $this->assertNotNull($template);
        $this->assertInstanceOf(MailTemplateRenderer::class, $template);
    }
}
