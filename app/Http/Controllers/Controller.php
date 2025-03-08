<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Send a success response.
     *
     * @param mixed $data
     * @param string $message
     * @param int $code
     * @return JsonResponse
     */
    protected function sendSuccessResponse($data, $message = 'Success', $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    /**
     * Send an error response.
     *
     * @param string $message
     * @param int $code
     * @param array $errors
     * @return JsonResponse
     */
    protected function sendErrorResponse($message, $code = 400, $errors = []): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $code);
    }

    /**
     * Validate data based on key name and validation rules.
     *
     * @param Request $request
     * @param array $rules
     * @return JsonResponse|null
     */
    protected function validateData(Request $request, array $rules): ?JsonResponse
    {
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->sendErrorResponse('Validation Error', 422, $validator->errors()->all());
        }

        return null;
    }

    protected function uploadImage(Request $request, string $fileKey, string $folder)
    {
        if (!$request->hasFile($fileKey)) {
            return $this->sendErrorResponse('No file provided', 400);
        }

        $file = $request->file($fileKey);

        if (!$file->isValid()) {
            return $this->sendErrorResponse('Invalid file upload', 400);
        }

        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = public_path($folder);
        $file->move($filePath, $fileName);

        $url = url($folder . '/' . $fileName);

        return $url;
    }
}
