<?php

namespace Netgen\BlockManager\Sylius\Item\ValueConverter;

use Netgen\BlockManager\Item\ValueConverterInterface;
use Sylius\Component\Product\Model\ProductInterface;

class ProductValueConverter implements ValueConverterInterface
{
    public function supports($object)
    {
        return $object instanceof ProductInterface;
    }

    public function getValueType($object)
    {
        return 'sylius_product';
    }

    public function getId($object)
    {
        return $object->getId();
    }

    public function getName($object)
    {
        return $object->getName();
    }

    public function getIsVisible($object)
    {
        return true;
    }
}
