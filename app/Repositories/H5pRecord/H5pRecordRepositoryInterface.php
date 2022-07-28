<?php

namespace App\Repositories\H5pRecord;

use App\Repositories\EloquentRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface H5pRecordRepositoryInterface extends EloquentRepositoryInterface
{

    /**
     * Get the libraries's fields semantics.
     *
     * @param int $contentId for brightcove video
     * @return array
     */
    public function getH5pRecordByPlaylistId($contentId);
}