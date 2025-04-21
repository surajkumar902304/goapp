<?php

namespace App\Http\Livewire;

use App\Models\User;
use App\Models\Shop;
use App\Models\Shop_user;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Hash;
use App\Mail\NewUserNotificationMail;
use App\Mail\ExistingUserNotificationMail;
use App\Mail\EmailChangedNotificationMail;
use Illuminate\Support\Facades\Mail;

class MangeUser extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $perPage = 10;
    public $search = '';
    public $selectedShopId;
    public $name, $email, $password, $userId;
    public $isExistingUser = false;
    public $isEditMode = false;

    public function mount()
    {
        $this->selectedShopId = session('selected_shop_id');
    }

    private function resetInputs()
    {
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->isEditMode = false;
    }

    // Show modal for adding new user
    public function openAddModal()
    {
        $this->resetInputs();
        $this->isEditMode = false;
        $this->dispatchBrowserEvent('open-add-modal');
    }

    // Check if email already exists
    public function updatedEmail()
    {
        $this->isExistingUser = User::where('email', $this->email)->exists();
    }

    // Store new user or associate existing user
    public function store()
    {
        $this->resetErrorBag();
        $this->validate([
            'name' => 'required_if:isExistingUser,false|string|min:3|max:50',
            'email' => [
                'required',
                'email',
                function ($attribute, $value, $fail) {
                    if ($this->isExistingUser) {
                        $existingUser = User::where('email', $value)->first();
                        if ($existingUser && Shop_user::where('user_id', $existingUser->id)->where('shop_id', $this->selectedShopId)->exists()) {
                            $fail('This email is already associated with this shop.');
                        }
                    }
                },
            ],
            'password' => 'required_if:isExistingUser,false|string|min:6',
        ], [
            'name.required_if' => 'Please enter the staff member name',
            'password.required_if' => 'Please enter the password',
            'password.min' => 'The password must be at least 6 characters long',
        ]);        

        if ($this->isExistingUser) {
            // Fetch existing user
            $existingUser = User::where('email', $this->email)->first();

            // Check if already associated with the shop
            if (Shop_user::where('user_id', $existingUser->id)->where('shop_id', $this->selectedShopId)->exists()) {
                session()->flash('error', 'This user is already associated with this shop.');
                return;
            }

            Shop_user::create([
                'shop_id' => $this->selectedShopId,
                'user_id' => $existingUser->id,
                'user_role' => 'staff',
            ]);

            \Mail::to($this->email)->send(new ExistingUserNotificationMail($existingUser->name, $this->selectedShopId));

            session()->flash('message', 'Request sent to the user successfully!');
            $this->dispatchBrowserEvent('close-add-modal');
            $this->resetInputs();
            return;
        }

        // Create a new user
        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
        ]);

        // Associate the user with the shop
        Shop_user::create([
            'shop_id' => $this->selectedShopId,
            'user_id' => $user->id,
            'user_role' => 'staff',
        ]);

        \Mail::to($this->email)->send(new NewUserNotificationMail($this->email, $this->password, $this->name));

        session()->flash('message', 'User created and associated with the shop successfully.');
        $this->resetInputs();
        $this->dispatchBrowserEvent('close-add-modal');
    }



    // Show modal for editing an existing user
    // public function editUser($userId)
    // {
    //     $this->isEditMode = true;
    //     $user = User::find($userId);
    //     $this->name = $user->name;
    //     $this->email = $user->email;
    //     $this->userId = $user->id;
    //     $this->dispatchBrowserEvent('open-edit-modal');
    // }

    // public function updateUser()
    // {
    //     $this->validate([
    //         'email' => 'required|email|unique:users,email,' . $this->userId,
    //     ]);

    //     $user = User::find($this->userId);

    //     $name = $user->name;
    //     $oldEmail = $user->email;
    //     $newEmail = $this->email;

    //     if ($oldEmail !== $newEmail) {
    //         // Update the email
    //         $user->email = $newEmail;
    //         $user->save();

    //         // Send notification emails
    //         \Mail::to($oldEmail)->send(new EmailChangedNotificationMail($oldEmail, $newEmail, $name));
    //         \Mail::to($newEmail)->send(new EmailChangedNotificationMail($oldEmail, $newEmail, $name));

    //         session()->flash('message', 'User email updated and notifications sent!');
    //     } else {
    //         session()->flash('message', 'No changes made to the email.');
    //     }

    //     $this->dispatchBrowserEvent('close-edit-modal');
    // }

    public function closeModal()
    {
        if ($this->isEditMode) {
            $this->dispatchBrowserEvent('close-edit-modal');
        } else {
            $this->dispatchBrowserEvent('close-add-modal');
        }
    }


    public function render()
    {
        $mangeusers = User::whereHas('shopUsers', function ($query) {
            $query->where('shop_id', $this->selectedShopId);
        })
        ->where('name', 'like', '%' . $this->search . '%')
        ->whereHas('shopUsers', function ($query) {
            $query->where('user_role', 'staff');
        })
        ->paginate($this->perPage);

        return view('livewire.mange-user', compact('mangeusers'));
    }
}
