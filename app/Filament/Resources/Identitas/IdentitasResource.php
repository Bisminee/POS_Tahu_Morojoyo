<?php

namespace App\Filament\Resources\Identitas;

use App\Filament\Resources\Identitas\Pages\CreateIdentitas;
use App\Filament\Resources\Identitas\Pages\EditIdentitas;
use App\Filament\Resources\Identitas\Pages\ListIdentitas;
use App\Models\Identitas;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class IdentitasResource extends Resource
{
    protected static ?string $model = Identitas::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingStorefront;
    protected static string | \UnitEnum | null $navigationGroup = 'Master Data';

    protected static ?string $navigationLabel = 'Identitas';
    protected static ?string $modelLabel = 'Identitas';
    protected static ?string $pluralModelLabel = 'Identitas';

    protected static ?string $recordTitleAttribute = 'nama_brand';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama_brand')
                    ->label('Nama Brand')
                    ->required()
                    ->maxLength(255),

                Textarea::make('deskripsi_brand')
                    ->label('Deskripsi Brand')
                    ->rows(4)
                    ->required(),

                TextInput::make('nomor_whatsapp')
                    ->label('Nomor WhatsApp')
                    ->tel()
                    ->required(),

                TextInput::make('nama_ig')
                    ->label('Nama Instagram')
                    ->required(),

                TextInput::make('link_wa')
                    ->label('Link WhatsApp')
                    ->url()
                    ->required(),

                TextInput::make('link_ig')
                    ->label('Link Instagram')
                    ->url()
                    ->required(),

                TimePicker::make('jam_buka')
                    ->label('Jam Buka')
                    ->required(),

                TimePicker::make('jam_tutup')
                    ->label('Jam Tutup')
                    ->required(),

                FileUpload::make('logo')
                    ->label('Logo')
                    ->image()
                    ->disk('public')
                    ->directory('logo')
                    ->imagePreviewHeight('150')
                    ->nullable(),

                FileUpload::make('promo')
                    ->label('Promo')
                    ->image()
                    ->disk('public')
                    ->directory('promo')
                    ->imagePreviewHeight('150')
                    ->nullable(),
            ]); // ← ini yang hilang
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('logo')
                    ->label('Logo')
                    ->disk('public'),

                TextColumn::make('nama_brand')
                    ->label('Nama Brand')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('nomor_whatsapp')
                    ->label('WhatsApp')
                    ->searchable(),

                TextColumn::make('nama_ig')
                    ->label('Instagram'),

                TextColumn::make('jam_buka')
                    ->label('Jam Buka')
                    ->time(),

                TextColumn::make('jam_tutup')
                    ->label('Jam Tutup')
                    ->time(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListIdentitas::route('/'),
            'create' => CreateIdentitas::route('/create'),
            'edit' => EditIdentitas::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        $user = Auth::user();
        return $user && $user->role !== 'karyawan';
    }

    public static function canCreate(): bool
    {
        $user = Auth::user();
        return $user && $user->role !== 'karyawan';
    }

    public static function canEdit($record): bool
    {
        $user = Auth::user();
        return $user && $user->role !== 'karyawan';
    }

    public static function canDelete($record): bool
    {
        $user = Auth::user();
        return $user && $user->role !== 'karyawan';
    }
}
