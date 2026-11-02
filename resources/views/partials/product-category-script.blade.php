<script>
    document.addEventListener('DOMContentLoaded', function () {
        const categorySelect = document.getElementById('productCategory');
        const customField = document.getElementById('customProductCategoryField');
        const customInput = document.getElementById('customProductCategory');

        function updateCustomCategoryField() {
            if (!categorySelect || !customField || !customInput) return;
            const show = categorySelect.value === '__other__';
            customField.hidden = !show;
            customInput.required = show;
            if (!show) customInput.value = '';
        }

        categorySelect?.addEventListener('change', updateCustomCategoryField);
        updateCustomCategoryField();
    });
</script>
