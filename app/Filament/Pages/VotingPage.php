<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class VotingPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-hand-raised';

    protected static string $view = 'filament.pages.voting-page';
    
    protected static ?string $navigationLabel = 'Buka Voting';
    
    protected static ?string $title = 'Halaman Voting';

    /**
     * Redirect ke halaman voting public
     */
    public function mount(): void
    {
        $this->redirect(route('voting.index'), navigate: false);
    }
}