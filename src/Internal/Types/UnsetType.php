<?php

namespace WTForms\Fields\Support;

/**
 * Sentinel type representing the absence of a value, distinct from null.
 *
 * Use as a default parameter to distinguish "no argument passed" from "null passed explicitly".
 */
final readonly class UnsetType{}