<?php

declare(strict_types=1);

namespace WTForms\Tests\PHPStan;

use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPUnit\Framework\TestCase;
use stdClass;
use WTForms\PHPStan\Extensions\ValidatorThrowTypeExtension;
use WTForms\Validators\Email;
use WTForms\Validators\Length;
use WTForms\Validators\Validator;

/**
 * Tests for ValidatorThrowTypeExtension.
 *
 * Note: PHPStan's ClassReflection is final, so we test the extracted
 * isValidatorClass() method directly. The actual integration with
 * PHPStan is verified through running PHPStan on the codebase.
 */
class ValidatorThrowTypeExtensionTest extends TestCase
{
  private ValidatorThrowTypeExtension $extension;

  protected function setUp(): void
  {
    $this->extension = new ValidatorThrowTypeExtension();
  }

  public function testIsValidatorClassReturnsTrueForValidatorBase(): void
  {
    $this->assertTrue(
      $this->extension->isValidatorClass(Validator::class, false)
    );
  }

  public function testIsValidatorClassReturnsTrueForValidatorSubclass(): void
  {
    $this->assertTrue(
      $this->extension->isValidatorClass(Email::class, true)
    );
  }

  public function testIsValidatorClassReturnsTrueForAnotherSubclass(): void
  {
    $this->assertTrue(
      $this->extension->isValidatorClass(Length::class, true)
    );
  }

  public function testIsValidatorClassReturnsFalseForNonValidatorClass(): void
  {
    $this->assertFalse(
      $this->extension->isValidatorClass(stdClass::class, false)
    );
  }

  public function testIsValidatorClassReturnsFalseWhenNotSubclass(): void
  {
    // A class that isn't Validator and isn't a subclass
    $this->assertFalse(
      $this->extension->isValidatorClass('SomeOtherClass', false)
    );
  }

  public function testGetThrowTypeReturnsNull(): void
  {
    $methodReflection = $this->createMock(MethodReflection::class);
    $methodCall = $this->createMock(MethodCall::class);
    $scope = $this->createMock(Scope::class);

    $result = $this->extension->getThrowTypeFromMethodCall($methodReflection, $methodCall, $scope);

    $this->assertNull($result);
  }

  /**
   * @dataProvider methodNameProvider
   */
  public function testIsMethodSupportedRequiresInvokeMethodName(string $methodName, bool $expected): void
  {
    $methodReflection = $this->createMock(MethodReflection::class);
    $methodReflection->method('getName')->willReturn($methodName);

    // For non-__invoke methods, getDeclaringClass shouldn't even be called
    if ('__invoke' !== $methodName) {
      $this->assertFalse($this->extension->isMethodSupported($methodReflection));
    }
  }

  /**
   * @return array<string, array{string, bool}>
   */
  public function methodNameProvider(): array
  {
    return [
      'non-invoke method getMessage' => ['getMessage', false],
      'non-invoke method validate'   => ['validate', false],
      'non-invoke method getOptions' => ['getOptions', false],
    ];
  }
}
