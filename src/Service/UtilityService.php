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

use Symfony\Component\Yaml\Yaml;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Component\Utility\Html;
use Symfony\Component\HttpFoundation\RequestStack;

use Drupal\Core\Link;
use Drupal\Core\Url;

use Psr\Log\LoggerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Field\FieldItemInterface;

use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\file\Entity\File;

use Drupal\Core\Entity\EntityInterface;

use Drupal\Component\Render\MarkupInterface;
use Drupal\Core\Theme\ThemeManagerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\StringTranslation\TranslationInterface;

/**
 *
 */
class UtilityService implements UtilityServiceInterface {

   use StringTranslationTrait;

  /**
   * The RequestStack service
   *
   *@var \Symfony\Component\HttpFoundation\RequestStack
   * */
   protected $requestStack;
  
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
   * @var \Drupal\Core\Entity\EntityFieldManagerInterface
   */
  protected $entityFieldManager;

  /**
   * Markup service
   *
   * @var \Drupal\Core\Render\Markup
   * */
  protected $markupService;

  /**
   * El servicio de File URL Generator.
   *
   * @var \Drupal\Core\File\FileUrlGeneratorInterface
   */
  protected $fileUrlGenerator;

  /**
   * Theme handler service
   *
   * @var \Drupal\Core\Extension\ThemeHandlerInterface
   * */
   protected $themeHandler;

  /**
   * The path matcher service.
   *
   * @var \Drupal\Core\Path\PathMatcher
   */
  protected $pathMatcher;

  /**
   * The file system service.
   *
   * @var \Drupal\Core\File\FileSystemInterface
   */
  protected $fileSystem;

  /**
   * El servicio de logger.
   *
   * @var \Drupal\Core\Logger\LoggerChannelInterface
   */
  protected $logger;

  /**
   * The messenger service.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * The module handler service.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * The routeMatch service.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * The theme manager service.
   *
   * @var \Drupal\Core\Theme\ThemeManagerInterface
   */
  protected $themeManager;

  /**
   * The translation service.
   *
   * @var \Drupal\Core\StringTranslation\TranslationInterface
   */
  protected $stringTranslation;

  

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
   * @param \Drupal\Core\Entity\EntityFieldManagerInterface $entity_field_manager
*   El administrador de campos de entidad.
   * @param \Drupal\Core\File\FileUrlGeneratorInterface $fileUrlGenerator
   *   El generador de URLs para archivos.
   * @param \Drupal\Core\Extension\ThemeHandlerInterface $themeHandler
   *   ThemeHandler service
   * @param \Drupal\Core\Path\PathMatcher $pathMatcher
   *   The path matcher service
   * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack
   *   The request stack service
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $logger
   *   The logger service
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger service
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $moduleHandler
   *   The module handler service.
   * @param \Drupal\Core\File\FileSystemInterface $file_system
   *   The file system service.
   * @param \Drupal\Core\Routing\RouteMatchInterface $routeMatch
   *   The route match service
   * @param \Drupal\Core\Theme\ThemeManagerInterface $theme_manager
   *   The theme manager service.
   * @param \Drupal\Core\StringTranslation\TranslationInterface $stringTranslation
   *   The translation service.
   */
  public function __construct(
    EntityTypeManagerInterface $entityTypeManager,
    ConfigFactoryInterface $configFactory,
    RendererInterface $renderService,
    EntityDisplayRepositoryInterface $entityDisplayRepository,
    EntityFieldManagerInterface $entity_field_manager,
    FileUrlGeneratorInterface $fileUrlGenerator,
    ThemeHandlerInterface $themeHandler,
    PathMatcher $pathMatcher,
    RequestStack $requestStack,
    LoggerChannelFactoryInterface $logger,
    MessengerInterface $messenger,
    ModuleHandlerInterface $moduleHandler,
    FileSystemInterface $file_system,
    RouteMatchInterface $routeMatch,
    ThemeManagerInterface $theme_manager,
    TranslationInterface $stringTranslation
  ) {
    $this->entityTypeManager = $entityTypeManager;
    $this->configFactory = $configFactory;
    $this->renderService = $renderService;
    $this->entityDisplayRepository = $entityDisplayRepository;
    $this->entityFieldManager = $entity_field_manager;
    $this->fileUrlGenerator = $fileUrlGenerator;
    $this->themeHandler = $themeHandler;
    $this->pathMatcher = $pathMatcher;
    $this->requestStack = $requestStack;
    $this->logger = $logger->get('bootstrap_toolbox');
    $this->messenger = $messenger;
    $this->moduleHandler = $moduleHandler;
    $this->fileSystem = $file_system;
    $this->routeMatch = $routeMatch;
    $this->themeManager = $theme_manager;
    $this->stringTranslation = $stringTranslation;
  }

