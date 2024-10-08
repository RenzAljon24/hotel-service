<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoomResource\Pages;
use App\Filament\Resources\RoomResource\RelationManagers;
use App\Models\Room;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RoomResource extends Resource
{
    protected static ?string $model = Room::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make()

            ->schema([
                Forms\Components\TextInput::make('room_name')
                ->required()
                ->maxLength(255),
                Forms\Components\Select::make('type')
                ->required()
                ->options([
                    'single' => 'Single',
                    'double' => 'Double',
                    'suite' => 'Suite',
                ]),
                Forms\Components\Textarea::class::make('description')
                ->required()
                ->maxLength(2000),
                Forms\Components\TextInput::make('price')
                ->required()
                ->numeric(),
                Forms\Components\FileUpload::make('image')
                ->disk('public')
                ->directory('room_images')
                ->preserveFilenames()
            ])
        ->columns(1), // Set to 1 to create a single-column grid
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('room_name'),
                Tables\Columns\TextColumn::make('type'),
                Tables\Columns\TextColumn::make('price'),
                Tables\Columns\TextColumn::make('description')->html(),
                Tables\Columns\ImageColumn::make('image'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRooms::route('/'),
            'create' => Pages\CreateRoom::route('/create'),
            'edit' => Pages\EditRoom::route('/{record}/edit'),
        ];
    }
}
