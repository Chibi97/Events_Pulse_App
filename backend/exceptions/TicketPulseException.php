<?php

namespace app\exceptions;

use yii\base\Exception;
use Throwable;

class TicketPulseException extends Exception
{
    /**
     * @var array|null Additional context or details about the error.
     */
    private ?array $context;

    /**
     * TicketPulseException constructor.
     *
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     * @param array|null $context
     */
    public function __construct(
        string $message = '',
        int $code = 0,
        ?Throwable $previous = null,
        ?array $context = null
    ) {
        $this->context = $context;
        parent::__construct($message, $code, $previous);
    }

    /**
     * Get the additional context or details about the error
     *
     * @return array|null
     */
    public function getContext(): ?array
    {
        return $this->context;
    }

    /**
     * Get a string representation of the exception
     *
     * @return string
     */
    public function __toString(): string
    {
        $baseMessage = parent::__toString();
        if ($this->context) {
            $contextMessage = 'Context: ' . print_r($this->context, true);
            return $baseMessage . "\n" . $contextMessage;
        }

        return $baseMessage;
    }

    /**
     * Get a structured response for the exception
     *
     * @return array
     */
    public function getResponse(): array
    {
        return [
            'code' => $this->getCode(),
            'message' => $this->getMessage(),
            'type' => get_class($this),
        ];
    }
}
