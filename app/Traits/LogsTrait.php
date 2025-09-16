<?php
namespace App\Traits;

use App\Models\Logs;

trait LogsTrait
{
    public static function bootLogsTrait()
    {
        static::updated(function($item) {
            $reflection = new \ReflectionClass($item);
            dd($reflection);
            // $loggable = strtolower($reflection->getShortName());
            /*$user = \Auth::user();
            $activity = new UserActivityLog;
            $activity->user()->associate($user);
            $activity->loggable_id = $item->id;
            $activity->loggable_type = $reflection->getName();
            $activity->action = 'created';
            $activity->description = "";
            $activity->ip = app('request')->ip();
            $activity->user_agent = app('request')->header('User-Agent');
            $activity->save();*/
        });
    }
}