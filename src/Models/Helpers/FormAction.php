<?php declare(strict_types=1);

namespace jhoopes\LaravelVueForms\Models\Helpers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use jhoopes\LaravelVueForms\Models\FormConfiguration;
use jhoopes\LaravelVueForms\Support\Facades\LaravelVueForms;

trait FormAction
{

    public function persistEntity(
        Model $entityModel,
        FormConfiguration $formConfiguration,
        array $validData,
        array $with = []
    ): Model {

        $fields = $this
            ->unFlattenFields(
                $this->getNonEAVValueFields($formConfiguration)
            );

        $this
            ->setImplicitFieldsOnModel($entityModel, $fields, $validData);

        // save the entity model to ensure it exists for related fields, and EAV fields
        $entityModel->save();

        if($this->getNonRelatedEAVFields($formConfiguration)->count() > 0) {
            $this->saveEAVFields(
                $entityModel,
                $validData,
                $this->getNonRelatedEAVFields($formConfiguration)
            );
        }

        $this->setRelatedFieldsOnModel($entityModel, $fields, $validData, $formConfiguration);

        return $entityModel->fresh($with);
    }



    /**
     * Get the value fields of NonEAV Fields for un-flattening them to set up saving structure
     *
     * @param FormConfiguration $formConfig
     * @return Collection
     */
    public function getNonEAVValueFields(FormConfiguration $formConfig) : Collection
    {
        return $formConfig->fields
            ->where('is_eav', 0)
            ->whereNotIn('widget', ['files'])
            ->pluck('value_field');
    }

    /**
     * Get NonRelated form fields that have a widget type of files
     *
     * @param FormConfiguration $formConfig
     * @return Collection
     */
    public function getNonRelatedFilesFields(FormConfiguration $formConfig): Collection
    {
        return $formConfig->fields->where('widget', 'files');
    }

    /**
     * Get fields that are EAV fields, but not related to any specific sub-model
     *
     * @param FormConfiguration $formConfig
     * @return Collection
     */
    public function getNonRelatedEAVFields(FormConfiguration $formConfig) : Collection
    {
        return $formConfig->fields->filter(function($field) {
            return !Str::contains($field->value_field, '.');
        })->where('is_eav', 1);
    }

    /**
     * Get the EAV fields for a related model/sub-model by the relationship name
     *
     * @param $relationship
     * @param FormConfiguration $formConfig
     * @return Collection
     */
    public function getRelatedEAVFields($relationship, FormConfiguration $formConfig) : Collection
    {
        return $formConfig->fields->filter(function($field) use($relationship) {
            return Str::contains($field->value_field, $relationship . '.');
        })->where('is_eav', 1);
    }

    /**
     * Set the implicit fields on the base model
     *
     * @param Model $model
     * @param Collection $fields
     * @param array $data
     */
    public function setImplicitFieldsOnModel(Model $model, Collection $fields, array $data)
    {
        $attributes = [];

        foreach($fields as $fieldKey => $field) {
            if(!is_array($field) && Arr::has($data, $field)) {
                $attributes[$field] = Arr::get($data, $field);
            }
        }
        $model->fill($attributes);
    }

    /**
     * @param Model $model
     * @param array $data
     * @param Collection|null $fields Default is to use non related EAV fields
     * @throws \InvalidArgumentException
     */
    public function saveEAVFields(Model $model, array $data, Collection|null$fields)
    {
        $traits = LaravelVueForms::class_uses_deep($model);
        if(!in_array(HasCustomAttributes::class, $traits)) {
            throw new \InvalidArgumentException('Invalid Model for EAV');
        }

        foreach($fields as $field) {
            if($field->widget === 'files') {
                continue;
            }
            $model->setEAVValue(
                $field,
                Arr::get($data, $field->value_field)
            );
        }
    }


    /**
     * Set related records fields on the base implicit model
     *
     * This function only works with 1 level deep of setting related models and fields
     *
     * E.G:
     * address.address
     * address.city
     * address.state
     * ---- or ---- (with updates in the "TODO" section in unflattening fields)
     * addresses.*.address
     * addresses.*.city
     * addresses.*.state
     *
     * @param $model
     * @param $fields
     * @param $data
     */
    public function setRelatedFieldsOnModel($model, $fields, $data, $formConfig)
    {
        foreach($fields as $relationship => $field) {

            if(is_array($field)) {

                if($field['many'] === false) {
                    // meaning one to one or has one
                    $attributes = [];

                    foreach($field['fields'] as $relatedField) {
                        if(Arr::has($data, $relationship . '.' . $relatedField)) {
                            $attributeValue = Arr::get($data, $relationship . '.' . $relatedField);
                            $attributes[$relatedField] = $attributeValue;
                        }
                    }

                    if($model->$relationship !== null) {
                        $relatedModel = $model->$relationship;
                        $relatedModel->fill($attributes)->save();
                    }else {
                        $relatedModel = $model->$relationship()->create($attributes);
                    }

                    $eavFields = $this->getRelatedEAVFields($relationship, $formConfig);
                    if($eavFields->count() > 0) {
                        $this->saveEAVFields($relatedModel, $data, $eavFields);
                    }

                }
            }
        }
    }


    /**
     * Un-flatten the field list for array/object accessors
     *
     * E.g.
     *
     * [
     *      'name', 'email', 'address.address', 'address.city', 'address.state'
     * ]
     *
     * becomes
     *
     * [
     *      'name',
     *      'email',
     *      'address' => [
     *          'many' => false,
     *          'fields' => [
     *              'address',
     *              'city',
     *              'state'
     *      ]
     * ]
     *
     * @param \Illuminate\Support\Collection $fields
     * @return Collection
     */
    public function unFlattenFields(\Illuminate\Support\Collection $fields) : Collection
    {

        $unFlattened = collect([]);
        foreach($fields as $field) {

            if(Str::contains($field, '.')) {


                $keys = explode('.', $field);
                $firstRelationship = array_shift($keys);

                if($firstRelationship === '*') {

                    // TODO: Need to find a good way to support wildcards ( one to many relationships)

                }else {

                    $newFields = $fields->filter(function($field) use($firstRelationship) {
                        return Str::startsWith($field, $firstRelationship);
                    })->map(function($field) use($firstRelationship) {
                        return str_replace($firstRelationship . '.', '', $field);
                    });

                    $unFlattened[$firstRelationship] = [
                        'many' => false,
                        'fields' => $this->unFlattenFields($newFields)
                    ];
                }
            } else {
                $unFlattened[] = $field;
            }
        }

        return $unFlattened;
    }

}
