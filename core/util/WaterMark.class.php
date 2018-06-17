<?php
// 仅支持png格式的水印文件
class Watermark
{

    public $watermark;

    public $alpha = 0.1;

    public function __construct ($watermark_file_name) {
        $this->watermark = imagecreatefrompng($watermark_file_name);
    }

    public function __desctruct () {
        imagedestroy($this->watermark);
    }

    public function addWatermark (&$base) {
        $watermark = $this->watermark;
        $f = $this->alpha;

        $base_width = imagesx($base);
        $base_height = imagesy($base);
        $watermark_width = imagesx($watermark);
        $watermark_height = imagesy($watermark);

        $left = $base_width / 2 - $watermark_width / 2 + rand(- 4, 4);
        $top = $base_height * 0.5 - $watermark_height / 2 + rand(- 4, 4);

        for ($x = 0; $x < $watermark_width; $x ++)
            for ($y = 0; $y < $watermark_height; $y ++) {
                $origin_color = imagecolorat($base, $left + $x, $top + $y);
                $r0 = ($origin_color >> 16) & 0xFF;
                $g0 = ($origin_color >> 8) & 0xFF;
                $b0 = $origin_color & 0xFF;
                $watermark_color = imagecolorat($watermark, $x, $y);
                $r1 = ($watermark_color >> 16) & 0xFF;
                $g1 = ($watermark_color >> 8) & 0xFF;
                $b1 = $watermark_color & 0xFF;

                $r = $r0 * $r1 * $f / 0xFF + $r0 * (1 - $f);
                $g = $g0 * $g1 * $f / 0xFF + $g0 * (1 - $f);
                $b = $b0 * $b1 * $f / 0xFF + $b0 * (1 - $f);

                $new_color = imagecolorallocate($base, $r, $g, $b);
                imagesetpixel($base, $left + $x, $top + $y, $new_color);
            }
    }
}