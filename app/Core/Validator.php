<?php

namespace App\Core;

/**
 * Lightweight form validator inspired by Laravel-style rules.
 */
class Validator
{
    protected $data = [];       // Input data (e.g. $_POST)
    protected $rules = [];      // Validation rules
    protected $errors = [];     // Collected error messages

    /**
     * Constructor to set data and rules, and run validation.
     */
    public function __construct(array $data, array $rules)
    {
        $this->data = $data;
        $this->rules = $rules;

        $this->validate();
    }

    /**
     * Runs through each rule and checks the data.
     */
    protected function validate(): void
    {
        foreach ($this->rules as $field => $ruleString) {
            $rules = explode('|', $ruleString);
            $value = trim($this->data[$field] ?? '');

            foreach ($rules as $rule) {
                // Required check
                if ($rule === 'required' && $value === '') {
                    $this->addError($field, 'is required.');
                }

                // Email format check
                if ($rule === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($field, 'must be a valid email address.');
                }

                // Minimum length check
                if (str_starts_with($rule, 'min:')) {
                    $min = (int) explode(':', $rule)[1];
                    if (strlen($value) < $min) {
                        $this->addError($field, "must be at least $min characters.");
                    }
                }

                // Maximum length check
                if (str_starts_with($rule, 'max:')) {
                    $max = (int) explode(':', $rule)[1];
                    if (strlen($value) > $max) {
                        $this->addError($field, "must not exceed $max characters.");
                    }
                }
            }
        }
    }

    /**
     * Add error message to error list.
     */
    protected function addError(string $field, string $message): void
    {
        $this->errors[$field][] = ucfirst($field) . ' ' . $message;
    }

    /**
     * Returns true if validation has failed.
     */
    public function fails(): bool
    {
        return !empty($this->errors);
    }

    /**
     * Returns all error messages.
     */
    public function errors(): array
    {
        return $this->errors;
    }

    /**
     * Get first error message for a specific field.
     */
    public function first(string $field): ?string
    {
        return $this->errors[$field][0] ?? null;
    }

    /**
     * Static entry point for convenience: Validator::make($data, $rules)
     */
    public static function make(array $data, array $rules): self
    {
        return new self($data, $rules);
    }
}
