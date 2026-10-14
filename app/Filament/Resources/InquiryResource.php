<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InquiryResource\Pages;
use App\Models\Destination;
use App\Models\Inquiry;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InquiryResource extends Resource
{
    protected static ?string $model = Inquiry::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox-arrow-down';

    protected static ?string $navigationGroup = 'Sales';

    protected static ?string $navigationLabel = 'Inquiries';

    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        $new = Inquiry::new()->count();

        return $new ? (string) $new : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function canCreate(): bool
    {
        return false;   // leads come from the website forms
    }

    private const STATUSES = ['new' => 'New', 'contacted' => 'Contacted', 'confirmed' => 'Confirmed', 'cancelled' => 'Cancelled'];

    public static function form(Form $form): Form
    {
        return $form->columns(2)->schema([
            Forms\Components\Section::make('Lead')->columns(2)->columnSpanFull()->schema([
                Forms\Components\Select::make('status')->options(self::STATUSES)->required(),
                Forms\Components\Placeholder::make('type')->content(fn (?Inquiry $record) => ucfirst((string) $record?->type)),
                Forms\Components\Placeholder::make('name')->content(fn (?Inquiry $record) => $record?->name),
                Forms\Components\Placeholder::make('phone')->content(fn (?Inquiry $record) => $record?->phone),
                Forms\Components\Placeholder::make('email')->content(fn (?Inquiry $record) => $record?->email ?: '—'),
                Forms\Components\Placeholder::make('received')->content(fn (?Inquiry $record) => $record?->created_at?->format('M j, Y g:i A')),
            ]),
            Forms\Components\Section::make('Request')->columns(2)->columnSpanFull()->schema([
                Forms\Components\Placeholder::make('destination')->content(fn (?Inquiry $record) => $record?->destination?->getTranslation('name', 'en') ?? '—'),
                Forms\Components\Placeholder::make('package')->content(fn (?Inquiry $record) => $record?->package?->getTranslation('name', 'en') ?? '—'),
                Forms\Components\Placeholder::make('topic')->content(fn (?Inquiry $record) => $record?->topic ?? '—'),
                Forms\Components\Placeholder::make('travel')->label('Travel date')->content(fn (?Inquiry $record) => $record?->travel_date?->format('M j, Y') ?? '—'),
                Forms\Components\Placeholder::make('guests')->content(fn (?Inquiry $record) => $record?->guests ?? '—'),
                Forms\Components\Placeholder::make('estimate')->content(fn (?Inquiry $record) => $record?->estimated_total ? '৳'.number_format($record->estimated_total) : '—'),
                Forms\Components\Placeholder::make('message')->columnSpanFull()->content(fn (?Inquiry $record) => $record?->message ?: '—'),
            ]),
            Forms\Components\Textarea::make('admin_notes')->label('Internal notes')->rows(4)->columnSpanFull()
                ->helperText('Only the team sees this.'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')->label('Received')->since()->sortable(),
                Tables\Columns\TextColumn::make('type')->badge()->color(fn (string $state) => $state === 'booking' ? 'primary' : 'gray'),
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('phone')->searchable()->copyable(),
                Tables\Columns\TextColumn::make('destination.name')->label('Destination')->placeholder('—'),
                Tables\Columns\TextColumn::make('travel_date')->date()->placeholder('—'),
                Tables\Columns\TextColumn::make('estimated_total')->money('BDT')->label('Estimate')->placeholder('—'),
                Tables\Columns\TextColumn::make('status')->badge()->color(fn (string $state) => match ($state) {
                    'new' => 'danger', 'contacted' => 'warning', 'confirmed' => 'success', default => 'gray',
                }),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(self::STATUSES),
                Tables\Filters\SelectFilter::make('type')->options(['booking' => 'Booking', 'contact' => 'Contact']),
                Tables\Filters\SelectFilter::make('destination_id')->label('Destination')->options(
                    fn () => Destination::published()->get()->mapWithKeys(fn (Destination $d) => [$d->id => $d->getTranslation('name', 'en')])->all()
                ),
            ])
            ->actions([
                Tables\Actions\Action::make('whatsapp')->label('WhatsApp')->icon('heroicon-o-chat-bubble-left-right')->color('success')
                    ->url(fn (Inquiry $record) => 'https://wa.me/'.ltrim(preg_replace('/\D+/', '', $record->phone), '0'))->openUrlInNewTab(),
                Tables\Actions\EditAction::make()->label('Open'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    ...collect(['contacted' => 'Mark as contacted', 'confirmed' => 'Mark as confirmed', 'cancelled' => 'Mark as cancelled'])
                        ->map(fn (string $label, string $status) => Tables\Actions\BulkAction::make("mark_{$status}")->label($label)
                            ->action(fn (Collection $records) => $records->each->update(['status' => $status]))
                            ->deselectRecordsAfterCompletion())->values()->all(),
                    Tables\Actions\BulkAction::make('export')->label('Export selected (CSV)')->icon('heroicon-o-arrow-down-tray')
                        ->action(fn (Collection $records) => self::csv($records))->deselectRecordsAfterCompletion(),
                ]),
            ]);
    }

    /** CSV download of the given inquiries (opens in Excel / Google Sheets). */
    public static function csv(iterable $records): StreamedResponse
    {
        return response()->streamDownload(function () use ($records) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");   // UTF-8 BOM so Excel reads Bangla names correctly
            fputcsv($out, ['Received', 'Type', 'Status', 'Name', 'Phone', 'Email', 'Destination', 'Package', 'Topic', 'Travel date', 'Guests', 'Estimate (BDT)', 'Message', 'Language', 'Notes']);

            foreach ($records as $i) {
                fputcsv($out, [
                    $i->created_at?->format('Y-m-d H:i'), $i->type, $i->status, self::safe($i->name), "\t".$i->phone, self::safe($i->email),
                    $i->destination?->getTranslation('name', 'en'), $i->package?->getTranslation('name', 'en'), $i->topic,
                    $i->travel_date?->format('Y-m-d'), $i->guests, $i->estimated_total, self::safe($i->message), $i->locale, self::safe($i->admin_notes),
                ]);
            }

            fclose($out);
        }, 'inquiries-'.now()->format('Y-m-d').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /** Stops spreadsheet formulas ("=1+1", "@SUM") in visitor-written text from running when the CSV is opened. */
    private static function safe(?string $value): ?string
    {
        return $value !== null && preg_match('/^[=+\-@\t\r]/', $value) ? "'".$value : $value;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInquiries::route('/'),
            'edit' => Pages\EditInquiry::route('/{record}/edit'),
        ];
    }
}
