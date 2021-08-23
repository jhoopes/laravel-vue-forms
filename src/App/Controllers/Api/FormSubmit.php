<?php declare(strict_types=1);

namespace jhoopes\LaravelVueForms\App\Controllers\Api;

use jhoopes\LaravelVueForms\Actions\ProcessFormUpdateOrCreate;
use jhoopes\LaravelVueForms\DTOs\FormUpdateOrCreateDTO;
use jhoopes\LaravelVueForms\Support\Form;
use jhoopes\LaravelVueForms\Support\Validation;
use jhoopes\LaravelVueForms\App\Controllers\Controller;
use Illuminate\Http\Request;

class FormSubmit extends Controller
{

    public function __construct(
        public ProcessFormUpdateOrCreate $processFormUpdateOrCreate
    ) {}

    public function updateOrCreate(Request $request)
    {
        $rules = [
            'formConfigurationId' => 'required|integer',
            'data'                => 'required|array'
        ];

        if($request->method() === 'PATCH') {
            $rules['entityId'] = 'required|integer';
        }
        $this->validate($request, $rules);

        $resourceDTO = $this->processFormUpdateOrCreate->execute(
            FormUpdateOrCreateDTO::fromFormApi(
                formConfigurationId: $request->get('formConfigurationId'),
                data: $request->get('data'),
                entityId: $request->get('entityId'),
            )
        );

        return $this->resourceResponse($resourceDTO->entity);
    }

}
