<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Inertia\Inertia;

class SubcategoryController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Subcategories/Index', [
            'categories' => Category::query()
                ->whereNull('parent_id')
                ->orderBy('name')
                ->get()
                ->map(fn (Category $category) => [
                    'id' => $category->id,
                    'name' => $category->name,
                ]),
            'subcategories' => Category::query()
                ->whereNotNull('parent_id')
                ->with('parent:id,name')
                ->orderBy('name')
                ->get()
                ->map(fn (Category $subcategory) => [
                    'id' => $subcategory->id,
                    'name' => $subcategory->name,
                    'slug' => $subcategory->slug,
                    'posts_count' => $subcategory->subcategoryPosts()->count(),
                    'parent' => $subcategory->parent ? [
                        'id' => $subcategory->parent->id,
                        'name' => $subcategory->parent->name,
                    ] : null,
                ]),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'parent_id' => ['required', 'integer', Rule::exists('categories', 'id')->whereNull('parent_id')],
        ]);

        Category::query()->create([
            'name' => $validated['name'],
            'slug' => $this->generateUniqueSlug($validated['name']),
            'parent_id' => $validated['parent_id'],
        ]);

        return redirect()
            ->route('admin.subcategories.index')
            ->with('success', 'Tag criada com sucesso.');
    }

    public function update(Request $request, Category $subcategory)
    {
        $this->ensureIsSubcategory($subcategory);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
        ]);

        $subcategory->name = $validated['name'];

        if ($subcategory->isDirty('name')) {
            $subcategory->slug = $this->generateUniqueSlug($validated['name'], $subcategory->id);
        }

        $subcategory->save();

        return redirect()
            ->route('admin.subcategories.index')
            ->with('success', 'Tag atualizada com sucesso.');
    }

    public function destroy(Category $subcategory)
    {
        $this->ensureIsSubcategory($subcategory);

        if ($subcategory->subcategoryPosts()->exists()) {
            return redirect()
                ->route('admin.subcategories.index')
                ->with('error', 'Remova ou altere os posts vinculados antes de excluir esta tag.');
        }

        $subcategory->delete();

        return redirect()
            ->route('admin.subcategories.index')
            ->with('success', 'Tag removida com sucesso.');
    }

    private function ensureIsSubcategory(Category $subcategory): void
    {
        abort_if($subcategory->parent_id === null, 404);
    }

    private function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $counter = 1;

        while (
            Category::query()
                ->where('slug', $slug)
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }
}
