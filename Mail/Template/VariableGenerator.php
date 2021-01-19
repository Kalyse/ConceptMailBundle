<?php

namespace App\Mail\Template;

    use JsonException;
    use Twig\Environment;
    use App\Utils\JsonUtil;
    use App\Entity\Reservation;
    use JMS\Serializer\Serializer;
    use App\Mail\Mime\TemplatedEmail;
    use JMS\Serializer\SerializerInterface;
    use Doctrine\ORM\EntityManagerInterface;
    use JMS\Serializer\SerializationContext;
    use Symfony\Bridge\Twig\Mime\WrappedTemplatedEmail;

    /**
     * Class Template.
     */
    class VariableGenerator
    {
        private EntityManagerInterface $entityManager;
        /**
         * @var Serializer
         */
        private SerializerInterface $serializer;

        private Environment $twig;

        /**
         * VariableGenerator constructor.
         */
        public function __construct(EntityManagerInterface $entityManager, SerializerInterface $serializer, Environment $twig)
        {
            $this->entityManager = $entityManager;
            $this->serializer = $serializer;
            $this->twig = $twig;
        }

        /**
         * @throws JsonException
         */
        public function generate(array $variables, ?TemplatedEmail $email = null): array
        {
            $context = [];
            $twig = $this->twig;
            foreach ($variables as $key => $variable) {
                if (is_object($variable) && $this->entityManager->getMetadataFactory()->isTransient($variable)) {
                    $serializedContext = $this->getContextFromFactory($variable);
                    $serialized = $this->serializer->serialize($variable, 'json', $serializedContext);
                    $data = JsonUtil::decodeInDeterministicOrder($serialized);
                    if (is_int($key)) {
                        $chunks = explode('\\', $this->entityManager->getMetadataFactory()->getMetadataFor(get_class($variable))->getName());
                        $key = strtolower(end($chunks));
                    }
                    $context[$key] = $data;
                } else {
                    $context[$key] = $variable;
                }
            }
            if ($email) {
                $context = array_merge($context, [
                    'email' => new WrappedTemplatedEmail($twig, $email),
                ]);
            }

            return $context;
        }

        /**
         * Basically, different serializations for different emails need different contexts.
         * This is to avoid leaking too much information, but to also ensure things are expanded correctly.
         *
         * @return \JMS\Serializer\Context|SerializationContext
         */
        public function getContextFromFactory(object $variable)
        {
            if ($variable instanceof Reservation) {
                return SerializationContext::create()->setSerializeNull(true)->setGroups(['Default', 'immutable', 'expanded_channel', 'expanded_collection', 'reservation_list', 'property_list', 'expanded_provider', 'listing_list', 'expanded_outboundProvider', 'expanded_schemaObject']);
            }

            return SerializationContext::create()->setGroups(['Default', 'immutable']);
        }
    }
