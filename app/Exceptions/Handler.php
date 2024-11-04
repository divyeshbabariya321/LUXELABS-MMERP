<?php

namespace App\Exceptions;

use App\Email;
use App\EmailLog;
use App\Exceptions;
use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Symfony\Component\ErrorHandler\Error\FatalError;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Throwable;
use Webklex\IMAP\Exceptions\ConnectionFailedException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @return void
     */
    /**
     * Render an exception into an HTTP response.
     *
     * @param  Request  $request
     */
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof FatalError) {
            return $this->jsonErrorResponse('Please check Fatal Error', $exception, 500);
        }

        if ($exception instanceof UnauthorizedException) {
            return $this->jsonErrorResponse('User does not have permission for this page access.', $exception, 403);
        }

        if ($exception instanceof MethodNotAllowedHttpException) {
            return $this->jsonErrorResponse('Method not allowed', $exception, 405);

        }

        if ($exception instanceof \UnexpectedValueException) {
            Log::error($exception);

            return $this->jsonErrorResponse('File permission issue on the folder', $exception, 405);
        }

        // Handle email-related exceptions
        if ($exception instanceof ConnectionFailedException) {
            $this->handleEmailException($request->route('id'), 'IMAP', 'Imap Connection Issue', $exception);

            return $this->jsonErrorResponse('Imap Connection Issue', $exception, 405);
        }

        if ($exception instanceof \Swift_RfcComplianceException) {
            $this->handleEmailException($request->reply_email_id, 'SMTP', 'Mail Compliance Issue', $exception);

            return $this->jsonErrorResponse('Mail Compliance Issue', $exception, 405);
        }

        if ($exception instanceof \Swift_TransportException) {
            $replymail_id = $this->getReplyMailId($request);
            $this->handleEmailException($replymail_id, 'SMTP', 'Mail Transport Issue', $exception);

            return $this->jsonErrorResponse('Mail Transport Issue', $exception, 405);
        }

        if (str_contains($exception->getMessage(), 'Failed to authenticate on SMTP server')) {
            $this->handleEmailException($request->forward_email_id, 'SMTP', 'Mail Transport Issue', $exception);

            return $this->jsonErrorResponse('Mail Compliance Issue', $exception, 405);
        }

        return parent::render($request, $exception);
    }

    private function jsonErrorResponse($message, Throwable $exception, $statusCode = 400)
    {
        Log::error($exception);

        return response()->json([
            'status' => 'failed',
            'message' => "{$message} => ".$exception->getMessage(),
            'code' => $exception->getCode(),
        ], $statusCode);
    }

    private function handleEmailException($emailId, $serviceType, $logMessage, Throwable $exception)
    {
        $email = Email::find($emailId);

        if ($email) {
            EmailLog::create([
                'email_id' => $email->id,
                'email_log' => 'Error in Sending Email',
                'message' => "{$logMessage} => ".$exception->getMessage(),
                'is_error' => 1,
                'service_type' => $serviceType,
            ]);

            $email->error_message = "{$logMessage} => ".$exception->getMessage();
            $email->save();
        }
    }

    private function getReplyMailId($request)
    {
        $replymail_id = $request->reply_email_id;
        if (! is_numeric($replymail_id)) {
            $replymail_id = request()->segment(count(request()->segments()));
        }

        return $replymail_id;
    }
}
