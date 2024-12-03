<?php

namespace App\Services\Responses;

use App\Enum\ResponseStatusEnum;
use Exception;
use Illuminate\Http\Request;

class ExceptionHandlerService
{
    /**
     * Handle exceptions and format the response.
     *
     * @param Exception $exception
     * @param string|null $message
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function handler(Exception $exception, $message = null, Request $request)
    {
        // Determine the status code
        $exceptionCode = $this->getExceptionCode($exception);

        // Build the response data
        $data = $this->buildResponseData($exception, $message);

        // Return JSON if the request expects it
        if ($request->expectsJson())
        {
            return $this->toJson($data, $exceptionCode);
        }

        // Handle non-JSON responses (e.g., HTML)
        return $this->toHTML($data, $exceptionCode);
    }

    /**
     * Determine the appropriate exception code.
     *
     * @param Exception $exception
     * @return int
     */
    private function getExceptionCode(Exception $exception)
    {
        return $exception->getCode() == 0 || is_string($exception->getCode())
            ? 500
            : $exception->getCode();
    }

    /**
     * Build the response data structure.
     *
     * @param Exception $exception
     * @param string|null $message
     * @return array
     */
    private function buildResponseData(Exception $exception, $message = null)
    {
        $data = [
            'status' => ResponseStatusEnum::FAILED,
            'message' => $message ?? "We're having trouble processing your request. Please retry shortly.",
            'error' => $exception->getMessage(),
        ];

        if (config('app.debug'))
        {
            $data['debug'] = [
                'trace' => $exception->getTrace(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ];
        }

        return $data;
    }

    /**
     * Return a JSON response.
     *
     * @param array $data
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    private function toJson(array $data, int $statusCode)
    {
        return response()->json($data, $statusCode);
    }

    /**
     * Return an HTML response for non-JSON requests.
     *
     * @param array $data
     * @param int $statusCode
     * @return \Illuminate\Http\Response
     */
    private function toHTML(array $data, int $statusCode)
    {
        $html = view('errors.generic', [
            'message' => $data['message'],
            'status' => $data['status'],
            'debug' => config('app.debug') ? $data['debug'] : null,
        ])->render();

        return response($html, $statusCode);
    }
}
