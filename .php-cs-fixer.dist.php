<?php

$finder = PhpCsFixer\Finder::create()
  ->in(__DIR__.'/phpstan')
  ->in(__DIR__.'/src')
  ->in(__DIR__.'/tests')
->append([
  __FILE__, // The php-cs-fixer config file itself
  'rector.php', // Rector's config file
]);

return (new PhpCsFixer\Config())
  ->setParallelConfig(
    new PhpCsFixer\Runner\Parallel\ParallelConfig(8)
  )
  ->setRules([
    '@Symfony'                            => true,
    'phpdoc_var_annotation_correct_order' => true,
    'indentation_type'                    => true,
    'statement_indentation'               => true,
    'array_syntax'                        => ['syntax' => 'short'],
    'array_indentation'                   => true,
    'trim_array_spaces'                   => true,
    'whitespace_after_comma_in_array'     => ['ensure_single_space' => true],
    'no_whitespace_before_comma_in_array' => true,
    'binary_operator_spaces'              => [
      'default'   => 'single_space',
      'operators' => [
        '=>' => 'align_single_space_minimal',
      ],
    ],
  ])
  ->setIndent('  ')
  ->setLineEnding("\n")
  ->setFinder($finder)
  ->setRiskyAllowed(false);
