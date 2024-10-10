<?php

namespace Drupal\bootstrap_toolbox;

/**
 *
 */
interface UtilityServiceInterface {

  /**
   * Get the scope label.
   *
   * @param string $id
   *
   * @return string
   */
  public function getScopeLabel($id): string;

    /**
   * Get html list from array.
   *
   * @param array $items
   *
   * @return object|string|null
   */
  public function arrayToHtmlList(array $items): object|string|null;

  /**
   * Sanitize text field. Remove carriage return and extra spaces.
   *
   * @param string $strValue
   *
   * @return string
   */
  public function sanitizeTextField($strValue): string;

 




  /**
   * Get style by id.
   *
   * @param string $id
   *
   * @return string
   */
  public function getStyleById(string $id): string;

  /**
   * Get style by scope.
   *
   * @param array $scope
   *
   * @return array
   */
  public function getStyleByScope(array $scope): array;

  /**
   * Get array list with styles filtered by scope.
   *
   * @param array $scope
   *
   * @return array
   */
  public function getScopeListFiltered(array $scope): array;

  /**
   * Get array list with styles filtered by scope.
   *
   * @param array $scope
   *
   * @return array
   */
  public function getScopeClassesListFiltered(array $scope): array;

  /**
   * Get a list with scope entities.
   *
   * @return array
   */
  public function getScopeList(): array;

  
  
  

  



  

  /**
   * Get active theme selectors.
   *
   * @param string $theme
   *   The theme name.
   *
   * @return array|null
   *   An array of theme selectors.
   */
  public function getThemeSelectors($theme): ?array;

   /**
   * Returns base themes
   *
   * @returns array|null
   */
  public function getBaseThemes(): array;

  /**
   * Get a list with Wrapper entities.
   *
   * @return array
   *   An array of Wrapper entities.
   */
  public function getWrapperList(): array;

  //~ /**
   //~ * Load a bootstrap_toolbox_wrapper entity by label.
   //~ *
   //~ * @param string $label
   //~ *   The label of the entity.
   //~ *
   //~ * @return mixed
   //~ *   The loaded entity or NULL if not found.
   //~ */
  //~ public function getWrapperByLabel($label);

  /**
   * Load a bootstrap_toolbox_wrapper entity by id.
   *
   * @param string $id
   *   The id of the entity.
   *
   * @return string|null
   *   The description of entity, or NULL if not found.
   */
  public function getWrapperById($id): ?string;

  /**
   * Filter non-zero values from an array.
   *
   * @param array $array
   *   The array to filter.
   *
   * @return array
   *   The filtered array.
   */
  public function filterNonZeroValues(array $array): array;

  /**
   * Filter empty values from an array.
   *
   * @param array $array
   *   The array to filter.
   *
   * @return array
   *   The filtered array.
   */
  public function filterNonEmptyValues(array $array): array;

  /**
   * Provide node types list.
   *
   * @return array
   *   All node types in an array.
   */
  public function getNodeTypes(): array;

  /**
   * Gets the block types.
   *
   * @return array
   *   An array of block types.
   */
  public function getBlockTypes(): array;

  /**
   * Build description list for entity types.
   *
   * @param array $entityTypes
   *   The entity types.
   * @param array $types
   *   The types to list.
   * @param string $action
   *   The action to display.
   *
   * @return string
   *   The built description list.
   */
  public function buildDescriptionList(array $entityTypes, array $types, string $action): string;

  /**
   * Create a field for a given entity type and bundle.
   *
   * @param string $entityType
   *   The entity type.
   * @param string $bundle
   *   The bundle.
   * @param string $fieldName
   *   The field name.
   * @param array $fieldConfig
   *   The field configuration.
   */
  public function createField(string $entityType, string $bundle, string $fieldName, array $fieldConfig): void;

  /**
   * Remove a field from a given entity type and bundle.
   *
   * @param string $entityType
   *   The entity type.
   * @param string $bundle
   *   The bundle.
   * @param string $fieldName
   *   The field name.
   */
  public function removeField(string $entityType, string $bundle, string $fieldName): void;

  /**
   * Get image styles.
   *
   * @return array
   *   The image styles array.
   */
  public function getImageStyles(): array;

  /**
   * Get view modes in bundle.
   * If get parm @includeFullMode, add the Full mode
   * If get parm @includeFieldsMode, add the fields mode (not display mode)
   *
   * @param string $targetType
   * @param bool $includeFullMode
   * @param array $includeFieldsMode
   *
   * @return array
   *   The view modes array.
   */
  public function getViewModes(string $targetType, bool $includeFullMode = FALSE, array $includeFieldsMode = []): array;

  /**
   * Gets fields from a bundle, with title and body, but without the rest of the base fields.
   * It's can filtered by type of field
   *
   * @param string $entity
   * @param string $bundle
   * @param array $types
   *
   * @return array
   */
  public function getBundleFields(string $entity, string $bundle, array $types = NULL): array;

  /**
   * Get entity render array.
   *
   * @param string $entityType
   * @param string $viewMode
   * @param string $entityId
   *
   * @return array
   */
  public function getEntityRenderArray(string $entityType, string $viewMode, string $entityId): array;

  /**
   * Get rendered entity.
   *
   * @param string $entityType
   * @param string $viewMode
   * @param string $entityId
   *
   * @return object|string|null
   */
  public function getRenderedEntity(string $entityType, string $viewMode, string $entityId): object|string|null;

