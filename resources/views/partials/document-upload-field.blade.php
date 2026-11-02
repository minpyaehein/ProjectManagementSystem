<div class="project-entry-field project-entry-remark">
    <label for="documents">Project documents</label>
    <input
        id="documents"
        name="documents[]"
        type="file"
        accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.csv,.jpg,.jpeg,.png,.zip"
        multiple
        style="padding: 10px;"
    >
    <small class="small-muted">
        Up to 10 files, 20 MB each. Allowed: PDF, Office documents, text, CSV, images, and ZIP.
    </small>
    @error('documents')<span class="field-error">{{ $message }}</span>@enderror
    @error('documents.*')<span class="field-error">{{ $message }}</span>@enderror
</div>
