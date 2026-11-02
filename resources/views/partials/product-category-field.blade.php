@php
    $currentCategory = isset($project)
        ? trim((string) $project->productCategory)
        : '';

    $selectedCategory = old('productCategory', $currentCategory);
    $showCustomCategory = $selectedCategory === '__other__';
@endphp

<div class="project-entry-field">
    <label for="productCategory">Product Category</label>
    <select id="productCategory" name="productCategory" required>
        <option value="">-- Select Product Category --</option>
        @foreach ($productCategories as $category)
            <option value="{{ $category }}" @selected($selectedCategory === $category)>
                {{ $category }}
            </option>
        @endforeach
        <option value="__other__" @selected($showCustomCategory)>Other</option>
    </select>
    @error('productCategory')
        <span class="field-error">{{ $message }}</span>
    @enderror
</div>

<div id="customProductCategoryField" class="project-entry-field" @if (! $showCustomCategory) hidden @endif>
    <label for="customProductCategory">New Product Category</label>
    <input
        id="customProductCategory"
        name="customProductCategory"
        type="text"
        value="{{ old('customProductCategory') }}"
        maxlength="255"
        autocomplete="off"
        placeholder="Enter a new category"
        @if ($showCustomCategory) required @endif
    >
    <small class="small-muted">The category will appear in future Project Create and Edit forms.</small>
    @error('customProductCategory')
        <span class="field-error">{{ $message }}</span>
    @enderror
</div>
