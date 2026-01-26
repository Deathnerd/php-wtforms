<?php

declare(strict_types=1);

namespace WTForms\PHPStan\Extensions;

use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\DynamicMethodThrowTypeExtension;
use PHPStan\Type\Type;
use WTForms\Validators\Validator;

/**
 * PHPStan extension that tells the analyzer that validator __invoke() calls
 * don't propagate exceptions to the caller.
 *
 * In WTForms, validators throw StopValidation and ValidationError as control
 * flow mechanisms. These exceptions are intentionally caught by
 * Field::runValidationChain() and should not be considered "uncaught" from
 * the caller's perspective.
 *
 * This extension returns null from getThrowTypeFromMethodCall(), indicating
 * that no exceptions propagate to the caller's context.
 */
class ValidatorThrowTypeExtension implements DynamicMethodThrowTypeExtension
{
  /**
   * Check if this extension handles the given method.
   *
   * Handles __invoke on Validator and all subclasses.
   * Note: No return type check because some validators return values
   * (e.g., Regexp returns string|void).
   */
  public function isMethodSupported(MethodReflection $methodReflection): bool
  {
    if ('__invoke' !== $methodReflection->getName()) {
      return false;
    }

    $declaringClass = $methodReflection->getDeclaringClass();

    return $this->isValidatorClass($declaringClass->getName(), $declaringClass->isSubclassOf(Validator::class));
  }

  /**
   * Check if a class is the Validator base class or a Validator subclass.
   *
   * Extracted for testability since PHPStan's ClassReflection is final.
   */
  public function isValidatorClass(string $className, bool $isSubclassOfValidator): bool
  {
    // Handle the base Validator class
    if (Validator::class === $className) {
      return true;
    }

    // Handle all Validator subclasses
    return $isSubclassOfValidator;
  }

  /**
   * Return the throw type for validator __invoke() calls.
   *
   * Returns null to indicate that no exceptions propagate to the caller.
   * The validation framework catches all StopValidation and ValidationError
   * exceptions internally.
   */
  public function getThrowTypeFromMethodCall(
    MethodReflection $methodReflection,
    MethodCall $methodCall,
    Scope $scope
  ): ?Type {
    // Returning null tells PHPStan that no exceptions escape to the caller
    return null;
  }
}
