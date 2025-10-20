# CRITICAL-001: XSS Vulnerability in Select Option Labels

## Priority
**CRITICAL** - Security vulnerability allowing XSS injection

## Category
Security - Cross-Site Scripting (XSS)

## Affected File
`src/widgets/core/Select.php` - Line 74

## Description
The `Select::renderOption()` method does not escape option labels before rendering them in HTML. This allows malicious content in choice labels to execute arbitrary JavaScript in users' browsers.

## Vulnerable Code
```php
public static function renderOption($value, $label, $selected, $options = [])
{
    // ...
    return sprintf("<option %s>%s</option>", html_params($options), $label);
    // $label is NOT escaped!
}
```

## Attack Example
```php
$form->country = new SelectField([
    'choices' => [
        ['us', '<script>alert("XSS")</script>'],
        ['uk', '<img src=x onerror=alert(document.cookie)>'],
    ]
]);

// Renders as:
// <option value="us"><script>alert("XSS")</script></option>
// <option value="uk"><img src=x onerror=alert(document.cookie)></option>
```

## Impact
- Cookie theft
- Session hijacking
- Phishing attacks
- Malware distribution
- Arbitrary JavaScript execution in victim's browser

## Suggested Fix
Apply the `e()` helper function to escape the label:

```php
public static function renderOption($value, $label, $selected, $options = [])
{
    // ...
    return sprintf("<option %s>%s</option>", html_params($options), e($label));
    //                                                               ^^^ Add escaping
}
```

## Testing Required
Add test case in `tests/widgets/BasicWidgetsTest.php`:

```php
public function testSelectOptionLabelEscaping()
{
    $field = new DummyField();
    $field->name = "xss";
    $choices = [
        ['val1', '<script>alert("XSS")</script>'],
        ['val2', '<img src=x onerror=alert(1)>'],
    ];

    $output = (new Select)->__invoke($field, ['choices' => $choices]);

    $this->assertContains('&lt;script&gt;', $output);
    $this->assertContains('&lt;img', $output);
    $this->assertNotContains('<script>', $output);
}
```

## References
- OWASP XSS Prevention Cheat Sheet: https://cheatsheetseries.owasp.org/cheatsheets/Cross_Site_Scripting_Prevention_Cheat_Sheet.html
