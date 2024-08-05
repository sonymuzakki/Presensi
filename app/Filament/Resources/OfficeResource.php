<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Office;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Humaidem\FilamentMapPicker\Fields\OSMMap;
// use Humaidem\FilamentMapPicker\Fields\OSMMap;
use App\Filament\Resources\OfficeResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\OfficeResource\RelationManagers;

class OfficeResource extends Resource
{
    protected static ?string $model = Office::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?int $navigationSort=1;

    // public static function form(Form $form): Form
    // {
    //     return $form
    //         ->schema([
    //             Forms\Components\TextInput::make('name')
    //                 ->required()
    //                 ->maxLength(255),
    //             // OSMMap::make('location')
    //             //     ->label('Location')
    //             //     ->showMarker()
    //             //     ->draggable()
    //             //     ->extraControl([
    //             //         'zoomDelta'           => 1,
    //             //         'zoomSnap'            => 0.25,
    //             //         'wheelPxPerZoomLevel' => 60
    //             //     ])
    //             //     ->afterStateHydrated(function (Forms\Get $get , Forms\Set $set, $record){
    //             //         if($record) {
    //             //             $latitude = $record->latitude;
    //             //             $longitude =  $record->longitude;

    //             //             // dd($latitude,$longitude);

    //             //             if($latitude && $longitude){
    //             //                 $set('location', ['lat' => $latitude, 'lng' => $longitude]);
    //             //             }
    //             //         }
    //             //     })
    //             //     ->afterStateUpdated(function ($state, Forms\Get $get, Forms\Set $set){
    //             //         $set('latitude', $state['lat']);
    //             //         $set('longitude', $state['lng']);
    //             //     })
    //             //     // tiles url (refer to https://www.spatialbias.com/2018/02/qgis-3.0-xyz-tile-layers/)
    //             //     ->tilesUrl('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}'),
    //             Forms\Components\TextInput::make('latitude')
    //                 ->required()
    //                 ->numeric(),
    //             Forms\Components\TextInput::make('longitude')
    //                 ->required()
    //                 ->numeric(),
    //             Forms\Components\TextInput::make('radius')
    //                 ->required()
    //                 ->numeric(),
    //         ]);
    // }

    // public static function table(Table $table): Table
    // {
    //     return $table
    //         ->columns([
    //             Tables\Columns\TextColumn::make('name')
    //                 ->searchable(),
    //             Tables\Columns\TextColumn::make('latitude')
    //                 ->numeric()
    //                 ->sortable(),
    //             Tables\Columns\TextColumn::make('logitude')
    //                 ->numeric()
    //                 ->sortable(),
    //             Tables\Columns\TextColumn::make('radius')
    //                 ->numeric()
    //                 ->sortable(),
    //             Tables\Columns\TextColumn::make('created_at')
    //                 ->dateTime()
    //                 ->sortable()
    //                 ->toggleable(isToggledHiddenByDefault: true),
    //             Tables\Columns\TextColumn::make('updated_at')
    //                 ->dateTime()
    //                 ->sortable()
    //                 ->toggleable(isToggledHiddenByDefault: true),
    //             Tables\Columns\TextColumn::make('deleted_at')
    //                 ->dateTime()
    //                 ->sortable()
    //                 ->toggleable(isToggledHiddenByDefault: true),
    //         ])
    //         ->filters([
    //             //
    //         ])
    //         ->actions([
    //             Tables\Actions\EditAction::make(),
    //         ])
    //         ->bulkActions([
    //             Tables\Actions\BulkActionGroup::make([
    //                 Tables\Actions\DeleteBulkAction::make(),
    //             ]),
    //         ]);
    // }

    // public static function getRelations(): array
    // {
    //     return [
    //         //
    //     ];
    // }

    // public static function getPages(): array
    // {
    //     return [
    //         'index' => Pages\ListOffices::route('/'),
    //         'create' => Pages\CreateOffice::route('/create'),
    //         'edit' => Pages\EditOffice::route('/{record}/edit'),
    //     ];
    // }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                OSMMap::make('location')
                                    ->label('Location')
                                    ->showMarker()
                                    ->draggable()
                                    ->extraControl([
                                        'zoomDelta'           => 1,
                                        'zoomSnap'            => 0.25,
                                        'wheelPxPerZoomLevel' => 60
                                    ])
                                    // ->reactive()
                                    ->afterStateUpdated(function ($state, Forms\Get $get, Forms\Set $set) {
                                        if (isset($state['lat']) && isset($state['lng'])) {
                                            $set('latitude', $state['lat']);
                                            $set('longitude', $state['lng']);
                                        }
                                    })
                                    ->tilesUrl('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}'),
                                Forms\Components\Group::make()
                                    ->schema([
                                        Forms\Components\TextInput::make('latitude')
                                            ->required()
                                            ->numeric()
                                            ->reactive()
                                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                                $set('location.lat', $state);
                                            }),
                                        Forms\Components\TextInput::make('longitude')
                                            ->required()
                                            ->numeric()
                                            ->reactive()
                                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                                $set('location.lng', $state);
                                            }),
                                    ])

                            ])

                    ]),
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema([


                                Forms\Components\Group::make()
                                    ->schema([

                                        Forms\Components\TextInput::make('radius')
                                            ->required()
                                            ->numeric(),
                                    ]),
                            ])
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('latitude')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('longitude')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('radius')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListOffices::route('/'),
            'create' => Pages\CreateOffice::route('/create'),
            'edit' => Pages\EditOffice::route('/{record}/edit'),
        ];
    }
}
