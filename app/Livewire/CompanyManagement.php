<?php

namespace App\Livewire;

use Livewire\Component;

class CompanyManagement extends Component
{
    public $view = 'manage-users';

    public function showInternalUsers()
    {
        $this->view = 'manage-users';
    }

    public function showPlan()
    {
        $this->view = 'plan';
    }

    public function render()
    {
        return view('livewire.company-management');
    }
}