  /**
   * Get a list of know themes.
   *
   * Bootstrap Toolbox needs to know there classes to hide sidebars and configure edge-to-edge mode.
   *
   * @return array
   */
  //~ public function getKnownThemes():array {
    //~ kint('lola');
    //~ $config = \Drupal::config('bootstrap_toolbox.known_themes');
    //~ $configKnownThemes = $config->get('known_themes');
    //~ $themes['custom'] = 'Custom selectors';
    //~ foreach($configKnownThemes as $themeId => $themeData){
      //~ $themes[$themeId] = $themeData['name'];
    //~ }
    //~ $publicPath = \Drupal::service('file_system')->realpath('public://');
    //~ $filePath = $publicPath . '/bootstrap_toolbox/theme_data.yml';
    //~ $customKnownThemes = Yaml::parse(file_get_contents($filePath))['knownthemes'];
    //~ foreach($customKnownThemes as $themeId => $themeData){
      //~ $themes[$themeId] = $themeData['name'];
    //~ }
    
    //~ return $themes;
  //~ }

  /**
   * Get style by id.
   *
   * @param string $id
   *  The style id
   *
   * @return string
   *  The classes associated to style
   */
  public function getStyleById(string $id): string {
    /** @var \Drupal\bootstrap_toolbox\BootstrapToolboxStyleInterface|null $style */
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
    $query->accessCheck(TRUE);
    $entityIds = $query->execute();

    // Load entities and filter manually.
    $entities = $storage->loadMultiple($entityIds);

    if (!empty($scope) && !$scope[0] == NULL) {
      $filteredentities = [];
      foreach ($entities as $id => $entity) {
        if ($entity instanceof \Drupal\bootstrap_toolbox\BootstrapToolboxStyleInterface) {
          $result = array_intersect($scope, $entity->getScope()) ? TRUE : FALSE;
          if ($result) {
            $filteredentities[$id] = $entity;
          }
        }
      }
      return $filteredentities;
    }
    return $entities;
  }


  /**
   * Get array list with styles filtered by scope.
   *
   * @param array $scope
   *
   * @return array
   */
  public function getScopeListFiltered(array $scope): array {
    $styleList = [];
    foreach ($this->getStyleByScope($scope) as $id => $scope) {
      $styleList[$id] = $scope->label();
    }
    return $styleList;
  }

