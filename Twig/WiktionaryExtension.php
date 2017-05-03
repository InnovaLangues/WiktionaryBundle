<?php

namespace Innova\WiktionaryBundle\Twig;

class WiktionaryExtension extends \Twig_Extension
{
    protected $wiktionaryManager;

    public function __construct($wiktionaryManager)
    {
        $this->wiktionaryManager = $wiktionaryManager;
    }

    public function getDefinitions($form, $language)
    {
        $definitions = $this->wiktionaryManager->getDefinitions($form, $language);

        return $definitions;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('getDefinitions', array($this, 'getDefinitions')),
        );
    }

    public function getName()
    {
        return 'getDefinitions';
    }
}
