# CRITICAL-002: XSS Vulnerability in Label Text

## Priority
**CRITICAL** - Security vulnerability allowing XSS injection

## Category
Security - Cross-Site Scripting (XSS)

## Affected File
`src/fields/core/Label.php` - Line 52

## Description
The `Label::__invoke()` method does not escape label text before rendering it in HTML. This allows malicious content in field labels to execute arbitrary JavaScript in users' browsers.

## Vulnerable Code
```php
public function __invoke($options = [])
{
    $options = array_merge($this->render_kw, $options);
    $options["for"] = $this->field_id;
    $text = array_key_exists("text", $options) ? $options["text"] : $this->text;
    unset($options['text']);

    return sprintf("<label %s>%s</label>", html_params($options), $text);
    //                                                             ^^^^^ NOT escaped
}
```

## Attack Example
```php
$form->username = new StringField([
    'label' => '<img src=x onerror=alert(document.cookie)>',
]);

// Renders as:
// <label for="username"><img src=x onerror=alert(document.cookie)>Username</label>
```

## Impact
- Cookie theft
- Session hijacking
- DOM manipulation
- Arbitrary JavaScript execution
- Particularly dangerous if labels come from database/user input

## Suggested Fix
Apply the `e()` helper function to escape the label text:

```php
public function __invoke($options = [])
{
    $options = array_merge($this->render_kw, $options);
    $options["for"] = $this->field_id;
    $text = array_key_exists("text", $options) ? $options["text"] : $this->text;
    unset($options['text']);

    return sprintf("<label %s>%s</label>", html_params($options), e($text));
    //                                                             ^^^ Add escaping
}
```

## Testing Required
Add test case in `tests/fields/LabelTest.php`:

```php
public function testLabelXSSEscaping()
{
    $label = new Label("field_id", '<script>alert("XSS")</script>');
    $output = $label();

    $this->assertContains('&lt;script&gt;', $output);
    $this->assertNotContains('<script>', $output);
}

public function testLabelXSSInOptions()
{
    $label = new Label("field_id", "Safe Label");
    $output = $label(['text' => '<img src=x onerror=alert(1)>']);

    $this->assertContains('&lt;img', $output);
    $this->assertNotContains('<img src=x', $output);
}
```

## References
- OWASP XSS Prevention Cheat Sheet: https://cheatsheetseries.owasp.org/cheatsheets/Cross_Site_Scripting_Prevention_Cheat_Sheet.html
