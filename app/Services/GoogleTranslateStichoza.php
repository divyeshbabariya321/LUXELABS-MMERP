<?php

namespace App\Services;

use App\Loggers\TranslateLog;
use Stichoza\GoogleTranslate\Exceptions\LargeTextException;
use Stichoza\GoogleTranslate\Exceptions\RateLimitException;
use Stichoza\GoogleTranslate\Exceptions\TranslationRequestException;
use Stichoza\GoogleTranslate\GoogleTranslate as StichozaTranslate;

class GoogleTranslateStichoza
{
    /**
     * Translate the given text from the source language to the target language.
     *
     * @param string $text The text to be translated.
     * @param string $target The target language
     * @return string The translated text.
     */
    public function translate(string $target, string $text = null): string
    {
        $translatedText = '';

        if (!(is_null($text))) {
            try {
                $translator = new StichozaTranslate();
                $translator->setSource();
                $translator->setTarget($target);

                $translatedText = $translator->translate($text);
            } catch (LargeTextException|RateLimitException|TranslationRequestException $e) {
                TranslateLog::log([
                    'google_traslation_settings_id' => 0,
                    'messages'                      => $e->getMessage(),
                    'code'                          => 404,
                    'domain'                        => 'Null',
                    'reason'                        => 'Null',
                ]);
                return null;
            }

        }
        return $translatedText;
    }

    public function detectLanguage($text)
    {
        $returnData = '';
        try {
            $translator = new StichozaTranslate();
            $translator->setOptions([
                'verify' => false,
            ]);
            $translator->setSource();
            $translator->setTarget('en');
            $translator->translate($text);
            
            $returnData = [
                    "languageCode" => $translator->getLastDetectedSource(),
                    "input" => $text,
                    "confidence" => 1
            ];
            return $returnData;
        } catch (LargeTextException|RateLimitException|TranslationRequestException $e) {
            TranslateLog::log([
                'google_traslation_settings_id' => 0,
                'messages'                      => $e->getMessage(),
                'code'                          => 404,
                'domain'                        => 'Null',
                'reason'                        => 'Null',
            ]);
            return null;
        }
    }
}