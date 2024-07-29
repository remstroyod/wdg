<?php

/**
 * Class Wdg
 */

namespace Wdg;

class Wdg
{

    use GlobalTrait;

    public function __construct()
    {

        add_action( 'widgets_init', [ $this, 'register_widgets' ] );
        add_action( 'widgets_init', [ $this, 'register_sidebar' ], 5 );
        add_filter( 'use_widgets_block_editor', '__return_false' );
        add_action('wp_enqueue_scripts', [ &$this, 'scripts' ], 10);
        add_action('wp_enqueue_scripts', [ &$this, 'styles' ], 10);

    }

    public function register_widgets(): void
    {
        register_widget( 'Wdg\WdgWidget' );
    }

    public function register_sidebar(): void
    {
        register_sidebar( [
            'name'          => __( 'Primary Sidebar', 'wdg' ),
            'id'            => 'primary-sidebar',
            'description'   => __( 'Main sidebar', 'wdg' ),
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<h2 class="widget-title">',
            'after_title'   => '</h2>',
        ] );
    }

    public function scripts(): void
    {

        if(!$this->isActive()) return;

        wp_enqueue_script('wdg-jsuites', 'https://jsuites.net/v4/jsuites.js', ['jquery'], null, false);
        wp_enqueue_script('wdg-script', WDG_PLUGIN_URL . '/assets/js/bundle.js', ['jquery'], null, false);

        $script_data_array = [
            'ajaxurl' => admin_url('admin-ajax.php'),
        ];
        wp_localize_script('wdg-script', 'wdg_ajax_handle', $script_data_array);
    }

    public function styles(): void
    {
        if(!$this->isActive()) return;

        wp_enqueue_style('wdg-jsuites', 'https://jsuites.net/v4/jsuites.css', [], null);
        wp_enqueue_style('wdg-rating-style', WDG_PLUGIN_URL . '/assets/css/bundle.css', [], null);
    }

}
