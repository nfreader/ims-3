<?php

namespace App\Extension\Twig;

use Exception;
use Random\Randomizer;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Symfony\WebpackEncoreBundle\Asset\EntrypointLookup;

/**
 * WebpackAssetLoader
 *
 * Basically a slimmed down, scaled back version of Symfony's Webpack Encore
 * Bundle.
 *
 * @link https://github.com/symfony/webpack-encore-bundle
 */
class WebpackAssetLoader extends AbstractExtension
{
    private $entrypointLookup;
    private $hashes = [];
    public function __construct(
        private string $entrypointDir,
        private bool $debug = false
    ) {
        try {
            $this->entrypointLookup = new EntrypointLookup($this->entrypointDir . "/build/entrypoints.json");
            $this->hashes = $this->entrypointLookup->getIntegrityData();
        } catch (Exception $e) {
            die("Could not find the entrypoints file from Webpack. Did you generate the assets?");
        }
    }
    /**
     * getFunctions
     *
     * @inheritDoc
     * @return array
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('webpack_entry_link_tags', [$this, 'renderWebpackLinkTags'], ['is_safe' => ['html']]),
            new TwigFunction('webpack_entry_script_tags', [$this, 'renderWebpackScriptTags'], ['is_safe' => ['html']])
        ];
    }

    /**
     * renderWebpackLinkTags
     *
     * Renders the <link> tags for the given $module (as defined
     * in your `webpack.config.js`).
     *
     * @param string $module
     * @return void
     */
    public function renderWebpackLinkTags(string $module = 'app')
    {
        $tags = '';
        foreach ($this->entrypointLookup->getCssFiles($module) as $entry) {
            $atts = $this->getLinkTagAttributes($entry);
            $tags .= sprintf("<link %s /> ", $atts);
        }
        return $tags;
    }

    /**
     * renderWebpackScriptTags
     *
     * Renders the <script> tags for the given $module (as defined
     * in your `webpack.config.js`).
     *
     * @param string $module
     * @return void
     */
    public function renderWebpackScriptTags(string $module = 'app')
    {
        $tags = '';
        foreach ($this->entrypointLookup->getJavaScriptFiles($module) as $entry) {
            $atts = $this->getScriptTagAttributes($entry);
            $tags .= sprintf("<script %s></script>\r", $atts);
        }
        return $tags;
    }

    /**
     * getScriptTagAttributes
     *
     * Converts the given entry into a string of HTML attributes
     *
     * @param string $entry
     * @return string
     */
    private function getScriptTagAttributes(string $entry): string
    {
        $randomizer = new Randomizer();
        $attributes = [
            'src' => $entry,
            'defer' => true
        ];
        if($this->debug) {
            $attributes['src'] = $attributes['src']."?".bin2hex($randomizer->getBytes(8));
        }
        if(isset($this->hashes[$entry])) {
            $attributes['integrity'] = $this->hashes[$entry];
        }
        return $this->convertArrayToAttributes($attributes);
    }

    /**
     * getLinkTagAttributes
     *
     * Converts the given entry into a string of HTML attributes
     *
     * @param string $entry
     * @return string
     */
    private function getLinkTagAttributes(string $entry): string
    {
        $randomizer = new Randomizer();
        $attributes = [
            'href' => $entry,
            'rel' => 'stylesheet'
        ];
        if($this->debug) {
            $attributes['src'] = $attributes['href']."?".bin2hex($randomizer->getBytes(8));
        }
        if(isset($this->hashes[$entry])) {
            $attributes['integrity'] = $this->hashes[$entry];
        }
        return $this->convertArrayToAttributes($attributes);
    }

    /**
     * convertArrayToAttributes
     *
     * Cribbed directly from Symfony Webpack Bundle
     *
     * @link https://github.com/symfony/webpack-encore-bundle/blob/2.x/src/Asset/TagRenderer.php#L167-L186
     *
     * @param array $attributesMap
     * @return string
     */
    private function convertArrayToAttributes(array $attributesMap): string
    {
        // remove attributes set specifically to false
        $attributesMap = array_filter($attributesMap, static function ($value) {
            return false !== $value;
        });

        return implode(' ', array_map(
            static function ($key, $value) {
                // allows for things like defer: true to only render "defer"
                if (true === $value || null === $value) {
                    return $key;
                }

                return sprintf('%s="%s"', $key, htmlentities($value));
            },
            array_keys($attributesMap),
            $attributesMap
        ));
    }
}
