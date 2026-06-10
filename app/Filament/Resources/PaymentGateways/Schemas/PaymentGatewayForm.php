<?php

namespace App\Filament\Resources\PaymentGateways\Schemas;

use Filament\Schemas\Schema;

class PaymentGatewayForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                \Filament\Forms\Components\Select::make('type')
                    ->options([
                        'automated' => 'Automated (e.g. UddoktaPay)',
                        'manual' => 'Manual (e.g. Binance, Bank Transfer)',
                    ])
                    ->required()
                    ->default('manual')
                    // Only allow changing to 'automated' if the gateway is already automated (i.e. seeded)
                    ->disabled(fn (?string $operation, ?\App\Models\PaymentGateway $record) => $operation === 'create' || ($record && $record->type === 'automated'))
                    ->dehydrated(),
                \Filament\Forms\Components\Textarea::make('payment_instructions')
                    ->label('Payment Instructions (Shown to Users)')
                    ->helperText('e.g., "Send money to this Binance address..."')
                    ->hidden(fn ($get) => $get('type') === 'automated'),

                \Filament\Forms\Components\KeyValue::make('credentials')
                    ->label('Credentials / API Keys / Wallet Info')
                    ->keyLabel('Key')
                    ->valueLabel('Value'),

                \Filament\Forms\Components\Repeater::make('user_input_fields')
                    ->label('Dynamic User Input Fields (For Manual Gateways)')
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('name')
                            ->label('Field Name')
                            ->required()
                            ->helperText('e.g. Sender Number, Payment Screenshot'),
                        \Filament\Forms\Components\Select::make('type')
                            ->label('Field Type')
                            ->options([
                                'text' => 'Text Input',
                                'file' => 'File Upload (Image/PDF)'
                            ])
                            ->required()
                            ->default('text'),
                        \Filament\Forms\Components\Toggle::make('required')
                            ->label('Is Required?')
                            ->default(true),
                    ])
                    ->columns(3)
                    ->hidden(fn ($get) => $get('type') === 'automated'),
                \Filament\Forms\Components\FileUpload::make('logo_path')
                    ->image()
                    ->directory('gateways'),
                \Filament\Forms\Components\Toggle::make('is_active')
                    ->required()
                    ->default(true),
            ]);
    }
}
