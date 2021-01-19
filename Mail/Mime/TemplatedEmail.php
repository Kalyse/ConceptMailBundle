<?php

namespace App\Mail\Mime;

    class TemplatedEmail extends \Symfony\Bridge\Twig\Mime\TemplatedEmail
    {
        public function replyTo(...$addresses)
        {
            $addresses = array_filter($addresses);
            if (! empty($addresses)) {
                return parent::replyTo(...$addresses);
            }

            return $this;
        }

        public function from(...$addresses)
        {
            $addresses = array_filter($addresses);
            if (! empty($addresses)) {
                return parent::from(...$addresses);
            }

            return $this;
        }

        public function bcc(...$addresses)
        {
            $addresses = array_filter($addresses);
            if (! empty($addresses)) {
                return parent::bcc(...$addresses);
            }

            return $this;
        }
    }
