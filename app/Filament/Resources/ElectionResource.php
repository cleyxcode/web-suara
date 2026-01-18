<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ElectionResource\Pages;
use App\Models\Election;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ElectionResource extends Resource
{
    protected static ?string $model = Election::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    
    protected static ?string $navigationLabel = 'Pemilihan';
    
    protected static ?string $modelLabel = 'Pemilihan';
    
    protected static ?string $pluralModelLabel = 'Pemilihan';

    /**
     * FORM SCHEMA - Untuk Create & Edit
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Input Nama Pemilihan
                Forms\Components\TextInput::make('name')
                    ->label('Nama Pemilihan')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Contoh: Pemilihan Ketua Ranting 2024'),

                // Input Jumlah Peserta
                Forms\Components\TextInput::make('total_participants')
                    ->label('Jumlah Total Peserta')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->default(0)
                    ->helperText('Masukkan jumlah peserta yang berhak memilih'),

                // Select Status
                Forms\Components\Select::make('status')
                    ->label('Status Pemilihan')
                    ->options([
                        'draft' => 'Draft',
                        'active' => 'Aktif',
                        'finished' => 'Selesai',
                    ])
                    ->default('draft')
                    ->required()
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
                // Kolom Nama
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Pemilihan')
                    ->searchable()
                    ->sortable(),

                // Kolom Jumlah Peserta
                Tables\Columns\TextColumn::make('total_participants')
                    ->label('Total Peserta')
                    ->numeric()
                    ->sortable()
                    ->alignCenter(),

                // Kolom Status dengan Badge
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'secondary' => 'draft',
                        'success' => 'active',
                        'danger' => 'finished',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'Draft',
                        'active' => 'Aktif',
                        'finished' => 'Selesai',
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
                // Filter berdasarkan Status
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Draft',
                        'active' => 'Aktif',
                        'finished' => 'Selesai',
                    ])
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // Nanti untuk relasi ke model lain (kandidat, dll)
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListElections::route('/'),
            'create' => Pages\CreateElection::route('/create'),
            'edit' => Pages\EditElection::route('/{record}/edit'),
        ];
    }
}