<?php

namespace App\Serializer;

use App\Entity\Transaction;
use App\Entity\Wallet;
use App\ValueObject\Money;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;

class MoneyNormalizer implements ContextAwareDenormalizerInterface, DenormalizerAwareInterface, ContextAwareNormalizerInterface, NormalizerAwareInterface
{
    use DenormalizerAwareTrait;
    use NormalizerAwareTrait;

    public const ALREADY_CALLED_DENORMALIZER = 'MONEY_DENORMALIZER_HAS_ALREADY_CALLED';
    public const ALREADY_CALLED_NORMALIZER = 'MONEY_NORMALIZER_HAS_ALREADY_CALLED';

    public function supportsDenormalization(mixed $data, string $type, string $format = null, array $context = []): bool
    {
        if (isset($context[self::ALREADY_CALLED_DENORMALIZER])) {
            return false;
        }

        return in_array($type, [Transaction::class, Wallet::class]);
    }

    public function denormalize(mixed $data, string $type, string $format = null, array $context = [])
    {
        if (!is_array($data)) {
            throw new \RuntimeException(sprintf('$data must be array, %s provided.', gettype($data)));
        }
        if (!isset($data['amount'])) {
            throw new \RuntimeException('$data[amount] must be provided.');
        }

        $data['amount'] = (int) round(($data['amount'] * 100));

        $context[self::ALREADY_CALLED_DENORMALIZER] = true;

        return $this->denormalizer->denormalize($data, $type, $format, $context);
    }

    public function supportsNormalization(mixed $data, string $format = null, array $context = []): bool
    {
        if (isset($context[self::ALREADY_CALLED_NORMALIZER])) {
            return false;
        }

        return $data instanceof Transaction || $data instanceof Wallet;
    }

    public function normalize(mixed $object, string $format = null, array $context = [])
    {
        $context[self::ALREADY_CALLED_NORMALIZER] = true;

        $data = $this->normalizer->normalize($object, $format, $context);

        if (!is_array($data)) {
            throw new \RuntimeException(sprintf('$data must be array, %s provided.', gettype($data)));
        }
        if (!isset($data['amount'])) {
            throw new \RuntimeException('$data[amount] must be provided.');
        }

        $data['amount'] = (new Money($data['amount']))->getMinor();

        return $data;
    }
}
