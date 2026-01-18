<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CandidateResource\Pages;
use App\Models\Candidate;
use App\Models\Election;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CandidateResource extends Resource
{
    protected static ?string $model = Candidate::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    
    protected static ?string $navigationLabel = 'Calon';
    
    protected static ?string $modelLabel = 'Calon';
    
    protected static ?string $pluralModelLabel = 'Calon';

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
                    ->options(Election::all()->pluck('name', 'id'))
                    ->required()
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->helperText('Pilih pemilihan yang akan diikuti calon ini'),

                // Input Nama Calon
                Forms\Components\TextInput::make('name')
                    ->label('Nama Calon')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Contoh: John Doe'),

                // Upload Foto Calon
                Forms\Components\FileUpload::make('photo')
                    ->label('Foto Calon')
                    ->image()
                    ->disk('public')
                    ->directory('candidates')
                    ->visibility('public')
                    ->maxSize(2048) // Max 2MB
                    ->imageEditor()
                    ->imageEditorAspectRatios([
                        '1:1',
                        '4:3',
                    ])
                    ->helperText('Upload foto calon (opsional). Maksimal 2MB.'),

                // Status (Read-only di form edit, hidden di create)
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Aktif',
                        'eliminated' => 'Tersingkir',
                    ])
                    ->default('active')
                    ->disabled() // Read-only, tidak bisa diubah manual
                    ->dehydrated(true) // Tetap kirim value ke database
                    ->hiddenOn('create') // Sembunyikan saat create
                    ->native(false),
            ]);
    }

    /**
     * TABLE SCHEMA - Untuk menampilkan data
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Kolom Foto (Thumbnail)
                Tables\Columns\ImageColumn::make('photo')
                    ->label('Foto')
                    ->circular()
                    ->defaultImageUrl(url('/images/default-avatar.png'))
                    ->size(50),

                // Kolom Nama Calon
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Calon')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                // Kolom Pemilihan (dengan relasi)
                Tables\Columns\TextColumn::make('election.name')
                    ->label('Pemilihan')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),

                // Kolom Status dengan Badge
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'success' => 'active',
                        'danger' => 'eliminated',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Aktif',
                        'eliminated' => 'Tersingkir',
                        default => $state,
                    }),

                // Kolom Tanggal Dibuat
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

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
                        'active' => 'Aktif',
                        'eliminated' => 'Tersingkir',
                    ])
                    ->native(false),
            ])
            ->actions([
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
            'index' => Pages\ListCandidates::route('/'),
            'create' => Pages\CreateCandidate::route('/create'),
            'edit' => Pages\EditCandidate::route('/{record}/edit'),
        ];
    }
}