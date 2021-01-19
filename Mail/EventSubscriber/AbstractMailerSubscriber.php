<?php

namespace App\Mail\EventSubscriber;

    use App\Mail\Services\Mailer;
    use Doctrine\ORM\EntityManagerInterface;
    use Symfony\Component\Validator\Validator\ValidatorInterface;
    use Symfony\Component\EventDispatcher\EventSubscriberInterface;

    /**
     * Class AbstractMailerSubscriber.
     */
    abstract class AbstractMailerSubscriber implements EventSubscriberInterface
    {
        protected EntityManagerInterface $entityManager;

        protected ValidatorInterface $validator;

        protected Mailer $mailer;

        public function __construct(EntityManagerInterface $entityManager, ValidatorInterface $validator, Mailer $mailer)
        {
            $this->entityManager = $entityManager;
            $this->validator = $validator;
            $this->mailer = $mailer;
        }
    }
