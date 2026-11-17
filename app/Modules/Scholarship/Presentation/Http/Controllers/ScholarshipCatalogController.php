<?php

namespace App\Modules\Scholarship\Presentation\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Scholarship\Application\UseCases\SearchScholarships;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ScholarshipCatalogController extends Controller
{
    public function __invoke(
        Request $request,
        SearchScholarships $searchScholarships
    ): Response {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:150'],
            'education_level' => ['nullable', 'string', 'max:30'],
            'location_scope' => ['nullable', 'string', 'max:30'],
            'funding_type' => ['nullable', 'string', 'max:30'],
            'field_of_study' => ['nullable', 'string', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1', 'max:100000'],
        ]);

        return Inertia::render('Mentora/Scholarships/Index', [
            'scholarships' => $searchScholarships->execute($filters),

            'filters' => [
                'search' => $filters['search'] ?? '',
                'education_level' => $filters['education_level'] ?? '',
                'location_scope' => $filters['location_scope'] ?? '',
                'funding_type' => $filters['funding_type'] ?? '',
                'field_of_study' => $filters['field_of_study'] ?? '',
            ],
        ]);
    }
}