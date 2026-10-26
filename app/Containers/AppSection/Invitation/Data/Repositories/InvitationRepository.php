<?php

namespace App\Containers\AppSection\Invitation\Data\Repositories;

use App\Containers\AppSection\Invitation\Models\Invitation;
use App\Ship\Parents\Repositories\Repository as ParentRepository;

/**
 * @template TModel of Invitation
 *
 * @extends ParentRepository<TModel>
 */
final class InvitationRepository extends ParentRepository
{
}
