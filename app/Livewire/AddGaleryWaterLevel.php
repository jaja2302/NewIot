<?php

namespace App\Livewire;

use App\Models\galerywaterlevel;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Livewire\Component;
use Illuminate\Contracts\View\View;
use App\Models\Waterlevel;
use App\Models\Waterlevellist;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;

class AddGaleryWaterLevel extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];
    public $stationID;
    public $files = []; // For multiple file uploads
    public $galleryImages = [];


    public function mount($selectedStation): void
    {
        $this->stationID = $selectedStation;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                FileUpload::make('file')
                    ->image()
                    ->multiple(5)
                    ->required(),
            ])
            ->statePath('data')
            ->model(Waterlevel::class);
    }

    public function create(): void
    {
        $data = $this->form->getState();

        // Update foto_lokasi for the specific station
        Waterlevellist::where('id', $this->stationID)
            ->update([
                'foto_lokasi' => $data['file']
            ]);

        // Optional: You can add success notification
        // $this->notification()->success('Updated successfully');

        Notification::make()
            ->title('Images succes to upload!')
            ->success()
            ->send();
        // Optional: Reset form after successful update
        $this->form->fill();

        $this->dispatch('close-modal', id: 'importGalery');
    }

    public function render(): View
    {
        return view('livewire.add-galery-water-level');
    }
}
