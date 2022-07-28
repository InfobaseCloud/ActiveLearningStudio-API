<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\H5pRecords;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\H5pRecordRequest;
use App\Http\Resources\V1\H5pRecordResource;
use App\Repositories\H5pRecord\H5pRecordRepository;

class H5pRecordsController extends Controller
{

    /**
     * ActivityController constructor.
     *
     * @param h5pRecordRepositoryInterface $h5pRecordRepository
     */
    public function __construct(H5pRecordRepository $h5pRecordRepository)
    {
        $this->h5pRecordRepository = $h5pRecordRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(H5pRecords $h5pRecords)
    {
        return response([
            'h5pRecords' => H5pRecordResource::collection($h5pRecords->all()),
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(H5pRecordRequest $h5pRecordRequest, H5pRecords $h5pRecords)
    {
        $data = $h5pRecordRequest->validated();
        $h5pRecord = $h5pRecords->create($data);
        if ($h5pRecord) {
            return response([
                'h5pRecord' => new H5pRecordResource($h5pRecord),
            ], 201);
        }
        return response([
            'errors' => ['Could not create playlist. Please try again later.'],
        ], 500);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\H5pRecords  $h5pRecords
     * @return \Illuminate\Http\Response
     */
    public function show(H5pRecords $h5pRecord)
    {
        return response([
            'h5pRecord' => new H5pRecordResource($h5pRecord),
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\H5pRecords  $h5pRecords
     * @return \Illuminate\Http\Response
     */
    public function update(H5pRecordRequest $h5pRecordRequest, H5pRecords $h5pRecord)
    {
        $data = $h5pRecordRequest->validated();
        $h5pRecord = $h5pRecord->update($data);
        if ($h5pRecord) {
            return response([
                'h5pRecord' => new H5pRecordResource($h5pRecord),
            ], 200);
        }
        return response([
            'errors' => ['Failed to update H5p records.'],
        ], 500);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\H5pRecords  $h5pRecords
     * @return \Illuminate\Http\Response
     */
    public function destroy(H5pRecords $h5pRecords)
    {
        //
    }

    public function getRecordByPlaylistId ($playlistId)
    {
        $records = $this->h5pRecordRepository->getH5pRecordByPlaylistId($playlistId);
        $results = [];
        foreach ($records as $key => $record) {
            $results[] = [
                'statement' => json_decode($record->statement),
                'activity_name' => trim($record->activity_name)
            ];
        }
        return response([
            'h5pRecords' => $results
        ], 200);
    }
}