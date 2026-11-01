@props([
    'name' => 'signature_data_uri',
    'inputId' => 'signature_data_uri',
    'stage' => 'origin',
    'customerName' => '',
])

<div x-data="signaturePad('{{ $inputId }}')" class="space-y-4">
    <div class="border-2 border-dashed border-slate-300 rounded-xl bg-white p-3 relative shadow-xs">
        <div class="flex items-center justify-between pb-2 mb-2 border-b border-slate-100 text-xs text-slate-500 font-medium">
            <span>Draw your signature with finger, stylus, or mouse below:</span>
            <button @click="clear()" type="button" class="text-rose-600 hover:text-rose-700 font-semibold px-2 py-1 rounded hover:bg-rose-50 transition-colors">
                Clear
            </button>
        </div>

        <!-- Canvas for drawing -->
        <div class="w-full h-44 sm:h-52 bg-slate-50/50 rounded-lg overflow-hidden border border-slate-200 touch-none flex items-center justify-center">
            <canvas x-ref="canvas" class="w-full h-full block cursor-crosshair"></canvas>
        </div>

        <input type="hidden" name="{{ $name }}" id="{{ $inputId }}" x-ref="dataInput">
    </div>

    <!-- Signer Details -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                Full Printed Name <span class="text-rose-600">*</span>
            </label>
            <input type="text" name="signer_name" value="{{ old('signer_name', $customerName) }}" required
                   placeholder="e.g. John Miller"
                   class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-blue-600 focus:border-blue-600">
            @error('signer_name')
                <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                Signer Authority <span class="text-rose-600">*</span>
            </label>
            <select name="signer_role" required class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-blue-600 focus:border-blue-600">
                <option value="customer" {{ old('signer_role') === 'customer' ? 'selected' : '' }}>Customer (Property Owner / Shipper)</option>
                <option value="authorized_agent" {{ old('signer_role') === 'authorized_agent' ? 'selected' : '' }}>Authorized Agent / Representative</option>
            </select>
            @error('signer_role')
                <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Legal Consent Acknowledgment -->
    <div class="p-3.5 rounded-xl bg-slate-100 border border-slate-200">
        <label class="flex items-start gap-3 cursor-pointer">
            <input type="checkbox" name="legal_acknowledgment" value="1" required
                   class="mt-1 w-5 h-5 rounded border-slate-300 text-blue-600 focus:ring-blue-500 shrink-0">
            <span class="text-xs text-slate-700 leading-relaxed">
                @if($stage === 'origin')
                    I confirm that I have inspected the room-by-room inventory manifest. The item counts, condition descriptions, and pre-existing damage notes are complete and accurate as of loading.
                @else
                    I confirm that all items listed in this manifest have been delivered and inspected at the destination address. Any noted exceptions or damage in transit have been logged above.
                @endif
            </span>
        </label>
        @error('legal_acknowledgment')
            <p class="mt-2 text-xs text-rose-600 font-medium">{{ $message }}</p>
        @enderror
    </div>
</div>
