<div class="min-h-screen bg-slate-50/70">
    <div class="mx-auto w-full max-w-6xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
        <x-ui.page-header
            :title="$title"
            :badge="$isEdit ? 'Update Product' : 'New Product'"
            :subtitle="$subtitle"
            :back-url="route('products.index')"
            back-text="Back to products"
        >
            <x-slot:icon>
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-indigo-600 text-white shadow-sm" aria-hidden="true">
                    @if($isEdit)
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 20h9" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 3.5a2.121 2.121 0 013 3L8 18l-4 1 1-4 11.5-11.5Z" />
                        </svg>
                    @else
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12M6 12h12" />
                        </svg>
                    @endif
                </div>
            </x-slot:icon>
        </x-ui.page-header>

        <form wire:submit="save" class="mt-6">
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <div class="space-y-6 lg:col-span-2">
                    <x-form.section
                        title="Basic information"
                        description="General information that identifies this product."
                    >
                        <div class="grid grid-cols-1 gap-6">
                            <x-form.input
                                label="Product Name"
                                name="form.name"
                                placeholder="e.g. Fresh Orange Juice"
                                required
                                wire:model.blur="form.name"
                            />

                            <x-form.textarea
                                label="Description"
                                name="form.description"
                                placeholder="Describe the product..."
                                rows="5"
                                wire:model.blur="form.description"
                            />
                        </div>
                    </x-form.section>

                    <x-form.section
                        title="Pricing & identification"
                        description="Set the selling price and barcode used to identify this product."
                    >
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <x-form.input
                                label="Price"
                                name="form.price"
                                type="number"
                                min="0"
                                step="0.01"
                                inputmode="decimal"
                                prefix="$"
                                placeholder="0.00"
                                required
                                wire:model.blur="form.price"
                            />

                            <x-form.input
                                label="Barcode"
                                name="form.barcode"
                                type="text"
                                inputmode="numeric"
                                placeholder="Enter barcode"
                                required
                                wire:model.blur="form.barcode"
                            />
                        </div>
                    </x-form.section>
                </div>

                <aside class="space-y-6">
                    <x-form.section
                        title="Product status"
                        description="Control whether this product is currently available."
                    >
                        <x-form.select-input
                            label="Status"
                            name="form.status"
                            :options="['active' => 'Active', 'inactive' => 'Inactive']"
                            placeholder="Select product status"
                            required
                            wire:model.live="form.status"
                        />
                    </x-form.section>

                    <x-form.section
                        title="Product image"
                        description="Upload an optional JPG, PNG, or WEBP image up to 2 MB."
                    >
                        @if($form->image)
                            <img
                                src="{{ $form->image->temporaryUrl() }}"
                                alt="Product image preview"
                                class="mb-4 h-40 w-full rounded-xl border border-slate-200 object-cover"
                            >
                        @elseif($isEdit && $form->product?->image && \Illuminate\Support\Facades\Storage::disk('public')->exists($form->product->image))
                            <img
                                src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($form->product->image) }}"
                                alt="Current product image"
                                class="mb-4 h-40 w-full rounded-xl border border-slate-200 object-cover"
                            >
                        @else
                            <div class="mb-4 flex h-40 items-center justify-center rounded-xl border border-dashed border-slate-300 bg-slate-50 text-sm text-slate-400">
                                No image selected
                            </div>
                        @endif

                        <label class="flex cursor-pointer items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                            Choose image
                            <input type="file" wire:model="form.image" accept="image/jpeg,image/png,image/webp" class="sr-only">
                        </label>

                        <div wire:loading wire:target="form.image" class="mt-2 text-sm text-slate-500">
                            Uploading image...
                        </div>

                        @error('form.image')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </x-form.section>

                    @if($isEdit && $form->product)
                        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                            <h2 class="text-sm font-semibold text-slate-900">Current product</h2>
                            <p class="mt-2 break-words text-sm text-slate-600">{{ $form->product->name }}</p>
                            <p class="mt-1 font-mono text-xs text-slate-500">{{ $form->product->barcode }}</p>
                            <p class="mt-2 text-sm font-bold text-slate-900">{{ number_format((float) $form->product->price, 2) }}</p>
                        </div>
                    @endif

                    <x-ui.info-card
                        :title="$isEdit ? 'Before you save' : 'Before you create'"
                        text="Review the product name, price, barcode, and status. Stock quantities are managed separately through stock records."
                    />
                </aside>

                <div class="lg:col-span-3">
                    <x-form.actions
                        :cancel-url="route('products.index')"
                        cancel-text="Cancel"
                        :submit-text="$submitText"
                        :loading-text="$loadingText"
                        loading-target="save"
                    >
                        Product information will be saved securely.
                    </x-form.actions>
                </div>
            </div>
        </form>
    </div>
</div>
