<?php

namespace App\Filament\Resources\Roles\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use App\Filament\Resources\Roles\RoleResource as RolesRoleResource;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Facades\Filament;

class RoleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label('Name')
                                    ->required()
                                    ->unique(ignoreRecord: true),
                                TextInput::make('guard_name')
                                    ->label('Guard')
                                    ->default(Utils::getFilamentAuthGuard())
                                    ->nullable(),
                            ]),
                        RolesRoleResource::getSelectAllFormComponent(),
                    ]),
                RolesRoleResource::getShieldFormComponents(),
            ]);
    }
}
