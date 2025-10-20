# MEDIUM-001: Weak HMAC Algorithm (SHA1)

## Priority
**MEDIUM** - Security issue (but not immediately exploitable)

## Category
Security - Cryptography

## Affected File
`src/csrf/session/SessionCSRF.php` - Line 81

## Description
The CSRF token generation uses SHA1 for HMAC calculation. While HMAC-SHA1 is not currently broken, SHA1 has known weaknesses and modern standards recommend SHA256 or better.

## Current Implementation
```php
public function generateCSRFToken(CSRFTokenField $field)
{
    // ...
    $hmac_csrf = hash_hmac('sha1', $csrf_build, $meta->csrf_secret);
    //                      ^^^^^ SHA1 is weak
    return "$expires##$hmac_csrf";
}
```

## Why SHA1 Is Problematic
1. **Collision attacks**: SHA1 has known collision vulnerabilities (SHAttered attack, 2017)
2. **Deprecation**: Major browsers and security standards deprecated SHA1
3. **Future-proofing**: As attacks improve, SHA1 weaknesses will worsen
4. **Standards compliance**: NIST deprecated SHA1 in 2011, disallowed after 2013

## Impact
- **Current**: Low - HMAC-SHA1 still considered secure for authentication
- **Future**: Growing risk as SHA1 weaknesses are exploited
- **Compliance**: May fail security audits requiring SHA256+

## Suggested Fix
Upgrade to SHA256:

```php
public function generateCSRFToken(CSRFTokenField $field)
{
    $meta = $field->meta;
    if (session_status() !== PHP_SESSION_ACTIVE) {
        throw new TypeError("Must have an active session to use SessionCSRF");
    }
    if (!$meta->csrf_secret) {
        throw new \Exception("Must set `csrf_secret` on class Meta for SessionCSRF to work");
    }
    if (!array_key_exists($this->session_key, $_SESSION)) {
        $_SESSION[$this->session_key] = sha1(openssl_random_pseudo_bytes(64));
    }
    if ($this->time_limit) {
        $expires = $this->now()->addSeconds($this->time_limit)->format(str_replace('%', '', self::TIME_FORMAT));
        $csrf_build = sprintf("%s%s", $_SESSION[$this->session_key], $expires);
        $hmac_csrf = hash_hmac('sha256', $csrf_build, $meta->csrf_secret);
        //                      ^^^^^^^ Upgrade to SHA256
        return "$expires##$hmac_csrf";
    } else {
        return hash_hmac('sha256', $_SESSION[$this->session_key], $meta->csrf_secret);
        //                ^^^^^^^ Upgrade to SHA256
    }
}
```

Also update validation (line 111):

```php
protected function validate_csrf_token(Form $form, Field $field)
{
    // ...
    if ($this->time_limit && count($csrf_token) > 1) {
        // ...
        $hmac_compare = hash_hmac('sha256', $csrf_build, $meta->csrf_secret);
        //                         ^^^^^^^ Upgrade to SHA256
        if ($csrf_token[1] != $hmac_compare) {
            throw new ValidationError("CSRF failed");
        }
    } else {
        $hmac_compare = hash_hmac('sha256', $_SESSION[$this->session_key], $meta->csrf_secret);
        //                         ^^^^^^^ Upgrade to SHA256
        if ($csrf_token != $hmac_compare) {
            throw new ValidationError("CSRF failed");
        }
    }
}
```

## Migration Considerations
This is a **breaking change** - existing tokens will become invalid. Options:

### Option 1: Hard Cutover (Recommended for new deployments)
Just update to SHA256. All users will need to refresh their forms once.

### Option 2: Gradual Migration (Recommended for production)
Support both algorithms during transition:

```php
private function generateTokenWithAlgo($algo = 'sha256')
{
    // ... generation logic with $algo parameter
}

protected function validate_csrf_token(Form $form, Field $field)
{
    // Try SHA256 first
    $valid_sha256 = $this->tryValidate($field, 'sha256');
    if ($valid_sha256) {
        return;
    }

    // Fall back to SHA1 for old tokens
    $valid_sha1 = $this->tryValidate($field, 'sha1');
    if ($valid_sha1) {
        return;
    }

    throw new ValidationError("CSRF failed");
}
```

## Testing Required
Update tests in `tests/csrf/SessionCSRFTest.php`:

```php
public function testTokenUsesSHA256()
{
    Pin::pinTime(Carbon::create(2013, 1, 15, 8, 11, 12));
    $form = new Pinned([]);
    $token = $form->csrf_token->current_token;

    // SHA256 produces 64-character hex string (vs 40 for SHA1)
    list($time, $hash) = explode('##', $token);
    $this->assertEquals(64, strlen($hash), "HMAC should be SHA256 (64 chars)");
}
```

## References
- NIST SP 800-107: https://csrc.nist.gov/publications/detail/sp/800-107/rev-1/final
- SHAttered Attack: https://shattered.io/
- RFC 6194 (SHA1 deprecation): https://tools.ietf.org/html/rfc6194
