<?php

declare(strict_types=1);

namespace App\View;

/** Validates untrusted, already-parsed HTML form fields. */
final class TaskFormValidator
{
    /**
     * New tasks always begin pending; the Day 29 create API has no status input.
     *
     * @param array<string, mixed> $fields
     */
    public function validateForCreate(array $fields): TaskFormData
    {
        $form = $this->validate($fields);

        if (
            $form->status === 'pending'
            || isset($form->errors['status'])
        ) {
            return $form;
        }

        $errors = $form->errors;
        $errors['status'] = 'New tasks must start as pending.';

        return new TaskFormData(
            $form->title,
            $form->description,
            $form->status,
            $errors,
        );
    }

    /**
     * @param array<string, mixed> $fields
     */
    public function validate(array $fields): TaskFormData
    {
        /** @var array<string, string> $errors */
        $errors = [];

        $title = $this->readText(
            $fields,
            'title',
            $errors,
            true,
        );

        $description = $this->readText(
            $fields,
            'description',
            $errors,
            false,
        );

        $status = $this->readText(
            $fields,
            'status',
            $errors,
            true,
        );

        if (! isset($errors['title'])) {
            $normalized = trim($title);

            if (
                str_contains($title, "\0")
                || preg_match('/\A.{1,200}\z/us', $normalized) !== 1
            ) {
                $errors['title'] = 'Title must contain 1 to 200 characters.';
            }
        }

        if (
            ! isset($errors['description'])
            && (
                strlen($description) > 65535
                || str_contains($description, "\0")
            )
        ) {
            $errors['description'] =
                'Description is too long or contains invalid characters.';
        }

        if (
            ! isset($errors['status'])
            && ! in_array(
                $status,
                ['pending', 'in_progress', 'completed'],
                true,
            )
        ) {
            $errors['status'] = 'Select a valid task status.';
        }

        return new TaskFormData(
            $title,
            $description,
            $status,
            $errors,
        );
    }

    /**
     * @param array<string, mixed> $fields
     * @param array<string, string> $errors
     */
    private function readText(
        array $fields,
        string $name,
        array &$errors,
        bool $required,
    ): string {
        if (! array_key_exists($name, $fields)) {
            if ($required) {
                $errors[$name] = 'This field is required.';
            }

            return '';
        }

        $value = $fields[$name];

        if (! is_string($value)) {
            $errors[$name] = 'This field must be text.';

            return '';
        }

        if (preg_match('//u', $value) !== 1) {
            $errors[$name] =
                'This field must contain valid UTF-8 text.';

            return '';
        }

        return $value;
    }
}
