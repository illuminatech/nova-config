<?php
/**
 * @link https://github.com/illuminatech
 * @copyright Copyright (c) 2019 Illuminatech
 * @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 */

namespace Illuminatech\NovaConfig\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * ConfigItemCollection represents list of config item field configurations.
 *
 * @see \Illuminatech\NovaConfig\Http\Resources\ConfigItemResource
 *
 * @author Paul Klimov <klimov.paul@gmail.com>
 * @since 1.0.2
 */
class ConfigItemCollection extends ResourceCollection
{
    /**
     * {@inheritdoc}
     */
    public function toArray($request): array
    {
        return [
            'data' => ConfigItemResource::collection($this->collection),
        ];
    }
}
