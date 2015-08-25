<?php

namespace Neochic\Woodlets;
use Neochic\Woodlets\FieldTypes\FieldTypeManager;
use Pimple\Container;

class ContainerFactory
{
    public static function createContainer() {
        $container = new Container();

        $container['dataKey'] = '_neochic-woodlets-data';

        $container['woodlet'] = function ($c) {
            return new Woodlet($c, $c['wordpressWrapper']);
        };

        $container['twig'] = function ($c) {
            return TwigFactory::createTwig($c['wordpressWrapper'], $c['basedir']);
        };

        $container['twigHelper'] = function ($c) {
            return new Twig\Helper($c['wordpressWrapper'], $c['widgetManager']);
        };

        $container['editorManager'] = function ($c) {
            return new EditorManager($c['twig'], $c['templateManager'], $c['widgetManager'] ,$c['wordpressWrapper']);
        };

        $container['templateManager'] = function ($c) {
            return new TemplateManager($c['twig'], $c['wordpressWrapper'], $c['widgetManager'], $c['fieldTypeManager'], $c['twigHelper']);
        };

        $container['fieldTypeManager'] = function ($c) {
            return new FieldTypeManager($c['wordpressWrapper'], $c);
        };

        $container['scriptsManager'] = function ($c) {
            return new ScriptsManager($c['baseurl'], $c['wordpressWrapper']);
        };

        $container['widgetManager'] = function ($c) {
            return new WidgetManager($c['twig'], $c['wordpressWrapper'], $c['fieldTypeManager']);
        };

        $container['noticeManager'] = function ($c) {
            return new NoticeManager($c['wordpressWrapper'], $c['twig']);
        };

        $container['wordpressWrapper'] = function ($c) {
            return new WordPressWrapper($c['dataKey']);
        };


        return $container;
    }
}