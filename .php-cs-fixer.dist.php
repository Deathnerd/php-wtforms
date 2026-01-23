<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ . '/src')
    ->in(__DIR__ . '/tests')
;

return (new PhpCsFixer\Config())
    ->setRules([
        'phpdoc_var_annotation_correct_order' => true,
    ])
    ->setFinder($finder)
    ->setRiskyAllowed(false)
;
