<?php declare(strict_types=1);

namespace jhoopes\LaravelVueForms\App\Controllers\Api\Admin\FormConfiguration;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use jhoopes\LaravelVueForms\Actions\CreateOrUpdateFormField;
use jhoopes\LaravelVueForms\Actions\RemoveFormFieldFromForm;
use jhoopes\LaravelVueForms\Actions\UpdateFormFieldOrderOnFormConfiguration;
use jhoopes\LaravelVueForms\DTOs\FormUpdateOrCreateDTO;
use jhoopes\LaravelVueForms\DTOs\RemoveFormFieldFromFormDTO;
use jhoopes\LaravelVueForms\DTOs\SetFormConfigFieldOrderDTO;
use jhoopes\LaravelVueForms\Support\Facades\LaravelVueForms;
use jhoopes\LaravelVueForms\App\Controllers\Controller;

class FormConfigurationFormFieldController extends Controller
{
    public function __construct(
        public CreateOrUpdateFormField $createOrUpdateFormField,
        public UpdateFormFieldOrderOnFormConfiguration $updateFormFieldOrderOnFormConfiguration,
        public RemoveFormFieldFromForm $removeFormFieldFromForm
    ){}

    public function create(Request $request, $formConfigId)
    {
        $this->authorizeAdminRequest();

        if($request->has('existingFieldId')) {
            $formConfiguration = LaravelVueForms::model('form_configuration')
                ->newQuery()
                ->findOrFail($formConfigId);
            $request->validate([
                'existingFieldId' => [
                    'required',
                    'integer',
                    Rule::exists('form_fields', 'id'),
                    function($attribute, $value, $fail) use($formConfiguration) {
                        if(
                            $formConfiguration->fields()
                            ->where('form_fields.id', $value)
                            ->count() > 0
                        ) {
                            $fail('You have already added this field.  Please select another field to add');
                        }
                    }
                ]
            ]);

            $savedFormField = LaravelVueForms::model('form_field')
                ->newQuery()
                ->findOrFail($request->get('existingFieldId'));

            // touch the form field for update
            $savedFormField->touch();
        }else {
            $formConfiguration = null;
            if($request->has('formConfigurationId')) {
                $this->validate($request, LaravelVueForms::getDefaultFormSubmissionValidationRules());
                $formConfiguration = $request->get('formConfigurationId');
                $data = $request->get('data');
            } else {

                $this->validate(
                    $request,
                    LaravelVueForms::getDefaultFormConfigurationValidationRule($formConfiguration, 'form_field_form')
                );
                $data = $request->all();
            }

            $savedFormField = $this->createOrUpdateFormField->execute(FormUpdateOrCreateDTO::fromFormApi(
                $formConfiguration,
                $data
            ));
        }

        $formConfigToAddField = LaravelVueForms::model('form_configuration')->findOrFail($formConfigId);
        $this->updateFormFieldOrderOnFormConfiguration->execute(
            SetFormConfigFieldOrderDTO::fromNewField(
                $formConfigToAddField,
                $savedFormField,
                $request->get('data.order', ($formConfigToAddField->fields()->count() + 1))
            )
        );

        return $this->resourceResponse($savedFormField);
    }

    public function update(Request $request, $formConfigId)
    {
        $this->authorizeAdminRequest();
        $formConfiguration = null;
        if($request->has('formConfigurationId')) {
            $this->validate($request, LaravelVueForms::getDefaultFormSubmissionValidationRules(true));
            $formConfiguration = $request->get('formConfigurationId');
            $data = $request->get('data');
        } else {

            $this->validate(
                $request,
                LaravelVueForms::getDefaultFormConfigurationValidationRule($formConfiguration, 'form_field_form')
            );
            $data = $request->all();
        }

        $savedFormField = $this->createOrUpdateFormField->execute(FormUpdateOrCreateDTO::fromFormApi(
            $formConfiguration,
            $data,
            $request->get('entityId'),
        ));

        return $this->resourceResponse($savedFormField);
    }

    public function delete($formConfigurationId, $formFieldId)
    {
        $this->authorizeAdminRequest();

        $this->removeFormFieldFromForm->execute(RemoveFormFieldFromFormDTO::fromFormAdminRequest(
            (int)$formConfigurationId,
            (int)$formFieldId
        ));

        return $this->infoResponse([
            'success' => true,
            'message' => 'Successfully removed field from form'
        ]);
    }

}
