<div class="field">
    <label for="assigned_user_id">Assigned user</label>

    <select id="assigned_user_id" name="assigned_user_id">
        <option value="">Assign to current user</option>

        @foreach ($assignableUsers as $assignableUser)
            <option
                value="{{ $assignableUser->user_id }}"
                @selected(
                    (string) old(
                        'assigned_user_id',
                        $record->assigned_user_id ?? ''
                    ) === (string) $assignableUser->user_id
                )
            >
                {{ $assignableUser->name }}
            </option>
        @endforeach
    </select>

    @error('assigned_user_id')
        <span class="field-error">{{ $message }}</span>
    @enderror
</div>
