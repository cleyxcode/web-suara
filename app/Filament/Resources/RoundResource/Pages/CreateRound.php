<?php

namespace App\Filament\Resources\RoundResource\Pages;

use App\Filament\Resources\RoundResource;
use App\Models\Election;
use App\Services\CandidateEliminationService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateRound extends CreateRecord
{
    protected static string $resource = RoundResource::class;

    /**
     * Hook sebelum form dibuka
     * Validasi apakah bisa membuat round baru
     */
    public function mount(): void
    {
        parent::mount();

        // Jika user memilih election_id via URL atau form
        // Kita tidak bisa validasi di sini karena election belum dipilih
        // Validasi akan dilakukan di mutateFormDataBeforeCreate
    }

    /**
     * Validasi sebelum create
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (isset($data['election_id'])) {
            $election = Election::find($data['election_id']);
            
            if ($election) {
                $service = new CandidateEliminationService();
                $check = $service->canCreateNewRound($election);

                if (!$check['can_create']) {
                    Notification::make()
                        ->title('Tidak Bisa Membuat Putaran Baru')
                        ->body($check['reason'])
                        ->danger()
                        ->persistent()
                        ->send();

                    // Redirect kembali ke list
                    $this->halt();
                }
            }
        }

        return $data;
    }

    /**
     * Redirect setelah create
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}