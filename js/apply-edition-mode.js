(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.applyEditionMode = {
    attach: function (context, settings) {
      // Verificar si los datos están disponibles en drupalSettings.
      if (drupalSettings.apply_edition_mode) {
        var settingsArray = drupalSettings.apply_edition_mode;

        // Recorrer cada elemento del array y aplicar las clases.
        $.each(settingsArray, function (index, item) {
          var $element = $(item.element);
          if ($element.length) {
            // Eliminar la clase si existe.
            if (item.class_to_remove) {
              $element.removeClass(item.class_to_remove);
            }
            // Agregar la clase si está definida.
            if (item.class_to_add) {
              $element.addClass(item.class_to_add);
            }
          }
        });
      }
    }
  };
})(jQuery, Drupal, drupalSettings);
