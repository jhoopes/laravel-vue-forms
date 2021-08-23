<?php declare(strict_types=1);

namespace jhoopes\LaravelVueForms\App\Http\Responses;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Neomerx\JsonApi\Schema\Error;
use Neomerx\JsonApi\Encoder\Encoder;
use Neomerx\JsonApi\Http\Headers\MediaType;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

class ErrorResponse
{
    /** @var \Exception  */
    protected $exception;

    /** @var Request  */
    protected $request;

    public function __construct($exception, Request $request)
    {
        $this->exception = $exception;
        $this->request = $request;
    }

    public function toResponse($request)
    {
        $hasMeta = false;
        $meta = [];
        if (config('app.debug')) {
            $hasMeta = true;
            $meta = [
                'trace' => $this->exception->getTrace(),
            ];
        }

        if ($this->exception instanceof ValidationException) {
            $errors = [];
            foreach ($this->exception->errors() as $field => $error) {
                $errors [] = new Error(
                    (string) Str::uuid(),
                    null,
                    null,
                    (string) $this->getStatus(),
                    $this->getCode(),
                    $this->getTitle(),
                    $this->getDetail(),
                    [
                        'field' => $field,
                        'messages' => $error
                    ],
                    $hasMeta,
                    $meta
                );
            }

            $response = Encoder::instance()->encodeErrors($errors);
        } else {
            $error = new Error(
                (string)Str::uuid(),
                null,
                null,
                (string) $this->getStatus(),
                $this->getCode(),
                $this->getTitle(),
                $this->getDetail(),
                $this->getSource(),
                $hasMeta,
                $meta
            );

            $response = Encoder::instance()->encodeError($error);
        }

        return response($response, $this->getStatus())
            ->withHeaders([
                'Content-Type' => MediaType::JSON_API_MEDIA_TYPE
            ]);
    }

    public function getStatus()
    {
        if (method_exists($this->exception, 'getStatusCode')) {
            return $this->exception->getStatusCode();
        }

        if ($this->exception instanceof HttpResponseException) {
            return $this->exception->getResponse()->getStatusCode();
        } elseif ($this->exception instanceof AuthenticationException) {
            return Response::HTTP_UNAUTHORIZED;
        } elseif ($this->exception instanceof AuthorizationException) {
            return Response::HTTP_FORBIDDEN;
        } elseif ($this->exception instanceof ValidationException) {
            return $this->exception->status;
        }

        return Response::HTTP_INTERNAL_SERVER_ERROR;
    }

    public function getCode()
    {
        return $this->exception->getCode();
    }

    public function getTitle()
    {
        $title = $this->exception->getMessage();
        if ($this->exception instanceof HttpResponseException) {
            $title = Response::$statusTexts[$this->exception->getResponse()->getStatusCode()] || 'unknown';
        } elseif ($this->exception instanceof AuthenticationException) {
            $title = 'Unauthenticated';
        } elseif ($this->exception instanceof AuthorizationException) {
            return 'Unauthorized';
        } elseif ($this->exception instanceof ValidationException) {
            $title = 'Unprocessable Entity';
        } elseif ($this->exception instanceof RouteNotFoundException) {
            $title = 'Route Not Found';
        }

        return $title;
    }

    public function getDetail()
    {
        $detail = '';
        if ($this->exception instanceof HttpResponseException) {
            $detail = $this->exception->getMessage();
        } elseif ($this->exception instanceof AuthenticationException) {
            $detail = '';
        } elseif ($this->exception instanceof AuthorizationException) {
            return '';
        } elseif ($this->exception instanceof ValidationException) {
            $detail = 'The document was well-formed but contains semantic errors.';
        }

        return $detail;
    }

    public function getSource()
    {
        $source = [];
        if ($this->exception instanceof AuthenticationException) {
            $source = [];
        } elseif ($this->exception instanceof ValidationException) {
            $source = [
                'message' => $this->exception->getMessage(),
                'errors' => $this->exception->errors(),
            ];
        }

        return $source;
    }
}
