<div>
    @if($payment->user_provided_data && count($payment->user_provided_data) > 0)
        @php
            $isAutomated = $payment->paymentGateway && $payment->paymentGateway->type === 'automated';
            $sectionTitle = $isAutomated ? 'UddoktaPay Payment Details' : 'User Input Data';
            $sectionColor = $isAutomated ? '#3b82f6' : '#8b5cf6'; // Blue for UddoktaPay, Purple for Manual
        @endphp

        <div style="margin-bottom: 1.5rem; border-bottom: 2px solid {{ $sectionColor }}; padding-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
            @if($isAutomated)
                <svg xmlns="http://www.w3.org/2000/svg" style="width: 1.5rem; height: 1.5rem; color: {{ $sectionColor }};" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            @else
                <svg xmlns="http://www.w3.org/2000/svg" style="width: 1.5rem; height: 1.5rem; color: {{ $sectionColor }};" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                </svg>
            @endif
            <h3 style="font-size: 1.125rem; font-weight: 700; color: #111827; margin: 0; text-transform: uppercase; letter-spacing: 0.05em;">{{ $sectionTitle }}</h3>
        </div>

        @php
            $displayData = $payment->user_provided_data;
            if ($isAutomated) {
                $orderedKeys = ['Full Name', 'Payment Method', 'Sender Number', 'Transaction ID', 'Invoice ID'];
                $orderedData = [];
                foreach ($orderedKeys as $key) {
                    if (array_key_exists($key, $displayData)) {
                        $orderedData[$key] = $displayData[$key];
                    }
                }
                foreach ($displayData as $key => $value) {
                    if (!in_array($key, $orderedKeys)) {
                        $orderedData[$key] = $value;
                    }
                }
                $displayData = $orderedData;
            }
        @endphp

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem;">
            @foreach($displayData as $key => $value)
                @php
                    $isImage = is_string($value) && str_contains($value, 'payment_proofs/');
                    $isJson = is_array($value) || is_object($value);
                    $gridColumn = ($isImage || $isJson || (is_string($value) && strlen($value) > 60)) ? 'grid-column: 1 / -1;' : '';
                @endphp

                <div style="border: 1px solid #e5e7eb; border-radius: 0.5rem; background-color: #ffffff; overflow: hidden; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); {{ $gridColumn }}">
                    <div style="background-color: #f9fafb; padding: 0.5rem 1rem; border-bottom: 1px solid #e5e7eb;">
                        <span style="font-size: 0.75rem; font-weight: 600; text-transform: uppercase; color: #6b7280; letter-spacing: 0.05em;">
                            {{ str_replace(['_', '-'], ' ', $key) }}
                        </span>
                    </div>
                    
                    <div style="padding: 1rem;">
                        @if($isImage)
                            <a href="{{ Storage::url($value) }}" target="_blank" style="display: block;">
                                <img src="{{ Storage::url($value) }}" alt="Proof" style="max-width: 100%; height: auto; max-height: 20rem; object-fit: contain; border-radius: 0.375rem; border: 1px solid #e5e7eb; background-color: #f9fafb; padding: 0.25rem;">
                            </a>
                        @elseif($isJson)
                            <pre style="font-size: 0.75rem; font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; background-color: #1f2937; color: #f3f4f6; padding: 1rem; border-radius: 0.5rem; overflow-x: auto; margin: 0; box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.06);">@php echo e(json_encode($value, JSON_PRETTY_PRINT)); @endphp</pre>
                        @else
                            <p style="font-size: 0.875rem; font-weight: 500; color: #111827; word-break: break-word; margin: 0;">{{ $value }}</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div style="text-align: center; padding: 3rem 1rem; background-color: #f9fafb; border: 1px dashed #d1d5db; border-radius: 0.75rem;">
            <p style="font-size: 0.875rem; color: #6b7280; font-weight: 500; margin: 0;">No additional data provided.</p>
        </div>
    @endif
</div>
