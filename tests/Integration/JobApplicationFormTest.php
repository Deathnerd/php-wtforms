<?php

declare(strict_types=1);

namespace WTForms\Tests\Integration;

use PHPUnit\Framework\TestCase;
use WTForms\DefaultMeta;
use WTForms\Fields\Core\BooleanField;
use WTForms\Fields\Core\Field;
use WTForms\Fields\Core\SelectField;
use WTForms\Fields\Core\StringField;
use WTForms\Fields\HTML5\EmailField;
use WTForms\Fields\HTML5\IntegerField;
use WTForms\Fields\HTML5\TelField;
use WTForms\Fields\HTML5\URLField;
use WTForms\Fields\Options\FieldOptions;
use WTForms\Fields\Options\SelectFieldOptions;
use WTForms\Fields\Simple\TextAreaField;
use WTForms\Form;
use WTForms\Validators\DataRequired;
use WTForms\Validators\Email;
use WTForms\Validators\Length;
use WTForms\Validators\NumberRange;
use WTForms\Validators\Optional;
use WTForms\Validators\Options\LengthOptions;
use WTForms\Validators\Options\NumberRangeOptions;
use WTForms\Validators\Options\RegexpOptions;
use WTForms\Validators\Regexp;

class JobApplication
{
  public ?string $full_name = null;
  public ?string $email = null;
  public ?string $phone = null;
  public ?string $position = null;
  public ?int $experience_years = null;
  public ?string $cover_letter = null;
  public ?string $portfolio_url = null;
  public bool $willing_to_relocate = false;
}

class JobApplicationForm extends Form
{
  public function __construct(array $options = [])
  {
    $this->meta = new DefaultMeta();
    parent::__construct($options);

    $this->full_name = new StringField(new FieldOptions([
      'validators' => [
        new DataRequired(),
        new Length('', new LengthOptions(['min' => 2, 'max' => 100])),
      ],
    ]));

    $this->email = new EmailField(new FieldOptions([
      'validators' => [
        new DataRequired(),
        new Email(),
      ],
    ]));

    $this->phone = new TelField(new FieldOptions([
      'validators' => [
        new DataRequired(),
        new Regexp('', new RegexpOptions(['regex' => '/^\+?[\d\s\-()]+$/'])),
      ],
    ]));

    $this->position = new SelectField(new SelectFieldOptions([
      'choices' => [
        ['senior_dev', 'Senior Developer'],
        ['junior_dev', 'Junior Developer'],
        ['qa_engineer', 'QA Engineer'],
        ['designer', 'Designer'],
      ],
      'validators' => [new DataRequired()],
    ]));

    $this->experience_years = new IntegerField(new FieldOptions([
      'validators' => [
        new DataRequired(),
        new NumberRange('', new NumberRangeOptions(['min' => 0, 'max' => 50])),
      ],
    ]));

    $this->cover_letter = new TextAreaField(new FieldOptions([
      'validators' => [
        new DataRequired(),
        new Length('', new LengthOptions(['min' => 50, 'max' => 5000])),
      ],
    ]));

    $this->portfolio_url = new URLField(new FieldOptions([
      'validators' => [new Optional()],
    ]));

    $this->willing_to_relocate = new BooleanField();
  }
}

class JobApplicationFormTest extends TestCase
{
  private function validFormData(): array
  {
    return [
      'full_name'           => ['Jane Doe'],
      'email'               => ['jane.doe@example.com'],
      'phone'               => ['+1 (555) 123-4567'],
      'position'            => ['senior_dev'],
      'experience_years'    => ['8'],
      'cover_letter'        => [str_repeat('I am very passionate about this role and would love to join. ', 2)],
      'portfolio_url'       => ['https://janedoe.dev'],
      'willing_to_relocate' => ['y'],
    ];
  }

  private function createForm(array $formdata = []): JobApplicationForm
  {
    $form = new JobApplicationForm();
    $form->process(['formdata' => $formdata]);

    return $form;
  }

  public function testValidSubmission(): void
  {
    $data = $this->validFormData();
    $form = $this->createForm($data);

    $this->assertTrue($form->validate(), 'Form should validate with valid data');
    $this->assertEquals('Jane Doe', $form->full_name->data);
    $this->assertEquals('jane.doe@example.com', $form->email->data);
    $this->assertEquals('+1 (555) 123-4567', $form->phone->data);
    $this->assertEquals('senior_dev', $form->position->data);
    $this->assertEquals(8, $form->experience_years->data);
    $this->assertStringContainsString('passionate', $form->cover_letter->data);
    $this->assertEquals('https://janedoe.dev', $form->portfolio_url->data);
    $this->assertTrue($form->willing_to_relocate->data);
  }

  public function testEmptySubmissionFailsValidation(): void
  {
    $form = $this->createForm([]);

    $this->assertFalse($form->validate());

    $errors = $form->errors;
    $this->assertArrayHasKey('full_name', $errors);
    $this->assertArrayHasKey('email', $errors);
    $this->assertArrayHasKey('phone', $errors);
    $this->assertArrayHasKey('experience_years', $errors);
    $this->assertArrayHasKey('cover_letter', $errors);
  }

