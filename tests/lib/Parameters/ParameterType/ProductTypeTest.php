<?php

declare(strict_types=1);

namespace Netgen\Layouts\Sylius\Tests\Parameters\ParameterType;

use Netgen\Layouts\Parameters\ParameterDefinition;
use Netgen\Layouts\Sylius\Parameters\ParameterType\ProductType;
use Netgen\Layouts\Sylius\Tests\Stubs\Product as ProductStub;
use Netgen\Layouts\Sylius\Tests\Validator\RepositoryValidatorFactory;
use Netgen\Layouts\Tests\Parameters\ParameterType\ParameterTypeTestTrait;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Product\Repository\ProductRepositoryInterface;
use Symfony\Component\OptionsResolver\Exception\InvalidArgumentException;
use Symfony\Component\Validator\Validation;

#[CoversClass(ProductType::class)]
final class ProductTypeTest extends TestCase
{
    use ParameterTypeTestTrait;

    private MockObject&ProductRepositoryInterface $repositoryMock;

    protected function setUp(): void
    {
        $this->repositoryMock = $this->createMock(ProductRepositoryInterface::class);

        $this->type = new ProductType();
    }

    public function testGetIdentifier(): void
    {
        self::assertSame('sylius_product', $this->type::getIdentifier());
    }

    /**
     * @param mixed[] $options
     * @param mixed[] $resolvedOptions
     */
    #[DataProvider('validOptionsDataProvider')]
    public function testValidOptions(array $options, array $resolvedOptions): void
    {
        $parameter = $this->getParameterDefinition($options);
        self::assertSame($resolvedOptions, $parameter->getOptions());
    }

    /**
     * @param mixed[] $options
     */
    #[DataProvider('invalidOptionsDataProvider')]
    public function testInvalidOptions(array $options): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->getParameterDefinition($options);
    }

    /**
     * Provider for testing valid parameter attributes.
     *
     * @return mixed[]
     */
    public static function validOptionsDataProvider(): array
    {
        return [
            [
                [],
                [],
            ],
        ];
    }

    /**
     * Provider for testing invalid parameter attributes.
     *
     * @return mixed[]
     */
    public static function invalidOptionsDataProvider(): array
    {
        return [
            [
                [
                    'undefined_value' => 'Value',
                ],
            ],
        ];
    }

    public function testValidationValid(): void
    {
        $this->repositoryMock
                ->expects(self::once())
                ->method('find')
                ->with(self::identicalTo(42))
                ->willReturn(new ProductStub(42));

        $parameter = $this->getParameterDefinition([], true);
        $validator = Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(new RepositoryValidatorFactory($this->repositoryMock))
            ->getValidator();

        $errors = $validator->validate(42, $this->type->getConstraints($parameter, 42));
        self::assertCount(0, $errors);
    }

    public function testValidationValidWithNonRequiredValue(): void
    {
        $this->repositoryMock
                ->expects(self::never())
                ->method('find');

        $parameter = $this->getParameterDefinition([], false);
        $validator = Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(new RepositoryValidatorFactory($this->repositoryMock))
            ->getValidator();

        $errors = $validator->validate(null, $this->type->getConstraints($parameter, null));
        self::assertCount(0, $errors);
    }

    public function testValidationInvalid(): void
    {
        $this->repositoryMock
                ->expects(self::once())
                ->method('find')
                ->with(self::identicalTo(42))
                ->willReturn(null);

        $parameter = $this->getParameterDefinition([], true);
        $validator = Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(new RepositoryValidatorFactory($this->repositoryMock))
            ->getValidator();

        $errors = $validator->validate(42, $this->type->getConstraints($parameter, 42));
        self::assertNotCount(0, $errors);
    }

    #[DataProvider('emptyDataProvider')]
    public function testIsValueEmpty(mixed $value, bool $isEmpty): void
    {
        self::assertSame($isEmpty, $this->type->isValueEmpty(new ParameterDefinition(), $value));
    }

    /**
     * @return mixed[]
     */
    public static function emptyDataProvider(): array
    {
        return [
            [null, true],
            [new ProductStub(42), false],
        ];
    }
}
