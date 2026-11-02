<?php

namespace App\Support;

use App\Models\Project;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class ProjectDocumentUpload
{
    /** @param array<int, UploadedFile> $files */
    public static function store(Project $project, array $files, int $userId): void
    {
        foreach ($files as $file) {
            $storedName = Str::uuid()->toString() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs(
                'project-documents/' . $project->project_id,
                $storedName,
                'local'
            );

            $project->documents()->create([
                'uploaded_by' => $userId,
                'original_name' => $file->getClientOriginalName(),
                'stored_name' => $storedName,
                'storage_path' => $path,
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
            ]);
        }
    }
}
