var paragraphs = document.querySelectorAll('p');
console.log('p');
paragraphs.forEach(function (paragraph) {
  // Eliminar el párrafo si está vacío o contiene solo espacios en blanco.
  if (paragraph.innerHTML.trim() === '') {
    paragraph.remove();
  }
});


