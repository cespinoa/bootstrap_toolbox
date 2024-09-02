<?php

declare(strict_types=1);

namespace Drupal\bootstrap_toolbox;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\Core\Render\RendererInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\bootstrap_toolbox\UtilityServiceInterface;
use Drupal\bootstrap_toolbox\Form\BootstrapToolboxStyleFilterForm;

use Drupal\Core\Routing\UrlGeneratorInterface;






/**
 * Provides a listing of bootstrap toolbox styles.
 */
final class BootstrapToolboxStyleListBuilder extends ConfigEntityListBuilder {

  protected $utilityService;
  
  /**
   * The renderer service.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * The URL generator service.
   *
   * @var \Drupal\Core\Routing\UrlGeneratorInterface
   */
  protected $urlGenerator;

  /**
   * Constructs a new BootstrapToolboxClassesListBuilder object.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entityType
   *   The entity type definition.
   * @param \Drupal\Core\Entity\EntityStorageInterface $storage
   *   The entity storage.
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   The renderer service.
   * @param \Drupal\bootstrap_toolbox\UtilityServiceInterface $utilityservice
   *    Custom services
   * @param \Drupal\Core\Routing\UrlGeneratorInterface $urlGenerator
   *   The URL generator service.
   * 
   */
  public function __construct(
    EntityTypeInterface $entityType,
    EntityStorageInterface $storage,
    RendererInterface $renderer,
    UtilityServiceInterface $utilityservice,
    UrlGeneratorInterface $urlGenerator
  ) {
    parent::__construct($entityType, $storage);
    $this->renderer = $renderer;
    $this->utilityService = $utilityservice;
    $this->urlGenerator = $urlGenerator;
  }

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entityType) {
    return new static(
      $entityType,
      $container->get('entity_type.manager')->getStorage($entityType->id()),
      $container->get('renderer'),
      $container->get('bootstrap_toolbox.utility_service'),
      $container->get('url_generator')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function render() {
    $build['filter_form'] = \Drupal::formBuilder()->getForm(BootstrapToolboxStyleFilterForm::class);
    $build += parent::render();
    return $build;
  }

  /**
   * {@inheritdoc}
   */
  protected function getEntityIds() {
    
    $scope = \Drupal::request()->query->get('scope');
    //~ if (!empty($scope) || !$scope) {
      // Ensure $scope is always an array.
    $scope = [$scope];//  $scope = is_array($scope) ? $scope : [$scope];
    //~ }
    $entities = $this->utilityService->getStyleByScope($scope);
    
    return array_keys($entities);
  }
  
  /**
   * {@inheritdoc}
   */
  public function buildHeader(): array {
    $headerContent['label'] = $this->t('Style');
    //~ $headerContent['wizar'] = $this->t('Wizar');
    $headerContent['classes'] = $this->t('Classes');
    $headerContent['scope'] = $this->t('Scope');
    return $headerContent + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity): array {
    /** @var \Drupal\bootstrap_toolbox\BootstrapToolboxStyleInterface $entity */
    $rowContent['label'] = $this->getLabelLink($entity);
    //~ $rowContent['wizar'] = $this->getWizarForm($entity);
    $rowContent['classes'] = $entity->getClassesAsHTMLList();
    $rowContent['scope'] = $entity->getScopeHTMLList();
    return $rowContent + parent::buildRow($entity);
  }

  /**
   * Generate a link to a wizar wiht classes as parameters
   * */
  //~ public function getWizarForm(EntityInterface $entity): object {
    //~ $classes = $entity->getClasses();

    //~ $url = Url::fromUserInput('/admin/config/bootstrap-toolbox/style-wizar', [
      //~ 'query' => [
        //~ 'classes' => $classes,
        //~ 'style' => $entity->id(),
      //~ ],
    //~ ]);
    
    //~ $link = Link::fromTextAndUrl($this->t('Wizar'), $url)->toRenderable();
    //~ return $this->renderer->renderPlain($link);
  //~ }

  /**
   * Generates a link with onclick attribute to open in a new window.
   */
  private function getLabelLink(EntityInterface $entity): object  {
    $classes = $entity->getClasses();

    $url = Url::fromUserInput('/bootstrap-toolbox/show-classes', [
      'query' => [
        'classes' => $classes,
        'mode' => 'show_classess',
      ],
    ]);
    
    $link = Link::fromTextAndUrl($entity->label(), $url)->toRenderable();
    $link['#attributes'] = [
      'onclick' => "window.open(this.href,'bootatrap_toolbox','toolbar=no,status=no,menubar=no,location=center,scrollbars=no,resizable=no,height=600,width=800'); return false;",
    ];
    return $this->renderer->renderPlain($link);
    
  }

  /**
   * {@inheritdoc}
   */
  public function getOperations(EntityInterface $entity) {
    $operations = parent::getOperations($entity);

    // Añadir una nueva operación personalizada.
    $operations['wizar'] = [
      'title' => $this->t('Wizar'),
      'url' => Url::fromRoute('style_wizar.form', [
        'custom_bootstrap_toolbox_styleentity' => $entity->id(),
        'classes' => $entity->getClasses(),
        'style' => $entity->id(),
        'style_name' => $entity->label()
      ]),
      'weight' => 0, // Peso para la ordenación.
    ];

    return $operations;
  }
  

}
