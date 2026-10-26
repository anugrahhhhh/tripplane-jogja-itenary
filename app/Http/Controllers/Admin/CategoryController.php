<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::withCount('destinations')->orderBy('name')->paginate(15);

        return view('admin.categories.index', ['categories' => $categories]);
    }

    public function create(): View
    {
        return view('admin.categories.create');
    }

    public function store(Request $request): RedirectResponse
    {
        Category::create($this->validateData($request));

        return redirect()->route('admin.categories.index')->with('status', 'Kategori berhasil ditambahkan.');
    }

    public function edit(Category $category): View
    {
        return view('admin.categories.edit', ['category' => $category]);
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $category->update($this->validateData($request, $category));

        return redirect()->route('admin.categories.index')->with('status', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        abort_if($category->destinations()->exists(), 422, 'Kategori masih memiliki destinasi, tidak bisa dihapus.');

        $category->delete();

        return redirect()->route('admin.categories.index')->with('status', 'Kategori berhasil dihapus.');
    }

    protected function validateData(Request $request, ?Category $category = null): array
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:255'],
        ]);

        return [
            'name' => $request->input('name'),
            'slug' => $this->resolveSlug($request->input('name'), $category),
            'icon' => $request->input('icon'),
        ];
    }

    /**
     * Sama seperti Admin\DestinationController::resolveSlug() — slug hanya
     * dihitung ulang kalau namanya berubah, dan auto-suffix kalau bentrok,
     * supaya menyimpan ulang tanpa mengganti nama tidak pernah memicu
     * error constraint database.
     */
    protected function resolveSlug(string $name, ?Category $category): string
    {
        if ($category && $category->name === $name) {
            return $category->slug;
        }

        $base = Str::slug($name);
        $slug = $base;
        $counter = 2;

        while (
            Category::where('slug', $slug)
                ->when($category, fn ($query) => $query->whereKeyNot($category->id))
                ->exists()
        ) {
            $slug = $base.'-'.$counter;
            $counter++;
        }

        return $slug;
    }
}
