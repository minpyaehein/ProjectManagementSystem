<section class="financial-section project-document-section">
    <div class="financial-section-heading">
        <div>
            <h2>Project documents</h2>
            <p>Download existing files or remove files that are no longer required.</p>
        </div>
    </div>

    <div class="financial-table-wrap">
        <table class="financial-table">
            <thead><tr><th>File</th><th>Type</th><th>Size</th><th>Uploaded</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse ($project->documents as $document)
                    <tr>
                        <td>{{ $document->original_name }}</td>
                        <td>{{ $document->mime_type ?: 'Unknown' }}</td>
                        <td>{{ number_format($document->file_size / 1024, 1) }} KB</td>
                        <td>{{ $document->created_at?->format('d M Y h:i A') ?? 'Not available' }}</td>
                        <td>
                            <div class="financial-row-actions">
                                <a class="action-btn action-view" href="{{ route('projects.documents.download', [$project, $document]) }}">Download</a>
                                @if ($canEditProject)
                                    <form action="{{ route('projects.documents.destroy', [$project, $document]) }}" method="POST"
                                        onsubmit="return confirm('Remove this document?');">
                                        @csrf @method('DELETE')
                                        <button class="action-btn action-delete" type="submit">Remove</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="financial-empty">No project documents uploaded.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
