<aside class="main-sidebar">
    <section class="sidebar">
        <ul class="sidebar-menu">
            <li class="header bg-success text-light py-3 px-3">
                <form action="{{ route('select.shop') }}" method="POST" class="d-flex align-items-center">
                    @csrf
                    <div class="flex-grow-1">
                        <select id="shopDropdown" name="shopId" class="form-select" onchange="this.form.submit()">
                            @php
                                $ownerShops = Auth::user()->ownerShops;
                                $otherShops = Auth::user()->shops->diff($ownerShops);
                                $allShops = $ownerShops->merge($otherShops);
                            @endphp
            
                            @foreach ($allShops as $shop)
                                <option value="{{ $shop->shop_id }}" {{ session('selected_shop_id') == $shop->shop_id ? 'selected' : '' }}>
                                    {{ $shop->shop_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </li>             
            <li>
                <a href="{{ route('home') }}">
                    <i class="fa-solid fa-dashboard me-2"></i>Dashboard
                </a>
            </li> 
            @php
                $selectedShopId = session('selected_shop_id');
                $isOwner = \App\Models\Shop_user::where('user_id', auth()->id())
                    ->where('shop_id', $selectedShopId)
                    ->where('user_role', 'owner')
                    ->exists();
            @endphp

            @if ($isOwner)
                <li>
                    <a href="{{ route('mange.user') }}">
                        <i class="fa-solid fa-user-plus me-2"></i>Manage Staff
                    </a>
                </li> 
            @endif 

            <li>
                <a href="{{ route('product.index')}}">
                    <i class="fa-solid fa-list me-2"></i>Product List  
                </a>  
            </li>    
            <li>
                <a href="{{ route('category.index')}}">
                    <i class="fa-solid fa-layer-group me-2"></i></i>Category  
                </a>  
            </li>               
            <li>
                <a href="{{ route('product_types.index') }}">
                    <i class="fa fa-file me-2"></i>Product Type
                </a>
            </li>
            <li>
                <a href="{{ route('brands.index') }}" class="mr-5">
                    <i class="fa fa-product-hunt me-2"></i>Brand
                </a>
            </li>
            <li>
                <a href="{{ route('tags.index') }}">
                    <i class="fa fa-users me-2"></i>Tag
                </a>
            </li>
        </ul>
    </section>
</aside>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const sidebarToggle = document.querySelector('.sidebar-toggle');
        const app = document.querySelector('#app');
        const contentWrapper = document.querySelector('.content-wrapper');

        sidebarToggle.addEventListener('click', function () {
            app.classList.toggle('hidden');
            contentWrapper.classList.toggle('expanded');
        });
    });
</script>

