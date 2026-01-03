<?php

namespace Daedelus\Cogent;

use Illuminate\Support\ServiceProvider;

/**
 *
 */
class CogentServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function boot(): void
    {
        Model::registerPostType(Post::class);
        Model::registerPostType(Page::class);
        Model::registerPostType(Attachment::class);
        Model::registerPostType(CustomLink::class);

        Model::registerTaxonomy(Category::class);
        Model::registerTaxonomy(Tag::class);
    }
}