<?php
namespace App\Common\Models\Live;

trait Redis
{

    /**
     * redis key前缀
     *
     * @var string
     */
    protected $prefix = "live::";

    /**
     * redis client
     *
     * @var \Predis\Client
     */
    protected $redis;
}