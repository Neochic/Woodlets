<?php

namespace Neochic\Woodlets;
use Neochic\Woodlets\FieldTypes\FieldTypeManager;
use Pimple\Container;

class ContainerFactory
{
    public static function createContainer() {
        $container = new Container();

        $container['dataKey'] = '_neochic-woodlets-data';

        $container['woodlets'] = function ($c) {
            return new Woodlets($c, $c['wordpressWrapper']);
        };

        $container['twig'] = function ($c) {
            return TwigFactory::createTwig($c['wordpressWrapper'], $c['basedir'], $c['twigI18n']);
        };

        $container['twigHelper'] = function ($c) {
            return new Twig\Helper($c['wordpressWrapper'], $c['widgetManager'], $c);
        };

        $container['twigI18n'] = function ($c) {
            return new Twig\I18n($c['wordpressWrapper']);
        };

        $container['editorManager'] = function ($c) {
            return new EditorManager($c['twig'], $c['templateManager'], $c['widgetManager'] ,$c['wordpressWrapper']);
        };

        $container['templateManager'] = function ($c) {
            return new TemplateManager($c['twig'], $c['wordpressWrapper'], $c['widgetManager'], $c['fieldTypeManager'], $c['twigHelper']);
        };

        $container['fieldTypeManager'] = function ($c) {
            return new FieldTypeManager($c['wordpressWrapper'], $c['twig'], $c);
        };

        $container['pageConfigurationManager'] = function($c) {
            return new PageConfigurationManager($c['wordpressWrapper'], $c["formManager"], $c["templateManager"]);
        };

        $container['scriptsManager'] = function ($c) {
            return new ScriptsManager($c['baseurl'], $c['wordpressWrapper']);
        };

        $container['formManager'] = function($c) {
            return new FormManager($c['fieldTypeManager'], $c['twig']);
        };

        $container['widgetManager'] = function ($c) {
            return new WidgetManager($c, $c['twig'], $c['wordpressWrapper'], $c['formManager']);
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
