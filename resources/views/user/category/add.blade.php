@extends('layouts.user')

@section('title', 'Add Category')

@section('content')
    <div class="row mb-3">
        <div class="col-md-6">
            <h2>Add Category</h2>
        </div>
        <div class="col-12">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        </div>
    </div>

    <!-- Category Form -->
    <form id="categoryForm" action="{{ route('category.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row">
            <!-- Title Field -->
            <div class="col-md-6 mb-3">
                <label for="categoryTitle" class="form-label">Title</label>
                <input 
                    type="text" 
                    name="title" 
                    class="form-control" 
                    id="categoryTitle" 
                    placeholder="Enter category title"
                    value="{{ old('title') }}"
                    required
                >
                @error('title')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        
            <!-- Image Field -->
            <div class="col-md-4 mb-3">
                <label for="image" class="form-label">Category Image</label>
                <input 
                    type="file" 
                    id="image" 
                    name="image" 
                    class="form-control" 
                    accept="image/*"
                    onchange="previewImage(event)" 
                >
                @error('image')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        
            <!-- Description Field -->
            <div class="col-md-6 mb-3">
                <label for="categoryDescription" class="form-label">Description</label>
                <textarea 
                    name="description" 
                    class="form-control" 
                    id="categoryDescription" 
                    rows="4"
                    placeholder="Enter category description"
                >{{ old('description') }}</textarea>
            </div>
        
            <!-- Image Preview -->
            <div class="col-md-6 mb-3">
                <img id="imagePreview" src="#" alt="Selected Image" style="display: none; width: auto; max-height: 137px; border: 1px solid #ccc;">
            </div>
        </div>
        
        <!-- Category Type Radio -->
        <div class="mb-3">
            <h3>Category Type</h3>
            <div class="form-check form-check-inline">
                <input 
                    class="form-check-input" 
                    type="radio" 
                    name="categorytype" 
                    id="manualCategory" 
                    value="manual" 
                    {{ old('categorytype') !== 'auto' ? 'checked' : '' }}
                >
                <label class="form-check-label" for="manualCategory">
                    Manual
                </label>
            </div>
            <div class="form-check form-check-inline">
                <input 
                    class="form-check-input" 
                    type="radio" 
                    name="categorytype" 
                    id="autoCategory" 
                    value="auto"
                    {{ old('categorytype') === 'auto' ? 'checked' : '' }}
                >
                <label class="form-check-label" for="autoCategory">
                    Auto
                </label>
            </div>
            @error('categorytype')
              <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div id="productsTable" style="display: none;">
            <h2 class="text-center">Products</h2>
            <table class="table table-bordered mt-3 w-75 mx-auto">
                <thead>
                    <tr>
                        <th>Select</th>
                        <th>Product Name</th>
                        <th>Product Image</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($products) && count($products) > 0)
                        @foreach($products as $product)
                            <tr>
                                <td>
                                    <input 
                                        type="checkbox" 
                                        name="selected_product[]" 
                                        value="{{ $product->product_id }}" 
                                        id="{{ $product->product_id }}"
                                    >
                                </td>
                                <td>
                                    <label for="{{ $product->product_id }}">
                                        {{ $product->product_title ?? '' }}
                                    </label>
                                </td>
                                <td>
                                    <img src="{{ config('app.cdn_url').$product->product_image }}" alt="{{ $product->product_title }}" width="50px" height="50px">
                                </td>                                
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="3" class="text-center">No products found.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Conditions Section (Only for Auto) -->
        <div class="mb-3" id="conditionsSection" style="display: none;">
            <h3>Conditions</h3>
            <label class="form-label me-5">Products must match:</label>
            <div class="form-check form-check-inline">
                <input 
                    class="form-check-input" 
                    type="radio" 
                    name="matchType" 
                    id="allConditions" 
                    value="all" 
                    checked
                >
                <label class="form-check-label" for="allConditions">
                    All conditions
                </label>
            </div>
            <div class="form-check form-check-inline">
                <input 
                    class="form-check-input" 
                    type="radio" 
                    name="matchType" 
                    id="anyCondition" 
                    value="any"
                >
                <label class="form-check-label" for="anyCondition">
                    Any condition
                </label>
            </div>
        </div>

        <!-- Dynamic Conditions Container (Only for Auto) -->
        <div id="conditionsContainer">
            <div class="row condition-row mb-3">
                <div class="col-md-4">
                    <select class="form-select field-select" name="fields[]">
                        <option value="" selected disabled>Select Field</option>
                        @foreach($fields as $field)
                            <option value="{{ $field->field_id }}">{{ $field->field_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <select class="form-select query-select" name="queries[]" disabled>
                        <option value="" selected disabled>Select Query</option>
                        <!-- Options will be populated dynamically via JavaScript -->
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="text" name="values[]" class="form-control value-input" placeholder="Enter value" disabled>
                    <datalist id="typeSuggestions">
                        @foreach($type as $t)
                            <option value="{{ $t->product_type_name }}">{{ $t->product_type_name }}</option>
                        @endforeach
                    </datalist>
                
                    <datalist id="brandSuggestions">
                        @foreach($brands as $b)
                            <option value="{{ $b->brand_name }}">{{ $b->brand_name }}</option>
                        @endforeach
                    </datalist>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger btn-sm remove-condition">X</button>
                </div>
            </div>
        </div>
        
        
        
        <div class="row">
            <div class="col-md-6">
                <button type="button" class="btn btn-outline-danger" id="addCondition">+ Add Another Condition</button>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('category.index') }}" class="btn btn-secondary">Back</a>
                <button type="submit" class="btn btn-primary">Save Category</button>
            </div>
        </div>
        
    </form>


