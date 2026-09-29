# Changelog

Tous les changements notables de ce bundle sont documentés dans ce fichier.

Le format suit [Keep a Changelog](https://keepachangelog.com/fr/1.0.0/).
Le schéma de version (X.Y.Z, canaux alpha/beta/main) est décrit dans
`.github/workflows/release.yml`.

## [Unreleased]

### Changed

- Compatibilité avec la Content-Security-Policy stricte de Kintai (`script-src 'self' 'nonce-…'`, sans `'unsafe-inline'`) : l'attribut d'événement inline des vues (`onclick=`/`onchange=`/`onsubmit=`/`oninput=`) est remplacé par des attributs `data-*` (`data-on-click`, `data-submit-on-change`, `data-confirm`… gérés par `csp-actions.js` du Core). Sans ce changement, les boutons, sélecteurs et confirmations de ces vues ne font plus rien sous la nouvelle politique, sans aucune erreur visible. **Nécessite Kintai Core 0.3.0 ou plus** (`kintai_core.min`), version qui introduit `csp-actions.js` et la CSP à nonce. `tests.yml` échoue désormais si un handler inline, un lien `javascript:` ou un `<script>` sans nonce réapparaît dans `Views/` ou `src/`.

## [1.1.0] - 2026-09-29

### Added

- La soumission d'une demande de congé ne notifiait jamais les managers (contrairement à daily-report, feedback, shift-claim, notebook, qui le font tous) — ils ne l'apprenaient qu'en consultant `/admin/timeoff`. `EmployeeTimeoffController::storeTimeoff()` notifie désormais les membres du store détenant `timeoff.approve`, avec le type de congé et la plage de dates, lien vers `/admin/timeoff`.

### Changed

- Aucun changement fonctionnel — bump de version pour aligner ce bundle sur la ligne 1.1.0 commune à tous les bundles officiels.
- Les notifications de congé (ajout direct, approbation, refus) ne disaient rien du congé concerné et ne menaient nulle part au clic. Le corps précise désormais le type et la plage de dates (`notif_timeoff_added_body`/`_approved_body`/`_refused_body`, côté Kintai Core, gagnent les placeholders `:type`/`:start`/`:end`), et le clic renvoie vers `/employee/timeoff`. **Nécessite** la version de Kintai Core introduisant le paramètre `$link` sur `notify()`/`notifyMany()`.
- Le CSS du formulaire (`.timeoff-form*`) vivait mélangé au fichier Core `features/employee.css` (page espace employé), pas dans ce dépôt. Il vit maintenant dans `public/css/timeoff.css`, fourni par ce bundle via `Bundle::loadAssetsFrom()`/`bundle_asset()`. **Nécessite** `kintai_core.min: "0.2.0"`.

## [1.0.0] - 2026-09-19

### Added

- Extraction initiale depuis Kintai (`src/Bundles/TimeOff`).
