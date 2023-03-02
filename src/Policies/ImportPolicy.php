<?php

namespace Corals\Modules\Aliexpress\Policies;

use Corals\Foundation\Policies\BasePolicy;
use Corals\User\Models\User;
use Corals\Modules\Aliexpress\Models\Import;

class ImportPolicy extends BasePolicy
{

    /**
     * @param User $user
     * @return bool
     */
    public function view(User $user)
    {
        if ($user->can('Aliexpress::import.view')) {
            return true;
        }
        return false;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function create(User $user)
    {
        return $user->can('Aliexpress::import.create');
    }

    /**
     * @param User $user
     * @param Import $import
     * @return bool
     */
    public function update(User $user, Import $import)
    {
        if ($user->can('Aliexpress::import.update')) {
            return true;
        }
        return false;
    }

    /**
     * @param User $user
     * @param Import $import
     * @return bool
     */
    public function destroy(User $user, Import $import)
    {
        if ($user->can('Aliexpress::import.delete')) {
            return true;
        }
        return false;
    }
}
