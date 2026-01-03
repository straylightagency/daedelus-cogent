<?php

namespace Daedelus\Cogent;

/**
 * @property Page $parent
 */
class Page extends Post
{
    /** @var string */
    public string $postType = 'page';
}