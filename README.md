<p align="center">
    <a href="https://github.com/illuminatech" target="_blank">
        <img src="https://avatars1.githubusercontent.com/u/47185924" height="100px">
    </a>
    <h1 align="center">A Laravel Nova tool for application configuration management</h1>
    <br>
</p>

This extension provides Laravel Nova web interface for the application configuration setup.

For license information check the [LICENSE](LICENSE.md)-file.

[![Latest Stable Version](https://img.shields.io/packagist/v/illuminatech/nova-config.svg)](https://packagist.org/packages/illuminatech/nova-config)
[![Total Downloads](https://img.shields.io/packagist/dt/illuminatech/nova-config.svg)](https://packagist.org/packages/illuminatech/nova-config)


Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist illuminatech/nova-config
```

or add

```json
"illuminatech/nova-config": "*"
```

to the require section of your composer.json.


Usage
-----

This extension provides Laravel Nova web interface for the application configuration setup.
It provides a single form for the configuration parameters setup and restore defaults feature.

This extension relies on [illuminatech/config](https://github.com/illuminatech/config) package for the actual configuration management.
Make sure you are familiar with [illuminatech/config](https://github.com/illuminatech/config) package before attempting to use this one.

First of all, you need to setup persistent configuration for your application, specifying the particular configuration items,
which should be editable from Nova admin panel. For example:

```php
<?php

namespace App\Providers;

use Illuminatech\Config\Providers\AbstractPersistentConfigServiceProvider;

class PersistentConfigServiceProvider extends AbstractPersistentConfigServiceProvider
{
    protected function items(): array
    {
        return [
            'app.name' => [
                'label' => __('Application name'),
                'rules' => ['sometimes', 'required'],
            ],
            'app.debug' => [
                'label' => __('Debug mode enabled'),
                'rules' => ['sometimes', 'required', 'boolean'],
                'cast' => 'boolean',
            ],
            'app.env' => [
                'label' => __('Application environment'),
                'rules' => ['sometimes', 'required'],
            ],
            // ...
        ];
    }

    // ...
}
```

Next, you should register the `\Illuminatech\NovaConfig\NovaConfig` tool within Nova at your `NovaServiceProvider`:

```php
<?php

namespace App\Providers;

use Laravel\Nova\NovaApplicationServiceProvider;

class NovaServiceProvider extends NovaApplicationServiceProvider
{
    public function tools()
    {
        return [
            new \Illuminatech\NovaConfig\NovaConfig(),
            // ...
        ];
    }
}
```

Once it is done the new tool called "Settings" will appear at the Nova sidebar menu, leading to the configuration setup form.


### Field Configuration <span id="field-configuration"></span>

The form field to be used for the particular configuration item management defined via `\Illuminatech\Config\Item::$options`.
By default, a regular text input will be used for the item. In case `\Illuminatech\Config\Item::$cast` is set to "bool" or "boolean",
the checkbox field will be used. The dropdown (e.g. select) input will be rendered in case 'options' array provided.
You can manually define the exact Nova field to be used, using 'component' key.
For example:

```php
<?php

namespace App\Providers;

use Illuminatech\Config\Providers\AbstractPersistentConfigServiceProvider;

class PersistentConfigServiceProvider extends AbstractPersistentConfigServiceProvider
{
    protected function items(): array
    {
        return [
            'app.name' => [
                'label' => __('Application name'),
                'rules' => ['sometimes', 'required'],
                // renders regular text input
            ],
            'app.debug' => [
                'label' => __('Debug mode enabled'),
                'rules' => ['sometimes', 'required', 'boolean'],
                'cast' => 'boolean', // renders checkbox input
            ],
            'app.env' => [
                'label' => __('Application environment'),
                'rules' => ['sometimes', 'required'],
                'options' => [ // dont't mess `Item::$options` with drop-down options!
                    'options' => [ // renders drop-down input
                        ['value' => 'production', 'label' => 'Production'],
                        ['value' => 'local', 'label' => 'Local'],
                        ['value' => 'testing', 'label' => 'Testing'],
                    ],
                ],
            ],
            'app.description' => [
                'label' => __('Application description'),
                'rules' => ['sometimes', 'required'],
                'options' => [
                    'component' => 'textarea-field', // renders textarea input
                ],
            ],
            // ...
        ];
    }

    // ...
}
```

In general any Nova field, which allowed to be used for resource management form (e.g. create/update record form), could be
used for the particular config item. However, some fields require extra configuration parameters, which you'll have to setup
manually. In order to get a proper configuration options, you can setup the field, you are interested with, for some of your
Nova resources, then navigate to this resource's creation form and search in browser network console for the XHR request to
URL like `http://example.com/nova-api/your-resource-name/creation-fields`. Its response contains JSON with key "fields",
within which you can find the proper configuration for your field.


### Access Restriction <span id="access-restriction"></span>

You can restrict access to the application configuration setup form using regular tool's `canSee()` method provided by Nova.
For example:

```php
<?php

namespace App\Providers;

use Laravel\Nova\NovaApplicationServiceProvider;

class NovaServiceProvider extends NovaApplicationServiceProvider
{
    public function tools()
    {
        return [
            (new \Illuminatech\NovaConfig\NovaConfig())
                ->canSee(function($request) {
                    return $request->user(config('nova.guard'))->is_super_admin;
                }),
            // ...
        ];
    }
}
```


### Localization <span id="localization"></span>

All static text used within this extension is translatable via Laravel localization feature.
You can publish the override for translations using following command:

```
php artisan vendor:publish --provider="Illuminatech\NovaConfig\NovaConfigServiceProvider" --tag=lang
```
