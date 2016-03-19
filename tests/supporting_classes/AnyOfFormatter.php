<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 2/4/2016
 * Time: 6:38 PM
 */
namespace WTForms\Tests\SupportingClasses;

use WTForms\Interfaces\FormatterInterface;

class AnyOfFormatter implements FormatterInterface
{
  public function run(array $values)
  {
    return implode("::", array_reverse($values));
  }
}