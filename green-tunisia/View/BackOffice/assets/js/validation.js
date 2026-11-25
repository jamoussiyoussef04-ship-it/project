/**
 * Validation JavaScript et interactions - Module Campagnes & Participations (Étudiant 2)
 * Utilise les classes Spark Admin (.is-invalid-custom, .is-valid-custom, .form-feedback-custom).
 */

document.addEventListener('DOMContentLoaded', function () {
    // 1. Initialisation des barres de progression (évite les styles inline dans le HTML)
    document.querySelectorAll('[data-progress]').forEach(function (bar) {
        const val = parseInt(bar.getAttribute('data-progress') || '0', 10);
        const clamped = Math.max(0, Math.min(100, isNaN(val) ? 0 : val));
        bar.style.width = clamped + '%';
    });

    // 2. Confirmation sur les formulaires de suppression / désinscription
    document.querySelectorAll('.js-confirm-form').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            const msg = form.getAttribute('data-confirm-message') || 'Confirmer cette action ?';
            if (!window.confirm(msg)) {
                event.preventDefault();
            }
        });
    });

    // 3. Bascule Nouveau bénévole / Bénévole existant en Front Office
    const modeRadios = document.querySelectorAll('.js-mode-toggle');
    const existingSection = document.getElementById('section-existing-benevole');
    const newSection = document.getElementById('section-new-benevole');

    if (modeRadios.length > 0 && existingSection && newSection) {
        modeRadios.forEach(function (radio) {
            radio.addEventListener('change', function () {
                const isNew = this.value === 'new';
                existingSection.classList.toggle('d-none', isNew);
                newSection.classList.toggle('d-none', !isNew);
            });
        });
    }

    // Helpers de feedback visuel Spark Admin
    function setFieldError(form, fieldName, message) {
        const field = form.querySelector('[name="' + fieldName + '"]');
        const errorBox = form.querySelector('#error-' + fieldName);
        if (field) {
            field.classList.remove('is-valid-custom');
            field.classList.add('is-invalid-custom');
        }
        if (errorBox) {
            errorBox.innerHTML = message ? '<i class="bi bi-exclamation-circle-fill"></i> ' + message : '';
        }
        return false;
    }

    function setFieldValid(form, fieldName) {
        const field = form.querySelector('[name="' + fieldName + '"]');
        const errorBox = form.querySelector('#error-' + fieldName);
        if (field) {
            field.classList.remove('is-invalid-custom');
            field.classList.add('is-valid-custom');
        }
        if (errorBox) {
            errorBox.innerHTML = '';
        }
        return true;
    }

    function clearFieldState(form, fieldName) {
        const field = form.querySelector('[name="' + fieldName + '"]');
        const errorBox = form.querySelector('#error-' + fieldName);
        if (field) {
            field.classList.remove('is-invalid-custom', 'is-valid-custom');
        }
        if (errorBox) {
            errorBox.innerHTML = '';
        }
    }

    function getTodayISO() {
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        return year + '-' + month + '-' + day;
    }

    // 4. Validation du formulaire Campagne (Création / Modification)
    const campagneForm = document.querySelector('form[data-validate="campagne"]');
    if (campagneForm) {
        const isEdit = campagneForm.getAttribute('data-is-edit') === '1';

        const validateCampagneField = function (fieldName) {
            const field = campagneForm.querySelector('[name="' + fieldName + '"]');
            if (!field) {
                return true;
            }
            const val = field.value.trim();

            if (fieldName === 'titre') {
                if (val.length < 3 || val.length > 150) {
                    return setFieldError(campagneForm, 'titre', 'Le titre doit contenir entre 3 et 150 caractères.');
                }
                return setFieldValid(campagneForm, 'titre');
            }

            if (fieldName === 'type_campagne') {
                const allowed = ['Nettoyage', 'Plantation', 'Sensibilisation'];
                if (!allowed.includes(val)) {
                    return setFieldError(campagneForm, 'type_campagne', 'Sélectionnez un type valide.');
                }
                return setFieldValid(campagneForm, 'type_campagne');
            }

            if (fieldName === 'date') {
                if (!/^\d{4}-\d{2}-\d{2}$/.test(val)) {
                    return setFieldError(campagneForm, 'date', 'Veuillez sélectionner une date valide.');
                }
                const today = getTodayISO();
                const statutField = campagneForm.querySelector('[name="statut"]');
                const statutVal = statutField ? statutField.value : 'Planifiee';
                if (!isEdit && val < today) {
                    return setFieldError(campagneForm, 'date', 'Impossible de créer une campagne avec une date passée.');
                }
                if (isEdit && (statutVal === 'Planifiee' || statutVal === 'En cours') && val < today) {
                    return setFieldError(campagneForm, 'date', 'Une campagne active doit avoir une date supérieure ou égale à aujourd\'hui.');
                }
                return setFieldValid(campagneForm, 'date');
            }

            if (fieldName === 'capacite_max') {
                const num = Number(val);
                if (!Number.isInteger(num) || num < 1 || num > 5000) {
                    return setFieldError(campagneForm, 'capacite_max', 'La capacité doit être un entier entre 1 et 5000.');
                }
                return setFieldValid(campagneForm, 'capacite_max');
            }

            if (fieldName === 'lieu') {
                if (val.length < 3 || val.length > 150) {
                    return setFieldError(campagneForm, 'lieu', 'Le lieu doit contenir entre 3 et 150 caractères.');
                }
                return setFieldValid(campagneForm, 'lieu');
            }

            if (fieldName === 'objectif') {
                if (val.length < 5 || val.length > 255) {
                    return setFieldError(campagneForm, 'objectif', 'L\'objectif doit contenir entre 5 et 255 caractères.');
                }
                return setFieldValid(campagneForm, 'objectif');
            }

            if (fieldName === 'description') {
                if (val.length < 10) {
                    return setFieldError(campagneForm, 'description', 'La description doit contenir au moins 10 caractères.');
                }
                return setFieldValid(campagneForm, 'description');
            }

            return true;
        };

        const fields = ['titre', 'type_campagne', 'date', 'capacite_max', 'lieu', 'objectif', 'description'];
        fields.forEach(function (name) {
            const el = campagneForm.querySelector('[name="' + name + '"]');
            if (el) {
                el.addEventListener('blur', function () {
                    validateCampagneField(name);
                });
                el.addEventListener('input', function () {
                    if (el.classList.contains('is-invalid-custom')) {
                        validateCampagneField(name);
                    }
                });
            }
        });

        const statutEl = campagneForm.querySelector('[name="statut"]');
        if (statutEl) {
            statutEl.addEventListener('change', function () {
                validateCampagneField('date');
            });
        }

        campagneForm.addEventListener('submit', function (event) {
            let allValid = true;
            fields.forEach(function (name) {
                if (!validateCampagneField(name)) {
                    allValid = false;
                }
            });
            if (!allValid) {
                event.preventDefault();
            }
        });
    }

    // 5. Validation des formulaires Participation (Front Office & Back Office)
    document.querySelectorAll('form[data-validate="participation"]').forEach(function (partForm) {
        partForm.addEventListener('submit', function (event) {
            let isValid = true;

            const campagneSelect = partForm.querySelector('select[name="id_campagne"]');
            if (campagneSelect) {
                if (!campagneSelect.value) {
                    isValid = setFieldError(partForm, 'id_campagne', 'Veuillez sélectionner une campagne.');
                } else {
                    setFieldValid(partForm, 'id_campagne');
                }
            }

            const checkedMode = partForm.querySelector('input[name="mode_benevole"]:checked');
            const hiddenMode = partForm.querySelector('input[type="hidden"][name="mode_benevole"]');
            const mode = checkedMode ? checkedMode.value : (hiddenMode ? hiddenMode.value : 'existing');

            if (mode === 'new') {
                clearFieldState(partForm, 'id_benevole');

                const prenomEl = partForm.querySelector('[name="prenom"]');
                const nomEl = partForm.querySelector('[name="nom"]');
                const emailEl = partForm.querySelector('[name="email"]');
                const telEl = partForm.querySelector('[name="telephone"]');

                if (!prenomEl || prenomEl.value.trim().length < 2) {
                    isValid = setFieldError(partForm, 'prenom', 'Prénom requis (min 2 caractères).');
                } else {
                    setFieldValid(partForm, 'prenom');
                }

                if (!nomEl || nomEl.value.trim().length < 2) {
                    isValid = setFieldError(partForm, 'nom', 'Nom requis (min 2 caractères).');
                } else {
                    setFieldValid(partForm, 'nom');
                }

                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailEl || !emailRegex.test(emailEl.value.trim())) {
                    isValid = setFieldError(partForm, 'email', 'Adresse email invalide.');
                } else {
                    setFieldValid(partForm, 'email');
                }

                if (telEl && telEl.value.trim() !== '') {
                    if (!/^[0-9+\s\-]{8,20}$/.test(telEl.value.trim())) {
                        isValid = setFieldError(partForm, 'telephone', 'Téléphone invalide (8 à 20 chiffres).');
                    } else {
                        setFieldValid(partForm, 'telephone');
                    }
                } else {
                    clearFieldState(partForm, 'telephone');
                }
            } else {
                ['prenom', 'nom', 'email', 'telephone'].forEach(function (f) {
                    clearFieldState(partForm, f);
                });

                const benevoleSelect = partForm.querySelector('[name="id_benevole"]');
                if (!benevoleSelect || !benevoleSelect.value) {
                    isValid = setFieldError(partForm, 'id_benevole', 'Veuillez sélectionner un bénévole.');
                } else {
                    setFieldValid(partForm, 'id_benevole');
                }
            }

            const commentaireEl = partForm.querySelector('[name="commentaire"]');
            if (commentaireEl && commentaireEl.value.trim().length > 255) {
                isValid = setFieldError(partForm, 'commentaire', 'Le commentaire ne doit pas dépasser 255 caractères.');
            } else if (commentaireEl) {
                clearFieldState(partForm, 'commentaire');
            }

            if (!isValid) {
                event.preventDefault();
            }
        });
    });
});
