<?php

//Import
Breadcrumbs::register('alix_imports', function ($breadcrumbs) {
    $breadcrumbs->parent('dashboard');
    $breadcrumbs->push(trans('Aliexpress::module.import.title'), url(config('aliexpress.models.import.resource_url')));
});

Breadcrumbs::register('alix_import_create_edit', function ($breadcrumbs) {
    $breadcrumbs->parent('alix_imports');
    $breadcrumbs->push(view()->shared('title_singular'));
});

Breadcrumbs::register('alix_import_show', function ($breadcrumbs) {
    $breadcrumbs->parent('alix_imports');
    $breadcrumbs->push(view()->shared('title_singular'));
});

//Settings
Breadcrumbs::register('aliexpress_settings', function ($breadcrumbs) {
    $breadcrumbs->parent('dashboard');
    $breadcrumbs->push(trans('Aliexpress::labels.settings.title'), 'aliexpress/settings');
});
