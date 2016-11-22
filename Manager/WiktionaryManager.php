<?php

namespace Innova\WiktionaryBundle\Manager;

class WiktionaryManager
{
    public function __construct()
    {
    }

    public function getDefinitions($lemma, $language)
    {
        $url = 'https://'.$language.'.wiktionary.org/wiki/'.$lemma;
        $handle = @fopen($url, 'r');
        $def = 'Oups...';

        if ($handle) {
            while (!feof($handle)) {
                $def = stream_get_contents($handle);
            }
            fclose($handle);
        }

        return $def;
    }
}
