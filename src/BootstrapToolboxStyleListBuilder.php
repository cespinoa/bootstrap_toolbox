<?php

declare(strict_types=1);

namespace Drupal\bootstrap_toolbox;

use Drupal\bootstrap_toolbox\Form\BootstrapToolboxStyleFilterForm;
use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Link;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\Routing\UrlGeneratorInterface;
use Drupal\Core\Url;
use Drupal\bootstrap_toolbox\UtilityServiceInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Drupal\Core\Form\FormBuilderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a listing of bootstrap toolbox styles.
 */
final class BootstrapToolboxStyleListBuilder extends ConfigEntityListBuilder {

  /**
   * The request stack.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

  /**
   * The form builder service.
   *
   * @var \Drupal\Core\Form\FormBuilderInterface
   */
  protected $formBuilder;

  /**
   * The utility service.
   *
   * @var \Drupal\bootstrap_toolbox\UtilityServiceInterface
   */
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
   * @param \Drupal\bootstrap_toolbox\UtilityServiceInterface $utilityService
   *   Custom services.
   * @param \Drupal\Core\Routing\UrlGeneratorInterface $urlGenerator
   *   The URL generator service.
   * @param \Symfony\Component\HttpFoundation\RequestStack $request_stack
   *   The request stack service.
   * @param \Drupal\Core\Form\FormBuilderInterface $form_builder
   *   The form builder service.
   */
  public function __construct(
    EntityTypeInterface $entityType,
    EntityStorageInterface $storage,
    RendererInterface $renderer,
    UtilityServiceInterface $utilityService,
    UrlGeneratorInterface $urlGenerator,
    RequestStack $request_stack,
    FormBuilderInterface $form_builder
  ) {
    parent::__construct($entityType, $storage);
    $this->renderer = $renderer;
    $this->utilityService = $utilityService;
    $this->urlGenerator = $urlGenerator;
    $this->requestStack = $request_stack;
    $this->formBuilder = $form_builder;
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
      $container->get('url_generator'),
      $container->get('request_stack'),
      $container->get('form_builder')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function render() {
    $build['filter_form'] = $this->formBuilder->getForm(BootstrapToolboxStyleFilterForm::class);
    $build += parent::render();
    return $build;
  }

  /**
   * {@inheritdoc}
   */
  protected function getEntityIds() {
    $request = $this->requestStack->getCurrentRequest();
    if ($request !== null) {
        $scope = $request->query->get('scope');
        $scope = [$scope];
        $entities = $this->utilityService->getStyleByScope($scope);
        return array_keys($entities);
    }
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader(): array {
    $headerContent['label'] = $this->t('Style');
    // ~ $headerContent['wizar'] = $this->t('Wizar');
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
    $rowContent['classes'] = $entity->getClassesAsHtmlList();
    $rowContent['scope'] = $entity->getScopeHtmlList();
    return $rowContent + parent::buildRow($entity);
  }

  /**
   * Generates a link with onclick attribute to open in a new window.
   *
   * @param \Drupal\bootstrap_toolbox\BootstrapToolboxStyleInterface $entity
   *   The entity for which the link is generated.
   *
   * @return object
   *   An object containing the generated link.
   */
  private function getLabelLink(EntityInterface $entity): object {
    $classes = $entity->getClasses();

    $url = Url::fromUserInput('/bootstrap-toolbox/show-classes', [
      'query' => [
        'classes' => $classes,
        'mode' => 'show_classess',
      ],
    ]);

    $label = (string) $entity->label();
    $link = Link::fromTextAndUrl($label, $url)->toRenderable();
    $link['#attributes'] = [
      'onclick' => "window.open(this.href,'bootatrap_toolbox','toolbar=no,status=no,menubar=no,location=center,scrollbars=no,resizable=no,height=600,width=800'); return false;",
    ];
    return $this->renderer->renderInIsolation($link);

  }

  /**
   * {@inheritdoc}
   *
   * @param \Drupal\bootstrap_toolbox\BootstrapToolboxStyleInterface $entity
   */
  public function getOperations(EntityInterface $entity) {
    $operations = parent::getOperations($entity);

    $operations['wizar'] = [
      'title' => $this->t('Wizar'),
      'url' => Url::fromRoute('style_wizar.form', [
        'custom_bootstrap_toolbox_styleentity' => $entity->id(),
        'classes' => $entity->getClasses(),
        'style' => $entity->id(),
        'style_name' => $entity->label(),
      ]),
      'weight' => 0,
    ];

    return $operations;
  }

}
