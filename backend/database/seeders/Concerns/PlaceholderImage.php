<?php

namespace Database\Seeders\Concerns;

/**
 * Generates simple placeholder PNGs with GD so seeding needs no external files.
 */
class PlaceholderImage
{
    public static function make(string $label, int $width = 800, int $height = 600, string $bg = '#0F1E36', string $fg = '#C9A24B'): string
    {
        $img = imagecreatetruecolor($width, $height);
        [$br, $bgc, $bb] = sscanf($bg, '#%02x%02x%02x');
        [$fr, $fgc, $fb] = sscanf($fg, '#%02x%02x%02x');
        imagefill($img, 0, 0, imagecolorallocate($img, $br, $bgc, $bb));

        $accent = imagecolorallocate($img, $fr, $fgc, $fb);
        imagerectangle($img, 20, 20, $width - 21, $height - 21, $accent);

        $font = 5;
        $textW = imagefontwidth($font) * strlen($label);
        $textH = imagefontheight($font);
        imagestring($img, $font, (int) (($width - $textW) / 2), (int) (($height - $textH) / 2), $label, $accent);

        $path = sys_get_temp_dir().DIRECTORY_SEPARATOR.'ph_'.md5($label.microtime(true)).'.png';
        imagepng($img, $path);
        imagedestroy($img);

        return $path;
    }
}
