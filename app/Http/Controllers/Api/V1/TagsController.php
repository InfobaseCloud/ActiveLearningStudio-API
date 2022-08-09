<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\SearchTagsRequest;
use App\Http\Resources\V1\TagResource;
use App\Models\Organization;
use App\Models\Tags;
use App\Repositories\Tags\TagsRepositoryInterface;
use App\Repositories\Subject\SubjectRepositoryInterface;
use Illuminate\Http\Response;

class TagsController extends Controller{
    private $tagsRepository;

    /**
     * TagsController constructor.
     *
     * @param TagsRepositoryInterface $TagsRepository
     */
    public function __construct(TagsRepositoryInterface $tagsRepository) {
        $this->tagsRepository = $tagsRepository;
    }

    /**
     * Get Subjects
     *
     * Get a list of all subjects.
     *
     * @responseFile responses/subject/subjects.json
     *
     * @param SearchTagsRequest $request
     * @param Organization $suborganization
     *
     * @return Response
     */

    public function index(SearchTagsRequest $request, Organization $suborganization)
    {
        return  TagResource::collection($this->tagsRepository->getAll($suborganization, $request->all()));
    }
}
