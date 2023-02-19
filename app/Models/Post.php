<?php

namespace App\Models;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Spatie\YamlFrontMatter\YamlFrontMatter;

class Post
{
    public string $title;

    public string $excerpt;

    public string $date;

    public string $body;

    public string $slug;

    /**
     * @param string $title
     * @param string $excerpt
     * @param string $date
     * @param string $body
     * @param string $slug
     */
    public function __construct(string $title, string $excerpt, string $date, string $body, string $slug)
    {
        $this->title = $title;
        $this->excerpt = $excerpt;
        $this->date = $date;
        $this->body = $body;
        $this->slug = $slug;
    }

    public static function all() : Collection
    {
        return cache()->rememberForever('posts.all', function () {
            return collect(File::files(resource_path('posts')))
                ->map(fn($file) => YamlFrontMatter::parseFile($file))
                ->map(fn($document) => new Post(
                    $document->title,
                    $document->excerpt,
                    $document->date,
                    $document->body(),
                    $document->slug
                ))->sortByDesc('date');

        });
    }

    public static function find($slug)
    {
        return static::all()->firstWhere('slug', $slug);
    }

    public static function findOrFail($slug)
    {
        $post = static::find($slug);

        if(!$post) {
            throw new ModelNotFoundException();
        }

        return $post;
    }
}
