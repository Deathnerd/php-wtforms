<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 6/7/2016
 * Time: 12:38 AM
 */

namespace WTForms\Tests\CSRF;

use WTForms\CSRF\Session\SessionCSRF;
use WTForms\DefaultMeta;
use WTForms\Fields\Core\StringField;
use WTForms\Form;

class TimePin extends SessionCSRF
{
  public static $pinned_time = null;

  public function pinTime($value)
  {
    $original = self::$pinned_time;
    self::$pinned_time = $value;
    yield;
    self::$pinned_time = $original;
  }

  public function now()
  {
    return self::$pinned_time;
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
   */
  public function testVariousFailures_2()
  {
    new FF(["meta" => ["csrf_secret" => null]]);
  }

  /*  public function testNoTimeLimit()
    {
      $_SESSION = [];
      $form = new NoTimeLimit(["meta" => ["csrf_context" => $_SESSION]]);
      $this->assertFalse($form->validate());
      $this->assertNotEmpty($form->csrf_token->errors);
      $this->assertArrayHasKey('csrf', $_SESSION);
      $expected_csrf = hash_hmac('sha1', $_SESSION['csrf'], 'foobar');
      $this->assertEquals('##'.$expected_csrf, $form->csrf_token->current_token);
  
  //    def _test_phase2(self, form_class, session, token, must_validate=True):
  //        form = form_class(
  //            formdata=DummyPostData(csrf_token=token),
  //            meta={'csrf_context': session}
  //        )
  //        if must_validate:
  //            assert form.validate()
  //        return form
    }*/
}
