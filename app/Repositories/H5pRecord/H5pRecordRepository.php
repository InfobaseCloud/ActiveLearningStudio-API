<?php

namespace App\Repositories\H5pRecord;

use Carbon\Carbon;
use App\Models\H5pRecords;
use Illuminate\Support\Collection;
use App\Repositories\BaseRepository;
use App\Models\H5pBrightCoveVideoContents;
use App\Repositories\H5pRecord\H5pRecordRepositoryInterface;

class H5pRecordRepository extends BaseRepository implements H5pRecordRepositoryInterface
{
    /**
     * H5pRecordRepository constructor.
     *
     * @param H5pRecords $model
     */
    public function __construct(H5pRecords $model)
    {
        parent::__construct($model);
    }

    /**
     * Get the h5p content for library.
     *
     * @param int $contentId for library
     * @return array
     */
    public function getH5pRecordByPlaylistId($contentId)
    {
        return  $this->model::select('statement', 'activity_name')->where('playlist_id', $contentId)->get();
    }
}