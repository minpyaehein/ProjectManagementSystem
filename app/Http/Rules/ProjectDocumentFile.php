<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;

class ProjectDocumentFile implements ValidationRule
{
    private const ALLOWED_EXTENSIONS = [
        'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx',
        'txt', 'csv', 'jpg', 'jpeg', 'png', 'zip',
    ];

    private const MAX_BYTES = 20 * 1024 * 1024;

    public function validate(
        string $attribute,
        mixed $value,
        Closure $fail
    ): void {
        if (! $value instanceof UploadedFile) {
            $fail('The selected project document is not a valid uploaded file.');
            return;
        }

        if (! $value->isValid()) {
            $fail('The selected project document could not be uploaded: ' . $value->getErrorMessage());
            return;
        }

        if (($value->getSize() ?: 0) > self::MAX_BYTES) {
            $fail('Each project document cannot exceed 20 MB.');
            return;
        }

        $extension = strtolower($value->getClientOriginalExtension());

        if (! in_array($extension, self::ALLOWED_EXTENSIONS, true)) {
            $fail('Allowed extensions are PDF, Word, Excel, PowerPoint, TXT, CSV, JPG, PNG, and ZIP.');
        }
    }
}
