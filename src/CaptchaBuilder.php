<?php

namespace VulcanPhp\Captcha;

/**
 * Builds a new captcha image
 *
 * @author Shahin Moyshan <shahin.moyshan2@gmail.com>
 * @author Gregwar <g.passault@gmail.com>
 * @author Jeremy Livingston <jeremy.j.livingston@gmail.com>
 */
class CaptchaBuilder
{
    protected $image, $phrase;

    public function __construct(string $phrase)
    {
        $this->phrase = $phrase;
    }

    public static function generate(string $phrase, ...$args): string
    {
        $image = new self($phrase);
        return $image->build(...$args)->inline();
    }

    /**
     * Draw lines over the image
     */
    protected function drawLine($image, $width, $height, $tcol = null)
    {
        $red    = $this->rand(100, 255);
        $green  = $this->rand(100, 255);
        $blue   = $this->rand(100, 255);

        if ($tcol === null) {
            $tcol = imagecolorallocate($image, $red, $green, $blue);
        }

        if ($this->rand(0, 1)) { // Horizontal
            $Xa   = $this->rand(0, $width / 2);
            $Ya   = $this->rand(0, $height);
            $Xb   = $this->rand($width / 2, $width);
            $Yb   = $this->rand(0, $height);
        } else { // Vertical
            $Xa   = $this->rand(0, $width);
            $Ya   = $this->rand(0, $height / 2);
            $Xb   = $this->rand(0, $width);
            $Yb   = $this->rand($height / 2, $height);
        }
        imagesetthickness($image, $this->rand(1, 3));
        imageline($image, $Xa, $Ya, $Xb, $Yb, $tcol);
    }

    /**
     * Apply some post effects
     */
    protected function postEffect($image)
    {
        if (!function_exists('imagefilter')) {
            return;
        }

        // Negate ?
        if ($this->rand(0, 1) == 0) {
            imagefilter($image, IMG_FILTER_NEGATE);
        }

        // Edge ?
        if ($this->rand(0, 10) == 0) {
            imagefilter($image, IMG_FILTER_EDGEDETECT);
        }

        // Contrast
        imagefilter($image, IMG_FILTER_CONTRAST, $this->rand(-50, 10));

        // Colorize
        if ($this->rand(0, 5) == 0) {
            imagefilter($image, IMG_FILTER_COLORIZE, $this->rand(-80, 50), $this->rand(-80, 50), $this->rand(-80, 50));
        }
    }

    /**
     * Writes the phrase on the image
     */
    protected function writePhrase($image, $phrase, $font, $width, $height)
    {
        $length = mb_strlen($phrase);
        if ($length === 0) {
            return \imagecolorallocate($image, 0, 0, 0);
        }

        // Gets the text size and start position
        $size = (int) round($width / $length) - $this->rand(0, 3) - 1;
        $box = \imagettfbbox($size, 0, $font, $phrase);
        $textWidth = $box[2] - $box[0];
        $textHeight = $box[1] - $box[7];
        $x = (int) round(($width - $textWidth) / 2);
        $y = (int) round(($height - $textHeight) / 2) + $size;

        $textColor = array($this->rand(0, 150), $this->rand(0, 150), $this->rand(0, 150));
        $col = \imagecolorallocate($image, $textColor[0], $textColor[1], $textColor[2]);

        // Write the letters one by one, with random angle
        for ($i = 0; $i < $length; $i++) {
            $symbol = mb_substr($phrase, $i, 1);
            $box = \imagettfbbox($size, 0, $font, $symbol);
            $w = $box[2] - $box[0];
            $angle = $this->rand(-8, 8);
            $offset = $this->rand(-5, 5);
            \imagettftext($image, $size, $angle, $x, $y + $offset, $col, $font, $symbol);
            $x += $w;
        }

        return $col;
    }

