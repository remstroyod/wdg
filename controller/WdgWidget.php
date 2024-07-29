<?php

/**
 * Class WdgWidget
 *
 * @package Wdg
 */

namespace Wdg;

use Exception;
use WP_Error;
use WP_Widget;

class WdgWidget extends WP_Widget
{

    use GlobalTrait;

    public function __construct()
    {
        parent::__construct(
            'wdg',
            __( 'WDG Articles', 'wdg' ),
            [ 'description' => __( 'A widget to display recent posts', 'wdg' ) ]
        );
    }

    public function widget( $args, $instance ): void
    {
        echo $args['before_widget'];

        if ( ! empty( $instance['title'] ) )
        {
            echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
        }

        $number_of_posts = ! empty( $instance['number_of_posts'] ) ? absint( $instance['number_of_posts'] ) : 5;

        $this->template($number_of_posts);

        echo $args['after_widget'];
    }

    public function form( $instance ): void
    {
        $title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Recent Posts', 'wdg' );
        $number_of_posts = ! empty( $instance['number_of_posts'] ) ? absint( $instance['number_of_posts'] ) : 5;

        ?>
        <p>
            <label for="<?= esc_attr( $this->get_field_id( 'title' ) ) ?>">
                <?= __( 'Title:', 'wdg' ) ?>
            </label>
            <input
                    class="widefat"
                    id="<?= esc_attr( $this->get_field_id( 'title' ) ) ?>"
                    name="<?= esc_attr( $this->get_field_name( 'title' ) ) ?>"
                    type="text"
                    value="<?= esc_attr( $title ) ?>"
            >
        </p>
        <p>
            <label for="<?= esc_attr( $this->get_field_id( 'number_of_posts' ) ) ?>">
                <?= __( 'Number of posts to show:', 'wdg' ) ?>
            </label>
            <input
                    class="tiny-text"
                    id="<?= esc_attr( $this->get_field_id( 'number_of_posts' ) ) ?>"
                    name="<?= esc_attr( $this->get_field_name( 'number_of_posts' ) ) ?>"
                    type="number"
                    step="1"
                    min="1"
                    value="<?= esc_attr( $number_of_posts ) ?>"
                    size="3"
            >
        </p>
        <p>
            <label for="<?= esc_attr($this->get_field_id('show_rating')) ?>">
                <?= __('Show Rating Panel:', 'wdg') ?>
            </label>
            <input
                    class="checkbox"
                    id="<?= esc_attr($this->get_field_id('show_rating')) ?>"
                    name="<?= esc_attr($this->get_field_name('show_rating')) ?>"
                    type="checkbox" <?php if (isset($instance['show_rating'])) { checked($instance['show_rating'], 'on'); } ?>
            >
        </p>
        <p>
            <label for="<?= esc_attr($this->get_field_id('position_rating')) ?>">
                <?= __('Rating Panel Position:', 'wdg'); ?>
            </label>
            <select
                    name="<?= esc_attr($this->get_field_name('position_rating')); ?>"
                    id="<?= esc_attr($this->get_field_id('position_rating')); ?>"
            >
                <option value="bottom_left" <?= isset($instance['position_rating']) && $instance['position_rating'] == 'bottom_left' ? 'selected' : '' ?>><?= __('Bottom Left', 'wdg') ?></option>
                <option value="bottom_center" <?= isset($instance['position_rating']) && $instance['position_rating'] == 'bottom_center' ? 'selected' : '' ?>><?= __('Bottom Center', 'wdg') ?></option>
                <option value="bottom_right" <?= isset($instance['position_rating']) && $instance['position_rating'] == 'bottom_right' ? 'selected' : '' ?>><?= __('Bottom Right', 'wdg') ?></option>
            </select>
        </p>

        <?php
    }

    public function update( $new_instance, $old_instance ): WP_Error|array
    {

        try {

            $instance = [];
            $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
            $instance['number_of_posts'] = ( ! empty( $new_instance['number_of_posts'] ) ) ? absint( $new_instance['number_of_posts'] ) : 5;
            $instance['show_rating'] = ( ! empty( $new_instance['show_rating'] ) ) ? strip_tags( $new_instance['show_rating'] ) : '';
            $instance['position_rating'] = ( ! empty( $new_instance['position_rating'] ) ) ? strip_tags( $new_instance['position_rating'] ) : '';

            return $instance;

        } catch (Exception $e) {

            error_log($e->getMessage());
            return new WP_Error(500, 'Error');

        }

    }

    private function template($number_of_posts): void
    {
        $recent_posts = get_posts([
            'posts_per_page'    => $number_of_posts,
            'orderby'           => 'date',
            'order'             => 'DESC',
        ]);

        $posts = [];

        foreach($recent_posts as $post)
        {
            $year = get_the_date('Y', $post);
            $month = get_the_date('F', $post);
            $posts[$year][$month][] = $post;
        }

        if ( $posts )
        {
            foreach ($posts as $year => $months)
            {
                foreach ($months as $month => $posts)
                {
                    setup_postdata($post);

                    echo sprintf('<h3>%s</h3>', $month);
                    echo '<ul class="wdg__list">';
                    foreach ($posts as $post)
                    {

                        $rating = $this->getVotes($post->ID);

                        echo sprintf('<li class="wdg__list-item"><a href="%s" class="link">%s</a><div class="wdg__list-item--cell"><div><date>%s</date></div><div class="rating" data-rating="%s" data-post="%s"></div></div></li>',
                            get_the_permalink($post),
                            get_the_title($post),
                            get_the_date('j F Y', $post),
                            $rating,
                            $post->ID
                        );
                    }
                    echo '</ul>';
                }

            }
            wp_reset_postdata();

        } else {
            echo __( 'No recent posts', 'wdg' );
        }
    }

}
