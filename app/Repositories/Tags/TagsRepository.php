<?php

namespace App\Repositories\Tags;

use App\Models\Tags;
use App\Repositories\BaseRepository;

class TagsRepository extends BaseRepository implements TagsRepositoryInterface
{
    /**
     * SubjectRepository constructor.
     *
     * @param Tags $model
     */
    public function __construct(Tags $model)
    {
        parent::__construct($model);
    }

    /**
     * @param $suborganization
     * @param $data
     *
     * @return mixed
     */
    public function getAll($suborganization, $data)
    {
        $query = $this->model;
        $q = $data['query'] ?? null;
        if ($q) {
            $query = $query->where('name', 'iLIKE', '%' .$q. '%');
        }

        if (isset($data['skipPagination']) && $data['skipPagination'] === 'true') {
            return $query->where('organization_id', $suborganization->id)->orderBy('order', 'ASC')->get();
        }
        $perPage = isset($data['size']) ? $data['size'] : config('constants.default-pagination-per-page');
        
        if (isset($data['order_by_column'])) {
            $orderByType = isset($data['order_by_type']) ? $data['order_by_type'] : 'ASC';
            $query = $query->orderBy($data['order_by_column'], $orderByType);
        } else {
            $query = $query->orderBy('order', 'ASC');
        }
        // dd($suborganization->id);
        // dd($query->where('organization_id', $suborganization->id)->paginate($perPage)->withQueryString());
        return $query->where('organization_id', $suborganization->id)->paginate($perPage)->withQueryString();
    }

    /**
     * @param $tagsIds
     *
     * @return mixed
     */
    public function getTagsIdsWithMatchingName($tagsIds)
    {
        $TagsNames = $this->model->whereIn('id', $tagsIds)->pluck('name');
        return $this->model->whereIn('name', $TagsNames)->pluck('id')->toArray();
    }
}