    /**
     * Generate the image
     */
    public function build(int $width = 150, int $height = 40)
    {
        $font = __DIR__ . '/resources/font/captcha' . $this->rand(1, 5) . '.ttf';

        // if background images list is not set, use a color fill as a background
        $image  = imagecreatetruecolor($width, $height);
        $bg     = imagecolorallocate($image, $this->rand(200, 255), $this->rand(200, 255), $this->rand(200, 255));

        imagefill($image, 0, 0, $bg);

        // Apply effects
        $square = $width * $height;
        $effects = $this->rand($square / 3000, $square / 2000);

        // set the lines to draw in front of the text
        for ($e = 0; $e < $effects; $e++) {
            $this->drawLine($image, $width, $height);
        }

        // Write CAPTCHA text
        $color = $this->writePhrase($image, $this->phrase, $font, $width, $height);

        // Apply effects
        $square = $width * $height;
        $effects = $this->rand($square / 3000, $square / 2000);

        // set the lines to draw in front of the text
        for ($e = 0; $e < $effects; $e++) {
            $this->drawLine($image, $width, $height, $color);
        }

        // Distort the image
        $image = $this->distort($image, $width, $height, $bg);

        // Post effects
        $this->postEffect($image);

        $this->image = $image;

        return $this;
    }

    /**
     * Distorts the image
     */
    public function distort($image, $width, $height, $bg)
    {
        $contents = imagecreatetruecolor($width, $height);
        $X          = $this->rand(0, $width);
        $Y          = $this->rand(0, $height);
        $phase      = $this->rand(0, 10);
        $scale      = 1.1 + $this->rand(0, 10000) / 30000;
        for ($x = 0; $x < $width; $x++) {
            for ($y = 0; $y < $height; $y++) {
                $Vx = $x - $X;
                $Vy = $y - $Y;
                $Vn = sqrt($Vx * $Vx + $Vy * $Vy);

                if ($Vn != 0) {
                    $Vn2 = $Vn + 4 * sin($Vn / 30);
                    $nX  = $X + ($Vx * $Vn2 / $Vn);
                    $nY  = $Y + ($Vy * $Vn2 / $Vn);
                } else {
                    $nX = $X;
                    $nY = $Y;
                }
                $nY = $nY + $scale * sin($phase + $nX * 0.2);
                $p  = $this->interpolate(
                    $nX - floor($nX),
                    $nY - floor($nY),
                    $this->getCol($image, floor($nX), floor($nY), $bg),
                    $this->getCol($image, ceil($nX), floor($nY), $bg),
                    $this->getCol($image, floor($nX), ceil($nY), $bg),
                    $this->getCol($image, ceil($nX), ceil($nY), $bg)
                );

                if ($p == 0) {
                    $p = $bg;
                }

                imagesetpixel($contents, $x, $y, $p);
            }
        }

        return $contents;
    }

    /**
     * Gets the image GD
     */
    public function getGd()
    {
        return $this->image;
    }

    /**
     * Gets the image contents
     */
    public function raw($quality = 90)
    {
        ob_start();
        imagejpeg($this->getGd(), null, $quality);

        return ob_get_clean();
    }

    /**
     * Gets the HTML inline base64
     */
    public function inline($quality = 90): string
    {
        return 'data:image/jpeg;base64,' . base64_encode($this->raw($quality));
    }

    /**
     * Returns a random number or the next number in the
     */
    protected function rand($min, $max)
    {
        return mt_rand((int)$min, (int)$max);
    }

    protected function interpolate($x, $y, $nw, $ne, $sw, $se)
    {
        list($r0, $g0, $b0) = $this->getRGB($nw);
        list($r1, $g1, $b1) = $this->getRGB($ne);
        list($r2, $g2, $b2) = $this->getRGB($sw);
        list($r3, $g3, $b3) = $this->getRGB($se);

        $cx = 1.0 - $x;
        $cy = 1.0 - $y;

        $m0 = $cx * $r0 + $x * $r1;
        $m1 = $cx * $r2 + $x * $r3;
        $r  = (int) ($cy * $m0 + $y * $m1);

        $m0 = $cx * $g0 + $x * $g1;
        $m1 = $cx * $g2 + $x * $g3;
        $g  = (int) ($cy * $m0 + $y * $m1);

        $m0 = $cx * $b0 + $x * $b1;
        $m1 = $cx * $b2 + $x * $b3;
        $b  = (int) ($cy * $m0 + $y * $m1);

        return ($r << 16) | ($g << 8) | $b;
    }

    protected function getCol($image, $x, $y, $background)
    {
        $L = imagesx($image);
        $H = imagesy($image);
        if ($x < 0 || $x >= $L || $y < 0 || $y >= $H) {
            return $background;
        }

        return imagecolorat($image, $x, $y);
    }

    protected function getRGB($col)
    {
        return array(
            (int) ($col >> 16) & 0xff,
            (int) ($col >> 8) & 0xff,
            (int) ($col) & 0xff,
        );
    }
}
