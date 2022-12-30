<?php

namespace Service;

class AvatarGeneratorService
{

    private string|false $image;

    public function __construct($string, $size = 400, $blocks = 5)
    {
        $generate = ceil($blocks / 2);

        $hash = md5($string);
        $hashsize = $blocks * $generate;
        $hash = str_pad($hash, $hashsize, $hash);

        $color = substr($hash, 0, 6);
        $blocksize = $size / $blocks;

        $image = imagecreate($size, $size);
        $color = imagecolorallocate($image, hexdec(substr($color, 0, 2)), hexdec(substr($color, 2, 2)), hexdec(substr($color, 4, 2)));
        $bg = imagecolorallocate($image, 255, 255, 255);

        for ($x = 0; $x < $blocks; $x++) {
            for ($y = 0; $y < $blocks; $y++) {
                if ($x < $generate) {
                    $pixel = hexdec($hash[($x * $blocks) + $y]) % 2 == 0;
                } else {
                    $pixel = hexdec($hash[(($blocks - 1 - $x) * $blocks) + $y]) % 2 == 0;
                }

                $pixelColor = $bg;
                if ($pixel) {
                    $pixelColor = $color;
                }
                imagefilledrectangle($image, $x * $blocksize, $y * $blocksize, ($x + 1) * $blocksize, ($y + 1) * $blocksize, $pixelColor);
            }
        }

        ob_start();
        imagepng($image);
        $image_data = ob_get_contents();
        ob_end_clean();
        $this->image = $image_data;
    }

    public function display(): void
    {
        header('Content-type: image/png');
        echo($this->image);
    }

    public function base64(): string
    {
        return 'data:image/png;base64,' . base64_encode($this->image);
    }

    public function save($filename)
    {
        if (file_put_contents($filename, $this->image)) {
            return $filename;
        }
    }

}