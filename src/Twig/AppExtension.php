<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('usort', [$this, 'usortFilter'])
        ];
    }
    
    public function usortFilter($item)
    {
        usort($item, function ($item1, $item2) {
            return $item1['id'] < $item2['id'] ? -1 : 1;
        });

        return $item;
    }
}
