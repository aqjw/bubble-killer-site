<?php

namespace App\Livewire;

use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ClearVerification extends Component
{
    public Media $image;
    public Media $mask;
    public Media $result;

    public function render()
    {
        return view('livewire.clear-verification');
    }
}