{{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}

<script>

document.addEventListener('DOMContentLoaded', function () {
    const manualRadio = document.getElementById('manualCategory');
    const autoRadio   = document.getElementById('autoCategory');
    const conditionsSection   = document.getElementById('conditionsSection');
    const conditionsContainer = document.getElementById('conditionsContainer');
    const addConditionBtn     = document.getElementById('addCondition');

    function toggleConditionsSection() {
        if (autoRadio.checked) {
            conditionsSection.style.display   = 'block';
            conditionsContainer.style.display = 'block';
            addConditionBtn.style.display     = 'inline-block';
            productsTable.style.display = 'none';
        } else {
            conditionsSection.style.display   = 'none';
            conditionsContainer.style.display = 'none';
            addConditionBtn.style.display     = 'none';
            productsTable.style.display = 'block';
        }
    }

    toggleConditionsSection();

    manualRadio.addEventListener('change', toggleConditionsSection);
    autoRadio.addEventListener('change', toggleConditionsSection);
});


document.addEventListener("DOMContentLoaded", function () {
    const addConditionButton = document.getElementById("addCondition");
    const conditionsContainer = document.getElementById("conditionsContainer");

    // field_query_relations + queries from server
    const field_query_relations = @json($field_query_relations);
    const queries = @json($queries);
    
    // Auto-suggestion lists from server
    const types = @json($type);
    const brands = @json($brands);

    function enableDropdowns(row) {
        const fieldSelect = row.querySelector(".field-select");
        const querySelect = row.querySelector(".query-select");
        const valueInput = row.querySelector(".value-input");

        // When user selects a field
        fieldSelect.addEventListener("change", function () {
            const selectedFieldId = parseInt(this.value);
            querySelect.innerHTML = '<option value="" selected disabled>Select Query</option>';
            querySelect.disabled = true;
            valueInput.disabled = true;
            valueInput.removeAttribute("list");

            if (selectedFieldId) {
                // Find related queries
                const relatedQueryIds = field_query_relations
                    .filter(relation => relation.field_id === selectedFieldId)
                    .map(relation => relation.query_id);

                // Populate the query dropdown
                queries.forEach(query => {
                    if (relatedQueryIds.includes(query.query_id)) {
                        const option = document.createElement("option");
                        option.value = query.query_id;
                        option.textContent = query.query_name;
                        querySelect.appendChild(option);
                    }
                });

                if (querySelect.options.length > 1) {
                    querySelect.disabled = false;
                }

                // Enable auto-suggestions for Type & Brand fields
                const selectedFieldText = fieldSelect.options[fieldSelect.selectedIndex].text.toLowerCase();
                if (selectedFieldText === "type") {
                    valueInput.setAttribute("list", "typeSuggestions");
                } else if (selectedFieldText === "brand") {
                    valueInput.setAttribute("list", "brandSuggestions");
                } else {
                    valueInput.removeAttribute("list");
                }

                valueInput.disabled = false;
            }
        });

        // When user selects a query
        querySelect.addEventListener("change", function () {
            valueInput.disabled = !this.value;
        });
    }

    // Enable on the first existing row
    document.querySelectorAll(".condition-row").forEach(enableDropdowns);

    // Add new row
    addConditionButton.addEventListener("click", () => {
        const conditionRow = document.createElement("div");
        conditionRow.classList.add("row", "condition-row", "mb-3");
        conditionRow.innerHTML = `
            <div class="col-md-4">
                <select class="form-select field-select" name="fields[]">
                    <option value="" selected disabled>Select Field</option>
                    @foreach($fields as $field)
                        <option value="{{ $field->field_id }}">{{ $field->field_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <select class="form-select query-select" name="queries[]" disabled>
                    <option value="" selected disabled>Select Query</option>
                </select>
            </div>
            <div class="col-md-3">
                <input type="text" name="values[]" class="form-control value-input" placeholder="Enter value" disabled>
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-danger btn-sm remove-condition">X</button>
            </div>
        `;
        conditionsContainer.appendChild(conditionRow);
        enableDropdowns(conditionRow);
    });

    // Remove row
    conditionsContainer.addEventListener("click", function (event) {
        if (event.target.classList.contains("remove-condition")) {
            event.target.closest(".condition-row").remove();
        }
    });
});


function previewImage(event) {
    const input = event.target;
    const imagePreview = document.getElementById('imagePreview');

    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function (e) {
            imagePreview.src = e.target.result;
            imagePreview.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    } else {
        imagePreview.src = "#";
        imagePreview.style.display = 'none';
    }
}

</script>
@endsection