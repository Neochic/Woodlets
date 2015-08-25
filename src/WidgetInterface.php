<?php

namespace Neochic\Woodlets;

interface WidgetInterface
{
    public function getReadableKey();
    public function widget( $args, $instance );
    public function widgetPreview($instance);
    public function form( $instance );
    public function update( $new_instance, $old_instance );
}