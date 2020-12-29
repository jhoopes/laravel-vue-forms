<?php

namespace jhoopes\LaravelVueForms\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use jhoopes\LaravelVueForms\Facades\LaravelVueForms;
use jhoopes\LaravelVueForms\Http\Controllers\Controller;

class FormConfigurationFormFieldOrderController extends Controller
{


    public function update(Request $request, $formConfigurationId)
    {
        $this->authorizeAdminRequest();

        $formConfiguration = LaravelVueForms::model('form_configuration')
            ->findOrFail($formConfigurationId);

        $request->validate([
            'formConfigurationFieldOrder' =>[
                'required',
                'array',
            ],
            'formConfigurationFieldOrder.*' => [
                'array',
            ]
        ]);

        //dd($request->get('formConfigurationFieldOrder'));

        $this->updateOrderAndSetParents(
            $formConfiguration,
            $request->get('formConfigurationFieldOrder'),
            1
        );

        return $this->collectedResponse($formConfiguration->fields()->get());
    }


    /**
     * Recursive function for setting
     *
     * @param $formConfiguration
     * @param $currentOrderSet
     * @param $currentOrder
     * @param null $parent
     * @return int
     */
    protected function updateOrderAndSetParents($formConfiguration, $currentOrderSet, $currentOrder, $parent = null): int
    {
        foreach($currentOrderSet as $currentOrderItem) {
            if(empty($currentOrderItem['id'])) {
                continue; // trying to save new item that doesn't exist yet
            }

            $fieldForItem = $formConfiguration->fields->firstWhere('id', $currentOrderItem['id']);
            if($parent !== null) {
                $fieldForItem->parent_id = $parent->id;
                $fieldForItem->save();
            }

            $formConfiguration->fields()->updateExistingPivot($fieldForItem->id, [
                'order' => $currentOrder
            ]);

            $currentOrder++;
            if(isset($currentOrderItem['children']) && is_array($currentOrderItem['children'])) {
                $currentOrder = $this->updateOrderAndSetParents($formConfiguration, $currentOrderItem['children'], $currentOrder, $fieldForItem);
            }
        }

        return $currentOrder;
    }

}
