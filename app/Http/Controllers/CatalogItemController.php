<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\CatalogCategory;
use App\Enums\ItemCategory;
use App\Http\Requests\Catalog\StoreCatalogItemRequest;
use App\Http\Requests\Catalog\UpdateCatalogItemRequest;
use App\Models\CatalogItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogItemController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', CatalogItem::class);

        $category = $request->query('category');
        $search = $request->query('search');

        $query = CatalogItem::query();

        if ($category && CatalogCategory::tryFrom($category)) {
            $query->where('category', $category);
        }

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        $items = $query->orderBy('category')->orderBy('name')->paginate(20)->withQueryString();

        return view('catalog.index', [
            'items' => $items,
            'categories' => CatalogCategory::cases(),
            'itemTypes' => ItemCategory::cases(),
            'selectedCategory' => $category,
            'search' => $search,
        ]);
    }

    public function store(StoreCatalogItemRequest $request): RedirectResponse
    {
        $this->authorize('create', CatalogItem::class);

        $item = CatalogItem::create($request->validated());

        return redirect()->route('catalog.index', ['category' => $item->category->value])
            ->with('success', "Catalog item '{$item->name}' created.");
    }

    public function update(UpdateCatalogItemRequest $request, CatalogItem $catalog): RedirectResponse
    {
        $this->authorize('update', $catalog);

        $catalog->update($request->validated());

        return redirect()->route('catalog.index', ['category' => $catalog->category->value])
            ->with('success', "Catalog item '{$catalog->name}' updated.");
    }

    public function destroy(CatalogItem $catalog): RedirectResponse
    {
        $this->authorize('delete', $catalog);

        $name = $catalog->name;
        $category = $catalog->category->value;
        $catalog->delete();

        return redirect()->route('catalog.index', ['category' => $category])
            ->with('success', "Catalog item '{$name}' deleted.");
    }
}
