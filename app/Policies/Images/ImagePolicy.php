<?php

namespace App\Policies\Images;

use App\User;
use App\Models\Images\Image;
use Illuminate\Auth\Access\HandlesAuthorization;

class ImagePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any images.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the image.
     *
     * @param  \App\User  $user
     * @param  \App\Models\Images\Image  $image
     * @return mixed
     */
    public function view(User $user, Image $image)
    {
        return ($user->id == $image->user_id);
    }

    /**
     * Determine whether the user can create images.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the image.
     *
     * @param  \App\User  $user
     * @param  \App\Models\Images\Image  $image
     * @return mixed
     */
    public function update(User $user, Image $image)
    {
        return ($user->id == $image->user_id);
    }

    /**
     * Determine whether the user can delete the image.
     *
     * @param  \App\User  $user
     * @param  \App\Models\Images\Image  $image
     * @return mixed
     */
    public function delete(User $user, Image $image)
    {
        return ($user->id == $image->user_id);
    }

    /**
     * Determine whether the user can restore the image.
     *
     * @param  \App\User  $user
     * @param  \App\Models\Images\Image  $image
     * @return mixed
     */
    public function restore(User $user, Image $image)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the image.
     *
     * @param  \App\User  $user
     * @param  \App\Models\Images\Image  $image
     * @return mixed
     */
    public function forceDelete(User $user, Image $image)
    {
        //
    }
}
