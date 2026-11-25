// Validation côté client (confort utilisateur). La vraie validation reste côté serveur (Controllers).
document.addEventListener('DOMContentLoaded', function () {
    var MAX_IMAGE = 2 * 1024 * 1024;
    var IMAGE_TYPES = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

    function validDate(s) {
        if (!/^\d{4}-\d{2}-\d{2}$/.test(s)) return false;
        var d = new Date(s + 'T00:00:00Z');
        return !isNaN(d) && d.toISOString().slice(0, 10) === s;
    }

    document.querySelectorAll('form[data-validate]').forEach(function (form) {
        form.addEventListener('submit', function (ev) {
            var errors = {};
            var get = function (n) { return form.elements[n] ? form.elements[n].value.trim() : ''; };

            if (form.dataset.validate === 'article') {
                if (get('titre') === '') errors.titre = "Le titre est obligatoire.";
                else if (get('titre').length > 255) errors.titre = "255 caractères maximum.";
                if (get('contenu') === '') errors.contenu = "Le contenu est obligatoire.";
                if (!validDate(get('date_publication'))) errors.date_publication = "Date invalide.";
                if (get('id_categorie') === '') errors.id_categorie = "Choisissez une catégorie.";

                var file = form.elements.image && form.elements.image.files[0];
                if (file) {
                    if (IMAGE_TYPES.indexOf(file.type) === -1) errors.image = "Format non autorisé (JPG, PNG, WEBP, GIF).";
                    else if (file.size > MAX_IMAGE) errors.image = "L'image ne doit pas dépasser 2 Mo.";
                }
            }

            if (form.dataset.validate === 'categorie') {
                if (get('nom') === '') errors.nom = "Le nom est obligatoire.";
                else if (get('nom').length > 100) errors.nom = "100 caractères maximum.";
            }

            form.querySelectorAll('.field-error').forEach(function (el) {
                el.textContent = errors[el.dataset.for] || '';
            });

            var first = Object.keys(errors)[0];
            if (first) {
                ev.preventDefault();
                form.elements[first].focus();
            }
        });
    });
});
