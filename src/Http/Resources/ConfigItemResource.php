<?php
/**
 * @link https://github.com/illuminatech
 * @copyright Copyright (c) 2019 Illuminatech
 * @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 */

namespace Illuminatech\NovaConfig\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * ConfigItemResource represents config item field configuration.
 *
 * @see \Illuminatech\NovaConfig\Http\Resources\ConfigItemCollection
 *
 * @property \Illuminatech\Config\Item $resource
 *
 * @author Paul Klimov <klimov.paul@gmail.com>
 * @since 1.0
 */
class ConfigItemResource extends JsonResource
{
    /**
     * {@inheritdoc}
     */
    public static $wrap = 'data';

    /**
     * {@inheritdoc}
     */
    public function toArray($request): array
    {
        return array_merge(
            [
                'component' => $this->detectFieldComponent(),
                'name' => $this->resource->label,
                'helpText' => $this->resource->hint,
                'nullable' => in_array('nullable', $this->resource->rules),
                'readonly' => false,
                'required' => in_array('required', $this->resource->rules),
                'textAlign' => 'left',
                'visible' => true,
                'dependsOn' => null,
                'displayedAs' => null,
                'fullWidth' => false,
                'indexName' => $this->resource->label,
                'panel' => 'Default',
                'placeholder' => null,
                'prefixComponent' => false,
                'sortable' => true,
                'sortableUriKey' => $this->resource->id,
                'stacked' => false,
                'usesCustomizedDisplay' => false,
                'wrapping' => false,
                'suggestions' => null,
                'dependentComponentKey' => null,
            ],
            (array) $this->resource->options,
            [
                'attribute' => $this->resource->id,
                'validationKey' => $this->resource->id,
                'value' => $this->resource->getValue(),
                'uniqueKey' => $this->resource->id . '-config-field',
            ]
        );
    }

    /**
     * @return string Nova VueJS component name.
     */
    protected function detectFieldComponent(): string
    {
        switch ($this->resource->cast) {
            case 'bool':
            case 'boolean':
                return 'boolean-field';
        }

        if (! empty($this->resource->options['options'])) {
            return 'select-field';
        }

        return 'text-field';
    }
}
