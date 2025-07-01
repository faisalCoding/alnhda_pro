<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Project;


class ProjectsCrud extends Component
{

    public $projects;

    public $new_project = [

    ];


    public function render()
    {
        $this->projects = Project::get();

        return view('livewire.admin.projects-crud');
    }
}
