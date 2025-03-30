<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeResource\Pages;
use App\Filament\Resources\EmployeeResource\RelationManagers;
use App\Models\Employee;
use DiscoveryDesign\FilamentGaze\Forms\Components\GazeBanner;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Rmsramos\Activitylog\Actions\ActivityLogTimelineTableAction;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = 'heroicon-o-identification';

    protected static ?string $activeNavigationIcon = 'heroicon-s-identification';

    public static function getNavigationGroup(): ?string
    {
        return __('employee_fields.pharmacy');
    }

    public static function getNavigationLabel(): string
    {
        return __('employee_fields.employees');
    }

    public static function getPluralLabel(): string
    {
        return __('employee_fields.employees');
    }

    public static function getModelLabel(): string
    {
        return __('employee_fields.employee');
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    protected static ?string $recordTitleAttribute = 'first_name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                GazeBanner::make()
                    ->lock()
                    ->canTakeControl(fn() => in_array('admin', Auth::guard('employee')->user()->role))
                    ->pollTimer(7)
                    ->columnSpan(2),
                Forms\Components\TextInput::make('first_name')->required()->label(__('employee_fields.first_name')),
                Forms\Components\TextInput::make('last_name')->required()->label(__('employee_fields.last_name')),
                FormS\Components\TextInput::make('email')->email()->required()->label(__('employee_fields.email'))->unique('employees', 'email', ignoreRecord: true),
                Forms\Components\TextInput::make('password')->password()->required()->label(__('employee_fields.password')),
                Forms\Components\TextInput::make('phone_number')->tel()->required()->label(__('employee_fields.phone_number'))->unique('employees', 'phone_number', ignoreRecord: true),
                Forms\Components\Select::make('gender')->options([
                    'male' =>   __('user_fields.male'),
                    'female' =>   __('user_fields.female'),
                ])
                    ->required()->label(__('employee_fields.gender')),
                Forms\Components\Select::make('day')->options([
                    'saturday' => __('employee_fields.saturday'),
                    'sunday' => __('employee_fields.sunday'),
                    'monday' => __('employee_fields.monday'),
                    'tuesday' => __('employee_fields.tuesday'),
                    'wednesday' => __('employee_fields.wednesday'),
                    'thursday' => __('employee_fields.thursday'),
                    'friday' => __('employee_fields.friday'),
                ])->multiple()->required()->label(__('employee_fields.days')),
                Forms\Components\Select::make('shift')->options([
                    'day' =>   __('employee_fields.day'),
                    'night' =>   __('employee_fields.night'),
                ])->required()->label(__('employee_fields.shift')),
                Forms\Components\DatePicker::make('birth_date')->required()->label(__('employee_fields.birth_date')),
                Forms\Components\Select::make('role')
                    ->options(function () {
                        $roles = [
                            'admin' => __('employee_fields.admin'),
                            'pharmacist' => __('employee_fields.pharmacist'),
                            'sales manager' => __('employee_fields.sales_manager'),
                            'hr' => __('employee_fields.hr'),
                        ];

                        $loggedInEmployee = Auth::guard('employee')->user();

                        if (!in_array('admin', $loggedInEmployee->role ?? [])) {
                            unset($roles['admin']);
                        }

                        return $roles;
                    })
                    ->multiple()
                    ->required()
                    ->label(__('employee_fields.role')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('fullName')->icon('heroicon-o-user')->iconColor('primary')->label(__('employee_fields.full_name')),
                Tables\Columns\TextColumn::make('email')->icon('heroicon-o-envelope')->iconColor('primary')->label(__('employee_fields.email')),
                Tables\Columns\TextColumn::make('phone_number')->icon('heroicon-o-phone')->iconColor('primary')->label(__('employee_fields.phone_number')),
                Tables\Columns\TextColumn::make('gender')->icon('heroicon-o-face-smile')->iconColor('primary')->label(__('employee_fields.gender')),
                Tables\Columns\TextColumn::make('day')->icon('heroicon-o-calendar')->iconColor('primary')->label(__('employee_fields.days')),
                Tables\Columns\TextColumn::make('shift')->icon('heroicon-o-clock')->iconColor('primary')->label(__('employee_fields.shift')),
                Tables\Columns\TextColumn::make('role')->icon('heroicon-o-shield-check')->iconColor('primary')->label(__('employee_fields.role')),
                Tables\Columns\TextColumn::make('birth_date')->icon('heroicon-o-calendar')->iconColor('primary')->date()->label(__('employee_fields.birth_date')),
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
                    ->label(__('employee_fields.gender'))
                    ->attribute('gender'),
                SelectFilter::make('shift')
                    ->options([
                        'day' =>   __('employee_fields.day'),
                        'night' =>   __('employee_fields.night'),
                    ])
                    ->label(__('employee_fields.shift'))
                    ->attribute('shift'),
                SelectFilter::make('role')
                    ->options([
                        'admin' => __('employee_fields.admin'),
                        'pharmacist' => __('employee_fields.pharmacist'),
                        'sales_manager' => __('employee_fields.sales_manager'),
                        'hr' => __('employee_fields.hr'),
                    ])->label(__('employee_fields.role'))->visible(
                        fn() => in_array('admin', Auth::guard('employee')->user()->role),
                    )
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->visible(
                        fn($record) =>
                        auth()->id() !== $record->id &&
                            !(in_array('hr', auth()->user()->role) && in_array('admin', $record->role))
                    ),
                ActivityLogTimelineTableAction::make(__('employee_fields.activity_log'))
                    ->visible(
                        fn() => in_array('admin', Auth::guard('employee')->user()->role),
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }
}
