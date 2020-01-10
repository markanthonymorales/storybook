<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\User;
use App\Story;
use Auth;

class StoryRule implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        dd($attribute, $value, Auth::user()->id);
        return Story::where('title', $value)->where('user_id', Auth::user()->id)->count() !== 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The title has already been created..';
    }
}
