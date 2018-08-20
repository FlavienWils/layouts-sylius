<?php

declare(strict_types=1);

namespace Netgen\Layouts\Sylius\Tests\Layout\Resolver\TargetType;

use Netgen\Layouts\Sylius\Layout\Resolver\TargetType\TaxonProduct;
use Netgen\Layouts\Sylius\Tests\Stubs\Product as ProductStub;
use Netgen\Layouts\Sylius\Tests\Stubs\Taxon as TaxonStub;
use Netgen\Layouts\Sylius\Tests\Validator\RepositoryValidatorFactory;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\ProductTaxon;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validation;

final class TaxonProductTest extends TestCase
{
    /**
     * @var \Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface&\PHPUnit\Framework\MockObject\MockObject
     */
    private $repositoryMock;

    /**
     * @var \Netgen\Layouts\Sylius\Layout\Resolver\TargetType\TaxonProduct
     */
    private $targetType;

    public function setUp(): void
    {
        $this->repositoryMock = $this->createMock(TaxonRepositoryInterface::class);

        $this->targetType = new TaxonProduct();
    }

    /**
     * @covers \Netgen\Layouts\Sylius\Layout\Resolver\TargetType\TaxonProduct::getType
     */
    public function testGetType(): void
    {
        self::assertSame('sylius_taxon_product', $this->targetType::getType());
    }

    /**
     * @covers \Netgen\Layouts\Sylius\Layout\Resolver\TargetType\TaxonProduct::getConstraints
     */
    public function testValidationValid(): void
    {
        $this->repositoryMock
            ->expects(self::once())
            ->method('find')
            ->with(self::identicalTo(42))
            ->will(self::returnValue(new TaxonStub(42)));

        $validator = Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(new RepositoryValidatorFactory($this->repositoryMock))
            ->getValidator();

        $errors = $validator->validate(42, $this->targetType->getConstraints());
        self::assertCount(0, $errors);
    }

    /**
     * @covers \Netgen\Layouts\Sylius\Layout\Resolver\TargetType\TaxonProduct::getConstraints
     */
    public function testValidationInvalid(): void
    {
        $this->repositoryMock
            ->expects(self::once())
            ->method('find')
            ->with(self::identicalTo(42))
            ->will(self::returnValue(null));

        $validator = Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(new RepositoryValidatorFactory($this->repositoryMock))
            ->getValidator();

        $errors = $validator->validate(42, $this->targetType->getConstraints());
        self::assertNotCount(0, $errors);
    }

    /**
     * @covers \Netgen\Layouts\Sylius\Layout\Resolver\TargetType\TaxonProduct::provideValue
     */
    public function testProvideValue(): void
    {
        $product = new ProductStub(42);
        foreach ([12, 13] as $taxonId) {
            $productTaxon = new ProductTaxon();
            $productTaxon->setTaxon(new TaxonStub($taxonId));

            $product->addProductTaxon($productTaxon);
        }

        $request = Request::create('/');
        $request->attributes->set('nglayouts_sylius_product', $product);

        self::assertSame([12, 13], $this->targetType->provideValue($request));
    }

    /**
     * @covers \Netgen\Layouts\Sylius\Layout\Resolver\TargetType\TaxonProduct::provideValue
     */
    public function testProvideValueWithNoTaxon(): void
    {
        $request = Request::create('/');

        self::assertNull($this->targetType->provideValue($request));
    }
}
