<?php

use Civi\Test\HeadlessInterface;

require_once __DIR__ . '/TestCase.php';

/**
 * Class CRM_Mosaico_MosaicoComposerTest
 *
 * General integration test for special composition behavior of
 * Mosaico-based mailings.
 *
 * @group headless
 */
class CRM_Mosaico_MosaicoComposerTest extends CRM_Mosaico_TestCase implements HeadlessInterface {

  use \Civi\Test\ContactTestTrait;

  public function setUpHeadless() {
    return \Civi\Test::headless()->installMe(__DIR__)->apply();
  }

  public function getComposerExamples() {
    // array($field, $inputValue, $expectRegex).
    $cases = [];

    $cases[] = [
      'body_html',
      '<p>Hello <img noise="1" src="foo.png"> world.</p>',
      ';noise="1" src="https?://[^"]+/foo.png";',
    ];
    $cases[] = [
      'body_text',
      'Go to [show_link]',
      ';Go to https?://[^"]+civicrm/mailing/view;',
    ];
    $cases[] = [
      'body_text',
      'Go to [unsubscribe_link]',
      ';Go to https?://[^"]+civicrm/mailing/unsubscribe;',
    ];

    return $cases;
  }

  /**
   * Generate a preview of an outgoing email. Check that the field is
   * rendered the expected way.
   *
   * @param string $field
   *   Ex: 'body_html', 'body_text', 'subject'.
   * @param string $inputValue
   *   Ex: 'Hello {contact.first_name}'.
   * @param string $expectRegex
   *   Ex: ';Hello .+;'
   *
   * @dataProvider getComposerExamples
   */
  public function testMailingPreview($field, $inputValue, $expectRegex): void {
    $this->assertEquals('installed', CRM_Extension_System::singleton()->getManager()->getStatus('org.civicrm.flexmailer'));

    $contactId = $this->createLoggedInUser();

    $params = [
      'template_type' => 'mosaico',
      'template_options' => ['nonce' => 1],
      'subject' => 'Placeholder',
      'body_text' => "Placeholder",
      'body_html' => "Placeholder",
      'name' => 'MosaicoComposerTest' . md5(uniqid()),
      'created_id' => $contactId,
      'header_id' => '',
      'footer_id' => '',
      'api.Mailing.preview' => [
        'id' => '$value.id',
        'contact_id' => $contactId,
      ],
      'options' => [
        'force_rollback' => 1,
      ],
    ];
    $params[$field] = $inputValue;

    $result = $this->callAPISuccess('mailing', 'create', $params);
    $previewResult = $result['values'][$result['id']]['api.Mailing.preview'];

    $this->assertMatchesRegularExpression($expectRegex, $previewResult['values'][$field]);
  }

}
