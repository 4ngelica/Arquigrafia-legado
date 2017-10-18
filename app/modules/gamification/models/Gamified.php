<?php

namespace modules\gamification\models;

class Gamified extends \Eloquent {

  /*
   * This function return the variation Id for a page based on user session.
   * @return  {Number}  "0" for non-gamified and "1" for gamefied
   */
  public static function getGamifiedVariationId() {
    if (\Session::has('gamified_variation_id')) {
      // Getting the variation Id
      $variationId = \Session::get('gamified_variation_id');
    } else {
      // If we don't have a variationId, we will define one and save on session
      $variationId = rand(0, 1);
      \Session::put('gamified_variation_id', $variationId);
    }

    return $variationId;
  }

  /*
   * Just returns if a variation corresponds to a gamified version or not
   * @return  {Boolean}  true for gamified, false for non-gamified
   */
  public static function isGamified($variationId) {
    return $variationId == 1;
  }

}
