<?php

namespace App\Mail\EventSubscriber;

    use Datetime;
    use App\Entity\Application;
    use App\Entity\MailTemplate;
    use App\Mail\Mime\TemplatedEmail;
    use Symfony\Component\Mime\Address;
    use Application\Sonata\UserBundle\Entity\User;
    use Symfony\Component\Validator\Constraints\Email;
    use App\Event\Reservation\UpsertConnectionAttemptFailedEvent;
    use Lycan\MailerBundle\EventSubscriber\Traits\ReservationTrait;

    /**
     * Class UpsertReservationFailureSubscriber.
     */
    class UpsertReservationFailureSubscriber extends AbstractMailerSubscriber
    {
        use ReservationTrait;

        /**
         * @return array The event names to listen to
         */
        public static function getSubscribedEvents(): array
        {
            return [
                UpsertConnectionAttemptFailedEvent::class => 'doNotifyRecepientsOfUpsertFailure',
            ];
        }

        public function doNotifyRecepientsOfUpsertFailure(UpsertConnectionAttemptFailedEvent $event): void
        {
            // We send should notifications to people....
            // 1. The owner of the property
            // 2. The application owner
            // 3. The collection owner if it's not the same as the property owner?
            // 3. All Lycan Admins?
            $variables['rec'] = $event->getReservationExternalConnection();
            $recipients = [];
            $bcc = [];
            $reservation = $event->getReservationExternalConnection()->getReservation();
            // 1. This is the owner of the property
            // Let's detect the "subject"/"target" of the reservation. So for example, who/where created this reservation.
            $target = '';
            if ($reservation->getListing() && $reservation->getListing()->getProvider()) {
                $target = $reservation->getListing()->getProvider()->getProviderType();
            }

            $subject = sprintf('[Action Required] - %s Reservation Creation Failure at %s', $target, (new Datetime())->format('H:i:sa'));

            if ($reservation->getProperty() && $reservation->getProperty()->getOwner()) {
                $recipient = $reservation->getProperty()->getOwner();
                if ($recipient) {
                    $recipients[] = new Address($recipient->getEmail(), $recipient->getFullname());
                }
                if ($recipient && $recipient->getTechnicalEmail()) {
                    $emailConstraint = new Email();
                    $errors = $this->validator->validate(
                        $recipient->getTechnicalEmail(),
                        $emailConstraint
                    );
                    if (0 === $errors->count()) {
                        $bcc[] = new Address($recipient->getTechnicalEmail());
                    }
                }
            }

            // 2. This is the application owners...
            $appOwners = $this->entityManager->getRepository(Application::class)->getApplicationOwnersAndSupers($reservation->getApplication());
            /** @var User $owner */
            foreach ($appOwners as $owner) {
                if (! isset($recipient) || ! $owner->isEqualTo($recipient)) {
                    $bcc[] = new Address($owner->getEmail(), $owner->getFullname());
                }
            }

            $mail = (new TemplatedEmail())
                ->subject($subject)
                // ->replyTo($replyToAddress)
                ->to(...$recipients)
                ->bcc(...$bcc)
                ->from($this->getFrom($reservation));

            $message = $this->mailer->createTemplatedEmail(
                MailTemplate::TYPE_RESERVATION_UPSERT_FAILURE,
                $mail,
                $variables
            );

            $this->mailer->send($message);
        }
    }