  /**
   * Get array list with styles filtered by scope.
   *
   * @param array $scope
   *
   * @return array
   */
  public function getScopeClassesListFiltered(array $scope): array {
    $styleList = [];
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
  public function getScopeList(): array {
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
    /** @var \Drupal\bootstrap_toolbox\BootstrapToolboxScopeInterface|null $scope */
    $scope = $this->entityTypeManager->getStorage('bootstrap_toolbox_scope')->load($id);
    
    if ($scope instanceof \Drupal\bootstrap_toolbox\BootstrapToolboxScopeInterface) {
      $label = $scope->label();

      // Si es un objeto TranslatableMarkup, convertirlo a string.
      if ($label instanceof \Drupal\Core\StringTranslation\TranslatableMarkup) {
        return $label->__toString();
      }

      // Si ya es una cadena, devolverla directamente.
      if (is_string($label)) {
        return $label;
      }
    }

    // Devuelve una cadena vacía si no se encuentra la entidad o no tiene label.
    return '';
  }



  /**
   * Get html list from array.
   *
   * @param array $items
   *
   * @return object|string|null
   */
  public function arrayToHtmlList(array $items): object|string|null {
    $list = [
      '#theme' => 'item_list',
      '#items' => $items,
    ];
    
    $renderedList = $this->renderArray($list);
    return $renderedList;
  }


  /**
   * Sanitize text field. Remove carriage return and extra spaces.
   *
   * @param string $strValue
   *
   * @return string
   */
  public function sanitizeTextField($strValue): string {
    if (!is_string($strValue)) {
        return $strValue;
    }
    $strValue = str_replace(["\r\n", "\n"], ' ', $strValue);
    $strValue = preg_replace('/\s+/', ' ', $strValue);
    $strValue = trim($strValue ?? '');
    return $strValue;
  }


  /**
   * Returns selectors and variables needed to change the behavior of the theme
   *
   * @params string $theme
   *
   * @returns array|null
   */
  public function getThemeSelectors($theme = ''): ?array {
    if ($theme) {
      $config = $this->configFactory->get('bootstrap_toolbox.known_themes');
      $knownThemes = $config->get('known_themes');
      $publicPath = $this->realpath('public://');
      
      $filePath = $publicPath . '/bootstrap_toolbox/theme_data.yml';
      $content = file_get_contents($filePath);
      if ($content){
        $content = Yaml::parse($content);
        $customKnownThemes = $content['knownthemes'];
        if($customKnownThemes){
          $knownThemes = array_merge($knownThemes, $customKnownThemes);
        }
      }
      return $knownThemes[$theme];  
    }
    return NULL;
  }

  /**
   * Returns base themes
   *
   * @returns array|null
   */
  public function getBaseThemes(): array {
    $config = $this->configFactory->get('bootstrap_toolbox.known_themes');
    $knownThemes = [];
    foreach ($config->get('known_themes') as $theme=>$data){
      $knownThemes[$theme] = $data['name'];
    }
    $publicPath = $this->realpath('public://');
    $filePath = $publicPath . '/bootstrap_toolbox/theme_data.yml';
    $content = file_get_contents($filePath);
    if ($content){
      $content = Yaml::parse($content);
      $customKnownThemes = $content['knownthemes'];
      foreach($customKnownThemes as $themeId => $themeData){
        $knownThemes[$themeId] = $themeData['name'];
      }      
    }
    return $knownThemes;  
  }
  

  /**
   * Get a list with Wrapper entities.
   *
   * @return array
   */
  public function getWrapperList(): array {
    $wrappers = [];
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
  //~ public function getWrapperByLabel($label) {

    //~ // Create an entity query to find the entity by label.
    
    //~ $query = \Drupal::entityQuery('bootstrap_toolbox_wrapper')
      //~ ->condition('id', $label)
      //~ ->range(0, 1);

    //~ $ids = $query->execute();
    //~ // If an ID is found, load and return the entity.
    //~ if ($ids) {
      //~ $id = reset($ids);
      //~ $wrapperEntity = $this->entityTypeManager->getStorage('bootstrap_toolbox_wrapper')->load($id);
      //~ return $wrapperEntity->get('description');
    //~ }

    //~ // Return NULL if no matching entity was found.
    //~ return NULL;
  //~ }

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
    /** @var \Drupal\bootstrap_toolbox\BootstrapToolboxWrapperInterface|null $wrapperEntity */
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
   * @param string $action
   *   The action to execute
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
      $config = $this->configFactory->get('bootstrap_toolbox.settings');
      $options = $config->get('selected_themes');
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
      $viewModeOptions['default'] = $this->t('Default');
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
   * @return array
   *   Render array of the entity.
   */
  public function getEntityRenderArray(string $entityType, string $viewMode, string $entityId): array {
      $entity = $this->entityTypeManager->getStorage($entityType)->load($entityId);
      if ($entity instanceof \Drupal\Core\Entity\EntityInterface) {
          $renderArray = $this->entityTypeManager->getViewBuilder($entityType)->view($entity, $viewMode);
          return $renderArray;
      }
      return [];
  }


  /**
   * Get rendered entity.
   *
   * @param string $entityType
   * @param string $viewMode
   * @param string $entityId
   *
   * @return object|string|null
   */
  public function getRenderedEntity(string $entityType, string $viewMode, string $entityId): object|string|null {
    $renderArray = $this->getEntityRenderArray($entityType, $viewMode, $entityId);
    if ($renderArray) {
      return $this->renderArray($renderArray);
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
   * @param string $mediaId
   * @param string $imageStyle
   *
   * @return string
   *   The media URI or an empty string if not found.
   */
  public function getMediaUriByMediaIdAndImageStyle(string $mediaId, string $imageStyle): string {
      $media = Media::load($mediaId);
      if (!$media) {
          return ''; 
      }

      $mediaFieldName = $media->getSource()->getConfiguration()['source_field'] ?? '';
      if (empty($mediaFieldName)) {
          return ''; 
      }

      $imageField = $media->get($mediaFieldName);

      if (!$imageField->isEmpty()) {
          $firstItem = $imageField->first();
          
          if ($firstItem instanceof FieldItemInterface) {
              $file = $firstItem->getEntity();
              /** @var \Drupal\image\Plugin\Field\FieldType\ImageItem $firstItem */
              $idFile = $firstItem->target_id;
              $file = File::load($idFile);
              if ($file instanceof File) {
                  $fileUri = $file->getFileUri();

                  if ($fileUri) {

                      if ($imageStyle !== 'default') {
                          $imageStyleEntity = ImageStyle::load($imageStyle);
                          if ($imageStyleEntity) {
                              return $imageStyleEntity->buildUri($fileUri);
                          }
                      } else {
                          return $file->uri->value; 
                      }
                  }
              }
          }
      }

      return ''; 
  }


  /**
   * Get media url by mediaId and imageStyle.
   *
   * @param string $mediaId
   * @param string $imageStyle
   *
   * @return string
   *   The media URL or an empty string if not found.
   */
  public function getMediaUrlByMediaIdAndImageStyle(string $mediaId, string $imageStyle): string {
    $media = Media::load($mediaId);
    if (!$media) {
        return ''; 
    }

    $mediaFieldName = $media->getSource()->getConfiguration()['source_field'] ?? '';
    if (empty($mediaFieldName)) {
        return ''; 
    }

    $imageField = $media->get($mediaFieldName);

    if (!$imageField->isEmpty()) {
      $firstItem = $imageField->first();
      if ($firstItem instanceof FieldItemInterface) {
        /** @var \Drupal\image\Plugin\Field\FieldType\ImageItem $firstItem */
        $idFile = $firstItem->target_id;
        if ($idFile){
          $file = File::load($idFile);
          if ($file instanceof File){
            $fileUri = $file->getFileUri();
            if ($fileUri){
              if ($imageStyle !== 'default') {
                $imageStyleEntity = ImageStyle::load($imageStyle);
                /** @var \Drupal\image\Entity\ImageStyle $imageStyleEntity */
                return $imageStyleEntity->buildUrl($fileUri);
              }
              else {
                return $this->fileUrlGenerator->generateAbsoluteString($fileUri);
              }
            }
          }
        }
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
   *   The media file data or an empty array if not found.
   */
  public function getMediaFileDataByMediaIdAndImageStyle(string $mediaId, string $imageStyle): array {
      $media = Media::load($mediaId);
      if (!$media) {
          return []; 
      }

      $mediaFieldName = $media->getSource()->getConfiguration()['source_field'] ?? '';
      if (empty($mediaFieldName)) {
          return [];
      }

      $imageField = $media->get($mediaFieldName);
      if (!$imageField->isEmpty()) {

      $firstItem = $imageField->first();
      if ($firstItem instanceof FieldItemInterface) {
        /** @var \Drupal\image\Plugin\Field\FieldType\ImageItem $firstItem */
        $idFile = $firstItem->target_id;
        if ($idFile){
          $file = File::load($idFile);
          if ($file instanceof File){
            $fileUri = $file->getFileUri();
            if ($fileUri){
              $data = [
                'alt' => $firstItem->get('alt')->getValue(),
              ];
              if ($imageStyle !== 'default') {
                $imageStyleEntity = ImageStyle::load($imageStyle);
                /** @var \Drupal\image\Entity\ImageStyle $imageStyleEntity */
                $data['url'] = $imageStyleEntity->buildUrl($fileUri);
              }
              else {
                $data['url'] = $this->fileUrlGenerator->generateAbsoluteString($fileUri);
              }
              return $data;
            }
          }

        }
      }
    }
    return [];
  }



   /**
     * Creates markup from a string
     *
     * @param string|null $text
     *   The input text, which can be a string, or null.
     *
     * @return object|string
     *   The markup object.
     */
    public function createMarkup($text): object|string {
      if ($text == null) {
          $text = '';
      }
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

  //~ /**
   //~ * Get an entity by entity Type and uuid.
   //~ *
   //~ * @param string $entityType
   //~ * @param string $uuid
   //~ *
   //~ * @return object.
   //~ * */
  //~ public function getEntityByTypeAndUuid($entityType, $uuid):array {
    //~ return $this->entityTypeManager->getStorage($entityType)->loadByProperties(['uuid' => $uuid]);
  //~ }

  /**
   * Get an entity by entity Type and id.
   *
   * @param string $entityType
   * @param string $id
   *
   * @return \Drupal\Core\Entity\EntityInterface|null
   *   The entity object or null if not found.
   */
  public function getEntityByTypeAndId($entityType, $id):?\Drupal\Core\Entity\EntityInterface {
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
    /** @var \Drupal\Core\Routing\RouteMatchInterface $routeMatch */
    $routeMatch = $this->getRouteMatch();
    $route = $routeMatch->getRouteName();
    if ($route) {
      $routeElements = explode('.', $route);
      if ($routeElements[0] == 'entity' && $routeElements[2] == 'canonical') {
        $currentEntity = $routeMatch->getParameter($routeElements[1]);
        $currentEntityToLink = $currentEntity->toLink();
        $currentEntityUrl = $currentEntityToLink->getUrl()->toString();
      }

      if ($url == $currentEntityUrl) {
        $active = TRUE;
      }      
    }

    return $active;
  }

  /**
   * Return an array with available custom themes
   *
   * @return array
   * */
  function getAllowedThemes(): array {
    $config = $this->configFactory->get('bootstrap_toolbox.settings');
    $themes = array_filter($config->get('selected_themes'), function($value){
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
  function getBootstrapToolboxParameters(): array {
    $config = $this->configFactory->get('bootstrap_toolbox.settings');
    $hideTitle = FALSE;
    $hideSidebars = FALSE;
    $hideBreadcrumb = FALSE;
    $edgeToEdge = FALSE;
    $forceToPanel = FALSE;
    $action = '';
    $settings = NULL;
    $isEditMode = FALSE;
    $customTheme = '';
    /** @var \Drupal\Core\Routing\RouteMatchInterface $routeMatch */
    $routeMatch = $this->getRouteMatch();
    /** @var \Symfony\Component\Routing\Route $routeObject */
    $routeObject = $routeMatch->getRouteObject();
    $routeName = $routeMatch->getRouteName();
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
    
    if($action){
    
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
        /** @var \Drupal\node\Entity\NodeType $nodeType */
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
        'customTheme' => $customTheme,
        'routeName' => NULL,
        'node' => NULL,
      ];
      if ($action == 'processNode'){
        $params['node'] = $node;
        $params['routeName'] = $routeName;
      }
      return $params;
    }
    else {
      return [];
    }

    
  }

  /**
   * Get behavior selectors
   *
   * @return string|NULL
   * */
  function getBehaviorSelectors(): ?string {

    $baseThemes = $this->getBaseThemes();
    $theme = $this->themeManager->getActiveTheme()->getName();
    $themeSettings = $this->configFactory->get("{$theme}.settings");
    $baseTheme = NULL;
    $third_party_settings = $themeSettings->get('third_party_settings') ?? NULL;
    
    if (isset($third_party_settings['bootstrap_toolbox']['base_theme'])) {
      $baseTheme = $third_party_settings['bootstrap_toolbox']['base_theme'];
      if(array_key_exists($baseTheme, $baseThemes)){
        return $baseTheme;
      }
    } 
    return NULL;
    
  }

  /**
   * Get custom base themes
   *
   * @return array|null 
   *
   * */
  public function getCustomBasethemes(): ?array {
    $customKnownThemes = [];
    $publicPath = $this->realpath('public://');
    $filePath = $publicPath . '/bootstrap_toolbox/theme_data.yml';
    $content = file_get_contents($filePath);
    if($content){
       $content = Yaml::parse($content);
       $customKnownThemes = $content['knownthemes'];
    }
    return $customKnownThemes;
  }



  /**
   * Returns config base themes
   *
   * @returns array|null
   */
  public function getConfigBaseThemes(): ?array {
    $config = $this->configFactory->get('bootstrap_toolbox.known_themes');
    $knownThemes = [];
    foreach ($config->get('known_themes') as $theme=>$data){
      $knownThemes[$theme] = $data['name'];
    }
    return $knownThemes;  
  }

  /**
   * Save custom base themes
   *
   * @param array $newData
   *   The new data to save in the YAML file.
   * 
   * @return bool
   *   Returns TRUE if the data was saved successfully, FALSE otherwise.
   *
   * */
  public function saveCustomBasethemes($newData): bool {
    $yamlContent = Yaml::dump(['knownthemes' => $newData], 4, 2, Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK);

    $comment = "#~ ===================================================\n";
    $comment .= "#~ Important Notice\n";
    $comment .= "#~ Editing this file manually can cause major errors.\n";
    $comment .= "#~ You must edit it via the Bootstrap Toolbox settings\n";
    $comment .= "#~ ===================================================\n\n";

    $finalContent = $comment . $yamlContent;
  
    $publicPath = $this->realpath('public://');
    $directoryPath = $publicPath. '/bootstrap_toolbox';
    if ($this->fileSystem->prepareDirectory($directoryPath, \Drupal\Core\File\FileSystemInterface::CREATE_DIRECTORY)){
      $filePath = $directoryPath . '/theme_data.yml';
      if (file_put_contents($filePath, $finalContent) === FALSE) {
        $action = 'addError';
        $message = $this->t('Failed to write to YAML file.');
        $this->displayMessage($action,$message);
        $this->logMessage('error', $message, []);
        return FALSE;
      } else {
        $action = 'addMessage';
        $message = $this->t('YAML file updated successfully.');
        $this->displayMessage($action,$message);
        $this->logMessage('info', $message, []);
        return TRUE;
      }
    }
    else {
      $action = 'addError';
      $message = $this->t('Failed to create directory @directoryPath in @publicPath',[
          '@directoryPath' => $directoryPath,
          '@publicPath' => $publicPath,
        ]
      );
      $this->displayMessage($action,$message);
      $this->logMessage('error', $message, []);
      return FALSE;
    }
    
    
  }

  /**
   * Get Route Match object
   *
   * @return object
   * */
  public function getRouteMatch(): object {
   return $this->routeMatch;
  }

  /**
   * Get a class name from a name
   *
   * @param string $name
   * 
   * @return string $className
   * 
   * */
  public function getClassName($name): string {
    return Html::getClass($name);
  }

  //~ /**
   //~ * Get the referer
   //~ *
   //~ * 
   //~ * 
   //~ * */
  //~ public function getReferer() {
    //~ $request = $this->requestStack->getCurrentRequest();
    //~ kint($request);
    //~ $referrer = $request->headers->get('referer');
    //~ return $referrer;
  //~ }

  /**
   * Get theme config
   *
   * @param string $themeId
   *
   * @return array
   *
   * */
  public function getThemeConfig($themeId): array {
    return $this->configFactory->get($themeId . '.settings')->getRawData();
  }

  /**
   * Get link from text and url
   *
   * @para string $text
   * @para string $url
   *
   * @return string
   * */
  public function getLinkFromTextAndUrl($text, $url): string {
    $url = Url::fromUri('internal:/' . $url, ['absolute' => TRUE]);
    $link = Link::fromTextAndUrl($text, $url);
    $link_html = $link->toString()->__toString();
    return $link_html;
  }

  /**
   * Get bootstrap_toolbox.settings
   *
   * @return array|NULL
   *
   * */
  public function getBootstrapToolboxSettings(): ?array {
    return ($this->configFactory->get('bootstrap_toolbox.settings')->getRawData());
  }

  /**
   * Get editable bootstrap_toolbox.settings
   *
   * @return \Drupal\Core\Config\Config
   *   The editable configuration object.
   *
   * */
  public function getEditableBootstrapToolboxSettings(): object {
    return ($this->configFactory->get('bootstrap_toolbox.settings'));
  }

  /**
   * Get node type label
   *
   * @param string $nodeType
   *
   * @return string|NULL
   *
   * */
  public function getNodeTypeLabel($nodeType): ?string {
    $nodeTypeEntity = $this->entityTypeManager->getStorage('node_type')->load($nodeType);
    if($nodeTypeEntity){
      return $nodeTypeEntity->label();  
    }
    return NULL;

  }

  /**
   * Get the default theme
   *
   * @return string
   * */
  public function getDefaultTheme():string {
    return $this->configFactory->get('system.theme')->get('default');
  }

  /**
   * Displays a message using the messenger service.
   *
   * @param string $action
   *   The messenger method to call. Possible values are: 'addWarning', 'addError', 'addMessage', 'addStatus'.
   * @param string|\Drupal\Core\StringTranslation\TranslatableMarkup $message
   *   The message to display. This can be either a plain string or a translatable string.
   *
   * @return void
   *   This method does not return a value.
   */
  public function displayMessage($action,$message): void{
    $this->messenger->$action($message);
  }

  /**
   * Log a message using the logger service.
   *
   * @param string $level
   *   The logging level method to call. Possible values are: 'error', 'warning', 'info', 'debug', 'notice', 'critical', 'alert', 'emergency'.
   * @param string|\Drupal\Core\StringTranslation\TranslatableMarkup $message
   *   The message to log. This can be either a plain string or a translatable string.
   * @param array $context
   *   (optional) An array of context information to include in the log entry.
   *
   * @return void
   *   This method does not return a value.
   */
  public function logMessage(string $level, $message, array $context = []): void {
    $this->logger->$level($message, $context);
  }

  /**
   * Render a renderable array in isolation.
   *
   * This method renders a renderable array using the `renderInIsolation` method
   * from the renderer service. Rendering in isolation ensures that the renderable
   * array is processed separately from other render contexts, avoiding potential
   * conflicts with other parts of the page rendering.
   *
   * @param array $renderableArray
   *   A renderable array containing structured content that Drupal can render.
   *   Renderable arrays are the standard way to represent content in Drupal, and
   *   can include elements such as forms, links, and HTML markup.
   *
   * @return \Drupal\Component\Render\MarkupInterface|string|null
   *   The rendered output of the renderable array as a safe string or markup object.
   *   The return value can be directly output or stored for later use.
   *
   * @throws \Throwable
   *   Thrown if the renderable array cannot be properly rendered.
   */
  public function renderArray(array $renderableArray): \Drupal\Component\Render\MarkupInterface|string|null {
    return $this->renderService->renderInIsolation($renderableArray);
  }


  /**
   * Retrieves the absolute path of a given file or directory.
   *
   * @param string $arg
   *   The URI of the file or directory (e.g., 'public://example.txt').
   *
   * @return string|false
   *   The absolute path to the file or directory, or FALSE if it does not exist.
   */
  public function realPath($arg): string|false {
    $realPath = $this->fileSystem->realpath($arg);
    if ($realPath !== false && file_exists($realPath)){
      return $realPath;
    }
    return FALSE;
  }

  /**
   * Checks if a module is installed and enabled.
   *
   * @param string $moduleName
   *   The machine name of the module to check.
   *
   * @return bool
   *   TRUE if the module exists and is enabled, FALSE otherwise.
   */
  public function checkModule($moduleName): bool {
    return $this->moduleHandler->moduleExists($moduleName);
  }

  /**
   * Retrieves a list of installed themes.
   *
   * @return array
   *   An associative array where the keys are the theme machine names,
   *   and the values are the human-readable theme names.
   */
  public function getThemes(): array {
    $installed_themes = $this->themeHandler->listInfo();
    $themes = [];
    foreach($installed_themes as $themeKey => $themeData){
      $themes[$themeKey] = $themeData->getName();
    }
    return $themes;
  }

  /**
   * Get config factory key
   *
   * @param string $configKey
   *
   * @return object
   *
   */
  public function getConfig($configKey): object {
    return $this->configFactory->get($configKey);
  }
}
