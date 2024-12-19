<?php

namespace App\Livewire;

use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MaskVerification extends Component
{
    public Media $image;
    public Media $mask;
    public ?Media $result = null;

    public function saveMask(string $base64): void
    {
        // Remove base64 prefix if exists
        $base64 = preg_replace('/^data:image\/\w+;base64,/', '', $base64);

        // Decode base64 data
        $fileData = base64_decode($base64, true);
        if ($fileData === false) {
            throw new \Exception('Invalid base64 data');
        }

        if ($this->result) {
            $this->image->setCustomProperty('remask', true);
            $this->image->save();
        }

        // Save file to storage
        Storage::disk($this->mask->disk)->put($this->mask->getPathRelativeToRoot(), $fileData);
        $this->mask->touch();

        // Notify success
        Notification::make()
            ->success()
            ->title('Маска успешно сохранена!')
            ->send();
    }

    public function render()
    {
        return view('livewire.mask-verification');
    }
}
