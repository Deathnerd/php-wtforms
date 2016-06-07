<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 6/7/2016
 * Time: 12:38 AM
 */

namespace WTForms\Tests\CSRF;

use Carbon\Carbon;
use WTForms\CSRF\Session\SessionCSRF;
use WTForms\DefaultMeta;
use WTForms\Fields\Core\StringField;
use WTForms\Form;

class Pin
{
  public static $pinned_time = null;

  public static function pinTime($value)
  {
    Pin::$pinned_time = $value;
  }

  public static function unPinTime()
  {
    Pin::$pinned_time = null;
  }
}

class TimePin extends SessionCSRF
{
  public function now()
  {
    return !is_null(Pin::$pinned_time) ? Pin::$pinned_time : parent::now();
  }
}

class FFMeta extends DefaultMeta
{
  public $csrf = true;
  public $csrf_secret = 'foobar';
}

class FF extends Form
{
  public function __construct(array $options = [])
  {
    if (!$this->meta || get_class($this->meta) == "DefaultMeta") {
      $this->meta = new FFMeta();
    }
    parent::__construct($options);
    $this->a = new StringField();
    $this->process($options);
  }
}

class NTLMeta extends DefaultMeta
{
  public $csrf_time_limit = null;
  public $csrf = true;
  public $csrf_secret = "foobar";
}

class NoTimeLimit extends FF
{
  public function __construct(array $options = [])
  {
    $this->meta = new NTLMeta();
    parent::__construct($options);
  }
}

class PinnedMeta extends DefaultMeta
{
  public $csrf_class = '\WTForms\Tests\CSRF\TimePin';
}

class Pinned extends FF
{
  public function __construct(array $options = [])
  {
    $this->meta = new PinnedMeta();
    parent::__construct($options);
  }
}

class SessionCSRFTest extends \PHPUnit_Framework_TestCase
{
  /**
   * @expectedException \WTForms\Exceptions\TypeError
   */
  public function testVariousFailures_1()
  {
    new FF();
  }

  /**
   * @expectedException \Exception
   * @expectedExceptionMessage Must set `csrf_secret` on class Meta for SessionCSRF to work
   */
  public function testVariousFailures_2()
  {
    new FF(["meta" => ["csrf_secret" => null]]);
  }

  public function testNoTimeLimit()
  {
    $_SESSION = [];
    // Phase 1
    $form = new NoTimeLimit([]);
    $this->assertFalse($form->validate());
    $this->assertNotEmpty($form->csrf_token->errors);
    $this->assertArrayHasKey('csrf', $_SESSION);
    // End Phase 1
    $expected_csrf = hash_hmac('sha1', $_SESSION['csrf'], 'foobar');
    $this->assertEquals('##' . $expected_csrf, $form->csrf_token->current_token);
    // Phase 2
    $form = new NoTimeLimit(["formdata" => ["csrf_token" => $form->csrf_token->current_token]]);
    $this->assertTrue($form->validate());
    // End Phase 2
  }

  public function testWithTimeLimit()
  {
    $_SESSION = [];
    $form = new FF([]);
    $this->assertFalse($form->validate());
    $this->assertNotEmpty($form->csrf_token->errors);
    $this->assertArrayHasKey('csrf', $_SESSION);
    $form = new FF(["formdata" => ["csrf_token" => $form->csrf_token->current_token]]);
    $this->assertTrue($form->validate());
  }

  public function testDetailedExpectedValues()
  {
    $_SESSION = ['csrf' => '93fed52fa69a2b2b0bf9c350c8aeeb408b6b6dfa'];
    Pin::pinTime(Carbon::create(2013, 1, 15, 8, 11, 12));
    $form = new Pinned(["meta" => ["csrf" => true, "csrf_secret" => "foobar", "csrf_time_limit" => 1800]]);
    $this->assertFalse($form->validate());
    $this->assertNotEmpty($form->csrf_token->errors);
    $this->assertArrayHasKey('csrf', $_SESSION);
    $token = $form->csrf_token->current_token;
    $this->assertEquals('20130115084112##53812764d65abb8fa88384551a751ca590dff5fb', $token);

    // Validate under normal case
    Pin::pinTime(Carbon::create(2013, 1, 15, 8, 18));
    $form = new Pinned(["meta" => ["csrf" => true, "csrf_secret" => "foobar", "csrf_time_limit" => 1800],
                        "formdata" => ["csrf_token" => $token]]);
    $this->assertTrue($form->validate());
    $new_token = $form->csrf_token->current_token;
    $this->assertFalse($token == $new_token);
    $this->assertEquals('20130115084800##e399e3a6a84860762723672b694134507ba21b58', $new_token);

    // Make sure CSRF fails when we're past time
    Pin::pinTime(Carbon::create(2013, 1, 15, 8, 43));
    $form = new Pinned(["meta" => ["csrf" => true, "csrf_secret" => "foobar", "csrf_time_limit" => 1800],
                        "formdata" => ["csrf_token" => $token]]);
    $this->assertFalse($form->validate());
    $this->assertEquals(["CSRF token expired"], $form->csrf_token->errors);
    // We can succeed with a slightly newer token
    $form = new Pinned(["meta" => ["csrf" => true, "csrf_secret" => "foobar", "csrf_time_limit" => 1800],
                        "formdata" => ["csrf_token" => $new_token]]);
    $this->assertTrue($form->validate());

    Pin::pinTime(Carbon::create(2013, 1, 15, 8, 44));
    $bad_token = '20130115084800##e399e3a6a84860762723672b694134507ba21b59';
    $form = new Pinned(["meta" => ["csrf" => true, "csrf_secret" => "foobar", "csrf_time_limit" => 1800],
                        "formdata" => ["csrf_token" => $bad_token]]);
    $this->assertFalse($form->validate());
  }
}
