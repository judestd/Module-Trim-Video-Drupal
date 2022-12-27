<?php

namespace Drupal\trim_video\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Defines HelloController class.
 */
class ConvertVideoController extends ControllerBase {

  /**
   * Display the markup.
   *
   * @return array
   *   Return markup array.
   */
  public function content() {
    $content =  [
      'trim_video' => \Drupal::request()->query->get('trim_video'),
      'thumb_video' => \Drupal::request()->query->get('thumb_video')
    ];
    return [
      '#theme' => 'trim-video',
      '#content' => $content
    ];
  }

}
