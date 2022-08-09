<?php

namespace App\Repositories\Tags;

use App\Repositories\EloquentRepositoryInterface;

interface TagsRepositoryInterface extends EloquentRepositoryInterface
{
    /**
     * @param $suborganization
     * @param $data
     * @return mixed
     */
    public function getAll($data, $suborganization);

    /**
     * @param $subjectIds
     *
     * @return mixed
     */
    public function getTagsIdsWithMatchingName($subjectIds);
}
