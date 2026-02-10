<?php

declare(strict_types=1);

namespace WTForms\PHPStan\Extensions;

use PHPStan\Reflection\ReflectionProvider;

/**
 * Helper for parsing `property-of<ClassName>` annotations and resolving
 * property names from classes.
 */
class PropertyOfDynamicAccessHelper
{
  private ReflectionProvider $reflectionProvider;

  public function __construct(ReflectionProvider $reflectionProvider)
  {
    $this->reflectionProvider = $reflectionProvider;
  }

  /**
   * Parse a `property-of<ClassName>` annotation from a PHPDoc string.
   *
   * @return string|null The class name, or null if not found
   */
  public function parsePropertyOfAnnotation(string $phpDoc): ?string
  {
    if (preg_match('/@property-of\s+(\S+)/', $phpDoc, $matches)) {
      return trim($matches[1]);
    }

    return null;
  }

  /**
   * Resolve a class name reference (static, self, $this, or FQCN) to an actual class name.
   */
  public function resolveClassName(string $reference, ?string $contextClass): ?string
  {
    if (null === $contextClass) {
      return null;
    }

    $lower = strtolower($reference);
    if (in_array($lower, ['static', 'self', '$this'], true)) {
      return $contextClass;
    }

    // Treat as FQCN
    if ($this->reflectionProvider->hasClass($reference)) {
      return $reference;
    }

    return null;
  }

  /**
   * Get the public property names of a class.
   *
   * @return string[]
   */
  public function getPropertyNames(string $className): array
  {
    if (!$this->reflectionProvider->hasClass($className)) {
      return [];
    }

    $classReflection = $this->reflectionProvider->getClass($className);
    $names = [];

    foreach ($classReflection->getNativeReflection()->getProperties(\ReflectionProperty::IS_PUBLIC) as $prop) {
      $names[] = $prop->getName();
    }

    return $names;
  }
}
