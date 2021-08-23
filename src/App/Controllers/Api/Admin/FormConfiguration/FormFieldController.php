<?php declare(strict_types=1);

namespace jhoopes\LaravelVueForms\App\Controllers\Api\Admin\FormConfiguration;

use Illuminate\Http\Request;
use jhoopes\LaravelVueForms\Actions\DeleteFormField;
use jhoopes\LaravelVueForms\DTOs\DeleteFormFieldDTO;
use jhoopes\LaravelVueForms\Support\Facades\LaravelVueForms;
use jhoopes\LaravelVueForms\App\Controllers\Controller;

class FormFieldController extends Controller
{

    public function __construct(
        public DeleteFormField $deleteFormField
    ) {}

    public function index(Request $request)
    {
        $this->authorizeAdminRequest();
        $request->validate([
            'q' => [
                'nullable',
                'string'
            ]
        ]);

        $query = LaravelVueForms::model('form_field')->newQuery();
        $q = $request->get('q');
        if(!empty($q)) {
            $query->where('name', 'like', '%' . $q . '%')
                ->orWhere('value_field', 'like', '%' . $q . '%')
                ->orWhere('label', 'like', '%' . $q . '%');
        }

        return $this->collectedResponse($query->get());
    }


    public function delete($formFieldId)
    {
        $this->authorizeAdminRequest();
        $DTO = DeleteFormFieldDTO::fromRequest((int) $formFieldId);
        $this->deleteFormField->execute($DTO);

        return $this->infoResponse([
            'success' => true,
            'message' => 'Successfully deleted form field: ' . $DTO->formField->name
        ]);
    }


}
