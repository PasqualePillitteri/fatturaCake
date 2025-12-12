<?php
declare(strict_types=1);

namespace App\Policy;

use App\Rbac\DatabaseRbac;
use Authorization\IdentityInterface;
use Authorization\Policy\RequestPolicyInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Request Policy
 *
 * Uses DatabaseRbac to check permissions for incoming requests.
 */
class RequestPolicy implements RequestPolicyInterface
{
    /**
     * Database RBAC instance.
     *
     * @var \App\Rbac\DatabaseRbac
     */
    protected DatabaseRbac $rbac;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->rbac = new DatabaseRbac();
    }

    /**
     * Method to check if the request can be accessed.
     *
     * @param \Authorization\IdentityInterface|null $identity Identity
     * @param \Psr\Http\Message\ServerRequestInterface $request Server Request
     * @return bool
     */
    public function canAccess(?IdentityInterface $identity, ServerRequestInterface $request): bool
    {
        // No identity = not authenticated
        if ($identity === null) {
            return false;
        }

        // Get user data from identity
        $user = $identity->getOriginalData();

        return $this->rbac->checkPermission($user, $request);
    }
}
