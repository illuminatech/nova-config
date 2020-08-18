<?php
/**
 * @link https://github.com/illuminatech
 * @copyright Copyright (c) 2019 Illuminatech
 * @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 */

namespace Illuminatech\NovaConfig\Http\Middleware;

use Laravel\Nova\Nova;
use Illuminatech\NovaConfig\NovaConfig;

/**
 * @author Paul Klimov <klimov.paul@gmail.com>
 * @since 1.1.5
 */
class Authorize
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Illuminate\Http\Response
     */
    public function handle($request, $next)
    {
        $tool = collect(Nova::registeredTools())->first([$this, 'matchesTool']);

        return optional($tool)->authorize($request) ? $next($request) : abort(403);
    }

    /**
     * Determine whether this tool belongs to the package.
     *
     * @param  \Laravel\Nova\Tool  $tool
     * @return bool
     */
    public function matchesTool($tool)
    {
        return $tool instanceof NovaConfig;
    }
}
