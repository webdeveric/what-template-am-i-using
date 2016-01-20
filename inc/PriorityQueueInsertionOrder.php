<?php
/**
 * This queue modifies priority with the insertion order.
 */
class PriorityQueueInsertionOrder extends SplPriorityQueue
{
    /**
     * @var int
     */
    protected $counter;

    /**
     * Construct the queue.
     */
    public function __construct()
    {
        $this->counter = PHP_INT_MAX;
    }

    /**
     * Insert an item into the queue.
     *
     * @param mixed $value
     * @param mixed $priority
     * @return void
     */
    public function insert( $value, $priority )
    {
        if ( is_int( $priority ) ) {
            $priority = array( $priority, --$this->counter );
        }

        parent::insert( $value, $priority );
    }

    /**
     * Remove an item from the queue.
     *
     * Since there isn't a remove method on the SplPriorityQueue class,
     * I have to do it myself. I'm using a temporary queue to hold everything
     * except what I want to remove, then I insert it back into $this.
     * (remember that items are extracted when iterated)
     *
     * @param mixed $value
     * @return void
     */
    public function remove( $value )
    {
        $temp = new self;
        $temp->setExtractFlags( SplPriorityQueue::EXTR_BOTH );

        $this->setExtractFlags( SplPriorityQueue::EXTR_BOTH );

        foreach ( $this as $entry ) {
            if( $value === $entry['data'] ) {
                continue;
            }

            $temp->insert( $entry['data'], $entry['priority'] );
        }

        foreach ( $temp as $entry ) {
            $this->insert( $entry['data'], $entry['priority'] );
        }

        $this->setExtractFlags( SplPriorityQueue::EXTR_DATA );
    }
}
