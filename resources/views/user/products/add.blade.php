@extends('layouts.user')

@section('title', 'Add Products')

@section('content')

    <form method="POST" action="{{ route('product.store') }}" enctype="multipart/form-data">
        @csrf
        <h2 class="text-center mb-4">Add Product</h2>
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="title" class="form-label">Product Title</label>
                <input type="text" id="title" name="title" class="form-control" value="{{ old('title') }}" placeholder="Enter product title" required>
                @error('title')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-md-6 mb-3">
                <label for="sku" class="form-label">SKU</label>
                <input type="text" id="sku" name="sku" class="form-control" value="{{ old('sku') }}" placeholder="Enter product SKU" required>
                @error('sku')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-md-6 mb-3">
                <label for="qty" class="form-label">Quantity</label>
                <input type="number" id="qty" name="qty" class="form-control" value="{{ old('qty') }}" min="1" placeholder="Enter product quantity">
                @error('qty')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-md-6 mb-3">
                <label for="price" class="form-label">Price</label>
                <input type="numric" id="price" name="price" class="form-control" value="{{ old('price') }}" min="1" placeholder="Enter product price" required>
                @error('price')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-md-6 mb-3">
                <label for="product_type" class="form-label">Product Type</label>
                <select name="product_type" id="product_type" class="form-select">
                    <option value="" disabled selected>Select product type</option>
                    @foreach ($ptypes as $ptype)
                    <option value="{{ $ptype->product_type_id }}">{{ $ptype->product_type_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label for="brand" class="form-label">Brand</label>
                <select name="brand" id="brand" class="form-select">
                    <option value="" disabled selected>Select brand</option>
                    @foreach ($brands as $brand)
                    <option value="{{ $brand->brand_id }}">{{ $brand->brand_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-12 mb-3">
            <label for="tag" class="form-label">Tag's</label>
            <div class="row row-cols-2 row-cols-md-3 row-cols-lg-5 g-1">
                @foreach ($tags as $index => $tag)    
                    <div class="col">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="tag_{{ $tag->tag_id }}" name="tags[]" value="{{ $tag->tag_id }}">
                            <label class="form-check-label" for="tag_{{ $tag->tag_id }}">{{ $tag->tag_name }}</label>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="option_ids" class="form-label">Option</label>
                <select name="option_ids" id="option_ids" class="form-select" onchange="addOptionField(this)">
                    <option value="" disabled selected>Add options</option>
                    @foreach ($options as $option)
                    <option value="{{ $option->option_id }}">{{ $option->option_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label for="image" class="form-label">Product Image</label>
                <input type="file" id="image" name="image" class="form-control" accept="image/*" onchange="previewImage(this)" required>
                @error('image')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="row">
            <div class="col-md-6" id="dynamic-fields">
                <!-- Dynamic input fields for options will be added here -->
            </div>
            <div class="col-md-6">
                <div class="mt-3">
                    <img id="imagePreview" src="#" alt="Selected Image" style="display: none; width: auto; max-height: 200px; border: 1px solid #ccc;">
                </div>
            </div>
        </div>

        <div class="text-end mt-4">
            <a href="{{ route('product.index') }}" class="btn btn-secondary me-2">Back</a>
            <button type="submit" class="btn btn-success">Add Product</button>
        </div>
    </form>

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

    // const opt = selectedOptions;
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
    console.log(fieldContainer);
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