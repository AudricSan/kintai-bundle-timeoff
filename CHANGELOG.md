# Changelog

Tous les changements notables de ce bundle sont documentés dans ce fichier.

Le format suit [Keep a Changelog](https://keepachangelog.com/fr/1.0.0/).
Le schéma de version (X.Y.Z, canaux alpha/beta/main) est décrit dans
`.github/workflows/release.yml`.

## [Unreleased]

### Changed

- Contrat de bundle versionné : le bundle n'utilise plus que les noms du contrat `kintai\Contract\…` du Core (requête, réponse, routes, middlewares, exceptions, droits, données du Core, services, vues et composants) et déclare `"kintai_contract": "^1.0"` dans `bundle.json` (`kintai_core.min` relevé à `0.3.6`, première version du Core qui fournit le contrat). Nouveau test `ContractUsageTest` : toute utilisation du Core hors contrat échoue, hormis 1 classe(s) propre(s) au bundle qui vivent encore dans le Core (extraction prévue).

### Added

- Socle de tests PHPUnit (composer.json, phpunit.xml) et CI qui l'exécute contre le vrai Core : les tests que le Core faisait tourner sur une copie figée du bundle (fixtures 1.0.0) vivent désormais ici et testent le code réel.

### Fixed

- Sécurité / intégrité — approuver ou refuser une demande de congé ignorait son statut : un gérant pouvait ressusciter une demande annulée par l'employé ou ré-approuver (double notification). Seule une demande en attente se décide. Les dates sont validées (format AAAA-MM-JJ, fin ≥ début) côté employé et gérant. Un employé multi-magasins choisit le magasin de sa demande (sélecteur) au lieu d'être rattaché à son premier magasin ; un gérant qui crée un congé pour un employé obtient son propre magasin au lieu d'un 403 ; un employé sans magasin ne peut plus déposer de demande (`store_id = 0`).
- Sécurité — `POST /api/v1/timeoff-requests` fusionnait le JSON brut du client dans `save()`, qui fait un upsert dès qu'un `id` est présent. La route de création étant en libre-service (`self => user_id`), un employé pouvait poster `status: approved` (auto-approbation), `processed_by`, un `store_id` qui n'est pas le sien, ou un `id` pour écraser la demande d'un collègue. Seuls `start_date`, `end_date`, `type` et `reason` sont lus ; `user_id` est celui de l'appelant (ou un tiers si `timeoff.create` sur le magasin), le statut initial est toujours `pending`, et l'appelant doit être membre du magasin. `PUT` ne modifie plus que les dates, le type, le motif, le statut et la note, et enregistre qui a traité la demande et quand ; `user_id` et `store_id` ne changent jamais. **Nécessite** Kintai Core avec `StoreUserRepositoryInterface` (déjà présent).

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
