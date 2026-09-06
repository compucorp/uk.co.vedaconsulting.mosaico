<?php

use Civi\Test\HeadlessInterface;

require_once __DIR__ . '/../TestCase.php';

/**
 * Test image operations via the intervention/image v3 graphics provider.
 *
 * @group headless
 * @see cv
 */
class CRM_Mosaico_Graphics_InterventionTest extends CRM_Mosaico_TestCase implements HeadlessInterface {

  private string $tmpDir;

  public function setUpHeadless() {
    return \Civi\Test::headless()->installMe(__DIR__)->apply();
  }

  protected function setUp(): void {
    parent::setUp();
    $this->tmpDir = sys_get_temp_dir() . '/mosaico_iv_' . uniqid();
    mkdir($this->tmpDir);
    Civi::settings()->set('mosaico_scale_width_limit1', '');
    Civi::settings()->set('mosaico_scale_factor1', '');
    Civi::settings()->set('mosaico_scale_width_limit2', '');
    Civi::settings()->set('mosaico_scale_factor2', '');
  }

  protected function tearDown(): void {
    parent::tearDown();
    array_map('unlink', glob($this->tmpDir . '/*') ?: []);
    rmdir($this->tmpDir);
  }

  private function createSourcePng(int $width, int $height, string $path): void {
    $img = imagecreatetruecolor($width, $height);
    $white = imagecolorallocate($img, 255, 255, 255);
    imagefill($img, 0, 0, $white);
    imagepng($img, $path);
    if (version_compare(PHP_VERSION, '8.5', '<')) {
      // phpcs:ignore PHPCompatibility.FunctionUse.RemovedFunctions.imagedestroyDeprecated
      imagedestroy($img);
    }
  }

  /**
   * Verify that intervention/image classes are resolvable via the extension autoloader.
   */
  public function testIsClassLoaded(): void {
    $this->assertTrue(CRM_Mosaico_Graphics_Intervention::isClassLoaded());
  }

  /**
   * Test createResizedImage produces correct dimensions when both width and height are given.
   */
  public function testCreateResizedImageBothDimensions(): void {
    $src  = $this->tmpDir . '/src.png';
    $dest = $this->tmpDir . '/resized.png';
    $this->createSourcePng(400, 300, $src);

    $intervention = new CRM_Mosaico_Graphics_Intervention(['driver' => 'gd']);
    $intervention->createResizedImage($src, $dest, 200, 150);

    $this->assertFileExists($dest);
    [$w, $h] = getimagesize($dest);
    $this->assertEquals(200, $w);
    $this->assertEquals(150, $h);
  }

  /**
   * Test createCoveredImage crops and fills a square target correctly.
   */
  public function testCreateCoveredImageSquare(): void {
    $src  = $this->tmpDir . '/src.png';
    $dest = $this->tmpDir . '/covered.png';
    $this->createSourcePng(400, 300, $src);

    $intervention = new CRM_Mosaico_Graphics_Intervention(['driver' => 'gd']);
    $intervention->createCoveredImage($src, $dest, 200, 200);

    $this->assertFileExists($dest);
    [$w, $h] = getimagesize($dest);
    $this->assertEquals(200, $w);
    $this->assertEquals(200, $h);
  }

  /**
   * Test createCoveredImage crops and fills a wide target correctly.
   */
  public function testCreateCoveredImageWide(): void {
    $src  = $this->tmpDir . '/src.png';
    $dest = $this->tmpDir . '/covered_wide.png';
    $this->createSourcePng(400, 300, $src);

    $intervention = new CRM_Mosaico_Graphics_Intervention(['driver' => 'gd']);
    $intervention->createCoveredImage($src, $dest, 300, 100);

    $this->assertFileExists($dest);
    [$w, $h] = getimagesize($dest);
    $this->assertEquals(300, $w);
    $this->assertEquals(100, $h);
  }

  /**
   * Test sendPlaceholder outputs a valid PNG binary stream.
   */
  public function testSendPlaceholderOutputsPng(): void {
    $intervention = new CRM_Mosaico_Graphics_Intervention(['driver' => 'gd']);
    ob_start();
    $intervention->sendPlaceholder(200, 100);
    $output = ob_get_clean();

    // PNG magic bytes
    $this->assertStringStartsWith("\x89PNG", $output);
  }

  /**
   * A source whose aspect ratio differs from the target must be fitted INSIDE the box rather than
   * stretched to fill it. testCreateResizedImageBothDimensions cannot catch this: its 400x300
   * fixture already has the target aspect ratio, so it passes either way.
   */
  public function testCreateResizedImageBothDimensionsPreservesAspectRatio(): void {
    $src  = $this->tmpDir . '/src-wide.png';
    $dest = $this->tmpDir . '/resized-wide.png';
    $this->createSourcePng(600, 200, $src);

    $intervention = new CRM_Mosaico_Graphics_Intervention(['driver' => 'gd']);
    $intervention->createResizedImage($src, $dest, 90, 90);

    $this->assertFileExists($dest);
    [$w, $h] = getimagesize($dest);
    $this->assertEquals(90, $w);
    $this->assertEquals(30, $h);
  }

}
