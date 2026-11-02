@php
    $projectDocuments = collect($project->documents ?? []);
@endphp

<div class="project-detail-section">
    <div class="project-detail-section-heading">
        <span class="project-detail-section-number">6</span>
        <div>
            <h2>Project Documents</h2>
            <p>Download documentation attached to this project.</p>
        </div>
    </div>

    <div class="financial-table-wrap">
        <table class="financial-table project-document-table">
            <thead>
                <tr>
                    <th>File</th>
                    <th>Type</th>
                    <th>Size</th>
                    <th>Uploaded By</th>
                    <th>Uploaded</th>
                    <th class="financial-actions-heading">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($projectDocuments as $document)
                    @php
                        $fileSize = (int) ($document->file_size ?? 0);
                        $extension = strtoupper(pathinfo(
                            $document->original_name,
                            PATHINFO_EXTENSION
                        ));
                    @endphp
                    <tr>
                        <td>
                            <div class="project-document-name">
                                <span class="project-document-icon" aria-hidden="true">
                                    {{ $extension ?: 'FILE' }}
                                </span>
                                <div>
                                    <strong>{{ $document->original_name }}</strong>
                                    <small>
                                        DOC-{{ str_pad(
                                            (string) $document->project_document_id,
                                            4,
                                            '0',
                                            STR_PAD_LEFT
                                        ) }}
                                    </small>
                                </div>
                            </div>
                        </td>
                        <td>{{ $extension ?: 'File' }}</td>
                        <td>
                            @if ($fileSize >= 1048576)
                                {{ number_format($fileSize / 1048576, 2) }} MB
                            @elseif ($fileSize >= 1024)
                                {{ number_format($fileSize / 1024, 1) }} KB
                            @else
                                {{ number_format($fileSize) }} bytes
                            @endif
                        </td>
                        <td>{{ $document->uploader?->name ?? 'Unknown user' }}</td>
                        <td>
                            {{ $document->created_at?->format('d M Y h:i A')
                                ?? 'Not available' }}
                        </td>
                        <td>
                            <a
                                class="action-btn action-view project-document-download"
                                href="{{ route('projects.documents.download', [
                                    'project' => $project,
                                    'document' => $document,
                                ]) }}"
                            >
                                Download
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="financial-empty">
                            No project documents have been uploaded.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('styles')
<style>
.project-document-table{min-width:850px}.project-document-name{display:flex;align-items:center;gap:10px;min-width:230px}.project-document-name>div{min-width:0}.project-document-name strong,.project-document-name small{display:block}.project-document-name strong{max-width:320px;overflow:hidden;color:var(--ink);font-size:10px;text-overflow:ellipsis;white-space:nowrap}.project-document-name small{margin-top:3px;color:var(--muted);font-size:8px}.project-document-icon{display:grid;flex:0 0 42px;width:42px;height:34px;padding:3px;border-radius:8px;background:var(--mint);color:var(--green);font-size:7px;font-weight:850;text-align:center;place-items:center}.project-document-download{display:inline-flex;align-items:center;justify-content:center;text-decoration:none}@media(max-width:720px){.project-document-table{min-width:780px}}
</style>
@endpush
