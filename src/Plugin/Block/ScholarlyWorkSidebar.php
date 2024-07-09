<?php

namespace Drupal\keep\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a scholarly_work_sidebar block.
 *
 * @Block(
 *   id = "scholarly_work_sidebar",
 *   admin_label = @Translation("Scholarly Work Sidebar"),
 *   category = @Translation("Custom"),
 *   context_definitions = {
 *     "node" = @ContextDefinition("entity:node", label = @Translation("Node"))
 *   }
 * )
 */
class ScholarlyWorkSidebar extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Contructs the sidebar.
   *
   * @param array $configuration
   *   A confguration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    EntityTypeManagerInterface $entity_type_manager,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    $node = $this->getContextValue('node');
    $view_builder = $this->entityTypeManager->getViewBuilder('media');
    $work_products = $node->get('field_work_products');

    // Grabbing a thumbnail from the first item.
    $first = $work_products->first();
    if ($thumbnail = $first->entity->get('thumbnail')) {
      $build['content'][] = $thumbnail->view();
    }

    // Build a list of the other items.
    $work_product_render_array = [];
    $cache_tags = ["node:{$node->id()}"];
    foreach ($work_products as $work_product) {
      $cache_tags[] = "media:{$work_product->entity->id()}";
      $work_product_render_array[] = $view_builder->view($work_product->entity, 'scholarly_work_sidebar');
    }
    $build['content'][] = [
      '#theme' => 'item_list',
      '#attached' => ['library' => ['keep/scholarly-work-sidebar']],
      '#list_type' => 'ol',
      '#items' => $work_product_render_array,
      '#cache' => ['contexts' => ['user.roles', 'route'], 'tags' => $cache_tags],
    ];
    return $build;
  }

}
