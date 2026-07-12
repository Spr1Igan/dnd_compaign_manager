<?php

namespace App\Http\Controllers;

use App\Support\DmgDataRepository;
use Illuminate\Http\Request;

class DataController extends Controller
{
    public function index(Request $request, DmgDataRepository $data)
    {
        $query = trim((string) $request->query('q', ''));

        return view('data.index', [
            'metadata' => $data->metadata(),
            'categories' => $data->categories(),
            'chapters' => array_slice($data->chapters(), 0, 12),
            'query' => $query,
            'results' => $data->search($query),
            'dataExists' => $data->exists(),
        ]);
    }

    public function category(Request $request, DmgDataRepository $data, string $category)
    {
        abort_unless($meta = $data->category($category), 404);

        $query = trim((string) $request->query('q', ''));
        $entries = $query !== ''
            ? $data->search($query, $category)
            : $data->entries($category);

        return view('data.category', [
            'category' => $meta,
            'entries' => $entries,
            'query' => $query,
        ]);
    }

    public function entity(DmgDataRepository $data, string $category, int $entry)
    {
        abort_unless($meta = $data->category($category), 404);
        abort_unless($current = $data->entry($category, $entry), 404);

        $entries = $data->entries($category);

        return view('data.entity', [
            'category' => $meta,
            'entry' => $current,
            'previous' => $entry > 0 ? $data->entry($category, $entry - 1) : null,
            'next' => $data->entry($category, $entry + 1),
            'total' => count($entries),
        ]);
    }

    public function chapters(DmgDataRepository $data)
    {
        return view('data.chapters', [
            'chapters' => $data->chapters(),
        ]);
    }

    public function chapter(Request $request, DmgDataRepository $data, string $chapter)
    {
        abort_unless($current = $data->chapter($chapter), 404);

        $pages = collect($current['pages'] ?? [])->values();
        $pageIndex = max(0, min((int) $request->query('page', 0), max(0, $pages->count() - 1)));

        return view('data.chapter', [
            'chapter' => $current,
            'page' => $pages[$pageIndex] ?? null,
            'pageIndex' => $pageIndex,
            'pagesCount' => $pages->count(),
        ]);
    }

    public function tables(DmgDataRepository $data)
    {
        return view('data.tables', [
            'tables' => $data->tableGroups(),
        ]);
    }
}
