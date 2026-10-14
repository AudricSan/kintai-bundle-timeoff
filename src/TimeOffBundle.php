<?php

declare(strict_types=1);

namespace kintai\Bundles\Installed\TimeOff;

use kintai\Bundles\Installed\TimeOff\Providers\PendingTimeoffProvider;
use kintai\Bundles\Installed\TimeOff\Providers\TimeoffAbsenceProvider;
use kintai\Contract\Bundle;
use kintai\Contract\Container;
use kintai\Contract\Extension\NavItem;
use kintai\Contract\Extension\StoreFeature;

/**
 * Contrairement aux autres bundles, TimeOff n'enregistre pas son propre
 * repository : TimeoffRequestRepositoryInterface reste un service Core
 * (RepositoryServiceProvider), car StoreStatsService, ShiftService,
 * AdminShiftController, IcalController et HomeController en dépendent tous
 * pour des calculs qui doivent continuer de fonctionner même si ce bundle
 * est désactivé ou désinstallé. Désactiver "timeoff" retire uniquement l'UI
 * de gestion des congés (créer/approuver/consulter une demande), pas les
 * données elles-mêmes.
 */
final class TimeOffBundle extends Bundle
{
    public function getName(): string
    {
        return 'timeoff';
    }

    public function getVersion(): string
    {
        return '1.2.0';
    }

    public function getLabel(): string
    {
        return __('bundle_timeoff');
    }

    public function getDescription(): string
    {
        return __('bundle_timeoff_desc');
    }

    public function register(): void
    {
        $this->loadViewsFrom($this->getPath() . '/Views', 'timeoff');
        $this->loadRoutesFrom($this->getPath() . '/routes.php');
        $this->loadAssetsFrom('public');
    }

    /**
     * Navigation (contrat 1.1) : entrée(s) de la barre du haut et de la barre mobile.
     */
    public function navigation(): array
    {
        return [
            new NavItem('timeoff', NavItem::REQUESTS, 'timeoff', 'admin.timeoff', 'employee.timeoff',
                icon: 'leaf', employeeLabel: 'my_timeoff', activePaths: ['/admin/timeoff', '/employee/timeoff'],
                storeFeature: 'timeoff', order: 10, mobile: true),
        ];
    }

    /**
     * Catégories de permissions (contrat 1.2), proposées sur la page des rôles.
     */
    public function permissions(): array
    {
        return ['timeoff' => ['view', 'create', 'update', 'approve', 'delete']];
    }

    /**
     * Fonctionnalité activable par magasin (contrat 1.3), sur la fiche magasin.
     */
    public function storeFeatures(): array
    {
        return [new StoreFeature('timeoff', 'feature_timeoff', 'feature_timeoff_desc', '🏖', 30)];
    }

    /**
     * Demandes en attente (contrat 1.9) : page « Demandes » et compteur du tableau de bord du Core.
     */
    public function pendingRequestsProviders(): array
    {
        return [new PendingTimeoffProvider(Container::getInstance())];
    }

    /**
     * Absences (contrat 1.10) : planning, export iCal et statistiques du Core.
     */
    public function absenceProviders(): array
    {
        return [new TimeoffAbsenceProvider(Container::getInstance())];
    }
}
