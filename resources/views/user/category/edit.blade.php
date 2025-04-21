@extends('layouts.user')

@section('title', 'Edit Category')

@section('content')
<div class="container">
    <h2>Edit Category</h2>

    <div class="row">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    </div>

    <form action="{{ route('category.update', $category->cat_id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT') 

        <div class="row">
            <!-- Title Field -->
            <div class="col-md-6 mb-3">
                <label for="categoryTitle" class="form-label">Title</label>
                <input 
                    type="text" 
                    name="title" 
                    class="form-control" 
                    id="categoryTitle" 
                    value="{{ $category->cat_title }}" 
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
                >{{ $category->cat_desc }}</textarea>
            </div>
        
            <!-- Image Preview -->
            <div class="col-md-6 mb-3">
                <img id="imagePreview" 
                src="{{ $category->cat_image ? config('app.cdn_url') . $category->cat_image : asset('default-image.png') }}" 
                alt="Selected Image" style="width: auto; max-height: 137px; border: 1px solid #ccc;">
            </div>
        </div>

        <!-- Products Table for Manual -->
        <div id="productsTable" style="display: {{ $category->cat_type === 'manual' ? 'block' : 'none' }}">
            <h2 class="text-center">Products</h2>
            <table class="table table-bordered mt-3 w-75 mx-auto">
                <thead>
                    <tr>
                        <th>Select</th>
                        <th>Product Name</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $product)
                        <tr>
                            <td>
                                <input 
                                    type="checkbox" 
                                    name="selected_product[]" 
                                    value="{{ $product->product_id }}" 
                                    id="product_{{ $product->product_id }}"
                                    {{ in_array($product->product_id, $manualCheckedProducts) ? 'checked' : '' }}
                                >
                            </td>
                            <td>{{ $product->product_title }}</td>
                            <td>
                                <img src="{{ config('app.cdn_url').$product->product_image }}" alt="{{ $product->product_title }}" width="50px" height="50px">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Conditions Section for Auto -->
        <div id="conditionsSection" style="display: {{ $category->cat_type === 'auto' ? 'block' : 'none' }}">
            <h3>Conditions</h3>
            <label class="form-label  me-5">Products must match:</label>
            <div class="form-check form-check-inline">
                <input 
                    type="radio" 
                    name="matchType" 
                    value="all" 
                    {{ $logicalOperator === 'all' ? 'checked' : '' }}
                >
                <label class="form-check-label">All conditions</label>
            </div>
            <div class="form-check form-check-inline">
                <input 
                    type="radio" 
                    name="matchType" 
                    value="any" 
                    {{ $logicalOperator === 'any' ? 'checked' : '' }}
                >
                <label class="form-check-label">Any condition</label>
            </div>

            <div id="conditionsContainer">
                @foreach($categoryRules as $rule)
                    <div class="row condition-row mb-3">
                        <div class="col-md-4">
                            <select class="form-select field-select" name="fields[]">
                                <option value="" disabled>Select Field</option>
                                @foreach($fields as $field)
                                    <option 
                                        value="{{ $field->field_id }}" 
                                        {{ $rule->field_id == $field->field_id ? 'selected' : '' }}
                                    >
                                        {{ $field->field_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select class="form-select query-select" name="queries[]">
                                <option value="" disabled>Select Query</option>
                                @foreach($queries as $query)
                                    <option 
                                        value="{{ $query->query_id }}" 
                                        {{ $rule->query_id == $query->query_id ? 'selected' : '' }}
                                    >
                                        {{ $query->query_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input 
                                type="text" 
                                class="form-control" 
                                name="values[]" 
                                value="{{ $rule->value }}"
                                placeholder="Enter value"
                            >
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn btn-danger btn-sm remove-condition">X</button>
                        </div>
                    </div>
                @endforeach
            </div>

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
        <div class="row">
            <div class="col-md-6">
                @if ($category->cat_type === 'auto')
                <button type="button" class="btn btn-outline-danger" id="addCondition">+ Add Another Condition</button>
                @endif
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('category.index') }}" class="btn btn-secondary">Back</a>
                <button type="submit" class="btn btn-primary">Update Category</button>
            </div>
        </div>
        
    </form>
    <div id="productsTable" style="display: {{ $category->cat_type === 'auto' ? 'block' : 'none' }}">
        <h2 class="text-center">Products</h2>
        <table class="table table-bordered mt-3 w-75 mx-auto">
            <thead>
                <tr>
                    <th>Product Image</th>
                    <th>Product Name</th>
                </tr>
            </thead>
            <tbody>
                @foreach($product_auto as $auto)
                    <tr>
                        <td>
                            <img src="{{ config('app.cdn_url').$auto->product_image }}" alt="{{ $auto->product_title }}" width="50px" height="50px">
                        </td>
                        <td>{{ $auto->product_title }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const addConditionButton = document.getElementById('addCondition');
        const conditionsContainer = document.getElementById('conditionsContainer');

        // field_query_relations + queries from server
        const field_query_relations = @json($field_query_relations);
        const queries = @json($queries);

        function enableDropdowns(row) {
            const fieldSelect = row.querySelector('.field-select');
            const querySelect = row.querySelector('.query-select');
            const valueInput = row.querySelector('input');

            // When user selects a field
            fieldSelect.addEventListener('change', function () {
                const selectedFieldId = parseInt(this.value);
                querySelect.innerHTML = '<option value="" selected disabled>Select Query</option>';
                querySelect.disabled = true;
                valueInput.disabled = true;

                if (selectedFieldId) {
                    // Find all queries related to this field via the field_query_relations table
                    const relatedQueryIds = field_query_relations
                        .filter(relation => relation.field_id === selectedFieldId)
                        .map(relation => relation.query_id);

                    // Populate the query dropdown
                    queries.forEach(query => {
                        if (relatedQueryIds.includes(query.query_id)) {
                            const option = document.createElement('option');
                            option.value = query.query_id;
                            option.textContent = query.query_name;
                            querySelect.appendChild(option);
                        }
                    });

                    if (querySelect.options.length > 1) {
                        querySelect.disabled = false;
                    }
                }
            });

            // When user selects a query
            querySelect.addEventListener('change', function () {
                valueInput.disabled = !this.value;
            });
        }

        // Add a new condition row dynamically
        addConditionButton.addEventListener('click', () => {
            const conditionRow = document.createElement('div');
            conditionRow.classList.add('row', 'condition-row');
            conditionRow.innerHTML = `
                <div class="col-md-4 mb-3">
                    <select class="form-select field-select" name="fields[]">
                        <option value="" selected disabled>Select Field</option>
                        @foreach($fields as $field)
                            <option value="{{ $field->field_id }}">{{ $field->field_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <select class="form-select query-select" name="queries[]" disabled>
                        <option value="" selected disabled>Select Query</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <input type="text" name="values[]" class="form-control" placeholder="Enter value" disabled>
                </div>
                <div class="col-md-1 mb-3">
                    <button type="button" class="btn btn-danger btn-sm remove-condition">X</button>
                </div>
            `;
            conditionsContainer.appendChild(conditionRow);
            enableDropdowns(conditionRow);
        });

        // Remove a condition row
        conditionsContainer.addEventListener('click', (event) => {
            if (event.target.classList.contains('remove-condition')) {
                event.target.closest('.condition-row').remove();
            }
        });

        // Enable dropdowns for existing rows
        document.querySelectorAll('.condition-row').forEach(enableDropdowns);
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