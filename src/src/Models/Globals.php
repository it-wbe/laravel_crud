<?php

namespace Wbe\Crud\Models;

class Globals
{
    /** @var array масив повідомлень до виводу, згрупований за типами */
    static public $messages = [0 => [], 1 => [], 2 => [], 3 => []];
    /** @var array масив типів повідомлень */
    static public $message_class = [0 => 'info', 1 => 'success', 2 => 'warning', 3 => 'danger'];
}