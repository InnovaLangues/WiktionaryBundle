<?php

namespace Innova\WiktionaryBundle\Manager;

class WiktionaryManager
{
    public function getDefinitions($form, $language)
    {
        $wiktionaryOutput = $this->requestWiktionary($form, $language);

        if ($wiktionaryOutput) {
            if (preg_match('/<span[\s\S]+?id="fr"[\s\S]*?(<ol>[\s\S]*?<\/ol>)/', $wiktionaryOutput, $matches)) {
                return $matches[1];
            }
        }

        return;
    }

    public function getRandomDefinition($form, $language)
    {
        $definitions = $this->getDefinitions($form, $language);

        if ($definitions) {
            $definitions = $this->removeExamples($definitions);
            if (preg_match_all('/<li>(.*?)<\/li>/s', $definitions, $matches)) {
                $definitions = $matches[1];
                $key = array_rand($definitions);
                $definition = strip_tags($definitions[$key]);

                return $definition;
            }
        }

        return;
    }

    private function removeExamples($definitions)
    {
        $definitions = preg_replace("/<ul>.*?<\/ul>/s", '', $definitions);

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
