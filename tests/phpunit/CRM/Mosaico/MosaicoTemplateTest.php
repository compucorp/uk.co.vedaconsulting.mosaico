<?php

use Civi\Test\HeadlessInterface;

require_once __DIR__ . '/TestCase.php';

/**
 * Test the MosaicoTemplate API.
 *
 * @group headless
 * @see cv
 */
class CRM_Mosaico_MosaicoTemplateTest extends CRM_Mosaico_TestCase implements HeadlessInterface {

  public function setUpHeadless() {
    return \Civi\Test::headless()->installMe(__DIR__)->apply();
  }

  public function setUp(): void {
    parent::setUp();
  }

  public function tearDown(): void {
    CRM_Core_DAO::executeQuery('DELETE FROM civicrm_mosaico_template WHERE title LIKE "MosaicoTemplateTest%"');
    parent::tearDown();
  }

  public function testCreateGetDelete(): void {
    $r = rand();
    $createResult = $this->callAPISuccess('MosaicoTemplate', 'create', [
      'title' => 'MosaicoTemplateTest ' . $r,
      'base' => 'versafix-1',
      'html' => '<p>hello</p>',
      'metadata' => json_encode(['foo' => 'bar']),
      'content' => json_encode(['abc' => 'def']),
    ]);

    $getResult = $this->callAPISuccess('MosaicoTemplate', 'get', [
      'title' => 'MosaicoTemplateTest ' . $r,
    ]);
    $this->assertEquals(1, $getResult['count']);
    $this->assertEquals(1, count($getResult['values']));
    $this->assertTrue(is_array($getResult['values'][$createResult['id']]));
    foreach ($getResult['values'] as $value) {
      $this->assertEquals('<p>hello</p>', $value['html']);
      $this->assertEquals(['foo' => 'bar'], json_decode($value['metadata'], 1));
      $this->assertEquals(['abc' => 'def'], json_decode($value['content'], 1));
    }

    $this->callAPISuccess('MosaicoTemplate', 'delete', [
      'id' => $createResult['id'],
    ]);
  }

  public function testClone(): void {
    $createResult = $this->callAPISuccess('MosaicoTemplate', 'create', [
      'title' => 'MosaicoTemplateTest foo',
      'base' => 'versafix-1',
      'html' => '<p>hello</p>',
      'metadata' => json_encode(['foo' => 'bar']),
      'content' => json_encode(['abc' => 'def']),
    ]);

    $cloneResult = $this->callAPISuccess('MosaicoTemplate', 'clone', [
      'id' => $createResult['id'],
      'title' => 'MosaicoTemplateTest bar',
    ]);
    $clone = $cloneResult['values'][$cloneResult['id']];

    $this->assertNotEquals($clone['id'], $createResult['id']);
    $this->assertEquals('MosaicoTemplateTest bar', $clone['title']);
    $this->assertEquals('versafix-1', $clone['base']);
    $this->assertEquals('<p>hello</p>', $clone['html']);
    $this->assertEquals(json_encode(['foo' => 'bar']), $clone['metadata']);
    $this->assertEquals(json_encode(['abc' => 'def']), $clone['content']);
  }

  /**
   * Test replaceUrls with full URL
   */
  public function testReplaceUrlsFull(): void {
    $createResult = $this->callAPISuccess('MosaicoTemplate', 'create', [
      'title' => 'MosaicoTemplateTest baz',
      'base' => 'versafix-1',
      // As extracted from a real template
      'html' => '<img src=\"https://old-site.org/civicrm/mosaico/img?src=https%3A%2F%2Fold-site.org%2Fsites%2Fdefault%2Ffiles%2Fcivicrm%2Fpersist%2Fcontribute%2Fimages%2Fuploads%2Flogo_bced05c6746c32f9adb3fea8a7060dac.png&amp;method=resize&amp;params=258%2Cnull\">',
      'metadata' => json_encode(['template' => '/sites/default/files/civicrm/mosaico_tpl/CiviVersafix/template-CiviVersafix.html', 'templateversion' => '1.0.5', 'editorversion' => '0.15.0'], JSON_UNESCAPED_SLASHES),
      'content' => json_encode(['src' => 'https://old-site.org/sites/default/files/civicrm/persist/contribute/images/uploads/logo_bced05c6746c32f9adb3fea8a7060dac.png']),
    ]);

    $this->callAPISuccess('MosaicoTemplate', 'replaceurls', [
      'from_url' => 'https://old-site.org/sites/default/files/civicrm/',
      'to_url' => 'https://new-site.org/wp-content/uploads/civicrm/',
    ]);

    $getResult = $this->callAPISuccess('MosaicoTemplate', 'getsingle', ['id' => $createResult['id']]);
    foreach (['html', 'content', 'metadata'] as $element) {
      $this->assertStringNotContainsString('old-site.org', $getResult[$element]);
      $this->assertStringNotContainsString('sites', $getResult[$element]);
      $this->assertStringContainsString('wp-content', $getResult[$element]);
      if ($element != 'metadata') {
        $this->assertStringContainsString('new-site.org', $getResult[$element]);
        $this->assertStringContainsString('logo_bced', $getResult[$element]);
      }
    }
  }

