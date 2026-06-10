<?php

namespace App\Filament\Resources\Payments\Schemas;

use Filament\Schemas\Schema;

class PaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                \Filament\Forms\Components\Select::make('plan_id')
                    ->relationship('plan', 'name')
                    ->required(),
                \Filament\Forms\Components\Select::make('payment_gateway_id')
                    ->relationship('paymentGateway', 'name')
                    ->required(),
                \Filament\Forms\Components\TextInput::make('amount')
                    ->numeric()
                    ->required()
                    ->prefix('$'),
                \Filament\Forms\Components\TextInput::make('invoice_id')
                    ->label('Invoice ID')
                    ->maxLength(255),
                \Filament\Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'completed' => 'Completed',
                        'failed' => 'Failed',
                        'rejected' => 'Rejected',
                    ])
                    ->required()
                    ->default('pending'),
                \Filament\Forms\Components\Placeholder::make('user_provided_data_view')
                    ->label('User Provided Details / Gateway Meta')
                    ->content(function ($record) {
                        if (! $record || empty($record->user_provided_data)) return 'None';
                        return new \Illuminate\Support\HtmlString(
                            '<pre style="font-size: 0.75rem; padding: 0.5rem; background-color: #f3f4f6; border-radius: 0.5rem; overflow-x: auto;">' . 
                            e(json_encode($record->user_provided_data, JSON_PRETTY_PRINT)) . 
                            '</pre>'
                        );
                    })
                    ->columnSpanFull(),
            ]);
    }
}
