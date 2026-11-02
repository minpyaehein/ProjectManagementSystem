<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectDocument extends Model
{
    protected $table = 'project_documents';

    protected $primaryKey = 'project_document_id';

    protected $fillable = [
        'project_id',
        'uploaded_by',
        'original_name',
        'stored_name',
        'storage_path',
        'mime_type',
        'file_size',
    ];

    protected function casts(): array
    {
        return [
            'project_id' => 'integer',
            'uploaded_by' => 'integer',
            'file_size' => 'integer',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(
            Project::class,
            'project_id',
            'project_id'
        );
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'uploaded_by',
            'user_id'
        );
    }
}