<?php

namespace App\Services;

use App\GoogleTranslate;
use App\googleTraslationSettings;
use App\Services\GoogleTranslateStichoza;
// use App\Services\CommonGoogleTranslateService;

class CommonGoogleTranslateService
{
    protected $translateType='0';

    public function __construct()
    {
        $this->translateType = googleTraslationSettings::query()->get()->first()->is_free;
    }

    /**
     * Translate the given text from the source language to the target language.
     *
     * @param string $text The text to be translated.
     * @param string $target The target language
     * @param bool $throwException By default false
     * @return string The translated text.
     */
    public function translate(string $target, string $text = null, bool $throwException = false): string
    {
        $translatedText = "";
        if($this->translateType == '0') {
            $googleTranslateStichoza = new GoogleTranslateStichoza();
            $translatedText = $googleTranslateStichoza->translate($target, $text);
        } elseif($this->translateType == '1') {
            $googleTranslate = new GoogleTranslate();
            $translatedText = $googleTranslate->translate($target, $text, $throwException);
        }
        return $translatedText;
    }

    public function detectLanguage($text)
    {
        if($this->translateType == '0') {
            $googleTranslateStichoza = new GoogleTranslateStichoza();
            $detectedLanguage = $googleTranslateStichoza->detectLanguage($text);
        } elseif($this->translateType == '1') {
            $googleTranslate = new GoogleTranslate();
            $detectedLanguage  = $googleTranslate->detectLanguage($text);
        }
        return $detectedLanguage;
    }
}