<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;
use STS\FilamentImpersonate\Actions\Impersonate as ImpersonatePageAction;
use Filament\Actions\Action;
use App\Models\AuditLog;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ImpersonatePageAction::make()->record($this->getRecord()),
            Action::make('clear_avatar')
                ->label('Clear Avatar')
                ->requiresConfirmation()
                ->action(function (): void {
                    $record = $this->getRecord();
                    $record->clearMediaCollection('avatar');
                    AuditLog::create([
                        'subject_type' => \App\Models\User::class,
                        'subject_id' => $record->id,
                        'action' => 'avatar_cleared',
                        'changes' => [],
                        'user_id' => optional(auth()->user())->id,
                    ]);
                }),
            Action::make('clear_attachments')
                ->label('Clear Attachments')
                ->requiresConfirmation()
                ->action(function (): void {
                    $record = $this->getRecord();
                    $record->clearMediaCollection('attachments');
                    AuditLog::create([
                        'subject_type' => \App\Models\User::class,
                        'subject_id' => $record->id,
                        'action' => 'attachments_cleared',
                        'changes' => [],
                        'user_id' => optional(auth()->user())->id,
                    ]);
                }),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
