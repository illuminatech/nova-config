<?php
/**
 * @link https://github.com/illuminatech
 * @copyright Copyright (c) 2019 Illuminatech
 * @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 */

namespace Illuminatech\NovaConfig;

use Illuminate\Http\Request;
use Laravel\Nova\Menu\MenuSection;
use Laravel\Nova\Nova;
use Laravel\Nova\Tool;

/**
 * NovaConfig defines a Nova tool.
 *
 * It should be registered at {@see \App\Providers\NovaServiceProvider::tools()}.
 *
 * @author Paul Klimov <klimov.paul@gmail.com>
 * @since 1.0
 */
class NovaConfig extends Tool
{
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        Nova::script('illuminatech-nova-config', __DIR__ . '/../dist/js/tool.js');
    }

    /**
     * {@inheritdoc}
     */
    public function menu(Request $request)
    {
        return MenuSection::make(__('Settings'))
            ->path('/app-config')
            ->icon('cog');
    }
}
