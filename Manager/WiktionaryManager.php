<?php

namespace Innova\WiktionaryBundle\Manager;

class WiktionaryManager
{
    protected $translator;

    public function __construct($translator)
    {
        $this->translator = $translator;
    }

    protected $patterns = [
        'fr' => [
            'definitions' => '/<span[\s\S]+?id="fr"[\s\S]*?(<ol>[\s\S]*?<\/ol>)/',
            'example' => '/<ul>.*?<\/ul>/s',
        ],
        'en' => [
            'definitions' => '/<p><strong class="Latn headword" lang="en.*?<ol>(.*?)<\/ol>/s',
            'example' => '/<(d|u)l>.*?<\/(d|u)l>/s',
        ],
    ];

    public function getDefinitions($form, $language)
    {
        $wiktionaryOutput = $this->requestWiktionary($form, $language);

        if ($wiktionaryOutput) {
            if (preg_match_all($this->patterns[$language]['definitions'], $wiktionaryOutput, $matches)) {
                $output = '';
                foreach ($matches[1] as $defgroup) {
                    $output .= $defgroup;
                }

                return $output;
            }
        }

        return $this->translator->trans("nowiktionarydef", ['%word%'=>$form, '%language%'=>$language]);
    }

    public function getRandomDefinition($form, $language)
    {
        $definitions = $this->getDefinitions($form, $language);

        if ($definitions) {
            $definitions = $this->removeExamples($definitions, $language);
            if (preg_match_all('/<li>(.*?)<\/li>/s', $definitions, $matches)) {
                $definitions = $matches[1];
                $key = array_rand($definitions);
                $definition = strip_tags($definitions[$key]);

                return $definition;
            }
        }

        return;
    }

    private function removeExamples($definitions, $language)
    {
        $definitions = preg_replace($this->patterns[$language]['example'], '', $definitions);

        return $definitions;
    }

    private function removeLink($definitions)
    {
        $definitions = preg_replace("/<a[^>]+>(.*?)<\/a>/s", '$1', $definitions);

        return $definitions;
    }

    private function requestWiktionary($form, $language)
    {
        $url = 'https://'.$language.'.wiktionary.org/wiki/'.$form;
        $handle = @fopen($url, 'r');
        $wiktionaryOutput = null;

        if ($handle) {
            while (!feof($handle)) {
                $wiktionaryOutput = stream_get_contents($handle);
                $wiktionaryOutput = $this->removeLink($wiktionaryOutput);
            }
            fclose($handle);
        }

        return $wiktionaryOutput;
    }
}
