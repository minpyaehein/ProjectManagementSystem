<?php
namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectDocument;
use App\Support\RecordAccess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProjectDocumentController extends Controller
{
    public function download(
        Request $request,
        Project $project,
        ProjectDocument $document
    ): StreamedResponse {
        RecordAccess::authorize(
            $request->user(),
            $project,
            'project_access_scope',
            'projects.view'
        );

        $this->ensureDocumentBelongsToProject($project, $document);

        abort_unless(
            Storage::disk('local')->exists($document->storage_path),
            404,
            'The requested document file was not found.'
        );

        return Storage::disk('local')->download(
            $document->storage_path,
            $document->original_name,
            [
                'Content-Type' => $document->mime_type
                    ?: 'application/octet-stream',
            ]
        );
    }

    public function destroy(
        Request $request,
        Project $project,
        ProjectDocument $document
    ): RedirectResponse {
        RecordAccess::authorize(
            $request->user(),
            $project,
            'project_access_scope',
            'projects.edit'
        );

        $this->ensureDocumentBelongsToProject($project, $document);

        if (Storage::disk('local')->exists($document->storage_path)) {
            Storage::disk('local')->delete($document->storage_path);
        }

        $document->delete();

        return back()->with(
            'success',
            'Project document removed successfully.'
        );
    }

    private function ensureDocumentBelongsToProject(
        Project $project,
        ProjectDocument $document
    ): void {
        abort_unless(
            (int) $document->project_id === (int) $project->project_id,
            404
        );
    }
}
