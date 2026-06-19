<?php

namespace EasyApiCore\Util;

use Symfony\Component\HttpFoundation\Response;

/**
 * A wrapper for holding data to be used for an application/problem+json response.
 *
 * @see https://tools.ietf.org/html/draft-nottingham-http-problem-06
 */
class ApiProblem
{
    public const PREFIX = 'core.error.';

    // region Error constants

    public const UNEXPECTED_ERROR = 'something.went.wrong';
    public const FORM_EXTRA_FIELDS_ERROR = 'form.extra_fields';

    public const INVALID_FORMAT = 'invalid.format.message';
    public const ROUTE_NOT_FOUND = 'route.not_found';
    public const ENTITY_NOT_FOUND = '%s.not_found';
    public const FORBIDDEN = 'forbidden';
    public const MAILING_ERROR = 'mailer.error';

    public const RESULT_ORDER_INCORRECT = 'order.incorrect_order';
    public const RESULT_SORT_MALFORMED = 'sort.malformed';

    public const PAGINATION_INCORRECT_PAGE_VALUE = 'pagination.incorrect_page_value';
    public const PAGINATION_INCORRECT_RESULT_PER_PAGE_VALUE = 'pagination.incorrect_results_per_page_value';

    public const ENTITY_FIELD_REQUIRED = '%s.%s.required';
    public const ENTITY_FIELD_INVALID = '%s.%s.invalid';
    public const ENTITY_FIELD_TOO_LONG = '%s.%s.too_long';

    public const UPLOAD_UNABLE_TO_WRITE_DIRECTORY = 'upload.unable.to.write.directory';

    // endregion

    // region JWT

    public const AUTHENTICATION_FAILURE = 'bad_credentials';
    public const RESTRICTED_ACCESS = 'restricted_access';
    public const JWT_INVALID = 'invalid_token';
    public const JWT_NOT_FOUND = 'missing_token';
    public const JWT_EXPIRED = 'token_expired';

    // endregion

    // region Users

    public const USER_USERNAME_ANONYMOUS_NOT_ALLOWED = 'user.username.anonymous_not_allowed';
    public const USER_USERNAME_ALREADY_EXISTS = 'user.username.already_exists';
    public const USER_EMAIL_ALREADY_EXISTS = 'user.email.already_exists';
    public const USER_EMAIL_MALFORMED = 'user.email.malformed';
    public const USER_PROFILE_INVALID_CIVILITY = 'user.profile.ref_civility.invalid';
    public const USER_USERNAME_INVALID = 'user.username.invalid';
    public const USER_RESPONSE_TYPE_INVALID = 'user.response_type.invalid';
    public const USER_CLIENT_INVALID = 'user.client.invalid';
    public const USER_ALLOWED_OR_REDIRECT_INVALID = 'user.allowed_or_redirect.invalid';
    public const USER_EMAIL_INVALID = 'user.email.invalid';
    public const USER_TOKEN_INVALID = 'user.token.invalid';
    public const USER_PASSWORD_INVALID = 'user.password.invalid';
    public const USER_PASSWORD_SAVE_FAILED = 'user.password.save.failed';

    // endregion

    /**
     * @var int
     */
    private $statusCode;

    /**
     * @var array
     */
    private $errors = [];

    /**
     * @var array
     */
    private $extraData = [];

    /**
     * ApiProblem constructor.
     *
     * @param string|array $errors
     */
    public function __construct(int $statusCode, $errors, bool $prefix = true)
    {
        $this->statusCode = $statusCode;
        if (!\is_array($errors)) {
            $errors = [$errors];
        }

        $this->normalizeErrors($statusCode, $errors, $prefix);
    }

    /**
     * Array representation.
     *
     * @return array
     */
    public function toArray()
    {
        return array_merge(
            $this->extraData,
            [
                'errors' => $this->errors,
            ]
        );
    }

    /**
     * Set some extra data.
     */
    public function set($name, $value)
    {
        $this->extraData[$name] = $value;
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Normalize error message.
     *
     * @return string
     */
    private function normalizeErrors(int $statusCode, array $errors, bool $prefix)
    {
        foreach ($errors as $error) {
            $this->errors[] = $this->normalizeError($statusCode, $error, $prefix);
        }
    }

    /**
     * Normalize error message.
     *
     * @return string
     */
    private function normalizeError(int $statusCode, string $type, bool $prefix)
    {
        // 400
        if (Response::HTTP_BAD_REQUEST === $statusCode) {
            if (preg_match('#^Invalid [a-zA-Z]+ message received$#', $type)) {
                $type = self::INVALID_FORMAT;
            } elseif (preg_match('#^Could not find any log entries under version#', $type)) {
                $type = self::INVALID_FORMAT;
            }
        // 401
        } elseif (Response::HTTP_UNAUTHORIZED === $statusCode) {
            if (preg_match('#^A Token was not found in the TokenStorage#', $type)) {
                $type = self::JWT_NOT_FOUND;
            }
        // 403
        } elseif (Response::HTTP_FORBIDDEN === $statusCode) {
            if (preg_match('#^Token does not have the required roles#', $type)
                || preg_match('#^Access Denied.#', $type)) {
                $type = self::RESTRICTED_ACCESS;
            }
        // 404
        } elseif (Response::HTTP_NOT_FOUND === $statusCode) {
            // Unknown entity ?
            if (preg_match('#^(.*\\\Entity\\\(.*)) object not found .*$#', $type, $matches)) {
                $type = mb_strtolower(
                    \sprintf(self::ENTITY_NOT_FOUND, self::normalizeClassName($matches[2]))
                );
            // Unknown route or resource
            } elseif (preg_match('#^No route found for#', $type)) {
                $type = self::ROUTE_NOT_FOUND;
            }
        // 405
        } elseif (Response::HTTP_METHOD_NOT_ALLOWED === $statusCode) {
            $type = self::ROUTE_NOT_FOUND; // Generic message :)
        }

        $this->statusCode = $statusCode;

        return $prefix ? self::PREFIX.$type : $type;
    }

    /**
     * Normalize class name for JSON
     * Ex : normalizeClassName("One\Two\ThreeFour") => "one.two.three_four".
     *
     * @return string
     */
    public static function normalizeClassName(string $className)
    {
        preg_match_all('#([A-Z\\\][A-Z0-9\\\]*(?=$|[A-Z\\\][a-z0-9\\\])|[A-Za-z\\\][a-z0-9\\\]+)#', $className, $matches);
        $ret = $matches[0];
        foreach ($ret as &$match) {
            $match = $match === mb_strtoupper($match) ? mb_strtolower($match) : lcfirst($match);
        }

        return preg_replace('#\\\_#', '.', implode('_', $ret));
    }
}
