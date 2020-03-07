<?php

namespace Drupal\ui_styles_layout_builder\EventSubscriber;

use Drupal\layout_builder\Event\SectionComponentBuildRenderArrayEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\layout_builder\LayoutBuilderEvents;
use Drupal\ui_styles\StylePluginManagerInterface;

/**
 * Class BlockComponentRenderArraySubscriber.
 */
class BlockComponentRenderArraySubscriber implements EventSubscriberInterface {

  /**
   * The style manager.
   *
   * @var \Drupal\ui_styles\StylePluginManagerInterface
   */
  protected $styleManager;

  /**
   * Dependency injection.
   *
   * @param \Drupal\ui_styles\StylePluginManagerInterface $style_manager
   *   The style manager.
   */
  public function __construct(StylePluginManagerInterface $style_manager) {
    $this->styleManager = $style_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    // Layout Builder also subscribes to this event to build the initial render
    // array. We use a higher weight so that we execute after it.
    $events[LayoutBuilderEvents::SECTION_COMPONENT_BUILD_RENDER_ARRAY] = ['onBuildRender', 50];
    return $events;
  }

  /**
   * Add each component's block styles to the render array.
   *
   * @param \Drupal\layout_builder\Event\SectionComponentBuildRenderArrayEvent $event
   *   The section component render event.
   */
  public function onBuildRender(SectionComponentBuildRenderArrayEvent $event) {
    $build = $event->getBuild();
    // This shouldn't happen - Layout Builder should have already created the
    // initial build data.
    if (empty($build)) {
      return;
    }
    $selected = $event->getComponent()->get('ui_styles') ?: [];
    $extra = $event->getComponent()->get('ui_styles_extra') ?: '';
    $this->styleManager->addClasses($build, $selected, $extra);
    $event->setBuild($build);
  }

}