  public function testInvalidEmail(): void
  {
    $data = $this->validFormData();
    $data['email'] = ['not-an-email'];
    $form = $this->createForm($data);

    $this->assertFalse($form->validate());

    $errors = $form->errors;
    $this->assertArrayHasKey('email', $errors);
    $this->assertContains('Invalid email address.', $errors['email']);
    $this->assertArrayNotHasKey('full_name', $errors);
    $this->assertArrayNotHasKey('phone', $errors);
  }

  public function testNameTooShort(): void
  {
    $data = $this->validFormData();
    $data['full_name'] = ['J'];
    $form = $this->createForm($data);

    $this->assertFalse($form->validate());

    $errors = $form->errors;
    $this->assertArrayHasKey('full_name', $errors);
    $this->assertContains('Field must be between 2 and 100 characters long.', $errors['full_name']);
  }

  public function testCoverLetterTooShort(): void
  {
    $data = $this->validFormData();
    $data['cover_letter'] = ['Too short.'];
    $form = $this->createForm($data);

    $this->assertFalse($form->validate());

    $errors = $form->errors;
    $this->assertArrayHasKey('cover_letter', $errors);
    $this->assertContains('Field must be between 50 and 5000 characters long.', $errors['cover_letter']);
  }

  public function testInvalidSelectChoice(): void
  {
    $data = $this->validFormData();
    $data['position'] = ['astronaut'];
    $form = $this->createForm($data);

    $this->assertFalse($form->validate());

    $errors = $form->errors;
    $this->assertArrayHasKey('position', $errors);
    $this->assertContains('Not a valid choice', $errors['position']);
  }

  public function testExperienceOutOfRange(): void
  {
    $data = $this->validFormData();
    $data['experience_years'] = ['-5'];
    $form = $this->createForm($data);

    $this->assertFalse($form->validate());

    $errors = $form->errors;
    $this->assertArrayHasKey('experience_years', $errors);
    $this->assertContains('Number must be between 0 and 50.', $errors['experience_years']);
  }

  public function testOptionalFieldAcceptsEmpty(): void
  {
    $data = $this->validFormData();
    unset($data['portfolio_url']);
    $form = $this->createForm($data);

    $this->assertTrue($form->validate(), 'Form should validate when optional field is omitted');
  }

  public function testRendering(): void
  {
    $form = $this->createForm($this->validFormData());

    // StringField renders as text input
    $nameHtml = $form->full_name->__toString();
    $this->assertStringContainsString('<input', $nameHtml);
    $this->assertStringContainsString('id="full_name"', $nameHtml);
    $this->assertStringContainsString('name="full_name"', $nameHtml);
    $this->assertStringContainsString('type="text"', $nameHtml);

    // EmailField renders as email input
    $emailHtml = $form->email->__toString();
    $this->assertStringContainsString('type="email"', $emailHtml);

    // TelField renders as tel input
    $phoneHtml = $form->phone->__toString();
    $this->assertStringContainsString('type="tel"', $phoneHtml);

    // SelectField renders with options
    $positionHtml = $form->position->__toString();
    $this->assertStringContainsString('<select', $positionHtml);
    $this->assertStringContainsString('value="senior_dev"', $positionHtml);
    $this->assertStringContainsString('Senior Developer', $positionHtml);
    $this->assertStringContainsString('value="junior_dev"', $positionHtml);
    $this->assertStringContainsString('selected', $positionHtml);

    // IntegerField renders as number input
    $expHtml = $form->experience_years->__toString();
    $this->assertStringContainsString('type="number"', $expHtml);

    // TextAreaField renders as textarea
    $coverHtml = $form->cover_letter->__toString();
    $this->assertStringContainsString('<textarea', $coverHtml);
    $this->assertStringContainsString('</textarea>', $coverHtml);

    // URLField renders as url input
    $urlHtml = $form->portfolio_url->__toString();
    $this->assertStringContainsString('type="url"', $urlHtml);

    // BooleanField renders as checkbox
    $relocateHtml = $form->willing_to_relocate->__toString();
    $this->assertStringContainsString('type="checkbox"', $relocateHtml);
  }

  public function testPopulateToDTO(): void
  {
    $form = $this->createForm($this->validFormData());
    $form->validate();

    $dto = $form->populateTo(JobApplication::class);

    $this->assertInstanceOf(JobApplication::class, $dto);
    $this->assertEquals('Jane Doe', $dto->full_name);
    $this->assertEquals('jane.doe@example.com', $dto->email);
    $this->assertEquals('+1 (555) 123-4567', $dto->phone);
    $this->assertEquals('senior_dev', $dto->position);
    $this->assertEquals(8, $dto->experience_years);
    $this->assertStringContainsString('passionate', $dto->cover_letter);
    $this->assertEquals('https://janedoe.dev', $dto->portfolio_url);
    $this->assertTrue($dto->willing_to_relocate);
  }

  public function testFormIteration(): void
  {
    $form = $this->createForm();

    $fieldNames = [];
    foreach ($form as $name => $field) {
      $this->assertInstanceOf(Field::class, $field);
      $fieldNames[] = $name;
    }

    $this->assertEquals([
      'full_name',
      'email',
      'phone',
      'position',
      'experience_years',
      'cover_letter',
      'portfolio_url',
      'willing_to_relocate',
    ], $fieldNames);
  }
}
