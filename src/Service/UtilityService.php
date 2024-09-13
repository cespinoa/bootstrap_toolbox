<?php

namespace Drupal\bootstrap_toolbox\Service;

use Drupal\block_content\Entity\BlockContentType;
use Drupal\bootstrap_toolbox\UtilityServiceInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\Entity\EntityFormDisplay;
use Drupal\Core\Entity\Entity\EntityViewDisplay;
use Drupal\Core\Entity\EntityDisplayRepositoryInterface;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\File\FileUrlGeneratorInterface;
use Drupal\Core\Render\Markup;
use Drupal\Core\Render\RendererInterface;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\image\Entity\ImageStyle;
use Drupal\media\Entity\Media;
use Drupal\node\Entity\NodeType;
use Drupal\Core\Extension\ThemeHandlerInterface;
use Drupal\Core\Path\PathMatcher;

/**
 *
 */
class UtilityService implements UtilityServiceInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The config factory service.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The render service.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderService;

  /**
   * The entity display repository.
   *
   * @var \Drupal\Core\Entity\EntityDisplayRepositoryInterface
   */
  protected $entityDisplayRepository;

  /**
   * The eentity field manager.
   *
   * @var Drupal\Core\Entity\EntityFieldManagerInterface
   */
  protected $entityFieldManager;

  /**
   * Markup service
   *
   * @var Drupal\Core\Render\Markup
   * */
  protected $markupService;

  /**
   * El servicio de File URL Generator.
   *
   * @var \Drupal\Core\File\FileUrlGenerator
   */
  protected $fileUrlGenerator;

  /**
   * Theme handler service
   *
   * @var Drupal\Core\Extension\ThemeHandlerInterface
   * */
   protected $themeHandler;

  /**
   * The path matcher service.
   *
   * @var \Drupal\Core\Path\PathMatcher
   */
  protected $pathMatcher;

  /**
   * Constructs a new UtilityService object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The config factory service.
   * @param \Drupal\Core\Render\RendererInterface $renderService
   *   The render service.
   * @param \Drupal\Core\Entity\EntityDisplayRepositoryInterface $entityDisplayRepository
   *   The entity display repository.
   * @param \Drupal\Core\Entity\EntityFieldManagerInterface $entityFieldManager
   *   The entity field manager.
   * @param \Drupal\Core\File\FileUrlGeneratorInterface $FileUrlGeneratorInterface
   *   El generador de URLs para archivos.
   * @param \Drupal\Core\Extension\ThemeHandlerInterface $themeHandler
   *   ThemeHandler service
   * @param \Drupal\Core\Path\PathMatcher $pathMatcher
   *   The path matcher service
   */
  public function __construct(
    EntityTypeManagerInterface $entityTypeManager,
    ConfigFactoryInterface $configFactory,
    RendererInterface $renderService,
    EntityDisplayRepositoryInterface $entityDisplayRepository,
    EntityFieldManagerInterface $entity_field_manager,
    FileUrlGeneratorInterface $fileUrlGenerator,
    ThemeHandlerInterface $themeHandler,
    PathMatcher $pathMatcher
  ) {
    $this->entityTypeManager = $entityTypeManager;
    $this->configFactory = $configFactory;
    $this->renderService = $renderService;
    $this->entityDisplayRepository = $entityDisplayRepository;
    $this->entityFieldManager = $entity_field_manager;
    $this->fileUrlGenerator = $fileUrlGenerator;
    $this->themeHandler = $themeHandler;
    $this->pathMatcher = $pathMatcher;
  }

  /**
   * Get a list of know themes.
   *
   * Bootstrap Toolbox needs to know there classes to hide sidebars and configure edge-to-edge mode.
   *
   * @return array
   */
  public function getKnownThemes():array {
    return [
      'custom'  => 'Custom selectors',
      'bootstrap_barrio'  => 'Bootstrap Barrio base theme',
      'bootstrap5'  => 'Bootstrap5',
      'bootstrap3'  => 'Bootstrap 3 for Drupal',
    ];
  }

  /**
   * Get active theme selectors .
   *
   * Bootstrap Toolbox needs to know there classes to hide sidebars and configure edge-to-edge mode.
   *
   * @ return array
   */

  /**
   * Get style by id.
   *
   * @param string $id
   *
   * @return string
   */
  public function getStyleById(string $id): string {
    $style = $this->entityTypeManager->getStorage('bootstrap_toolbox_style')->load($id);
    if ($style) {
      return $style->getClasses();
    }
    return '';
  }

  /**
   * Get style by scope.
   *
   * @param array $scope
   *
   * @return array
   */
  public function getStyleByScope(array $scope): array {
    $storage = $this->entityTypeManager->getStorage('bootstrap_toolbox_style');
    $query = $storage->getQuery();
    $query->sort('label');
    $entityIds = $query->execute();

    // Load entities and filter manually.
    $entities = $storage->loadMultiple($entityIds);

    if (!empty($scope) && !$scope[0] == NULL) {
      $filteredentities = [];
      foreach ($entities as $id => $entity) {
        $result = array_intersect($scope, $entity->getScope()) ? TRUE : FALSE;
        if ($result) {
          $filteredentities[$id] = $entity;
        }
      }
      return $filteredentities;
    }
    return $entities;
  }

  /**
   * Get array list with styles filtered by scope.
   *
   * @param array @scope
   *
   * @return array
   */
  public function getScopeListFiltered(array $scope) {
    foreach ($this->getStyleByScope($scope) as $id => $scope) {
      $styleList[$id] = $scope->label();
    }
    return $styleList;
  }

  /**
   * Get array list with styles filtered by scope.
   *
   * @param array @scope
   *
   * @return array
   */
  public function getScopeClassesListFiltered(array $scope) {
    foreach ($this->getStyleByScope($scope) as $id => $scope) {
      $styleList[$id] = $scope->getClasses();
    }
    return $styleList;
  }

  /**
   * Get a list with scope entities.
   *
   * @return array
   */
  public function getScopeList() {
    $scopes = [];
    foreach ($this->entityTypeManager->getStorage('bootstrap_toolbox_scope')->loadMultiple() as $id => $scope) {
      $scopes[$id] = $scope->label();
    }

    return $scopes;
  }

  /**
   * Get the scope label.
   *
   * @param string $id
   *
   * @return string
   */
  public function getScopeLabel($id): string {
    return $this->entityTypeManager->getStorage('bootstrap_toolbox_scope')->load($id)->label();
  }

  /**
   * Get html list from array.
   *
   * @param array $items
   *
   * @return object
   */
  public function arrayToHtmlList($items): object {
    // ~ $items_labels = [];
    // ~ foreach($items as $item){
    // ~ $items_labels[] = \Drupal::service('bootstrap_toolbox.utility_service')->getScopeLabel($item);
    // ~ }
    $renderer = \Drupal::service('renderer');
    $list = [
      '#theme' => 'item_list',
      '#items' => $items,
    ];
    $list = $renderer->render($list);
    return $list;
  }

  /**
   * Sanitize text field. Remove carriage return and extra spaces.
   *
   * @param string $strValue
   *
   * @return string
   */
  public function sanitizeTextField($strValue): string {
    $strValue = str_replace(["\r\n", "\n"], ' ', $strValue);
    $strValue = preg_replace('/\s+/', ' ', $strValue);
    $strValue = trim($strValue);
    return $strValue;
  }

  /**
   *
   */
  public function getThemeSelectors($theme = ''): array {
    $knownthemes = [
      'bootstrap_barrio' => [
        'sidebars_variables' => [
          'sidebar_fisrt',
          'sidebar_second',
        ],
        'main_area_selector' => '#main',
        'main_area_class' => 'container-fluid',
        'central_panel_class' => 'container',
        'edit_mode_fields_area' => '.layout-region-node-main',
        'edit_mode_advanced_area' => '.layout-region-node-secondary',
        'edit_mode_fields_area_remove_class' => 'col-md-6',
        'edit_mode_advanced_area_remove_class' => 'col-md-6',
      ],
    ];

    return $knownthemes[$theme];
  }

  /**
   * Get a list with Wrapper entities.
   *
   * @return array
   */
  public function getWrapperList(): array {
    foreach ($this->entityTypeManager->getStorage('bootstrap_toolbox_wrapper')->loadMultiple() as $key => $wrapper) {
      $wrappers[$key] = $wrapper->label();
    }
    return $wrappers;
  }

  /**
   * Load a bootstrap_toolbox_wrapper entity by label.
   *
   * @param string $label
   *   The label of the entity.
   *
   * @return \Drupal\bootstrap_toolbox\Entity\BootstrapToolboxWrapper|null
   *   The loaded entity, or NULL if not found.
   */
  public function getWrapperByLabel($label) {

    // Create an entity query to find the entity by label.
    $query = \Drupal::entityQuery('bootstrap_toolbox_wrapper')
      ->condition('id', $label)
      ->range(0, 1);

    $ids = $query->execute();
    // If an ID is found, load and return the entity.
    if ($ids) {
      $id = reset($ids);
      $wrapperEntity = $this->entityTypeManager->getStorage('bootstrap_toolbox_wrapper')->load($id);
      return $wrapperEntity->get('description');
    }

    // Return NULL if no matching entity was found.
    return NULL;
  }

  /**
   * Load a bootstrap_toolbox_wrapper entity by id.
   *
   * @param string $id
   *   The id of the entity.
   *
   * @return string|null
   *   The description of entity, or NULL if not found.
   */
  public function getWrapperById($id): ?string {

    $wrapperEntity = $this->entityTypeManager->getStorage('bootstrap_toolbox_wrapper')->load($id);
    if ($wrapperEntity) {
      $description = $wrapperEntity->get('description');
      return $description;
    }

    // Return NULL if no matching entity was found.
    return NULL;
  }

  /**
   * Filter non-zero values from an array.
   *
   * @param array $array
   *   The array to filter.
   *
   * @return array
   *   The filtered array.
   */
  public function filterNonZeroValues(array $array): array {
    return array_filter($array, function ($value) {
      return $value !== 0;
    });
  }

  /**
   * Filter empty values from an array.
   *
   * @param array $array
   *   The array to filter.
   *
   * @return array
   *   The filtered array.
   */
  public function filterNonEmptyValues(array $array): array {
    return array_filter($array, function ($value) {
      return $value !== '';
    });
  }

  /**
   * Provide node types list.
   *
   * @return array
   *   All node types in an array.
   */
  public function getNodeTypes(): array {
    $nodeTypes = NodeType::loadMultiple();
    $list = [];
    foreach ($nodeTypes as $nodeType) {
      $list[$nodeType->id()] = $nodeType->label();
    }
    return $list;
  }

  /**
   * Gets the block types.
   *
   * @return array
   *   An array of block types.
   */
  public function getBlockTypes(): array {
    $blockBundles = BlockContentType::loadMultiple();
    $list = [];
    foreach ($blockBundles as $blockBundle) {
      $list[$blockBundle->id()] = $blockBundle->label();
    }
    return $list;
  }

  /**
   * Build description list for entity types.
   *
   * @param array $entityTypes
   *   The entity type.
   * @param array $types
   *   The types to list.
   * @param string $message
   *   The message to display.
   * @param string $additional_message
   *   The additional message to display.
   *
   * @return string
   *   The built description list.
   */
  public function buildDescriptionList(array $entityTypes, array $types, string $action): string {
    if ($action == 'remove') {
      $list = '
        <div class="system-status-report__row clearfix">
          <div class="system-status-report__status-title system-status-report__status-icon system-status-report__status-icon--warning" role="button">
            @warning
          </div>
          <div class="system-status-report__entry__value">
            <div class="description">
              <p>@msg_delete_1:</p>
              <ul>';
    }
    else {
      $list = '
        <div class="system-status-report__row">
          <div class="system-status-report__status-title" role="button">
              
          </div>
          <div class="system-status-report__entry__value">
          <p>@msg_create:</p>
            <ul>
      ';
    }

    foreach ($types as $key => $value) {
      $list .= "<li>{$entityTypes[$key]} ({$key})</li>";
    }

    if ($action == 'remove') {
      $list .= '
              </ul>
              <p><strong>@msg_delete_2</strong></p>
            </div>
          </div>
        </div>
      ';
    }
    else {
      $list .= '
            </ul>
          </div>
        </div>
      ';
    }

    return $list;
  }

  /**
   * Create a field for a given entity type and bundle.
   *
   * @param string $entityType
   *   The entity type.
   * @param string $bundle
   *   The bundle.
   * @param string $fieldname
   *   The field name.
   * @param array $fieldConfig
   *   The field configuration.
   */
  public function createField(string $entityType, string $bundle, string $fieldname, array $fieldConfig): void {
    $fieldType = $fieldConfig['type'] ?? 'boolean';
    if (!FieldStorageConfig::loadByName($entityType, $fieldname)) {
      if($fieldname == 'custom_theme'){
        FieldStorageConfig::create([
          'field_name' => $fieldname,
          'entity_type' => $entityType,
          'type' => $fieldType,
          'settings' => [],
          'cardinality' => 1,
          'settings' => [
            'allowed_values' => [],
            'allowed_values_function' => 'bootstrap_toolbox_allowed_values_function',
          ],
          'translatable' => FALSE,
          'module' => 'options',
        ])->save();
      }
      else{
        FieldStorageConfig::create([
          'field_name' => $fieldname,
          'entity_type' => $entityType,
          'type' => $fieldType,
          'settings' => [],
          'cardinality' => 1,
          'translatable' => FALSE,
          
        ])->save();
      }
    }

    if (!FieldConfig::loadByName($entityType, $bundle, $fieldname)) {
      FieldConfig::create([
        'field_name' => $fieldname,
        'entity_type' => $entityType,
        'bundle' => $bundle,
        'label' => $fieldConfig['label'],
        'description' => $fieldConfig['description'],
        'required' => FALSE,
        'settings' => [],
      ])->save();
    }

    $formDisplay = EntityFormDisplay::load("$entityType.$bundle.default");
    if (!$formDisplay) {
      $formDisplay = EntityFormDisplay::create([
        'targetEntityType' => $entityType,
        'bundle' => $bundle,
        'mode' => 'default',
        'status' => TRUE,
      ]);
    }
    if ($fieldType === 'list_string') {
      $options = \Drupal::config('bootstrap_toolbox.settings')->get('selected_themes');
      $formDisplay->setComponent($fieldname, [
        'type' => 'options_select',
        'weight' => 0,
      ])->save();
    } else {
      $formDisplay->setComponent($fieldname, [
        'type' => 'boolean_checkbox',
        'weight' => 0,
      ])->save();
    }
  }

  /**
   * Remove a field from a given entity type and bundle.
   *
   * @param string $entityType
   *   The entity type.
   * @param string $bundle
   *   The bundle.
   * @param string $fieldname
   *   The field name.
   */
  public function removeField(string $entityType, string $bundle, string $fieldname): void {
    $formDisplay = EntityFormDisplay::load("$entityType.$bundle.default");
    if ($formDisplay) {
      $formDisplayComponents = $formDisplay->getComponents();
      if (isset($formDisplayComponents[$fieldname])) {
        $formDisplay->removeComponent($fieldname)->save();
      }
    }

    $viewDisplay = EntityViewDisplay::load("$entityType.$bundle.default");
    if ($viewDisplay) {
      $viewDisplayComponents = $viewDisplay->getComponents();
      if (isset($viewDisplayComponents[$fieldname])) {
        $viewDisplay->removeComponent($fieldname)->save();
      }
    }

    $fieldConfig = FieldConfig::loadByName($entityType, $bundle, $fieldname);
    if ($fieldConfig) {
      $fieldConfig->delete();
    }

    $fieldstorage = FieldStorageConfig::loadByName($entityType, $fieldname);
    if ($fieldstorage && empty($fieldstorage->getBundles())) {
      $fieldstorage->delete();
    }
  }

  /**
   * Get image styles.
   *
   * @return array
   *   The image styles array.
   */
  public function getImageStyles(): array {
    $imageStyles = $this->entityTypeManager->getStorage('image_style')->loadMultiple();
    $imageStyleOptions = [
      'default' => 'Default',
    ];

    foreach ($imageStyles as $imageStyle) {
      $imageStyleOptions[$imageStyle->id()] = $imageStyle->label();
    }

    return $imageStyleOptions;
  }

  /**
   * Get view modes in bundle.
   * If get parm @includeFullMode, add the Full mode (Full mode has not status, we need include it manually)
   * If get parm @includeFieldsMode, add the fields mode (a custom not display mode)
   *
   * @param string $targetType
   * @param bool $includeFullMode
   * @param array $aditionalViewModeOptions
   *
   * @return array
   *   The view modes array.
   */
  public function getViewModes(string $targetType, bool $includeFullMode = FALSE, array $aditionalViewModeOptions = []): array {

    $viewModes = $this->entityDisplayRepository->getViewModes($targetType);
    $viewModeOptions = [];
    if ($includeFullMode) {
      $viewModeOptions['default'] = t('Default');
    }

    foreach ($viewModes as $viewMode => $info) {
      if ($info['status']) {
        $viewModeOptions[$viewMode] = $info['label'];
      }
    }

    foreach ($aditionalViewModeOptions as $key => $value) {
      $viewModeOptions[$key] = $value;
    }

    return $viewModeOptions;
  }

  /**
   * Gets fields from a bundle, with title and body, but without the rest of the base fields.
   * It's can filtered by type of field
   *
   * @param string $entity
   * @param string $bundle
   * @param array $types
   *
   * @return array $fieldsList
   * */
  public function getBundleFields(string $entity, string $bundle, array $types = NULL): array {

    $fields = $this->entityFieldManager->getFieldDefinitions($entity, $bundle);

    $fieldOptions = [];

    foreach ($fields as $field_name => $field_definition) {
      // ~ $field_type = $field_definition->getType();
      $fieldStorage = $field_definition->getFieldStorageDefinition();

      if ($field_name == 'title' || $field_name == 'body' || $field_name == 'info' || !$fieldStorage->isBaseField()) {
        if ($types && !in_array($fieldStorage->getType(), $types)) {
          break;
        }
        $fieldOptions[$field_name] = $field_definition->getLabel();
      }
    }

    return $fieldOptions;
  }

  /**
   * Get entity render array.
   *
   * @param string $entityType
   * @param string $viewMode
   * @param string $entityId
   *
   * @return object
   * */
  public function getEntityRenderArray(string $entityType, string $viewMode, string $entityId): array {
    $entity = $this->entityTypeManager->getStorage($entityType)->load($entityId);
    $renderArray = $this->entityTypeManager->getViewBuilder($entityType)->view($entity, $viewMode);
    if ($renderArray) {
      return $renderArray;
    }
    else {
      return NULL;
    }
  }

  /**
   * Get rendered entity.
   *
   * @param string $entityType
   * @param string $viewMode
   * @param string $entityId
   *
   * @return object
   * */
  public function getRenderedEntity(string $entityType, string $viewMode, string $entityId): object {
    $renderArray = $this->getEntityRenderArray($entityType, $viewMode, $entityId);
    if ($renderArray) {
      return \Drupal::service('renderer')->render($renderArray);
      ;
    }
    else {
      return NULL;
    }
  }

  /**
   * Provide tags styles.
   *
   * @return array
   * */
  public function getTagStyles(): array {
    return [
      'h1' => 'H1',
      'h2' => 'H2',
      'h3' => 'H3',
      'h4' => 'H4',
      'h5' => 'H5',
      'h6' => 'H6',
      'div' => 'DIV',
    ];
  }

  /**
   * Get media uri by mediaId and imageStyle.
   *
   * @param string mediaId $mediaId
   * @param string imageStyle
   *
   * @return string
   * */
  public function getMediaUriByMediaIdAndImageStyle(string $mediaId, string $imageStyle): string {
    $media = Media::load($mediaId);
    $mediaFieldName = $media->getSource()->getConfiguration()['source_field'];
    $imageField = $media->get($mediaFieldName);
    if (!$imageField->isEmpty()) {
      if ($imageStyle != 'default') {
        return ImageStyle::load($imageStyle)->buildUri($imageField->entity->getFileUri());
      }
      else {
        return $imageField->first()->entity->uri->value;
      }

    }
    return NULL;
  }

  /**
   * Get media uri by mediaId and imageStyle.
   *
   * @param string mediaId $mediaId
   * @param string imageStyle
   *
   * @return string
   * */
  public function getMediaUrlByMediaIdAndImageStyle(string $mediaId, string $imageStyle): string {
    $media = Media::load($mediaId);
    $mediaFieldName = $media->getSource()->getConfiguration()['source_field'];
    $imageField = $media->get($mediaFieldName);
    if (!$imageField->isEmpty()) {
      if ($imageStyle != 'default') {
        return ImageStyle::load($imageStyle)->buildUrl($imageField->entity->getFileUri());
      }
      else {
        $imageField = $media->get($mediaFieldName)->entity;
        $imageUri = $imageField->getFileUri();
        $imageUrl = $this->fileUrlGenerator->generateAbsoluteString($imageUri);
        return($imageUrl);
      }
    }
    return '';
  }

  /**
   * Get media file data by mediaId and imageStyle.
   *
   * @param string $mediaId
   * @param string $imageStyle
   *
   * @return array
   * */
  public function getMediaFileDataByMediaIdAndImageStyle(string $mediaId, string $imageStyle): array {
    $media = Media::load($mediaId);
    $mediaFieldName = $media->getSource()->getConfiguration()['source_field'];
    $imageField = $media->get($mediaFieldName);
    if (!$imageField->isEmpty()) {
      $data = $imageField->first()->toArray();
      if ($imageStyle != 'default') {
        $data['url'] = ImageStyle::load($imageStyle)->buildUrl($imageField->entity->getFileUri());
      }
      else {
        $imageField = $media->get($mediaFieldName)->entity;
        $imageUri = $imageField->getFileUri();
        $data['url'] = $this->fileUrlGenerator->generateAbsoluteString($imageUri);
      }

      return $data;
    }
    return [];
  }

  /**
   * Returns markup from text.
   *
   * @param strint $text
   *
   * @return object
   * */
  public function createMarkup($text):object {
    return Markup::create($text);
  }

  /**
   * Get entities by entity Type and bundle.
   *
   * @param string $entityType
   * @param string $bundle
   * @param string|null $sortField
   * @param string $direction
   *
   * @return array
   */
  public function getEntitiesByTypeAndBundle($entityType, $bundle, $sortField = NULL, $direction = 'asc'): array {
    $query = $this->entityTypeManager->getStorage($entityType)->getQuery();
    $query->condition('type', $bundle);

    // Añadir la ordenación si se proporciona un campo de orden.
    if ($sortField) {
      $query->sort($sortField, $direction);
    }

    // Opcionalmente deshabilitar la verificación de acceso.
    $query->accessCheck(FALSE);

    $entityIds = $query->execute();
    $entities = $this->entityTypeManager->getStorage($entityType)->loadMultiple($entityIds);

    return $entities;
  }

  /**
   * Get an entity by entity Type and uuid.
   *
   * @param string $entityType
   * @param string $uuid
   *
   *   return object.
   * */
  public function getEntityByTypeAndUuid($entityType, $uuid):array {
    return $this->entityTypeManager->getStorage($entityType)->loadByProperties(['uuid' => $uuid]);
  }

  /**
   * Get an entity by entity Type and id.
   *
   * @param string $entityType
   * @param string $id
   *
   *   return object.
   * */
  public function getEntityByTypeAndId($entityType, $id):object {
    return $this->entityTypeManager->getStorage($entityType)->load($id);
  }

  /**
   * Check if the parameter $url is the current url.
   *
   * @param string $url
   *
   * @return bool
   * */
  public function isCurrentUrl($url):bool {

    $active = FALSE;
    $currentEntityUrl = NULL;
    $routeMatch = \Drupal::routeMatch();
    $routeMatch = \Drupal::service('current_route_match');
    $route = $routeMatch->getRouteName();

    $routeElements = explode('.', $route);
    if ($routeElements[0] == 'entity' && $routeElements[2] == 'canonical') {
      $currentEntity = $routeMatch->getParameter($routeElements[1]);
      $currentEntityToLink = $currentEntity->toLink();
      $currentEntityUrl = $currentEntityToLink->getUrl()->toString();
    }

    if ($url == $currentEntityUrl) {
      $active = TRUE;
    }

    return $active;
  }

  /**
   * Return an array with available custom themes
   *
   * @return array
   * */
  function getAllowedThemes(): array {
    $themes = array_filter(\Drupal::config('bootstrap_toolbox.settings')
      ->get('selected_themes'), function($value){
      return $value !== 0;
    });
    $allowedThemes = [];
    foreach($themes as $theme=>$data){
      $allowedThemes[$theme] = $this->themeHandler->getName($theme);
    }
    return $allowedThemes;
  }


  /**
   *
   * Prepara custom parameters
   *
   * @return array
   * 
   * */
  function getBootstrapToolboxParameters() {
    $config = \Drupal::config('bootstrap_toolbox.settings');
    $hideTitle = FALSE;
    $hideSidebars = FALSE;
    $hideBreadcrumb = FALSE;
    $edgeToEdge = FALSE;
    $forceToPanel = FALSE;
    $action = '';
    $settings = NULL;
    $isEditMode = FALSE;
    
    $routeMatch = \Drupal::routeMatch();
    $route = $routeMatch->getRouteName();
    $routeObject = $routeMatch->getRouteObject();
    $routeName = \Drupal::routeMatch()->getRouteName();
    $params = $routeMatch->getParameters()->keys();
    
    // Check page type and set the action 
    if (array_key_exists( 0, $params )){
      if ($params[0] === 'node'){
        $action = 'processNode';  
      }
      elseif($params[0] === 'view_id'){
        $action = 'processView';
      }
    }
    elseif ($routeObject->getOption('bootstrap_toolbox')) {
      $action = 'processController';
    }
    
    if ($this->pathMatcher->isFrontPage()){
      $action = 'processFrontPage';
    }
    
    // Get config source
    if ($action == 'processFrontPage') {
      $settings = $config->get('front_page_options');
      $settings['custom_theme'] = $config->get('custom_theme');
    }
    elseif ($action == 'processController'){
      $settings = $routeObject->getOption('bootstrap_toolbox');
    }
    elseif ($action == 'processView'){
      $viewId = $routeMatch->getParameter('view_id');
      $displayId = $routeMatch->getParameter('display_id');
      $key = "{$viewId}_{$displayId}";
      $settings = $config->get("views_custom_themes.{$key}");
    }
    elseif ($action == 'processNode'){
      $node = $routeMatch->getParameter('node');
      $nodeTypeId = $node->bundle();
      $nodeType = NodeType::load($nodeTypeId);
      $settings = $nodeType->getThirdPartySettings('bootstrap_toolbox');
    }

    // Get config variables
    if ($settings){
      $hideTitle = $settings['hide_title'] ?? FALSE;
      $hideSidebars = $settings['hide_sidebars'] ?? FALSE;
      $hideBreadcrumb = $settings['hide_breadcrumb'] ?? FALSE;
      $edgeToEdge = $settings['edge_to_edge'] ?? FALSE;
      $customTheme = $settings['custom_theme'] ?? FALSE;
    }

    // If node override nodeType settings
    if ($action == 'processNode' &&
      $node->hasField('override_node_settings') &&
      $node->get('override_node_settings')->value)
      {
      $hideSidebars = $node->hide_sidebars->value ?? $hideSidebars;
      $hideTitle = $node->hide_title->value ?? $hideTitle;
      $hideBreadcrumb = $node->hide_breadcrumb->value ?? $hideBreadcrumb;
      $edgeToEdge = $node->edge_to_edge->value ?? $edgeToEdge;
      $customTheme = $node->edge_to_edge->value ?? $customTheme;
    }

    $params = [
      'hideSidebars' =>  $hideSidebars,
      'hideTitle' => $hideTitle,
      'hideBreadcrumb' => $hideBreadcrumb,
      'edgeToEdge' => $edgeToEdge,
      'custom_theme' => $customTheme,
    ];
    if ($action = 'processNode'){
      $params['node'] = $node;
    }
    return($params);

    
  }

}