  /**
   * Provide tags styles.
   *
   * @return array
   */
  public function getTagStyles(): array;

  /**
   * Get media uri by mediaId and imageStyle.
   *
   * @param string $mediaId
   * @param string $imageStyle
   *
   * @return string
   *   The media URI or an empty string if not found.
   */
  public function getMediaUriByMediaIdAndImageStyle(string $mediaId, string $imageStyle): string;

  /**
   * Get media url by mediaId and imageStyle.
   *
   * @param string $mediaId
   * @param string $imageStyle
   *
   * @return string
   */
  public function getMediaUrlByMediaIdAndImageStyle(string $mediaId, string $imageStyle): string;

  /**
   * Get media file data by mediaId and imageStyle.
   *
   * @param string $mediaId
   * @param string $imageStyle
   *
   * @return array
   *   The media file data or an empty array if not found.
   */
  public function getMediaFileDataByMediaIdAndImageStyle(string $mediaId, string $imageStyle): array;

  /**
   * Creates markup from a string
   *
   * @param string|null $text
   *   The input text, which can be a string, or null.
   *
   * @return object|string
   *   The markup object.
   */
  public function createMarkup($text): object|string;

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
  public function getEntitiesByTypeAndBundle(string $entityType, string $bundle, string $sortField = NULL, string $direction = 'asc'): array;

  //~ /**
   //~ * Get an entity by entity Type and uuid.
   //~ *
   //~ * @param string $entityType
   //~ * @param string $uuid
   //~ *
   //~ * @return array
   //~ */
  //~ public function getEntityByTypeAndUuid(string $entityType, string $uuid): array;

  /**
   * Get an entity by entity Type and id.
   *
   * @param string $entityType
   * @param string $id
   *
   * @return \Drupal\Core\Entity\EntityInterface|null
   *   The entity object or null if not found.
   */
  public function getEntityByTypeAndId($entityType, $id):?\Drupal\Core\Entity\EntityInterface;

  /**
   * Check if the parameter $url is the current url.
   *
   * @param string $url
   *
   * @return bool
   */
  public function isCurrentUrl(string $url): bool;

  /**
   * Return an array with available custom themes
   *
   * @return array
   * */
  function getAllowedThemes(): array;

  /**
   * Get behavior selectors
   *
   * @return string|NULL
   * */
  function getBehaviorSelectors(): ?string;

  /**
   *
   * Prepara custom parameters
   *
   * @return array
   * 
   * */
  function getBootstrapToolboxParameters(): array;

  /**
   * Get custom base themes
   *
   * @return array|null 
   *
   * */
  public function getCustomBasethemes(): ?array;

  /**
   * Returns config base themes
   *
   * @returns array|null
   */
  public function getConfigBaseThemes(): ?array;

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
  public function saveCustomBasethemes($newData): bool;

  /**
   * Get Route Match object
   *
   * @return object
   * */
  public function getRouteMatch(): object;

  /**
   * Get a class name from a name
   *
   * @param string $name
   * 
   * @return string $className
   * 
   * */
  public function getClassName($name): string ;

  //~ /**
   //~ * Get the referer
   //~ *
   //~ * 
   //~ * 
   //~ * */
  //~ public function getReferer();

  /**
   * Get theme config
   *
   * @param string $themeId
   *
   * @return array
   *
   * */
  public function getThemeConfig($themeId): array;

  /**
   * Get link from text and url
   *
   * @para string $text
   * @para string $url
   *
   * @return string
   * */
  public function getLinkFromTextAndUrl($text, $url): string;

  /**
   * Get bootstrap_toolbox.settings
   *
   * @return array|NULL
   *
   * */
  public function getBootstrapToolboxSettings(): ?array;

  /**
   * Get editable bootstrap_toolbox.settings
   *
   * @return \Drupal\Core\Config\Config
   *   The editable configuration object.
   *
   * */
  public function getEditableBootstrapToolboxSettings(): object;

  /**
   * Get node type label
   *
   * @param string $nodeType
   *
   * @return string|NULL
   *
   * */
  public function getNodeTypeLabel($nodeType): ?string;

  /**
   * Get the default theme
   *
   * @return string
   * */
  public function getDefaultTheme():string;

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
  public function displayMessage($action,$message): void;

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
  public function logMessage(string $level, $message, array $context = []): void;

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
  public function renderArray(array $renderableArray): \Drupal\Component\Render\MarkupInterface|string|null;

  /**
   * Retrieves the absolute path of a given file or directory.
   *
   * @param string $arg
   *   The URI of the file or directory (e.g., 'public://example.txt').
   *
   * @return string|false
   *   The absolute path to the file or directory, or FALSE if it does not exist.
   */
  public function realPath($arg): string|false;

   /**
   * Checks if a module is installed and enabled.
   *
   * @param string $moduleName
   *   The machine name of the module to check.
   *
   * @return bool
   *   TRUE if the module exists and is enabled, FALSE otherwise.
   */
  public function checkModule($moduleName): bool;

  /**
   * Retrieves a list of installed themes.
   *
   * @return array
   *   An associative array where the keys are the theme machine names,
   *   and the values are the human-readable theme names.
   */
  public function getThemes(): array;

  /**
   * Get config factory key
   *
   * @param string $configKey
   *
   * @return object
   *
   */
  public function getConfig($configKey):object;

}
