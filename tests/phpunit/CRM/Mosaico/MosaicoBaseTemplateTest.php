<?php

use Civi\Test\HeadlessInterface;

require_once __DIR__ . '/TestCase.php';

/**
 * Test the MosaicoBaseTemplate API.
 *
 * @group headless
 * @see cv
 */
class CRM_Mosaico_MosaicoBaseTemplateTest extends CRM_Mosaico_TestCase implements HeadlessInterface {

  public function setUpHeadless() {
    return \Civi\Test::headless()->installMe(__DIR__)->apply();
  }

  public function setUp(): void {
    parent::setUp();
  }

  public function tearDown(): void {
    parent::tearDown();
  }

  public function testGet(): void {
    $result = $this->callAPISuccess('MosaicoBaseTemplate', 'get', []);
    $this->assertTrue(is_array($result['values']));
    $this->assertEquals('versafix-1', $result['values']['versafix-1']['name']);
    $this->assertMatchesRegularExpression(';\.html$;', $result['values']['versafix-1']['path']);
    $this->assertMatchesRegularExpression(';https?://.*mosaico.*versafix-1.*png;', $result['values']['versafix-1']['thumbnail']);
  }

  public function testGetSingle(): void {
    $result = $this->callAPISuccess('MosaicoBaseTemplate', 'getsingle', ['name' => 'tedc15']);
    $this->assertEquals('tedc15', $result['name']);
    $this->assertMatchesRegularExpression(';\.html$;', $result['path']);
    $this->assertMatchesRegularExpression(';https?://.*mosaico.*tedc15.*png;', $result['thumbnail']);
  }

}
