{{-- Use these input definitions in project-edit.blade.php for all four amount fields. --}}

{{-- New Cost or Revenue --}}
<input
    type="text"
    name="amount"
    value="{{ old('amount') }}"
    inputmode="decimal"
    maxlength="23"
    pattern="\d{1,20}(\.\d{1,2})?"
    placeholder="Amount"
    title="Enter up to 20 digits and 2 decimal places"
    required
>

{{-- Cost inline edit --}}
<input
    type="text"
    name="amount"
    value="{{ $costItem->amount }}"
    inputmode="decimal"
    maxlength="23"
    pattern="\d{1,20}(\.\d{1,2})?"
    title="Enter up to 20 digits and 2 decimal places"
    required
>

{{-- Revenue inline edit --}}
<input
    type="text"
    name="amount"
    value="{{ $revenueItem->amount }}"
    inputmode="decimal"
    maxlength="23"
    pattern="\d{1,20}(\.\d{1,2})?"
    title="Enter up to 20 digits and 2 decimal places"
    required
>
