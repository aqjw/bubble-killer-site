<?php

namespace App\Livewire;

use Livewire\Component;

class ToggleComponent extends Component
{
    public array $item;
    public string|int $key;
    public string $component;

    public function toggleComponent()
    {
        $this->component = $this->component === 'mask' ? 'clear' : 'mask';
    }

    public function render()
    {
        return view('livewire.toggle-component');
    }
}
