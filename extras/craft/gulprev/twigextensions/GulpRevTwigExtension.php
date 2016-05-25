<?php

namespace Craft;

use Twig_Extension;
use Twig_Filter_Method;

class GulpRevTwigExtension extends Twig_Extension
{
    public function getName()
    {
        return 'gulpRev';
    }

    public function getFilters()
    {
        return array(
            'gulp_rev' => new Twig_Filter_Method($this, 'gulpRev')
        );
    }

    /**
     * The "gulp_rev" filter checks a manifest file to properly rev assets.
     *
     * Usage: {{ "assets/stylesheets/app.css" | gulp_rev }}
     */
    public function gulpRev($file)
    {
        static $manifest = null;

        // If the file is not defined in the asset manifest
        // just return the original string
        $path          = $file;
        $manifest_path = 'rev-manifest.json';

        // rev-manifest.json should be in the /public folder
        // just in case locale is on and locale index is in
        // a subfolder of public, try path one step up
        if (!file_exists($manifest_path)) {
            $manifest_path = '../' . $manifest_path;
        }

        // looking for rev-manifest file
        // and storing the contents of the file
        if (is_null($manifest) && file_exists($manifest_path)) {
            $manifest = json_decode(file_get_contents($manifest_path), true);
        }

        // Find the revved version path of the file in the manifest
        if (isset($manifest[$file])) {
            $path = $manifest[$file];
        }

        // All asset paths should start with a slash
        $path = substr($path, 0, 1) !== '/' ? '/' . $path : $path;
        return $path;
    }
}