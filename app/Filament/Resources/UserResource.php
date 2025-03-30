<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Filament\Resources\UserResource\RelationManagers\CartsRelationManager;
use App\Filament\Resources\UserResource\RelationManagers\MedicalHistoriesRelationManager;
use App\Filament\Resources\UserResource\RelationManagers\OrdersRelationManager;
use App\Models\Cart;
use App\Models\Order;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    public static function getNavigationLabel(): string
    {
        return __('user_fields.patients');
    }

    public static function getPluralLabel(): string
    {
        return __('user_fields.patients');
    }

    public static function getModelLabel(): string
    {
        return __('user_fields.patient');
    }

    protected static ?string $navigationIcon = 'heroicon-o-user';

    protected static ?string $activeNavigationIcon = 'heroicon-s-user';

    protected static ?string $recordTitleAttribute = 'first_name';

    public static function getNavigationGroup(): ?string
    {
        return __('user_fields.patients');
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('first_name')->required()->label(__('user_fields.first_name')),
                Forms\Components\TextInput::make('last_name')->required()->label(__('user_fields.last_name')),
                Forms\Components\TextInput::make('email')->email()->required()->label(__('user_fields.email'))->unique('users', 'email', ignoreRecord: true),
                Forms\Components\TextInput::make('password')->password()->required()->label(__('user_fields.password')),
                Forms\Components\TextInput::make('phone_number')->tel()->required()->label(__('user_fields.phone_number'))->unique('users', 'phone_number', ignoreRecord: true),
                Forms\Components\Select::make('gender')->options(
                    [
                        'male' =>   __('user_fields.male'),
                        'female' =>   __('user_fields.female'),
                    ]
                )->required()->label(__('user_fields.gender')),
                Forms\Components\DatePicker::make('birth_date')->required()->label(__('user_fields.birth_date')),
                Forms\Components\TextInput::make('address')->required()->label(__('user_fields.address')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('fullName')->icon('heroicon-o-user')->iconColor('primary')->label('Full Name')->searchable()->label(__('user_fields.full_name')),
                Tables\Columns\TextColumn::make('email')->icon('heroicon-o-envelope')->iconColor('primary')->label('Email')->label(__('user_fields.email')),
                Tables\Columns\TextColumn::make('phone_number')->icon('heroicon-o-phone')->iconColor('primary')->label('Phone Number')->searchable()->label(__('user_fields.phone_number')),
                Tables\Columns\TextColumn::make('gender')->icon('heroicon-o-face-smile')->iconColor('primary')->label('Gender')->label(__('user_fields.gender')),
                Tables\Columns\TextColumn::make('address')->icon('heroicon-o-map-pin')->iconColor('primary')->label('Address')->label(__('user_fields.address')),
                // For Birth Date Column
                Tables\Columns\TextColumn::make('birth_date')
                    ->icon('heroicon-o-calendar')
                    ->iconColor('primary')
                    ->label('Birth Date')
                    ->date()
                    ->label(__('user_fields.birth_date')),
                Tables\Columns\TextColumn::make('age')
                    ->label('Age')
                    ->icon('heroicon-o-cake')
                    ->iconColor('primary')
                    ->getStateUsing(function ($record) {
                        $birthDate = $record->birth_date;

                        if ($birthDate) {
                            return \Carbon\Carbon::parse($birthDate)->age;
                        }

                        return 'N/A';
                    })
                    ->label(__('user_fields.age')),
            ])
            ->filters([
                SelectFilter::make('gender')
                    ->options([
                        'male' =>   __('user_fields.male'),
                        'female' =>   __('user_fields.female'),
                    ])
                    ->label(__('user_fields.gender'))
                    ->attribute('gender'),
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
            MedicalHistoriesRelationManager::class,
            CartsRelationManager::class,
            OrdersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
