<?php

namespace App\Mail\Tests\Template;

use Twig\Environment;
use Psr\Log\LoggerInterface;
use PHPUnit\Framework\TestCase;
use App\Mail\Template\MailTemplateRenderer;
use PHPUnit\Framework\MockObject\MockObject;
use App\Entity\Interfaces\MailTemplateInterface;
use App\Mail\Exception\MailTemplateNotGeneratedException;

/**
 * Class MailTemplateRendererTest.
 */
class MailTemplateRendererTest extends TestCase
{
    /**
     * @var MailTemplateRenderer|MockObject
     */
    private $mailTemplateRenderer;
    /**
     * @var Environment|MockObject
     */
    private $twig;
    /**
     * @var MailTemplateInterface|MockObject
     */
    private $mailTemplate;
    /**
     * @var LoggerInterface|MockObject
     */
    private $logger;

    protected function setUp(): void
    {
        parent::setUp();
        $this->twig = $this->createMock(Environment::class);
        $this->mailTemplate = $this->createMock(MailTemplateInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->mailTemplateRenderer = new MailTemplateRenderer($this->twig, $this->mailTemplate, $this->logger);
    }

    public function testGetBodyNotGenerated()
    {
        $this->twig->expects($this->once())->method('createTemplate')->will($this->throwException(new \Twig_Error('')));

        $this->logger->expects($this->once())->method('error');

        $this->expectException(MailTemplateNotGeneratedException::class);

        $this->mailTemplateRenderer->getBody();
    }

    public function testGetSubjectNotGenerated()
    {
        $this->twig->expects($this->once())->method('createTemplate')->will($this->throwException(new \Twig_Error('')));

        $this->logger->expects($this->once())->method('error');

        $this->expectException(MailTemplateNotGeneratedException::class);

        $this->mailTemplateRenderer->getSubject();
    }
}
