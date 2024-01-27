<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class APIResponse
{
    /**
     * Return a server error response.
     *
     * @param  mixed|null   $details  Optional error details.
     * @param  string|null  $message  Optional error message.
     *
     * @return JsonResponse Server error JSON response.
     */
    public static function responseServerError(mixed $details = null, ?string $message = null): JsonResponse
    {
        return self::APIError(Response::HTTP_INTERNAL_SERVER_ERROR, $message, $details);
    }

    /**
     * Return a custom error response.
     *
     * @param  mixed  $title       Error title.
     * @param  mixed  $details     Error details.
     * @param  int    $statusCode  HTTP status code.
     *
     * @return JsonResponse Custom error JSON response.
     */
    public static function responseWithCustomError(mixed $title, mixed $details, int $statusCode): JsonResponse
    {
        return self::APIError($statusCode, $title, $details);
    }

    /**
     * Return an unprocessable entity error response.
     *
     * @param  mixed|null   $details  Optional error details.
     * @param  string|null  $message  Optional error message.
     *
     * @return JsonResponse Unprocessable entity JSON response.
     */
    public static function responseUnprocessable(mixed $details = null, ?string $message = null): JsonResponse
    {
        return self::APIError(Response::HTTP_UNPROCESSABLE_ENTITY, $message, $details);
    }

    /**
     * Return a bad request error response.
     *
     * @param  mixed|null   $details  Optional error details.
     * @param  string|null  $message  Optional error message.
     *
     * @return JsonResponse Bad request JSON response.
     */
    public static function responseBadRequest(mixed $details = null, ?string $message = null): JsonResponse
    {
        return self::APIError(Response::HTTP_BAD_REQUEST, $message, $details);
    }

    /**
     * Return a not found error response.
     *
     * @param  mixed|null   $details  Optional error details.
     * @param  string|null  $message  Optional error message.
     *
     * @return JsonResponse Not found JSON response.
     */
    public static function responseNotFound(mixed $details = null, ?string $message = 'Record not found!'): JsonResponse
    {
        return self::APIError(Response::HTTP_NOT_FOUND, $message, $details);
    }

    /**
     * Return an unauthorized error response.
     *
     * @param  string  $details  Optional error details.
     * @param  string  $message  Optional error message.
     *
     * @return JsonResponse Unauthorized JSON response.
     */
    public static function responseUnAuthorized(
        string $details = 'you are not authorized to perform this action',
        string $message = 'Unauthorized!'
    ): JsonResponse
    {
        return self::APIError(Response::HTTP_FORBIDDEN, $message, $details);
    }

    /**
     * Return an unauthenticated error response.
     *
     * @param  string  $details  Optional error details.
     * @param  string  $message  Optional error message.
     *
     * @return JsonResponse Unauthenticated JSON response.
     */
    public static function responseUnAuthenticated(
        string $details = 'you are not authenticated to perform this action',
        string $message = 'Unauthenticated!'
    ): JsonResponse
    {
        return self::APIError(Response::HTTP_UNAUTHORIZED, $message, $details);
    }

    /**
     * Return a conflict error response.
     *
     * @param  string  $details  Optional error details.
     * @param  string  $message  Optional error message.
     *
     * @return JsonResponse Conflict error JSON response.
     */
    public static function responseConflictError(
        string $details = 'conflict',
        string $message = 'Conflict!'
    ): JsonResponse
    {
        return self::APIError(Response::HTTP_CONFLICT, $message, $details);
    }

    /**
     * Return a success response.
     *
     * @param  string|null  $message  Optional success message.
     * @param  mixed|null   $data     Optional data to include in the response.
     *
     * @return JsonResponse Success JSON response.
     */
    public static function responseSuccess(?string $message = null, mixed $data = null): JsonResponse
    {
        return new JsonResponse([
            'status' => Response::HTTP_OK,
            'message' => $message,
            'data' => $data,
        ], Response::HTTP_OK);
    }

    /**
     * Return a created response.
     *
     * @param  string|null  $message  Optional created message.
     * @param  mixed|null   $data     Optional data to include in the response.
     *
     * @return JsonResponse Created JSON response.
     */
    public static function responseCreated(?string $message = 'Record created successfully', mixed $data = null): JsonResponse
    {
        return new JsonResponse([
            'status' => Response::HTTP_CREATED,
            'message' => $message,
            'data' => $data,
        ], Response::HTTP_CREATED);
    }

    /**
     * Return a deleted response.
     *
     * @return JsonResponse Deleted JSON response.
     */
    public static function responseDeleted(): JsonResponse
    {
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Create a JSON response for validation errors.
     *
     * @param  ValidationException  $exception  The validation exception.
     *
     * @return JsonResponse A JSON response containing validation error information.
     */
    public static function ResponseValidationError(ValidationException $exception): JsonResponse
    {
        // Extract validation errors and format them into an array.
        $errors = collect($exception->validator->errors())->map(function ($error, $key) {
            return [
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'title' => 'Validation Error',
                'detail' => $error[0],
                'source' => [
                    'pointer' => '/' . str_replace('.', '/', $key),
                ],
            ];
        })->values();

        // Create the JSON response with the formatted errors.
        $responseData = [
            'errors' => $errors,
        ];

        // Set the Content-Type header to specify JSON problem format.
        $headers = [
            'Content-Type' => 'application/problem+json',
        ];

        return new JsonResponse($responseData, Response::HTTP_UNPROCESSABLE_ENTITY, $headers);
    }

    /**
     * Create a JSON response for API errors.
     *
     * @param  int          $code     The HTTP status code for the error.
     * @param  string|null  $title    A brief error description (default: generic message).
     * @param  mixed|null   $details  Additional details about the error (default: null).
     *
     * @return JsonResponse A JSON response containing the error information.
     */
    private static function APIError(int $code, ?string $title, mixed $details = null): JsonResponse
    {
        // If no title is provided, use a generic error message.
        $formattedTitle = $title ?? 'Oops. Something went wrong. Please try again or contact support';

        // Create the JSON response with error information.
        $responseData = [
            // 'errors' => [
            //     [
            //         'status' => $code,
            //         'title' => $formattedTitle,Z
            //         'detail' => $details,
            //     ],
            // ],
            'errors' => [
                'message' => $formattedTitle,
            ]
        ];

        // Set the Content-Type header to specify JSON problem format.
        $headers = [
            'Content-Type' => 'application/problem+json',
        ];

        return new JsonResponse($responseData, $code, $headers);
    }
}
