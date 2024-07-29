<?php

/**
 * Trait GlobalTrait
 *
 * The GlobalTrait provides common functionality that can be used by multiple classes or objects.
 */

namespace Wdg;

trait GlobalTrait
{

   protected function isActive(): bool
   {
       return is_active_widget( false, false, 'wdg', true ) && is_single();
   }

   protected function getVotes($post): mixed
   {
       $rating_data = get_post_meta($post, '_rating', true);

       if (empty($rating_data) ||
           !is_array($rating_data) ||
           $rating_data['total_votes'] == 0)
       {
           $average_rating = 0;
       } else {

           $average_rating = round($rating_data['total_score'] / $rating_data['total_votes']);

           $average_rating = min($average_rating, 5);
       }

       return $average_rating;
   }

}
