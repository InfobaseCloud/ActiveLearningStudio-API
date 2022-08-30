<?php

namespace App\Repositories\User;

use App\User;
use Carbon\Carbon;
use Lcobucci\JWT\Parser;
use Illuminate\Support\Arr;
use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Log;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use App\Models\OrganizationRoleType;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Resources\V1\NotificationListResource;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    /**
     * UserRepository constructor.
     *
     * @param User $model
     */
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    /**
     * Update the user if soft deleted, otherwise create new
     *
     * @param array $data
     * @return false|Model
     */
    public function create(array $data)
    {
        try {
            $data['deleted_at'] = null;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        return false;
    }

    /**
     * Search by name
     *
     * @param $name
     * @return mixed
     */
    public function searchByName($name)
    {
        return $this->model->name($name)->paginate();
    }

    /**
     * Search by name and email
     *
     * @param $key
     * @return mixed
     */
    public function searchByEmailAndName($key)
    {
        return $this->model->searchByEmailAndName($key)->paginate();
    }

    /**
     * @param $accessToken
     * @return bool
     */
    public function parseToken($accessToken)
    {
        $key_path = Passport::keyPath('oauth-public.key');
        $parseTokenKey = file_get_contents($key_path);

        $token = (new Parser())->parse((string) $accessToken);

        $signer = new Sha256();

        if ($token->verify($signer, $parseTokenKey)) {
            $userId = $token->getClaim('sub');

            return $userId;
        } else {
            return false;
        }
    }

    /**
     * To arrange listing of notifications
     * @param $notifications
     * @return array
     */
    public function fetchListing($notifications)
    {
        $returnNotifications = [];
        $yesterdayNotifications = clone $notifications;
        $olderNotifications = clone $notifications;
        $returnNotifications['today'] = NotificationListResource::collection($notifications->with('notifiable')->whereDate('created_at', Carbon::today())->get());
        $returnNotifications['yesterday'] = NotificationListResource::collection($yesterdayNotifications->with('notifiable')->whereDate('created_at', Carbon::yesterday())->get());
        $returnNotifications['older'] = NotificationListResource::collection($olderNotifications->with('notifiable')->whereDate('created_at', '<', Carbon::yesterday())->get());
        return $returnNotifications;
    }

    /**
     * Check if user has the specified permission in the provided organization
     *
     * @param $user
     * @param $permission
     * @param $organization
     * @return boolean
     */
    public function hasPermissionTo($user, $permission, $organization)
    {
        $hasPermissionTo =  $organization->userRoles()
                            ->wherePivot('user_id', $user->id)
                            ->whereHas('permissions', function (Builder $query) use ($permission) {
                                $query->where('name', '=', $permission);
                            })->get();

        if ($hasPermissionTo->count()) {
            return true;
        } elseif ($organization->parent) {
            return $this->hasPermissionTo($user, $permission, $organization->parent);
        }

        return false;
    }

    /**
     * Check if user has the specified permission in the provided team role
     *
     * @param $user
     * @param $permission
     * @param $team
     * @return boolean
     */
    public function hasTeamPermissionTo($user, $permission, $team)
    {
        if (is_null($team)) {
            return true;
        }
        $hasTeamPermissionTo =  $team->userRoles()
                            ->wherePivot('user_id', $user->id)
                            ->whereHas('permissions', function (Builder $query) use ($permission) {
                                $query->where('name', '=', $permission);
                            })->get();

        if ($hasTeamPermissionTo->count()) {
            return true;
        }

        return false;
    }

    /**
     * Users basic report, projects, playlists and activities count
     * @param $data
     * @return mixed
     */
    public function reportBasic($data)
    {
        $perPage = isset($data['size']) ? $data['size'] : config('constants.default-pagination-per-page');
        $q = $data['query'] ?? null;

        $this->query = $this->model->select(['id', 'first_name', 'last_name', 'email'])->withCount(['projects', 'playlists', 'activities'])
            ->when($data['mode'] === 'subscribed', function ($query) {
                return $query->where(function ($query) {
                    return $query->where('subscribed', true);
                });
            });

        if ($q) {
            $this->query->where(function($qry) use ($q) {
                $qry->orWhere('first_name', 'iLIKE', '%' .$q. '%');
                $qry->orWhere('last_name', 'iLIKE', '%' .$q. '%');
                $qry->orWhere('email', 'iLIKE', '%' .$q. '%');
            });
        }

        return $this->query->paginate($perPage)->appends(request()->query());
    }

    /**
     * To get exported project list of last 10 days
     * @param $data
     * @return mixed
     */
    public function getUsersExportProjectList($data)
    {
        $days_limit = isset($data['days_limit']) ? $data['days_limit'] : config('constants.default-exported-projects-days-limit');
        
        $date = Carbon::now()->subDays($days_limit);

        $perPage = isset($data['size']) ? $data['size'] : config('constants.default-pagination-per-page');
        $query = auth()->user()->notifications();
        $q = $data['query'] ?? null;
        // if simple request for getting project listing with search
        if ($q) {
            $query = $query->where(function($qry) use ($q) {
                $qry->where('data', 'iLIKE', '%' .$q. '%');
            });
        }
        
        $query =  $query->where('type', 'App\Notifications\ProjectExportNotification');
        $query =  $query->where('created_at', '>=', $date);

        if (isset($data['order_by_column']) && $data['order_by_column'] !== '') {
            $orderByType = isset($data['order_by_type']) ? $data['order_by_type'] : 'ASC';
            $query = $query->orderBy($data['order_by_column'], $orderByType);
        }
        
        return  $query->paginate($perPage)->withQueryString();
    }

    /**
     * To get exported project list of last 10 days
     * @param $data
     * @return mixed
     */
    public function getUsersExportIndependentActivitiesList($data)
    {
        $days_limit = isset($data['days_limit']) ? $data['days_limit'] : config('constants.default-exported-independent-activities-days-limit');
        
        $date = Carbon::now()->subDays($days_limit);

        $perPage = isset($data['size']) ? $data['size'] : config('constants.default-pagination-per-page');
        $query = auth()->user()->notifications();
        $q = $data['query'] ?? null;
        // if simple request for getting project listing with search
        if ($q) {
            $query = $query->where(function($qry) use ($q) {
                $qry->where('data', 'iLIKE', '%' .$q. '%');
            });
        }
        
        $query =  $query->where('type', 'App\Notifications\ActivityExportNotification');
        $query =  $query->where('created_at', '>=', $date);

        if (isset($data['order_by_column']) && $data['order_by_column'] !== '') {
            $orderByType = isset($data['order_by_type']) ? $data['order_by_type'] : 'ASC';
            $query = $query->orderBy($data['order_by_column'], $orderByType);
        }
        
        return  $query->paginate($perPage)->withQueryString();
    }

    public function getFirstUser()
    {
        return $this->model->orderBy('id', 'asc')->first();
    }
}