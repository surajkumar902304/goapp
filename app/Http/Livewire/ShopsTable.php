<?php

namespace App\Http\Livewire;

use App\Mail\ExistingUserNotificationMail;
use App\Mail\NewUserNotificationMail;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Shop;
use App\Models\User;
use App\Models\Shop_user;

class ShopsTable extends Component
{
    use WithPagination;

    public $shop_id, $shop_name, $shop_status;
    public $perPage = 10;
    public $search = '';
    public $new_shop_name = '';
    public $isEditMode = false;
    protected $paginationTheme = 'bootstrap';

    public $name, $email, $password;
    
    public $selectedShopId, $selectedUserId;

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function toggleStatus($shopId)
    {
        $shop = Shop::find($shopId);

        if ($shop) {
            $shop->shop_status = $shop->shop_status === 1 ? 0 : 1;
            $shop->save();
        }
    }

    public function openAddModal()
    {
        $this->isEditMode = false;
        $this->reset(['new_shop_name']);
        $this->dispatchBrowserEvent('open-modal');
    }

    public function store()
    {
        $this->validate([
            'new_shop_name' => 'required|string|min:3|max:20|regex:/^[A-Za-z0-9][A-Za-z0-9\s]*$/',
        ], [
            'new_shop_name.required' => 'Shop name is required.',
            'new_shop_name.min' => 'Shop name must be at least 3 characters.',
            'new_shop_name.max' => 'Shop name cannot exceed 20 characters.',
            'new_shop_name.regex' => 'Only letters and numbers are allowed.',
        ]);

        $existingShop = Shop::where('shop_name', $this->new_shop_name)->first();

        if ($existingShop) {
            session()->flash('error', 'Shop already exists.');
            return;
        }

        Shop::create([
            'shop_name' => $this->new_shop_name,
        ]);

        session()->flash('message', 'Shop added successfully.');
        $this->dispatchBrowserEvent('close-add-modal');
    }

    public function edit($shop_id)
    {
        $this->isEditMode = true;
        $shop = Shop::findOrFail($shop_id);

        $this->shop_id = $shop->shop_id;
        $this->shop_name = $shop->shop_name;
        $this->dispatchBrowserEvent('open-modal');
    }

    public function update()
    {
        $this->validate([
            'shop_name' => 'required|string|min:3|max:20|regex:/^[A-Za-z0-9][A-Za-z0-9\s]*$/',
        ]);

        $existingShop = Shop::where('shop_name', $this->shop_name)
            ->where('shop_id', '!=', $this->shop_id)
            ->first();

        if ($existingShop) {
            session()->flash('error', 'Shop already exists.');
            return;
        }

        $shop = Shop::findOrFail($this->shop_id);
        $shop->update([
            'shop_name' => $this->shop_name,
        ]);

        session()->flash('message', 'Shop updated successfully.');
        $this->dispatchBrowserEvent('close-add-modal');
    }

    public function prepareModal($shopId)
    {
        $this->selectedShopId = $shopId;
        $this->selectedUserId = null;
    }

    public function addUserToShop()
    {
        $this->validate([
            'selectedUserId' => 'required|exists:users,id',
        ]);

        $existingUser = User::find($this->selectedUserId);

        Shop_user::create([
            'shop_id' => $this->selectedShopId,
            'user_id' => $this->selectedUserId,
            'user_role' => 'owner',
        ]);
        
        \Mail::to($existingUser->email)->send(new ExistingUserNotificationMail($existingUser->name, $this->selectedShopId));


        $this->dispatchBrowserEvent('close-modal');
        session()->flash('message', 'User added to shop successfully!');
    }

    public function createUserToShop()
    {
        $this->validate([
            'name' => 'required|string|min:3|max:50',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'selectedShopId' => 'required|exists:shops,shop_id',
        ]);

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => bcrypt($this->password),
        ]);

        Shop_user::create([
            'shop_id' => $this->selectedShopId,
            'user_id' => $user->id,
            'user_role' => 'owner',
        ]);

        $name = $user->name;
        $email = $user->email;
        $password = $this->password;

        \Mail::to($email)->send(new NewUserNotificationMail($email, $password, $name));

        $this->dispatchBrowserEvent('close-modal');
        session()->flash('message', 'User created and added to shop successfully!');
    }

    public function render()
    {
        $shops = Shop::with(['users' => function ($query) {
            $query->where('user_role', 'owner');
        }])
        ->where('shop_name', 'like', '%' . $this->search . '%') 
        ->paginate($this->perPage); 
    
        $users = User::all(); 

        return view('livewire.shops-table', compact('shops', 'users'));
    }
}
