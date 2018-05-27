<?php

namespace RestApiBundle\Pagination;

class PaginatedCollection
{
    private $items;

    private $total;

    private $count;

    private $_links = array();

    public function __construct(array $items, $totalItems)
    {
        $this->items = $items;
        $this->total = $totalItems;
        $this->count = count($items);
    }
    
    public function returnArray()
    { 
        $returnArray = array();
        foreach ($this->items as $item) {
            $returnArray[] = (array)$item;
        } 
        return array(
            'items' => $returnArray,
            'total' => $this->total,
            'count' => $this->count,
            '_links' => $this->_links
        ) ;
    }

    public function addLink($ref, $url)
    {
        $this->_links[$ref] = $url;
    }
}
