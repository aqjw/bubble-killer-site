<?php

namespace App\Livewire;

use App\Services\TaskService;
use Livewire\Component;
use Livewire\WithFileUploads;

class ImageUploadForm extends Component
{
    use WithFileUploads;

    public $file;
    public $model = 'lama';
    public array $models = ['lama', 'ldm', 'zits', 'mat', 'fcf', 'sd1.5', 'anything4', 'realisticVision1.4', 'cv2', 'manga', 'sd2', 'paint_by_example,', 'instruct_pix2pix'];

    protected $rules = [
        'file' => 'required|file|mimes:jpeg,png,jpg,gif,zip|max:10240',
        'model' => 'required|string',
    ];

    public function submit()
    {
        $data = $this->validate();
        $taskService = app(TaskService::class);
        $task = $taskService->create($this->file, $data['model']);

        return redirect()->route('task', $task->id);
    }

    public function render()
    {
        return view('livewire.image-upload-form');
    }
}
