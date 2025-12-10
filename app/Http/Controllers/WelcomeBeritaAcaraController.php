<?php

namespace App\Http\Controllers;

use App\Models\BeritaAcara;
use Illuminate\Http\Request;

class WelcomeBeritaAcaraController extends Controller
{
    public function __invoke(Request $request)
    {
        $search = $request->input('search');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $minutesQuery = BeritaAcara::with(['documents', 'images'])
            ->when($search, function ($query, $term) {
                $query->where(function ($inner) use ($term) {
                    $inner->where('title', 'like', "%{$term}%")
                        ->orWhere('summary', 'like', "%{$term}%")
                        ->orWhere('location', 'like', "%{$term}%");
                });
            })
            ->when($startDate, function ($query, $from) {
                $query->whereDate('meeting_date', '>=', $from);
            })
            ->when($endDate, function ($query, $to) {
                $query->whereDate('meeting_date', '<=', $to);
            })
            ->orderByDesc('meeting_date')
            ->orderByDesc('created_at');

        $minutes = $minutesQuery
            ->paginate(9)
            ->appends($request->query());

        return view('welcome-berita-acara', [
            'minutes' => $minutes,
            'filters' => [
                'search' => $search,
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
        ]);
    }
}
