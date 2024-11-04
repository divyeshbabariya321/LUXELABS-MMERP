<?php

namespace App\Helpers;
use Intervention\Image\ImageManagerStatic as Image;

class CompareImagesHelper
{
    public static function mimeType($i)
    {
        /*returns array with mime type and if its jpg or png. Returns false if it isn't jpg or png*/
        $mime   = getimagesize($i);
        $return = [$mime[0], $mime[1]];

        switch ($mime['mime']) {
            case 'image/jpeg':
                $return[] = 'jpg';

                return $return;
            case 'image/png':
                $return[] = 'png';

                return $return;
            default:
                return false;
        }
    }

    public static function createImage($i)
    {
        /*retuns image resource or false if its not jpg or png*/
        $mime = self::mimeType($i);

        if ($mime[2] == 'jpg') {
            return @imagecreatefromjpeg($i);
        } elseif ($mime[2] == 'png') {
            return @imagecreatefrompng($i);
        } else {
            return false;
        }
    }

    public static function resizeImage($i, $source)
    {
        /*resizes the image to a 8x8 squere and returns as image resource*/
        $mime = self::mimeType($source);

        $image = Image::make($source);

        $image->resize(8, 8);

        $resizedImage = $image->getCore();

        return $resizedImage;
    }

    public static function colorMeanValue($i)
    {
        /*returns the mean value of the colors and the list of all pixel's colors*/
        $colorList = [];
        $colorSum  = 0;
        for ($a = 0; $a < 8; $a++) {
            for ($b = 0; $b < 8; $b++) {
                $rgb         = imagecolorat($i, $a, $b);
                $colorList[] = $rgb & 0xFF;
                $colorSum += $rgb & 0xFF;
            }
        }

        return [$colorSum / 64, $colorList];
    }

    public static function bits($colorMean)
    {
        /*returns an array with 1 and zeros. If a color is bigger than the mean value of colors it is 1*/
        $bits = [];

        foreach ($colorMean[1] as $color) {
            $bits[] = ($color >= $colorMean[0]) ? 1 : 0;
        }

        return $bits;
    }

    public static function compare($a, $b)
    {
        /*main function. returns the hammering distance of two images' bit value*/
        $i1 = self::createImage($a);
        $i2 = self::createImage($b);

        if (! $i1 || ! $i2) {
            return false;
        }

        $i1 = self::resizeImage($i1, $a);
        $i2 = self::resizeImage($i2, $b);

        imagefilter($i1, IMG_FILTER_GRAYSCALE);
        imagefilter($i2, IMG_FILTER_GRAYSCALE);

        $colorMean1 = self::colorMeanValue($i1);
        $colorMean2 = self::colorMeanValue($i2);

        $bits1 = self::bits($colorMean1);
        $bits2 = self::bits($colorMean2);

        $hammeringDistance = 0;

        for ($a = 0; $a < 64; $a++) {
            if ($bits1[$a] != $bits2[$a]) {
                $hammeringDistance++;
            }
        }

        return $hammeringDistance;
    }
}
