<?php

    namespace Cryptomkt\Exchange\Resource;

    class ResourceCollection implements \IteratorAggregate, \Countable, \ArrayAccess{
        
        private $page;
        private $previous;
        private $next;

        private $resources = [];

        public function __construct($page = null, $previous = null, $next = null){
            $this->page = $page;
            $this->previous = $previous;
            $this->next = $next;
        }

        public function getPage(){
            return $this->page;
        }

        public function getPrevious(){
            return $this->previous;
        }

        public function getNext(){
            return $this->next;
        }

        public function hasPreviousPage(){
            return (Boolean) $this->previous;
        }
        
        public function hasNextPage(){
            return (Boolean) $this->next;
        }

        public function mergePreviousPage(ResourceCollection $previousPage){
            $resources = array_reverse($previousPage->all());
            foreach ($resources as $resource) {
                array_unshift($this->resources, $resource);
            }
            $this->previousUri = $previousPage->getPreviousUri();
        }

        public function mergeNextPage(ResourceCollection $nextPage){
            foreach ($nextPage as $resource) {
                $this->add($resource);
            }
            $this->nextUri = $nextPage->getNextUri();
        }

        public function all(){
            return $this->resources;
        }

        public function set($index, Resource $resource){
            $this->resources[$index] = $resource;
        }

        public function add(Resource $resource){
            $this->resources[] = $resource;
        }

        public function get($index){
            if (isset($this->resources[$index])) {
                return $this->resources[$index];
            }
        }

        public function has($index){
            return isset($this->resources[$index]);
        }

        public function remove($index){
            unset($this->resources[$index]);
        }

        public function getIterator(){
            return new \ArrayIterator($this->resources);
        }

        public function offsetExists($offset){
            return $this->has($offset);
        }

        public function offsetGet($offset){
            return $this->get($offset);
        }

        public function offsetSet($offset, $value){
            if (null === $offset) {
                $this->add($value);
            } else {
                $this->set($offset, $value);
            }
        }

        public function offsetUnset($offset){
            $this->remove($offset);
        }

        public function count(){
            return count($this->resources);
        }
    }
?>