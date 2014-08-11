<?php
namespace Icicle\Promise;

use Exception;
use Icicle\Loop\Loop;

class RejectedPromise extends ResolvedPromise
{
    /**
     * @var Exception
     */
    private $exception;
    
    /**
     * @param   Exception $exception
     */
    public function __construct(Exception $exception)
    {
        $this->exception = $exception;
    }
    
    /**
     * {@inheritdoc}
     */
    public function then(callable $onFulfilled = null, callable $onRejected = null)
    {
        if (null === $onRejected) {
            return new static($this->exception);
        }
        
        return new Promise(function ($resolve, $reject) use ($onRejected) {
            Loop::schedule(function () use ($resolve, $reject, $onRejected) {
                try {
                    $resolve($onRejected($this->exception));
                } catch (Exception $exception) {
                    $reject($exception);
                }
            });
        });
    }
    
    /**
     * {@inheritdoc}
     */
    public function done(callable $onFulfilled = null, callable $onRejected = null)
    {
        if (null !== $onRejected) {
            Loop::schedule($onRejected, $this->exception);
        } else {
            Loop::schedule(function () {
                throw $this->exception;
            });
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function delay($time)
    {
        return $this->then();
    }
    
    /**
     * {@inheritdoc}
     */
    public function isFulfilled()
    {
        return false;
    }
    
    /**
     * {@inheritdoc}
     */
    public function isRejected()
    {
        return true;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getResult()
    {
        return $this->exception;
    }
}