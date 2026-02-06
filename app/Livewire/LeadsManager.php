<?php

namespace App\Livewire;

use Livewire\Component;

class LeadsManager extends Component
{

    public $view = 'kanban';

    public function showKanban()
    {
        $this->view = 'kanban';
    }

    public function showClientes()
    {
        $this->view = 'clientes';
    }

    public function render()
    {
        return view('livewire.leads-manager');
    }
}
