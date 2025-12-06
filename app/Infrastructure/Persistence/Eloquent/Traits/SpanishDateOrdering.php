<?php

namespace App\Infrastructure\Persistence\Eloquent\Traits;

use Illuminate\Database\Eloquent\Builder;

/**
 * Trait for ordering records by Spanish formatted dates
 * 
 * Provides reusable methods for ordering dates in format "Mes. YYYY"
 * (e.g., "Ene. 2023", "Sept. 2024")
 */
trait SpanishDateOrdering
{
    /**
     * Get the SQL CASE expression for ordering Spanish months
     */
    protected function getMonthOrderCase(string $column = 'start_date'): string
    {
        return "CASE 
            WHEN SUBSTR({$column}, 1, INSTR({$column}, '.') - 1) = 'Ene' THEN 1
            WHEN SUBSTR({$column}, 1, INSTR({$column}, '.') - 1) = 'Feb' THEN 2
            WHEN SUBSTR({$column}, 1, INSTR({$column}, '.') - 1) = 'Mar' THEN 3
            WHEN SUBSTR({$column}, 1, INSTR({$column}, '.') - 1) = 'Abr' THEN 4
            WHEN SUBSTR({$column}, 1, INSTR({$column}, '.') - 1) = 'May' THEN 5
            WHEN SUBSTR({$column}, 1, INSTR({$column}, '.') - 1) = 'Jun' THEN 6
            WHEN SUBSTR({$column}, 1, INSTR({$column}, '.') - 1) = 'Jul' THEN 7
            WHEN SUBSTR({$column}, 1, INSTR({$column}, '.') - 1) = 'Ago' THEN 8
            WHEN SUBSTR({$column}, 1, INSTR({$column}, '.') - 1) = 'Sept' THEN 9
            WHEN SUBSTR({$column}, 1, INSTR({$column}, '.') - 1) = 'Oct' THEN 10
            WHEN SUBSTR({$column}, 1, INSTR({$column}, '.') - 1) = 'Nov' THEN 11
            WHEN SUBSTR({$column}, 1, INSTR({$column}, '.') - 1) = 'Dic' THEN 12
            ELSE 0 END";
    }

    /**
     * Apply date ordering to a query (year DESC, month DESC)
     */
    protected function applyDateOrdering(Builder $query, string $column = 'start_date'): Builder
    {
        return $query
            ->orderByRaw("CAST(SUBSTR({$column}, -4) AS INTEGER) DESC")
            ->orderByRaw($this->getMonthOrderCase($column) . ' DESC');
    }

    /**
     * Apply year range filter for a given year
     */
    protected function applyYearFilter(Builder $query, int $year, string $startColumn = 'start_date', string $endColumn = 'end_date'): Builder
    {
        return $query->where(function ($q) use ($year, $startColumn, $endColumn) {
            $q->whereRaw("CAST(SUBSTR({$startColumn}, -4) AS INTEGER) <= ?", [$year])
                ->where(function ($subQ) use ($year, $endColumn) {
                    $subQ->whereNull($endColumn)
                        ->orWhereRaw("CAST(SUBSTR({$endColumn}, -4) AS INTEGER) >= ?", [$year]);
                });
        });
    }
}
