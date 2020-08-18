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
 * @property \Illuminatech\Config\Item $resource
 */
class ConfigItemResource extends JsonResource
{
    /**
     * {@inheritdoc}
     */
    public function toArray($request): array
    {
        return array_merge([
            'attribute' => $this->resource->id,
            'value' => $this->resource->getValue(),
            'component' => $this->detectFieldComponent(),
            'name' => $this->resource->label,
            'helpText' => $this->resource->hint,
            'nullable' => in_array('nullable', $this->resource->rules),
            'readonly' => false,
            'required' => in_array('required', $this->resource->rules),
            'textAlign' => 'left',
            'validationKey' => $this->resource->id,
        ], (array) $this->resource->options);
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
