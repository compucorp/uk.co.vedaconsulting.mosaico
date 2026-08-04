<?php

use Civi\FlexMailer\FlexMailer as FM;
use Civi\Test\HeadlessInterface;

require_once __DIR__ . '/TestCase.php';

/**
 * Class CRM_Mosaico_DefaultComposerScopeTest
 *
 * Composing a Mosaico mailing has to suppress FlexMailer's default composer,
 * and has to stop suppressing it when the compose event ends. The default
 * composer is a container singleton: a suppression that outlives the event
 * empties the body of every non-Mosaico mailing composed later in the same PHP
 * process (a cron run delivering several mailings), and delivery silently skips
 * a task with no content.
 *
 * @group headless
 */
class CRM_Mosaico_DefaultComposerScopeTest extends CRM_Mosaico_TestCase implements HeadlessInterface {

  use \Civi\Test\ContactTestTrait;

  /**
   * @var int
   */
  private $contactId;

  /**
   * @var bool
   */
  private $defaultComposerWasActive;

  public function setUpHeadless() {
    return \Civi\Test::headless()->installMe(__DIR__)->apply();
  }

  public function setUp(): void {
    parent::setUp();
    $this->assertEquals('installed', CRM_Extension_System::singleton()->getManager()->getStatus('org.civicrm.flexmailer'));
    $this->contactId = $this->createLoggedInUser();
    $this->defaultComposerWasActive = $this->getDefaultComposer()->isActive();
  }

  public function tearDown(): void {
    // A failed assertion can leave the singleton disabled, which would empty
    // the mailings of every later test in this process.
    $this->getDefaultComposer()->setActive($this->defaultComposerWasActive);
    parent::tearDown();
  }

  /**
   * A non-Mosaico mailing composed after a Mosaico one still renders a body.
   */
  public function testComposeAfterMosaicoMailing(): void {
    $this->assertStringContainsString('before mosaico', $this->previewHtml('traditional', 'before mosaico'));
    $this->assertStringContainsString('mosaico body', $this->previewHtml('mosaico', 'mosaico body'));

    $this->assertTrue($this->getDefaultComposer()->isActive(),
      'Composing a Mosaico mailing left the default composer disabled.');
    $this->assertStringContainsString('after mosaico', $this->previewHtml('traditional', 'after mosaico'));
  }

  /**
   * The default composer is still suppressed where Mosaico needs it to be:
   * within the compose event, at the slot the default composer runs in.
   */
  public function testDefaultComposerSuppressedDuringMosaicoCompose(): void {
    $observed = [];
    $observer = function () use (&$observed) {
      $observed[] = $this->getDefaultComposer()->isActive();
    };
    \Civi::dispatcher()->addListener(FM::EVENT_COMPOSE, $observer, FM::WEIGHT_MAIN - 100);

    try {
      $this->previewHtml('mosaico', 'mosaico');
      $this->previewHtml('traditional', 'traditional');
    }
    finally {
      \Civi::dispatcher()->removeListener(FM::EVENT_COMPOSE, $observer);
    }

    $this->assertSame([FALSE, TRUE], $observed);
  }

  /**
   * The bracket restores the value it saved rather than forcing the composer
   * back on, so it cannot enable one that something else disabled on purpose.
   */
  public function testDisabledDefaultComposerStaysDisabled(): void {
    $this->getDefaultComposer()->setActive(FALSE);

    $this->assertStringContainsString('mosaico body', $this->previewHtml('mosaico', 'mosaico body'));
    $this->assertFalse($this->getDefaultComposer()->isActive());
  }

  /**
   * @return \Civi\FlexMailer\Listener\DefaultComposer
   */
  private function getDefaultComposer() {
    return \Civi::service('civi_flexmailer_default_composer');
  }

  /**
   * Compose one mailing through the preview path and return its html body.
   *
   * @param string $templateType
   *   Ex: 'mosaico', 'traditional'.
   * @param string $marker
   *   Text to put in the body, so the caller can tell it was composed.
   *
   * @return string
   */
  private function previewHtml(string $templateType, string $marker): string {
    $params = [
      'template_type' => $templateType,
      'name' => 'DefaultComposerScopeTest' . md5(uniqid()),
      'subject' => 'Placeholder',
      'body_html' => "<p>$marker</p>",
      'body_text' => $marker,
      'created_id' => $this->contactId,
      'header_id' => '',
      'footer_id' => '',
      'api.Mailing.preview' => [
        'id' => '$value.id',
        'contact_id' => $this->contactId,
      ],
      'options' => [
        'force_rollback' => 1,
      ],
    ];
    if ($templateType === 'mosaico') {
      $params['template_options'] = ['nonce' => 1];
    }

    $result = $this->callAPISuccess('Mailing', 'create', $params);
    $preview = $result['values'][$result['id']]['api.Mailing.preview']['values'];

    // An empty body only means what this test thinks it means if FlexMailer
    // composed the preview.
    $this->assertSame('flexmailer', $preview['_rendered_by_'] ?? NULL);

    return (string) $preview['body_html'];
  }

}
