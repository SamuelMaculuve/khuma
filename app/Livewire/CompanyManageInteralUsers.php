<?php

namespace App\Livewire;

use App\Enums\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class CompanyManageInteralUsers extends Component
{
    use WithPagination;

    public $search = '';

    public $showModal = false;

    // Form fields
    public $name;
    public $email;
    public $phone;
    public $role = Role::CLIENT_EMPLOYEE->value;

    protected $paginationTheme = 'tailwind';

    protected function rules()
    {
        return [
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable',
            'role' => Rule::in([
                Role::CLIENT_MANAGER->value,
                Role::CLIENT_EMPLOYEE->value,
            ]),
        ];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openModal()
    {
        if (! $this->canAddCompanyUser()) {
            $this->addError('members_limit', 'O limite de membros do plano foi atingido. Atualize a subscrição para adicionar mais utilizadores.');

            return;
        }

        $this->resetForm();
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    private function resetForm()
    {
        $this->name = '';
        $this->email = '';
        $this->phone = '';
        $this->role = Role::CLIENT_EMPLOYEE->value;
    }

    public function save()
    {
        $this->validate();
        $company_id = auth()->user()->company_id;

        if (! $this->canAddCompanyUser()) {
            $this->addError('members_limit', 'O limite de membros do plano foi atingido. Atualize a subscrição para adicionar mais utilizadores.');

            return;
        }

        $salesRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => $this->role]);

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'company_id' => $company_id,
            'password' => Hash::make('password')
        ]);

        $user->assignRole($salesRole);

        $this->closeModal();
        $this->resetForm();
    }

    public function render()
    {
        $company = auth()->user()->company_id;

        $users = User::query()
            ->where(function ($query) {
                $query->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%")
                    ->orWhere('phone', 'like', "%{$this->search}%");
            })->where('company_id', $company)
            ->latest()
            ->paginate(10);

        return view('livewire.company-manage-interal-users', compact('users'));
    }

    private function canAddCompanyUser(): bool
    {
        $user = auth()->user();
        $limit = $user->featureLimit('members', null);

        if ($limit === null || $limit === 'unlimited') {
            return true;
        }

        return User::where('company_id', $user->company_id)->count() < (int) $limit;
    }
}
