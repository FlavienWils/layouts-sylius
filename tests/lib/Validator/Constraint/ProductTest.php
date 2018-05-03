<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Sylius\Tests\Validator\Constraint;

use Netgen\BlockManager\Sylius\Validator\Constraint\Product;
use PHPUnit\Framework\TestCase;

final class ProductTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Sylius\Validator\Constraint\Product::validatedBy
     */
    public function testValidatedBy(): void
    {
        $constraint = new Product();
        $this->assertEquals('ngbm_sylius_product', $constraint->validatedBy());
    }
}
