<?php
/**
 * @link https://github.com/illuminatech
 * @copyright Copyright (c) 2019 Illuminatech
 * @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 */

namespace Illuminatech\NovaConfig\Http\Controllers;

use Illuminate\Contracts\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminatech\NovaConfig\Http\Resources\ConfigItemResource;

/**
 * ConfigController handles API request for application configuration management.
 *
 * @author Paul Klimov <klimov.paul@gmail.com>
 * @since 1.1.5
 */
class ConfigController extends Controller
{
    /**
     * @var \Illuminatech\Config\PersistentRepository persistent config repository.
     */
    private $config;

    /**
     * Constructor.
     *
     * @param  \Illuminate\Contracts\Container\Container  $container DI container instance.
     */
    public function __construct(Container $container)
    {
        $this->config = $container->get('config');
    }

    /**
     * Returns current data for configuration items.
     *
     * @return mixed response.
     */
    public function index()
    {
        return ConfigItemResource::collection($this->config->restore()->getItems());
    }

    /**
     * Updates app configuration.
     *
     * @param  \Illuminate\Http\Request  $request HTTP request instance.
     * @return mixed response.
     */
    public function update(Request $request)
    {
        $validatedData = $this->config->validate($request->all());

        $this->config->save($validatedData);

        return ConfigItemResource::collection($this->config->getItems());
    }

    /**
     * Resets app configuration to default values.
     *
     * @return mixed response
     */
    public function reset()
    {
        $this->config->reset();

        return ConfigItemResource::collection($this->config->getItems());
    }
}
