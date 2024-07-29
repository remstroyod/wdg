<?php

/**
 * Class WdgRating
 *
 * @package Wdg
 */

namespace Wdg;

class WdgRating
{

    use GlobalTrait;

    public function __construct()
    {

        add_action('wp_ajax_wdg_rating', [$this, 'ajax_rating']);
        add_action('wp_ajax_nopriv_wdg_rating', [$this, 'ajax_rating']);

        add_action( 'wp_footer', [$this, 'panel'] );

    }

    public function ajax_rating()
    {
        $post = absint($_POST['post']);
        $value = absint($_POST['value']);

        if($post <= 0)
        {
            wp_send_json_error();
        }

        $rating_data = get_post_meta($post, '_rating', true);
        if (empty($rating_data) || !is_array($rating_data))
        {
            $rating_data = ['total_votes' => 0, 'total_score' => 0];
        }

        $rating_data['total_votes']++;
        $rating_data['total_score'] += $value;

        update_post_meta($post, '_rating', $rating_data);

        wp_send_json_success();

    }

    public function panel()
    {
        if ( !$this->isActive() ) return;

        $widget_options = get_option( 'widget_wdg' );

        $instance_options = null;

        foreach ($widget_options as $key => $options)
        {
            if ($key !== '_multiwidget')
            {
                $instance_options = $options;
                break;
            }
        }

        if(is_null($instance_options) ||
            !array_key_exists('show_rating', $instance_options) ||
            $instance_options['show_rating'] != 'on')
            return;

        $value = $this->getVotes(get_the_ID());
        ?>

        <div class="wdg__rating <?= $instance_options['position_rating'] ?>">
            <div class="rating" data-rating="<?= $value ?>" data-post="<?= get_the_ID() ?>"></div>
        </div>

        <?php

    }

}
