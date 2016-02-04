<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 2/3/2016
 * Time: 7:51 PM
 */

namespace Deathnerd\WTForms\CSRF\Session;

use Deathnerd\WTForms\CSRF\Core\CSRF;
use Deathnerd\WTForms\CSRF\Core\CSRFTokenField;
use Deathnerd\WTForms\Form;
use Deathnerd\WTForms\Utils\Meta;
use Deathnerd\WTForms\Validators\ValidationError;

/**
 * @property int time_limit
 */
class SessionCSRF extends CSRF
{
    const TIME_FORMAT = "YmdHMS";
    /**
     * @var Meta
     */
    public $form_meta;


    public function setup_form(Form $form)
    {
        $this->form_meta = $form->meta;
        return parent::setup_form($form);
    }

    /**
     * Implementations must override this to provide a method with which one
     * can get a CSRF token for this form.
     *
     * A CSRF token is usually a string that is generated deterministically
     * based on some sort of user data, though it can be anything which you
     * can validate on a subsequent request.
     *
     * @param CSRFTokenField $csrf_token_field The field which is being used for CSRF
     * @return string
     * @throws \Exception
     * @throws \TypeError
     */
    public function generate_csrf_token(CSRFTokenField $csrf_token_field)
    {
        $meta = $this->form_meta;
        if (!$meta->csrf_secret) {
            throw new \Exception("Must set `csrf_secret` on class Meta for SessionCSRF to work");
        }
        if (!$meta->csrf_context) {
            throw new \TypeError("Must provide a session-like object as CSRF context");
        }

        if (!in_array($_SESSION, "csrf")) {
            $_SESSION['csrf'] = sha1(openssl_random_pseudo_bytes(64));
        }

        if ($this->time_limit) {
            $expires = strftime(self::TIME_FORMAT, $this->now()->getTimestamp() + $this->time_limit);
            $csrf_build = sprintf("%s%s", $_SESSION['csrf'], $expires);
        } else {
            $expires = '';
            $csrf_build = $_SESSION['csrf'];
        }
        $hmac_csrf = hash_hmac('sha1', $csrf_build, $meta->csrf_secret);

        return sprintf("%s##%s", $expires, $hmac_csrf);
    }

    /**
     * Override this method to provide custom CSRF validation logic.
     *
     * The default CSRF validation logic simply checks if the recently
     * generated token equals the one we received as formdata.
     *
     * @param Form $form The form which has this CSRF token
     * @param CSRFTokenField $field The CSRF token field
     * @throws ValidationError
     */
    public function validate_csrf_token(Form $form, CSRFTokenField $field)
    {
        $meta = $this->form_meta;
        if (!$field->data || !str_contains($field->data, "##")) {
            throw new ValidationError($field->gettext("CSRF token missing"));
        }

        list($expires, $hmac_csrf) = explode("##", $field->data);

        // TODO: Probably not correct. $_SESSION['csrf'] is a string I think
        $check_val = strval($_SESSION['csrf'] + $expires);

        $hmac_compare = hash_hmac('sha1', $check_val, $meta->csrf_secret);
        if ($hmac_compare !== $hmac_csrf) {
            throw new ValidationError("CSRF failed");
        }

        if ($this->time_limit) {
            $now_formatted = strftime(self::TIME_FORMAT, $this->now()->getTimestamp());
            if ((new \DateTime($now_formatted)) > (new \DateTime($expires))) {
                throw new ValidationError("CSRF token expired");
            }
        }
    }

    /**
     * Get the current time. used for test mocking/overriding mainly.
     * @return \DateTime
     */
    public function now()
    {
        return new \DateTime();
    }

    /**
     * @param $name string
     * @return mixed
     * @link http://php.net/manual/en/language.oop5.overloading.php#language.oop5.overloading.members
     */
    function __get($name)
    {
        if ($name == "time_limit") {
            if (property_exists($this->form_meta, "csrf_time_limit")) {
                return $this->form_meta->csrf_time_limit;
            }
            return $this->now()->diff((new \DateTime(strtotime("+30 days"))));
        } elseif ($name == "session") {
            if (property_exists($this->form_meta->csrf_context, 'session')) {
                return $this->form_meta->csrf_context->session;
            }
            return $this->form_meta->csrf_context;
        }
        return null;
    }


}