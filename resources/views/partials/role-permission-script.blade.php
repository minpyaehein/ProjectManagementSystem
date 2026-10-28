@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const permissionCheckboxes = Array.from(
                document.querySelectorAll('.permission-checkbox')
            );

            function setCheckboxes(checkboxes, checked) {
                checkboxes.forEach(function (checkbox) {
                    checkbox.checked = checked;
                });
            }

            function refreshGroupButton(group) {
                const checkboxes = Array.from(
                    group.querySelectorAll('.permission-checkbox')
                );
                const button = group.querySelector('.permission-group-toggle');
                const allChecked = checkboxes.length > 0 &&
                    checkboxes.every(function (checkbox) {
                        return checkbox.checked;
                    });

                if (button) {
                    button.textContent = allChecked ? 'Clear group' : 'Select group';
                }
            }

            document.getElementById('selectAllPermissions')
                ?.addEventListener('click', function () {
                    setCheckboxes(permissionCheckboxes, true);
                    document.querySelectorAll('.permission-group')
                        .forEach(refreshGroupButton);
                });

            document.getElementById('clearAllPermissions')
                ?.addEventListener('click', function () {
                    setCheckboxes(permissionCheckboxes, false);
                    document.querySelectorAll('.permission-group')
                        .forEach(refreshGroupButton);
                });

            document.querySelectorAll('.permission-group-toggle')
                .forEach(function (button) {
                    button.addEventListener('click', function () {
                        const group = button.closest('.permission-group');
                        const checkboxes = Array.from(
                            group.querySelectorAll('.permission-checkbox')
                        );
                        const allChecked = checkboxes.length > 0 &&
                            checkboxes.every(function (checkbox) {
                                return checkbox.checked;
                            });

                        setCheckboxes(checkboxes, !allChecked);
                        refreshGroupButton(group);
                    });
                });

            permissionCheckboxes.forEach(function (checkbox) {
                checkbox.addEventListener('change', function () {
                    const group = checkbox.closest('.permission-group');

                    if (group) {
                        refreshGroupButton(group);
                    }

                    if (
                        checkbox.dataset.permissionSlug === 'admin.full' &&
                        checkbox.checked
                    ) {
                        setCheckboxes(permissionCheckboxes, true);
                        document.querySelectorAll('.permission-group')
                            .forEach(refreshGroupButton);
                    }
                });
            });

            document.querySelectorAll('.permission-group')
                .forEach(refreshGroupButton);
        });
    </script>
@endpush
