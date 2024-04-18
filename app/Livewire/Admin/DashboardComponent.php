<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Component;

class DashboardComponent extends Component
{
    public $total_users, $total_posts, $visitors_today, $total_visitors;


    public function mount()
    {
        $this->total_users = User::count();
        $this->total_posts = 0;
        $this->visitors_today = 0;
        $this->total_visitors = 0;
    }

    public function render()
    {
        $customers = User::orderBy('id', 'DESC')->take(5)->get();

        $this->dispatch('reload_scripts');
        return view('livewire.admin.dashboard-component', ['customers'=>$customers])->layout('livewire.admin.layouts.base');
    }
}
