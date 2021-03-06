<?php

namespace Neochic\Woodlets\Twig;

use \Twig_Loader_Filesystem;

class Loader extends Twig_Loader_Filesystem
{
    public function searchTemplates($search, $namespaces = null) {
        $namespaces = $namespaces !== null ? $namespaces : $this->getNamespaces();
        $matches = array();
        foreach($namespaces as $namespace) {
            foreach($this->getPaths($namespace) as $templateDir) {
                $files = glob($templateDir.'/'.$search);

                foreach($files as $file) {
                    $fileInfo = pathinfo($file);
                    $name = ucfirst($fileInfo['filename']);
                    $template = substr($file, strlen($templateDir));

                    $matches['@'.$namespace.$template] = $name;
                }
            }
        }

        return $matches;
    }
}
