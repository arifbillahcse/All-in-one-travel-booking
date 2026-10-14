<?php

namespace Database\Seeders;

use App\Models\Destination;
use App\Models\Post;
use App\Models\PostCategory;
use Carbon\Carbon;
use Database\Seeders\Concerns\LoadsLegacyContent;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PostSeeder extends Seeder
{
    use LoadsLegacyContent;

    public function run(): void
    {
        $destinations = Destination::pluck('id', 'slug');
        $categories = [];

        foreach ($this->legacy('posts') as $row) {
            ['en' => $en, 'bn' => $bn] = $row;

            $slug = Str::slug($en['category']);
            $category = $categories[$slug] ??= $this->category($slug, $en['category']);

            // Bangla blocks carry only the text; the block type comes from the English post.
            $bnBody = [];
            foreach ($en['body'] as $i => $block) {
                $bnBody[] = ['type' => $block['type']] + $bn['body'][$i];
            }

            $post = Post::firstOrNew(['slug' => $en['slug']]);
            $post->fill([
                'post_category_id' => $category->id,
                'destination_id' => $destinations[$en['destination'] ?? ''] ?? null,
                'read_minutes' => $en['readMins'],
                'published_at' => Carbon::parse($en['date']),
                'is_published' => true,
            ]);
            $post->setTranslations('title', ['en' => $en['title'], 'bn' => $bn['title']]);
            $post->setTranslations('excerpt', ['en' => $en['excerpt'], 'bn' => $bn['excerpt']]);
            $post->setTranslations('body', ['en' => $en['body'], 'bn' => $bnBody]);
            $post->save();
        }
    }

    private function category(string $slug, string $name): PostCategory
    {
        $category = PostCategory::firstOrNew(['slug' => $slug]);
        $category->sort_order = PostCategory::count() + 1;
        $category->setTranslations('name', $this->pair($name));
        $category->save();

        return $category;
    }
}
