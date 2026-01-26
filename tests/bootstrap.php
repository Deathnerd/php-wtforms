<?php

/**
 * PHPUnit bootstrap file.
 *
 * Starts output buffering early to allow session tests to work
 */
ob_start();

require_once __DIR__.'/../vendor/autoload.php';
