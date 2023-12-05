<?php

use Carbon\Carbon;

if (! function_exists('time_since')) {
   function time_since($timestamp)
   {
      $now = Carbon::now();
      $difference = $timestamp->diffInDays($now);

      if($difference > 14) {

         if ($timestamp->year == $now->year) {
            return $timestamp->format('d M');
        } 
        else {
            return $timestamp->format('d M Y');
        }
      }
      else
      {   
         $timeSince = $timestamp->diffForHumans($now, [
            'parts' => 1,
            'syntax' => Carbon::DIFF_ABSOLUTE
         ]);

         $abbreviations = [
            'seconds' => 'sec',
            'second' => 'sec',
            'minutes' => 'min',
            'minute' => 'min',
            'hours' => 'h',
            'hour' => 'h',
            'days' => 'd',
            'day' => 'd',
         ];

         foreach ($abbreviations as $full => $abbr) {
            $timeSince = str_replace($full, $abbr, $timeSince);
         }

         return $timeSince;
      }

   }
}

