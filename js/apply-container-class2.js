(function (Drupal, once) {
  Drupal.behaviors.bootstrapToolboxApplyContainerClass = {
    attach: function (context, settings) {
      // Obtener la clase y el selector del elemento desde drupalSettings.
      var className = settings.apply_container_class.className;
      var elementSelector = settings.apply_container_class.elementSelector;

      // Asegurarse de que el elemento esté presente y aplicar once.
      var elements = once('bootstrap-toolbox', context.querySelector(elementSelector));
      console.log(elements);
      if (elements.length) {
        var element = elements[0];
        console.log(element);
        // Remover todas las clases que contengan "container".
        var classesToRemove = [];
        element.classList.forEach(function (classItem) {
          if (classItem.includes('container')) {
            classesToRemove.push(classItem);
          }
        });
        classesToRemove.forEach(function (classItem) {
          element.classList.remove(classItem);
        });

        // Añadir la nueva clase al elemento.
        element.classList.add(className);
        
      }
    }
  };
})(Drupal, once);


