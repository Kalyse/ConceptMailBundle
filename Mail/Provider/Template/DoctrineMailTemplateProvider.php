<?php

namespace App\Mail\Provider\Template;

use App\Repository\MailTemplateRepository;
use App\Entity\Interfaces\MailTemplateInterface;
use App\Mail\Exception\MailTemplateNotFoundException;

/**
 * Class MailProviderDoctrine.
 */
class DoctrineMailTemplateProvider implements MailTemplateProviderInterface
{
    /**
     * @var MailTemplateRepository
     */
    private $mailTemplateRepository;

    /**
     * MailProviderDoctrine constructor.
     */
    public function __construct(MailTemplateRepository $mailTemplateRepository)
    {
        $this->mailTemplateRepository = $mailTemplateRepository;
    }

    /**
     * @param $code
     *
     * @return MailTemplateInterface
     *
     * @throws MailTemplateNotFoundException
     */
    public function findOneTemplateByCode($code)
    {
        $mailTemplate = $this->mailTemplateRepository->findOneByCode($code);

        if (! $mailTemplate instanceof MailTemplateInterface) {
            throw new MailTemplateNotFoundException();
        }

        return $mailTemplate;
    }
}
