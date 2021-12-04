<?php
namespace lib;

interface InterfaceCache
{
    const TTL = 86400; //один день
    const init_dir = 'activity';
    const base_dir = 'cache';

    const today_for_hours = 'today_for_hours';
    const day_for_month = 'day_for_month';
}
