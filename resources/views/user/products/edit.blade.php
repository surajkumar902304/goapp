@extends('layouts.user')

@section('title', 'Edit Product')

@section('content')
<form method="POST" action="{{ route('product.update', $product->product_id) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="col-12">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    </div>
    <div class="row mb-3">
        <div class="col-6">
            <h2>Edit Product</h2>
        </div>
        <div class="col-6">
            <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#duplicateProductModal">
                Duplicate Product
            </button>
        </div>
    </div>
    
    <div class="row">
        <!-- Product Title -->
        <div class="col-md-6 mb-3">
            <label for="title" class="form-label">Product Title</label>
            <input type="text" id="title" name="title" class="form-control" 
                   value="{{ old('title', $product->product_title) }}" required>
            @error('title')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <!-- SKU -->
        <div class="col-md-6 mb-3">
            <label for="sku" class="form-label">SKU</label>
            <input type="text" id="sku" name="sku" class="form-control" 
                   value="{{ old('sku', $product->variants->sku ?? '') }}" required>
            @error('sku')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <!-- Quantity -->
        <div class="col-md-6 mb-3">
            <label for="qty" class="form-label">Quantity</label>
            <input type="number" id="qty" name="qty" class="form-control" 
                   value="{{ old('qty', $product->variants->qty ?? '') }}" required>
            @error('qty')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <!-- Price -->
        <div class="col-md-6 mb-3">
            <label for="price" class="form-label">Price</label>
            <input type="text" id="price" name="price" class="form-control" 
                   value="{{ old('price', $product->variants->price ?? '') }}" required>
            @error('price')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <!-- Product Type -->
        <div class="col-md-6 mb-3">
            <label for="product_type" class="form-label">Product Type</label>
            <select name="product_type" id="product_type" class="form-select" required>
                @foreach ($productTypes as $type)
                    <option value="{{ $type->product_type_id }}" 
                            {{ $type->product_type_id == ($product->variants->product_type_id ?? '') ? 'selected' : '' }}>
                        {{ $type->product_type_name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Brand -->
        <div class="col-md-6 mb-3">
            <label for="brand" class="form-label">Brand</label>
            <select name="brand" id="brand" class="form-select" required>
                @foreach ($brands as $brand)
                    <option value="{{ $brand->brand_id }}" 
                            {{ $brand->brand_id == ($product->variants->brand_id ?? '') ? 'selected' : '' }}>
                        {{ $brand->brand_name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Tags -->
    <div class="col-md-12 mb-3">
        <label for="tags" class="form-label">Tags</label>
        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-5 g-1">
            @foreach ($tags as $tag)
                <div class="col">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="tag_{{ $tag->tag_id }}" name="tags[]" 
                               value="{{ $tag->tag_id }}" 
                               {{ in_array($tag->tag_id, $product->tags->pluck('tag_id')->toArray()) ? 'checked' : '' }}>
                        <label class="form-check-label" for="tag_{{ $tag->tag_id }}">{{ $tag->tag_name }}</label>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="row">
        <!-- Options -->
        <div class="col-md-6 mb-3">
            <label for="option_ids" class="form-label">Options</label>
            <select name="option_ids" id="option_ids" class="form-select" onchange="addOptionField(this)">
                <option value="" disabled selected>Add options</option>
                @foreach ($options as $option)
                    <option value="{{ $option->option_id }}" 
                        @if (in_array($option->option_id, $selectedOptions[""])) selected @endif>
                        {{ $option->option_name }}
                    </option>
                @endforeach
            </select>
        </div>
    
        <!-- Image Upload -->
        <div class="col-md-6 mb-3">
            <label for="image" class="form-label">Product Image</label>
            <input type="file" id="image" name="image" class="form-control" accept="image/*" onchange="previewImage(this)">
            <div class="mt-3"></div>
            @error('image')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6" id="dynamic-fields">
            <!-- Preload existing options -->
            @foreach ($selectedOptions[''] as $optionId => $value)
                <div class="mb-3 dynamic-field" data-option-id="{{ $optionId }}">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <label for="option_value_{{ $optionId }}" class="form-label">
                                {{ $options->firstWhere('option_id', $optionId)->option_name ?? 'Unknown Option' }}
                            </label>
                            <input 
                                type="text" 
                                name="options[{{ $optionId }}]" 
                                id="option_value_{{ $optionId }}" 
                                class="form-control" 
                                placeholder="Enter value for {{ $options->firstWhere('option_id', $optionId)->option_name ?? 'Unknown Option' }}" 
                                value="{{ old('options.' . $optionId, $value ?? '') }}" 
                                required>
                        </div>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-danger btn-sm mt-4" onclick="removeOptionField({{ $optionId }}, '{{ $options->firstWhere('option_id', $optionId)->option_name ?? 'Unknown Option' }}')">
                                Remove
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach

        </div>
        <div class="col-md-6">
            <div class="mt-3">
                <img id="imagePreview" src="{{ $product->product_image ? config('app.cdn_url') . $product->product_image : asset('default-image.png') }}" alt="Selected Image" style="width: auto; max-height: 200px; border: 1px solid #ccc;">
            </div>
        </div>
    </div>
    
    <!-- Action Buttons -->
    <div class="text-end mt-4">
        <a href="{{ route('product.index') }}" class="btn btn-secondary me-2">Back</a>
        <button type="submit" class="btn btn-success">Update Product</button>
    </div>
    
</form>

<!-- Duplicate Product Confirmation Modal -->
<div class="modal fade" id="duplicateProductModal" tabindex="-1" aria-labelledby="duplicateProductModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="duplicateProductModalLabel">Confirm Duplication</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to duplicate this product? This will create a new product with the same data.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('product.duplicate', $product->product_id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary">Yes, Duplicate</button>
                </form>
            </div>
        </div>
    </div>
</div>


<script>
    const selectedOptions = new Set();

function addOptionField(selectElement) {
    const selectedOption = selectElement.options[selectElement.selectedIndex];
    const optionId = selectedOption.value;
    const optionName = selectedOption.text;

    if (selectedOptions.has(optionId)) {
        return;
    }

    selectedOptions.add(optionId);
    selectedOption.classList.add('selected');

    const fieldContainer = document.createElement('div');
    fieldContainer.classList.add('mb-3', 'dynamic-field');
    fieldContainer.setAttribute('data-option-id', optionId);

    fieldContainer.innerHTML = `
        <div class="row align-items-center">
            <div class="col-md-8">
                <label for="option_value_${optionId}" class="form-label">${optionName}</label>
                <input 
                    type="text" 
                    name="options[${optionId}]" 
                    id="option_value_${optionId}" 
                    class="form-control" 
                    placeholder="Enter value for ${optionName}" 
                    required>
            </div>
            <div class="col-md-4">
                <button type="button" class="btn btn-danger btn-sm mt-4" onclick="removeOptionField(${optionId}, '${optionName}')">Remove</button>
            </div>
        </div>
    `;

    document.getElementById('dynamic-fields').appendChild(fieldContainer);
}

function removeOptionField(optionId, optionName) {
    const fieldContainer = document.querySelector(`[data-option-id="${optionId}"]`);
    if (fieldContainer) {
        fieldContainer.remove();
        selectedOptions.delete(`${optionId}`);
    }

    const selectElement = document.getElementById('option_ids');
    const option = selectElement.querySelector(`option[value="${optionId}"]`);
    if (option) {
        option.classList.remove('selected');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    // Preload the existing fields for editing
    const preloadedFields = document.querySelectorAll('.dynamic-field[data-option-id]');
    preloadedFields.forEach(field => {
        selectedOptions.add(field.getAttribute('data-option-id'));
    });
});


function previewImage(input) {
        const imagePreview = document.getElementById('imagePreview');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function (e) {
                imagePreview.src = e.target.result;
                imagePreview.style.display = 'block';
            };
            reader.readAsDataURL(input.files[0]);
        } else {
            imagePreview.style.display = 'none';
        }
    }

</script>
@endsection
