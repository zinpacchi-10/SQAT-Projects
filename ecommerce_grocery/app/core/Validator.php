<?php
/**
 * Validator
 * A tiny helper for consistent, descriptive server-side validation
 * error messages across every form in the app.
 */
class Validator
{
    private array $errors = [];

    public function required($value, string $field): self
    {
        if ($value === null || trim((string)$value) === '') {
            $this->errors[] = "{$field} is required.";
        }
        return $this;
    }

    public function email($value, string $field = 'Email'): self
    {
        if ($value && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[] = "{$field} must be a valid email address.";
        }
        return $this;
    }

    public function minLength($value, int $len, string $field): self
    {
        if ($value && strlen((string)$value) < $len) {
            $this->errors[] = "{$field} must be at least {$len} characters.";
        }
        return $this;
    }

    public function numeric($value, string $field): self
    {
        if ($value !== null && $value !== '' && !is_numeric($value)) {
            $this->errors[] = "{$field} must be a number.";
        }
        return $this;
    }

    public function min($value, float $min, string $field): self
    {
        if ($value !== null && $value !== '' && (float)$value < $min) {
            $this->errors[] = "{$field} must be at least {$min}.";
        }
        return $this;
    }

    public function inArray($value, array $allowed, string $field): self
    {
        if ($value !== null && $value !== '' && !in_array($value, $allowed, true)) {
            $this->errors[] = "{$field} is invalid.";
        }
        return $this;
    }

    public function date($value, string $field): self
    {
        if ($value && !strtotime($value)) {
            $this->errors[] = "{$field} must be a valid date.";
        }
        return $this;
    }

    public function fails(): bool
    {
        return count($this->errors) > 0;
    }

    public function errors(): array
    {
        return $this->errors;
    }
}