  /**
   * Test replaceUrls with host only
   */
  public function testReplaceUrlsHost(): void {
    $createResult = $this->callAPISuccess('MosaicoTemplate', 'create', [
      'title' => 'MosaicoTemplateTest baz',
      'base' => 'versafix-1',
      // As extracted from a real template
      'html' => '<img src=\"https://old-site.org/civicrm/mosaico/img?src=https%3A%2F%2Fold-site.org%2Fsites%2Fdefault%2Ffiles%2Fcivicrm%2Fpersist%2Fcontribute%2Fimages%2Fuploads%2Flogo_bced05c6746c32f9adb3fea8a7060dac.png&amp;method=resize&amp;params=258%2Cnull\">',
      'metadata' => json_encode(['template' => '/sites/default/files/civicrm/mosaico_tpl/CiviVersafix/template-CiviVersafix.html', 'templateversion' => '1.0.5', 'editorversion' => '0.15.0'], JSON_UNESCAPED_SLASHES),
      'content' => json_encode(['src' => 'https://old-site.org/sites/default/files/civicrm/persist/contribute/images/uploads/logo_bced05c6746c32f9adb3fea8a7060dac.png']),
    ]);

    $this->callAPISuccess('MosaicoTemplate', 'replaceurls', [
      'from_url' => 'https://old-site.org/',
      'to_url' => 'https://new-site.org/',
    ]);

    $getResult = $this->callAPISuccess('MosaicoTemplate', 'getsingle', ['id' => $createResult['id']]);
    foreach (['html', 'content', 'metadata'] as $element) {
      $this->assertStringNotContainsString('old-site.org', $getResult[$element]);
      $this->assertStringContainsString('sites', $getResult[$element]);
      if ($element != 'metadata') {
        $this->assertStringContainsString('new-site.org', $getResult[$element]);
        $this->assertStringContainsString('logo_bced', $getResult[$element]);
      }
    }
  }

  /**
   * Test replaceUrls with path only
   */
  public function testReplaceUrlsPath(): void {
    $createResult = $this->callAPISuccess('MosaicoTemplate', 'create', [
      'title' => 'MosaicoTemplateTest baz',
      'base' => 'versafix-1',
      // As extracted from a real template
      'html' => '<img src=\"https://old-site.org/civicrm/mosaico/img?src=https%3A%2F%2Fold-site.org%2Fsites%2Fdefault%2Ffiles%2Fcivicrm%2Fpersist%2Fcontribute%2Fimages%2Fuploads%2Flogo_bced05c6746c32f9adb3fea8a7060dac.png&amp;method=resize&amp;params=258%2Cnull\">',
      'metadata' => json_encode(['template' => '/sites/default/files/civicrm/mosaico_tpl/CiviVersafix/template-CiviVersafix.html', 'templateversion' => '1.0.5', 'editorversion' => '0.15.0'], JSON_UNESCAPED_SLASHES),
      'content' => json_encode(['src' => 'https://old-site.org/sites/default/files/civicrm/persist/contribute/images/uploads/logo_bced05c6746c32f9adb3fea8a7060dac.png']),
    ]);

    $this->callAPISuccess('MosaicoTemplate', 'replaceurls', [
      'from_url' => '/sites/default/files/civicrm/',
      'to_url' => '/wp-content/uploads/civicrm/',
    ]);

    $getResult = $this->callAPISuccess('MosaicoTemplate', 'getsingle', ['id' => $createResult['id']]);
    foreach (['html', 'content', 'metadata'] as $element) {
      $this->assertStringNotContainsString('sites', $getResult[$element]);
      $this->assertStringContainsString('wp-content', $getResult[$element]);
      if ($element != 'metadata') {
        $this->assertStringContainsString('old-site.org', $getResult[$element]);
        $this->assertStringContainsString('logo_bced', $getResult[$element]);
      }
    }
  }

}
