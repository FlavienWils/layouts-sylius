<?php

declare(strict_types=1);

namespace Netgen\Layouts\Sylius\Doctrine\ORM;

use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface as BaseProductRepositoryInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;

interface ProductRepositoryInterface extends BaseProductRepositoryInterface
{
    public function findByTaxon(ChannelInterface $channel, ?TaxonInterface $taxon, string $locale, int $offset, int $count, array $sorting = []): array;

    public function countByTaxon(ChannelInterface $channel, ?TaxonInterface $taxon, string $locale): int;

    public function findLatestByTaxon(ChannelInterface $channel, ?TaxonInterface $taxon, string $locale, int $offset, int $count): array;

    public function countLatestByTaxon(ChannelInterface $channel, ?TaxonInterface $taxon, string $locale): int;
}
