<?php
// Implementation of data structures for the travel agency system

// Queue implementation for booking requests (FIFO)
class BookingQueue {
    private $queue = array();
    
    // Add a booking to the queue
    public function enqueue($booking) {
        array_push($this->queue, $booking);
    }
    
    // Remove and return the first booking from the queue
    public function dequeue() {
        if($this->isEmpty()) {
            return null;
        }
        return array_shift($this->queue);
    }
    
    // View the first booking without removing it
    public function peek() {
        if($this->isEmpty()) {
            return null;
        }
        return $this->queue[0];
    }
    
    // Check if queue is empty
    public function isEmpty() {
        return empty($this->queue);
    }
    
    // Get the size of the queue
    public function size() {
        return count($this->queue);
    }
    
    // Get all bookings in the queue
    public function getAll() {
        return $this->queue;
    }
}

// Stack implementation for search history (LIFO)
class SearchHistoryStack {
    private $stack = array();
    
    // Add a search term to the stack
    public function push($searchTerm) {
        array_push($this->stack, $searchTerm);
    }
    
    // Remove and return the last search term
    public function pop() {
        if($this->isEmpty()) {
            return null;
        }
        return array_pop($this->stack);
    }
    
    // View the last search term without removing it
    public function peek() {
        if($this->isEmpty()) {
            return null;
        }
        return end($this->stack);
    }
    
    // Check if stack is empty
    public function isEmpty() {
        return empty($this->stack);
    }
    
    // Get the size of the stack
    public function size() {
        return count($this->stack);
    }
    
    // Get all search terms in the stack (most recent first)
    public function getAll() {
        return array_reverse($this->stack);
    }
    
    // Clear the stack
    public function clear() {
        $this->stack = array();
    }
}

// Binary Search implementation for packages
class PackageSearch {
    // Binary search for packages by price
    public static function binarySearchByPrice($packages, $targetPrice) {
        // Sort packages by price
        usort($packages, function($a, $b) {
            return $a['price'] - $b['price'];
        });
        
        $left = 0;
        $right = count($packages) - 1;
        $closestIndex = -1;
        $minDiff = PHP_INT_MAX;
        
        while($left <= $right) {
            $mid = floor(($left + $right) / 2);
            $diff = abs($packages[$mid]['price'] - $targetPrice);
            
            if($diff < $minDiff) {
                $minDiff = $diff;
                $closestIndex = $mid;
            }
            
            if($packages[$mid]['price'] < $targetPrice) {
                $left = $mid + 1;
            } else if($packages[$mid]['price'] > $targetPrice) {
                $right = $mid - 1;
            } else {
                return $packages[$mid];
            }
        }
        
        return ($closestIndex !== -1) ? $packages[$closestIndex] : null;
    }
    
    // Binary search for packages by rating
    public static function binarySearchByRating($packages, $targetRating) {
        // Sort packages by rating
        usort($packages, function($a, $b) {
            return $b['rating'] - $a['rating']; // Descending order
        });
        
        $left = 0;
        $right = count($packages) - 1;
        $closestIndex = -1;
        $minDiff = PHP_INT_MAX;
        
        while($left <= $right) {
            $mid = floor(($left + $right) / 2);
            $diff = abs($packages[$mid]['rating'] - $targetRating);
            
            if($diff < $minDiff) {
                $minDiff = $diff;
                $closestIndex = $mid;
            }
            
            if($packages[$mid]['rating'] < $targetRating) {
                $right = $mid - 1; // For descending order
            } else if($packages[$mid]['rating'] > $targetRating) {
                $left = $mid + 1; // For descending order
            } else {
                return $packages[$mid];
            }
        }
        
        return ($closestIndex !== -1) ? $packages[$closestIndex] : null;
    }
}

// QuickSort implementation for sorting packages
class PackageSorter {
    // QuickSort for packages by price (ascending or descending)
    public static function quickSortByPrice(&$packages, $low, $high, $ascending = true) {
        if($low < $high) {
            $pivot = self::partitionByPrice($packages, $low, $high, $ascending);
            self::quickSortByPrice($packages, $low, $pivot - 1, $ascending);
            self::quickSortByPrice($packages, $pivot + 1, $high, $ascending);
        }
    }
    
    private static function partitionByPrice(&$packages, $low, $high, $ascending) {
        $pivot = $packages[$high]['price'];
        $i = $low - 1;
        
        for($j = $low; $j < $high; $j++) {
            if(($ascending && $packages[$j]['price'] <= $pivot) || 
               (!$ascending && $packages[$j]['price'] >= $pivot)) {
                $i++;
                // Swap packages[$i] and packages[$j]
                $temp = $packages[$i];
                $packages[$i] = $packages[$j];
                $packages[$j] = $temp;
            }
        }
        
        // Swap packages[$i+1] and packages[$high]
        $temp = $packages[$i + 1];
        $packages[$i + 1] = $packages[$high];
        $packages[$high] = $temp;
        
        return $i + 1;
    }
    
    // QuickSort for packages by rating (ascending or descending)
    public static function quickSortByRating(&$packages, $low, $high, $ascending = false) {
        if($low < $high) {
            $pivot = self::partitionByRating($packages, $low, $high, $ascending);
            self::quickSortByRating($packages, $low, $pivot - 1, $ascending);
            self::quickSortByRating($packages, $pivot + 1, $high, $ascending);
        }
    }
    
    private static function partitionByRating(&$packages, $low, $high, $ascending) {
        $pivot = $packages[$high]['rating'];
        $i = $low - 1;
        
        for($j = $low; $j < $high; $j++) {
            if(($ascending && $packages[$j]['rating'] <= $pivot) || 
               (!$ascending && $packages[$j]['rating'] >= $pivot)) {
                $i++;
                // Swap packages[$i] and packages[$j]
                $temp = $packages[$i];
                $packages[$i] = $packages[$j];
                $packages[$j] = $temp;
            }
        }
        
        // Swap packages[$i+1] and packages[$high]
        $temp = $packages[$i + 1];
        $packages[$i + 1] = $packages[$high];
        $packages[$high] = $temp;
        
        return $i + 1;
    }
}
?>