<?php

namespace App\Policies\Items;

use App\User;
use App\Models\Items\Item;
use Illuminate\Auth\Access\HandlesAuthorization;

class ItemPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any items.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the item.
     *
     * @param  \App\User  $user
     * @param  \App\Models\Items\Item  $item
     * @return mixed
     */
    public function view(User $user, Item $item)
    {
        return ($user->id == $item->user_id);
    }

    /**
     * Determine whether the user can create items.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the item.
     *
     * @param  \App\User  $user
     * @param  \App\Models\Items\Item  $item
     * @return mixed
     */
    public function update(User $user, Item $item)
    {
        return ($user->id == $item->user_id);
    }

    /**
     * Determine whether the user can delete the item.
     *
     * @param  \App\User  $user
     * @param  \App\Models\Items\Item  $item
     * @return mixed
     */
    public function delete(User $user, Item $item)
    {
        return ($user->id == $item->user_id);
    }

    /**
     * Determine whether the user can restore the item.
     *
     * @param  \App\User  $user
     * @param  \App\Models\Items\Item  $item
     * @return mixed
     */
    public function restore(User $user, Item $item)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the item.
     *
     * @param  \App\User  $user
     * @param  \App\Models\Items\Item  $item
     * @return mixed
     */
    public function forceDelete(User $user, Item $item)
    {
        //
    }
}
