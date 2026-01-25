<?php

$finder = PhpCsFixer\Finder::create()
                           ->in(__DIR__ . '/src')
                           ->in(__DIR__ . '/tests');
return (new PhpCsFixer\Config())
  ->setParallelConfig(
    new \PhpCsFixer\Runner\Parallel\ParallelConfig(8)
  )
  ->setRules([
               'phpdoc_var_annotation_correct_order' => true,
               'indentation_type' => true,
               'statement_indentation' => true,
             ])
  ->setIndent('  ')
  ->setLineEnding("\n")
  ->setFinder($finder)
  ->setRiskyAllowed(false);
