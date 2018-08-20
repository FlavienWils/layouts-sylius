<?php

declare(strict_types=1);

namespace Netgen\Layouts\Sylius\Tests\Validator;

use Netgen\BlockManager\Tests\TestCase\ValidatorTestCase;
use Netgen\Layouts\Sylius\Tests\Stubs\Taxon as TaxonStub;
use Netgen\Layouts\Sylius\Validator\Constraint\Taxon;
use Netgen\Layouts\Sylius\Validator\TaxonValidator;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintValidatorInterface;

final class TaxonValidatorTest extends ValidatorTestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $repositoryMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->constraint = new Taxon();
    }

    public function getValidator(): ConstraintValidatorInterface
    {
        $this->repositoryMock = $this->createMock(TaxonRepositoryInterface::class);

        return new TaxonValidator($this->repositoryMock);
    }

    /**
     * @covers \Netgen\Layouts\Sylius\Validator\TaxonValidator::__construct
     * @covers \Netgen\Layouts\Sylius\Validator\TaxonValidator::validate
     */
    public function testValidateValid(): void
    {
        $this->repositoryMock
            ->expects(self::once())
            ->method('find')
            ->with(self::identicalTo(42))
            ->will(self::returnValue(new TaxonStub(42)));

        self::assertValid(true, 42);
    }

    /**
     * @covers \Netgen\Layouts\Sylius\Validator\TaxonValidator::__construct
     * @covers \Netgen\Layouts\Sylius\Validator\TaxonValidator::validate
     */
    public function testValidateNull(): void
    {
        $this->repositoryMock
            ->expects(self::never())
            ->method('find');

        self::assertValid(true, null);
    }

    /**
     * @covers \Netgen\Layouts\Sylius\Validator\TaxonValidator::__construct
     * @covers \Netgen\Layouts\Sylius\Validator\TaxonValidator::validate
     */
    public function testValidateInvalid(): void
    {
        $this->repositoryMock
            ->expects(self::once())
            ->method('find')
            ->with(self::identicalTo(42))
            ->will(self::returnValue(null));

        self::assertValid(false, 42);
    }

    /**
     * @covers \Netgen\Layouts\Sylius\Validator\TaxonValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "Netgen\Layouts\Sylius\Validator\Constraint\Taxon", "Symfony\Component\Validator\Constraints\NotBlank" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidConstraint(): void
    {
        $this->constraint = new NotBlank();
        self::assertValid(true, 'value');
    }

    /**
     * @covers \Netgen\Layouts\Sylius\Validator\TaxonValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "scalar", "array" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidValue(): void
    {
        self::assertValid(true, []);
    }
}
