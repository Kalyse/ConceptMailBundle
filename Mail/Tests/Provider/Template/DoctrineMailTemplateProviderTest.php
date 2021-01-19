<?php

namespace App\Mail\Tests\Provider\Template;

use App\Entity\Mail;
use App\Entity\MailTemplate;
use PHPUnit\Framework\TestCase;
use App\Repository\MailTemplateRepository;
use App\Mail\Exception\MailTemplateNotFoundException;
use App\Mail\Provider\Template\DoctrineMailTemplateProvider;

class DoctrineMailTemplateProviderTest extends TestCase
{
    private $mailTemplateRepository;
    private $doctrineMailTemplateProvider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mailTemplateRepository = $this->createMock(MailTemplateRepository::class);
        $this->doctrineMailTemplateProvider = new DoctrineMailTemplateProvider($this->mailTemplateRepository);
    }

    public function testFindOneTemplateByCode()
    {
        $templateCode = 'test';
        $mailTemplate = new MailTemplate();
        $this->mailTemplateRepository->expects($this->once())->method('findOneByCode')->with($templateCode)
            ->willReturn($mailTemplate);
        $this->doctrineMailTemplateProvider->findOneTemplateByCode($templateCode);
    }

    public function testFindOneTemplaceByCodeWrongClass()
    {
        $templateCode = 'test';
        $mailTemplate = new Mail('Subject', 'body', ['recipient@test.com']);
        $this->mailTemplateRepository->expects($this->once())->method('findOneByCode')->with($templateCode)
            ->willReturn($mailTemplate);
        $this->expectException(MailTemplateNotFoundException::class);
        $this->doctrineMailTemplateProvider->findOneTemplateByCode($templateCode);
    }
}
