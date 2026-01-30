<?php

/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 3/18/2016
 * Time: 9:47 PM.
 */

namespace WTForms\Tests\Common;

use PHPUnit\Framework\TestCase;

class HelpersTest extends TestCase
{
  public function testHtmlParams()
  {
    $actual = html_params([
      'foo'          => true,
      'bar'          => 'baz',
      'data_value'   => 'shazbot',
      'id'           => 'some_id',
      'class__'      => ['fa', 'fa-envelope'],
      'for'          => 'another_id',
      'data_foo_bar' => 'data-foo-bar',
    ]);
    $this->assertStringContainsString('foo', $actual);
    $this->assertStringContainsString('bar="baz"', $actual);
    $this->assertStringContainsString('data-value="shazbot"', $actual);
    $this->assertStringContainsString('id="some_id"', $actual);
    $this->assertStringContainsString('class="fa fa-envelope"', $actual);
    $this->assertStringContainsString('for="another_id"', $actual);
    $this->assertStringContainsString('data-foo-bar', $actual);
  }

  public function testVsprintfNamed()
  {
    $expected = 'Sally walks with Rick';
    $actual = vsprintf_named('%(foo)s walks with %(bar)s', ['foo' => 'Sally', 'bar' => 'Rick']);
    $this->assertEquals($expected, $actual);
    $expected = 'Sally has 5 cats.';
    $actual = vsprintf_named('%(foo)s has %(num)d cats.', ['foo' => 'Sally', 'num' => 5]);
    $this->assertEquals($expected, $actual);
    $expected = "Sally is not a crazy cat lady; she's just going through a rough romantic period in her life";
    $actual = vsprintf_named('%(foo)s is not a crazy cat lady; '.
        "she's just going through a rough romantic period in her life", ['foo' => 'Sally']);
    $this->assertEquals($expected, $actual);
  }
}
