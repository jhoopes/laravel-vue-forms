<?php declare(strict_types=1);

namespace jhoopes\LaravelVueforms\Actions;

use Illuminate\Support\Arr;
use jhoopes\LaravelVueForms\DTOs\CastFormFieldValuesDTO;

class CastFormFieldValues
{
    public function execute(CastFormFieldValuesDTO $fieldValuesDTO): array
    {
        return $fieldValuesDTO->data->map(function($dataValue, $dataValueField) use($fieldValuesDTO) {

            if(
                !$formField = $fieldValuesDTO
                    ->formFields
                    ->firstWhere('value_field', $dataValueField)
            ) {
                return $dataValue;
            }

            if(
                is_string($dataValue) &&
                Arr::get($formField->field_extra, 'base64_encode', false)
            ) {
                $dataValue = base64_decode($dataValue);
            }

            if(
                is_string($dataValue) &&
                $formField->cast_to === 'array'
            ) {
                $dataValue = json_decode(
                    $dataValue,
                    true,
                    512,
                    JSON_THROW_ON_ERROR
                );
            }

            return $dataValue;
        })->toArray();
    }
}
