<?php

namespace App\Filament\Widgets;

use DB;
use App\Models\Product;
use App\Models\OrderProduct;
use Carbon\Carbon;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget as BaseWidget;

class ProductFavorite extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 5;
    protected static ?string $heading = 'Produk Ter-favorit';
    public function table(Table $table): Table
    {

        $startDate = now()->startOfDay();
        $endDate = now()->endOfDay();

        if (!is_null($this->filters['startDate'] ?? null)) {
            $startDate = Carbon::parse($this->filters['startDate'])->startOfDay();
        }

        if (!is_null($this->filters['endDate'] ?? null)) {
            $endDate = Carbon::parse($this->filters['endDate'])->endOfDay();
        }
        
        $productsQuery = Product::query()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->withCount('orderProducts')
            ->orderByDesc('order_products_count')
            ->take(10);
        return $table
            ->query($productsQuery)
            ->columns([
                Tables\Columns\ImageColumn::make('image'),
                Tables\Columns\TextColumn::make('name')
                    ->description(fn (Product $record): string => ($record->category) ? $record->category->name : '-')
                    ->searchable(),
                Tables\Columns\TextColumn::make('order_products_count')
                    ->label('Dipesan') // Label untuk kolom jumlah pesanan
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->money('Rp.')
                    ->sortable(),
            ])->defaultPaginationPageOption(5);
    }
}
