<?php

use CRM_Mosaico_ExtensionUtil as E;
use Intervention\Image\ImageManager;

/**
 * Class CRM_Mosaico_Graphics_Intervention
 *
 * @see https://github.com/voidlabs/mosaico/blob/master/backend/README.txt
 * @see https://image.intervention.io/v3
 */
class CRM_Mosaico_Graphics_Intervention extends CRM_Mosaico_Graphics_Interface {

  const FONT_PATH = 'packages/mosaico/dist/rs/notoregular/noto-sans-400-normal.ttf';

  private ImageManager $manager;

  /**
   * CRM_Mosaico_Graphics_Intervention constructor.
   *
   * @param array $options  Expects ['driver' => 'gd'] or ['driver' => 'imagick']
   * @throws CRM_Mosaico_Graphics_Exception
   */
  public function __construct($options) {
    if (!self::isClassLoaded()) {
      throw new CRM_Mosaico_Graphics_Exception("Failed to locate classes for \"intervention/image\" API. Ensure that you have downloaded all dependencies.");
    }
    $driver = match($options['driver'] ?? 'gd') {
      'imagick' => new \Intervention\Image\Drivers\Imagick\Driver(),
      default   => new \Intervention\Image\Drivers\Gd\Driver(),
    };
    $this->manager = new ImageManager($driver);
  }

  /**
   * @return bool
   */
  public static function isClassLoaded(): bool {
    self::loadVendorAutoloader();
    return class_exists('Intervention\Image\ImageManager');
  }

  private static function loadVendorAutoloader(): void {
    $path = \CRM_Mosaico_ExtensionUtil::path('vendor/autoload.php');
    if (file_exists($path)) {
      require_once $path;
    }
  }

  public function sendPlaceholder($width, $height): void {
    $img = $this->manager->create($width, $height);
    $img->fill('#707070');

    $x = 0;
    $y = 0;
    $size = 40;
    while ($y < $height) {
      $points1 = [
        ["x" => $x, "y" => $y],
        ["x" => $x + $size, "y" => $y],
        ["x" => $x + $size * 2, "y" => $y + $size],
        ["x" => $x + $size * 2, "y" => $y + $size * 2],
      ];
      $img->drawPolygon(function ($polygon) use ($points1) {
        foreach ($points1 as $p) {
          $polygon->point($p['x'], $p['y']);
        }
        $polygon->background('#808080');
      });
      $points2 = [
        ["x" => $x, "y" => $y + $size],
        ["x" => $x + $size, "y" => $y + $size * 2],
        ["x" => $x, "y" => $y + $size * 2],
      ];
      $img->drawPolygon(function ($polygon) use ($points2) {
        foreach ($points2 as $p) {
          $polygon->point($p['x'], $p['y']);
        }
        $polygon->background('#808080');
      });
      $x += $size * 2;
      if ($x > $width) {
        $x = 0;
        $y += $size * 2;
      }
    }

    $img->text("{$width} x {$height}", (int) round($width / 2), (int) round($height / 2), function ($font) use ($width) {
      $font->filename(E::path(self::FONT_PATH));
      $font->size((int) round($width / 5));
      $font->align('center');
      $font->valign('middle');
      $font->color('#B0B0B0');
    });

    CRM_Utils_System::setHttpHeader('Content-Type', 'image/png');
    echo $img->toPng();
  }

  public function createResizedImage($srcFile, $destFile, $width, $height): void {
    $config = CRM_Mosaico_Utils::getConfig();
    $img = $this->manager->read($srcFile);
    $this->adjustResizeDimensions($img->width(), $img->height(), $width, $height);

    if ($width && $height) {
      // "Inside the dimensions" per the interface docblock: resize() would stretch to exactly WxH.
      $img->scale($width, $height);
    }
    elseif ($width && !$height) {
      $mobileMinWidth = $config['MOBILE_MIN_WIDTH'];
      $img->scale(width: max($width, $mobileMinWidth));
    }
    elseif (!$width && $height) {
      $mobileMinHeight = (int) ceil($img->height() * $config['MOBILE_MIN_WIDTH'] / $img->width());
      $img->scale(height: max($height, $mobileMinHeight));
    }

    $img->save($destFile);
  }

  public function createCoveredImage($srcFile, $destFile, $width, $height): void {
    $img = $this->manager->read($srcFile);
    $this->adjustResizeDimensions($img->width(), $img->height(), $width, $height);

    $ratios = [];
    if ($width) {
      $ratios[] = $width / $img->width();
    }
    if ($height) {
      $ratios[] = $height / $img->height();
    }
    if (!$width && !$height) {
      throw new \Exception("Must specify a width and/or height");
    }

    $scaledRatio = max($ratios);
    $scaledBox = [
      'w' => (int) round($img->width() * $scaledRatio),
      'h' => (int) round($img->height() * $scaledRatio),
    ];
    $tgtBox = [
      'w' => $width  ?: (int) round($img->width() * $height / $img->height()),
      'h' => $height ?: (int) round($img->height() * $width / $img->width()),
    ];

    $img->resize($scaledBox['w'], $scaledBox['h']);

    // Make tgtBox from the center of the scaledBox.
    $img->crop(
      $tgtBox['w'],
      $tgtBox['h'],
      (int) round(($scaledBox['w'] - $tgtBox['w']) / 2),
      (int) round(($scaledBox['h'] - $tgtBox['h']) / 2)
    );

    $img->save($destFile);
  }

}
