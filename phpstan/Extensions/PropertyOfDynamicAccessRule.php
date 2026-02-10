<?php

declare(strict_types=1);

namespace WTForms\PHPStan\Extensions;

use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\Foreach_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * A PHPStan rule that suppresses dynamic property access errors when
 * the variable used as a property name is annotated with
 * `@phpstan-var property-of<ClassName>`.
 *
 * @implements Rule<Assign>
 */
class PropertyOfDynamicAccessRule implements Rule
{
  private PropertyOfDynamicAccessHelper $helper;

  public function __construct(PropertyOfDynamicAccessHelper $helper)
  {
    $this->helper = $helper;
  }

  public function getNodeType(): string
  {
    return Assign::class;
  }

  /**
   * @param Assign $node
   */
  public function processNode(Node $node, Scope $scope): array
  {
    // Only handle $this->$var = ...
    if (!$node->var instanceof PropertyFetch) {
      return [];
    }

    $fetch = $node->var;

    // LHS object must be $this
    if (!$fetch->var instanceof Variable || 'this' !== $fetch->var->name) {
      return [];
    }

    // Property name must be a variable (dynamic access)
    if (!$fetch->name instanceof Variable) {
      return [];
    }

    $varName = $fetch->name->name;
    if (!is_string($varName)) {
      return [];
    }

    // Look for property-of annotation in the PHPDoc of this node or its parent foreach
    $phpDoc = $this->findPropertyOfAnnotation($node, $varName);
    if (null === $phpDoc) {
      return [];
    }

    $className = $this->helper->parsePropertyOfAnnotation($phpDoc);
    if (null === $className) {
      return [];
    }

    $contextClass = $scope->isInClass() ? $scope->getClassReflection()->getName() : null;
    $resolvedClass = $this->helper->resolveClassName($className, $contextClass);

    if (null === $resolvedClass) {
      return [
        RuleErrorBuilder::message(
          sprintf('@property-of %s references unknown class.', $className)
        )->identifier('wtforms.propertyOf.unknownClass')->build(),
      ];
    }

    // Annotation found and class resolved — this is the signal to trust the access.
    // We return no errors. The actual suppression of PHPStan's built-in error
    // is handled by the ignoreErrors configuration in phpstan.neon.
    return [];
  }

  /**
   * Search for a `@phpstan-var property-of<...>` annotation for the given variable name
   * in the node's own doc comment or in an enclosing foreach's doc comment.
   */
  private function findPropertyOfAnnotation(Assign $node, string $varName): ?string
  {
    // Check the assign node's own doc comment
    $doc = $node->getDocComment();
    if (null !== $doc && $this->docMentionsPropertyOf($doc, $varName)) {
      return $doc->getText();
    }

    // Walk up through attributes to find a parent foreach
    // PHPStan provides parent nodes via the node's attribute
    $parent = $node->getAttribute('parent');
    while (null !== $parent) {
      if ($parent instanceof Foreach_) {
        $doc = $parent->getDocComment();
        if (null !== $doc && $this->docMentionsPropertyOf($doc, $varName)) {
          return $doc->getText();
        }
        // Also check doc comments on the foreach's stmts (block-level comment)
        foreach ($parent->stmts as $stmt) {
          $stmtDoc = $stmt->getDocComment();
          if (null !== $stmtDoc && $this->docMentionsPropertyOf($stmtDoc, $varName)) {
            return $stmtDoc->getText();
          }
          break; // Only check first statement
        }
      }
      $parent = $parent->getAttribute('parent');
    }

    return null;
  }

  private function docMentionsPropertyOf(Doc $doc, string $varName): bool
  {
    $text = $doc->getText();

    return false !== strpos($text, '@property-of')
      && preg_match('/@property-of\s+\S+\s+\$' . preg_quote($varName, '/') . '/', $text);
  }
}
