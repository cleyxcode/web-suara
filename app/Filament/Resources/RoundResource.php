<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoundResource\Pages;
use App\Models\Election;
use App\Models\Round;
use App\Services\CandidateEliminationService;
use App\Services\RoundValidationService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RoundResource extends Resource
{
    protected static ?string $model = Round::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';
    
    protected static ?string $navigationLabel = 'Putaran';
    
    protected static ?string $modelLabel = 'Putaran';
    
    protected static ?string $pluralModelLabel = 'Putaran';

    /**
     * FORM SCHEMA - Untuk Create & Edit
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Select Pemilihan (Election)
                Forms\Components\Select::make('election_id')
                    ->label('Pemilihan')
                    ->options(
                        Election::whereIn('status', ['draft', 'active'])
                            ->pluck('name', 'id')
                    )
                    ->required()
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->helperText('Pilih pemilihan untuk putaran ini')
                    ->disabled(fn ($record) => $record !== null), // Disabled saat edit

                // Round Number (Auto-generated, read-only)
                Forms\Components\TextInput::make('round_number')
                    ->label('Putaran Ke-')
                    ->disabled()
                    ->dehydrated(false) // Tidak kirim ke database (auto-generate)
                    ->default(fn ($get) => 'Auto-generated')
                    ->helperText('Nomor putaran akan dibuat otomatis'),

                // Status (Read-only di form, dikelola via action)
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Draft',
                        'active' => 'Aktif',
                        'invalid' => 'Invalid',
                        'completed' => 'Selesai',
                    ])
                    ->default('draft')
                    ->disabled() // Read-only
                    ->dehydrated(true)
                    ->hiddenOn('create')
                    ->native(false),

                // Total Votes (Read-only, placeholder)
                Forms\Components\TextInput::make('total_votes')
                    ->label('Total Suara')
                    ->numeric()
                    ->default(0)
                    ->disabled()
                    ->dehydrated(true)
                    ->hiddenOn('create')
                    ->helperText('Placeholder - belum digunakan'),
            ]);
    }

    /**
     * TABLE SCHEMA - Untuk menampilkan data
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Kolom Pemilihan
                Tables\Columns\TextColumn::make('election.name')
                    ->label('Pemilihan')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),

                // Kolom Round Number
                Tables\Columns\TextColumn::make('round_number')
                    ->label('Putaran Ke-')
                    ->sortable()
                    ->alignCenter()
                    ->weight('bold')
                    ->formatStateUsing(fn ($state) => "Putaran {$state}"),

                // Kolom Status dengan Badge
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'secondary' => 'draft',
                        'success' => 'active',
                        'danger' => 'invalid',
                        'warning' => 'completed',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'Draft',
                        'active' => 'Aktif',
                        'invalid' => 'Invalid',
                        'completed' => 'Selesai',
                        default => $state,
                    })
                    ->description(function (Round $record) {
                        if ($record->is_eliminated) {
                            return '✅ Sudah dieliminasi';
                        }
                        if ($record->status === 'completed') {
                            return '⏳ Belum dieliminasi';
                        }
                        return null;
                    }),

                // Kolom Total Votes
                Tables\Columns\TextColumn::make('total_votes')
                    ->label('Total Suara')
                    ->numeric()
                    ->alignCenter()
                    ->sortable()
                    ->description(function (Round $record) {
                        $totalParticipants = $record->election->total_participants;
                        $totalVotes = $record->total_votes;
                        
                        if ($totalVotes === 0) {
                            return "Target: {$totalParticipants} suara";
                        }
                        
                        if ($totalVotes === $totalParticipants) {
                            return "✅ Sesuai target";
                        }
                        
                        $diff = $totalParticipants - $totalVotes;
                        return "⚠️ Kurang {$diff} suara";
                    }),

                // Kolom Tanggal Dibuat
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),

                // Kolom Tanggal Update
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Filter berdasarkan Pemilihan
                Tables\Filters\SelectFilter::make('election_id')
                    ->label('Pemilihan')
                    ->relationship('election', 'name')
                    ->searchable()
                    ->preload()
                    ->native(false),

                // Filter berdasarkan Status
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Draft',
                        'active' => 'Aktif',
                        'invalid' => 'Invalid',
                        'completed' => 'Selesai',
                    ])
                    ->native(false),
            ])
           ->actions([
    // Action: Eliminasi Calon - PALING ATAS!
    Tables\Actions\Action::make('eliminate')
        ->label('Eliminasi')
        ->icon('heroicon-o-fire')
        ->color('danger')
        ->requiresConfirmation()
        ->modalHeading('Eliminasi Calon')
        ->modalDescription(function (Round $record) {
            $service = new CandidateEliminationService();
            $results = $service->getVotingResults($record);
            
            $description = "Hasil Voting Putaran {$record->round_number}:\n\n";
            foreach ($results as $result) {
                $status = $result['will_be_eliminated'] ? '❌ GUGUR' : '✅ LOLOS';
                $description .= "{$result['name']}: {$result['votes']} suara - {$status}\n";
            }
            $description .= "\nCalon dengan suara < 5 akan tersingkir. Lanjutkan?";
            
            return $description;
        })
        ->action(function (Round $record) {
            $service = new CandidateEliminationService();
            $result = $service->eliminate($record);

            if ($result['success']) {
                Notification::make()
                    ->title('Eliminasi Berhasil!')
                    ->body($result['message'])
                    ->success()
                    ->duration(20000)
                    ->send();
            } else {
                Notification::make()
                    ->title('Eliminasi Gagal!')
                    ->body($result['message'])
                    ->danger()
                    ->send();
            }
        })
        ->visible(fn (Round $record) => $record->status === 'completed' && !$record->is_eliminated),

    // Action: Activate Round
    Tables\Actions\Action::make('activate')
        ->label('Aktifkan')
        ->icon('heroicon-o-play')
        ->color('success')
        ->requiresConfirmation()
        ->modalHeading('Aktifkan Putaran')
        ->modalDescription('Yakin ingin mengaktifkan putaran ini? Putaran aktif lainnya akan dinonaktifkan.')
        ->action(function (Round $record) {
            if ($record->election->status === 'finished') {
                Notification::make()
                    ->title('Gagal!')
                    ->body('Tidak bisa mengaktifkan putaran karena pemilihan sudah selesai.')
                    ->danger()
                    ->send();
                return;
            }

            Round::where('election_id', $record->election_id)
                ->where('status', 'active')
                ->update(['status' => 'draft']);

            $record->update(['status' => 'active']);

            Notification::make()
                ->title('Berhasil!')
                ->body("Putaran {$record->round_number} telah diaktifkan.")
                ->success()
                ->send();
        })
        ->visible(fn (Round $record) => in_array($record->status, ['draft', 'invalid'])),

    // Action: Close Round
    Tables\Actions\Action::make('close')
        ->label('Tutup')
        ->icon('heroicon-o-check-circle')
        ->color('warning')
        ->requiresConfirmation()
        ->modalHeading('Tutup Putaran')
        ->modalDescription('Yakin ingin menutup putaran ini? Status akan diubah menjadi completed.')
        ->action(function (Round $record) {
            $record->update(['status' => 'completed']);

            Notification::make()
                ->title('Berhasil!')
                ->body("Putaran {$record->round_number} telah ditutup. Silakan lakukan validasi untuk memastikan jumlah suara.")
                ->success()
                ->send();
        })
        ->visible(fn (Round $record) => $record->status === 'active'),

    // Action: Validate Round
    Tables\Actions\Action::make('validate')
        ->label('Validasi')
        ->icon('heroicon-o-shield-check')
        ->color('info')
        ->requiresConfirmation()
        ->modalHeading('Validasi Putaran')
        ->modalDescription(function (Round $record) {
            $service = new RoundValidationService();
            $summary = $service->getRoundSummary($record);
            
            return "Jumlah peserta: {$summary['total_participants']} | Jumlah suara: {$summary['total_votes']} | Partisipasi: {$summary['percentage']}%";
        })
        ->action(function (Round $record) {
            $service = new RoundValidationService();
            $result = $service->validateRound($record);

            if ($result['is_valid']) {
                Notification::make()
                    ->title('PUTARAN SAH!')
                    ->body($result['message'])
                    ->success()
                    ->duration(10000)
                    ->send();
            } else {
                Notification::make()
                    ->title('PUTARAN TIDAK SAH!')
                    ->body($result['message'])
                    ->danger()
                    ->duration(15000)
                    ->send();
            }
        })
        ->visible(fn (Round $record) => $record->status === 'completed'),

    // Action: Reset for Revote
    Tables\Actions\Action::make('reset')
        ->label('Reset')
        ->icon('heroicon-o-arrow-path')
        ->color('gray')
        ->requiresConfirmation()
        ->modalHeading('Reset Putaran')
        ->modalDescription('Putaran akan di-reset ke status draft. Vote sebelumnya tetap tersimpan sebagai arsip.')
        ->action(function (Round $record) {
            $service = new RoundValidationService();
            $result = $service->resetRoundForRevote($record);

            if ($result['success']) {
                Notification::make()
                    ->title('Berhasil!')
                    ->body($result['message'])
                    ->success()
                    ->send();
            } else {
                Notification::make()
                    ->title('Gagal!')
                    ->body($result['message'])
                    ->danger()
                    ->send();
            }
        })
        ->visible(fn (Round $record) => $record->status === 'invalid'),

    // Standard Actions
    Tables\Actions\ViewAction::make(),
    Tables\Actions\EditAction::make(),
    Tables\Actions\DeleteAction::make(),
    
])
->bulkActions([
    Tables\Actions\BulkActionGroup::make([
        Tables\Actions\DeleteBulkAction::make(),
    ]),
])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            // Nanti untuk relasi lain jika diperlukan
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRounds::route('/'),
            'create' => Pages\CreateRound::route('/create'),
            'edit' => Pages\EditRound::route('/{record}/edit'),
        ];
    }
}