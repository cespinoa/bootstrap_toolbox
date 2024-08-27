<?php

namespace Drupal\bootstrap_toolbox;

interface UtilityServiceInterface {

  /**
   * Get the scope label
   *
   * @param string $id
   * @return string
   *   
   */
  public function getScopeLabel($id): string;

  /**
   * Get a list of known themes.
   *
   * @return array
   *   An array of known themes.
   */
  public function getKnownThemes();

  /**
   * Get active theme selectors.
   *
   * @param string $theme
   *   The theme name.
   *
   * @return array
   *   An array of theme selectors.
   */
  public function getThemeSelectors($theme);

  /**
   * Get a list with Wrapper entities.
   *
   * @return array
   *   An array of Wrapper entities.
   */
  public function getWrapperList();

  /**
   * Load a bootstrap_toolbox_wrapper entity by label.
   *
   * @param string $label
   *   The label of the entity.
   *
   * @return mixed
   *   The loaded entity or NULL if not found.
   */
  public function getWrapperByLabel($label);

  //~ /**
   //~ * Get a list with Classes groups entities.
   //~ *
   //~ * @return array
   //~ *   
   //~ */
  //~ function getClassesGroupList();

  /**
   * Load a bootstrap_toolbox_wrapper entity by id.
   *
   * @param string $id
   *   The id of the entity.
   *
   * @return string
   *   The description of the entity or NULL if not found.
   */
  public function getWrapperById($id);

  //~ /**
   //~ * Load a bootstrap_toolbox_classes entity by id.
   //~ *
   //~ * @param string $id
   //~ *   The id of the entity.
   //~ *
   //~ * @return string
   //~ *   The description of the entity or NULL if not found.
   //~ */
  //~ public function loadClassesById($id);




  //~ /**
   //~ * Load bootstrap_toolbox_classes entities by classes_group.
   //~ *
   //~ * @param array $classes_group
   //~ *   The classes_group of the entities.
   //~ *
   //~ * @return array
   //~ *   The classes with this classes_group or NULL if not found.
   //~ */
  //~ public function loadClassesByClassesGroup(array $classes_group): array;


  //~ /**
   //~ * Load a bootstrap_toolbox_classes_group by  id.
   //~ *
   //~ * @param string $id
   //~ *   The id of the entity.
   //~ *
   //~ * @return object
   //~ *   The description of entity, or NULL if not found.
   //~ */
  //~ public function loadClassesGroupById($id): object;


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
   * @return array $imageStyles
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
   * @return array $viewModes
   *   The view modes array.
   */
  public function getViewModes(string $targetType, bool $includeFullMode = FALSE, array $includeFieldsMode = NULL): array;

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
  public function getBundleFields(string $entity, string $bundle, array $types = NULL ): array;

  /**
   * Get entity render array
   *
   * @param string $entityType
   * @param string $viewMode
   * @param string $entityId
   *
   * @return object
   * */
  public function getRenderedEntity(string $entityType,string $viewMode, string $entityId): array;

  /**
   * Provide tags styles
   *
   * @return array
   * */
  public function getTagStyles(): array;

    /**
   * Get media uri by mediaId and imageStyle
   *
   * @param string mediaId $mediaId
   * @param string imageStyle
   *
   * @return string
   * */
  public function getMediaUriByMediaIdAndImageStyle(string $mediaId, string $imageStyle): string;

  /**
   * Get media uri by mediaId and imageStyle
   *
   * @param string mediaId $mediaId
   * @param string imageStyle
   *
   * @return string
   * */
  public function getMediaUrlByMediaIdAndImageStyle(string $mediaId, string $imageStyle): string;

  /**
   * Returns markup from text
   *
   * @param strint $text
   *
   * @return object
   * */
  public function createMarkup($text):object;

  /**
   * Get an entity by entity Type and uuid
   *
   * @param string $entityType
   * @param string $uuid
   *
   * return object
   * */
  public function getEntityByTypeAndUuid($entityType, $uuid):array;
  
}
