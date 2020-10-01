<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Http\Requests\DiscussionRequest;
use App\Http\Requests\ResearchGroupStoreRequest;
use App\Http\Resources\AnnouncementListDataResource;
use App\Http\Resources\CommentResource;
use App\Http\Resources\DetailGroupResource;
use App\Http\Resources\DiscussionResource;
use App\Http\Resources\ListMemberGroupResource;
use App\Http\Resources\ResearchGroupListDataResource;
use App\Models\ResearchGroup;
use App\Models\ResearchGroupComment;
use App\Models\ResearchGroupDiscussion;
use App\Models\ResearchGroupMember;
use App\Transformers\ResearchGroupTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class ResearchGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $model = ResearchGroup::query();

        return DataTables::eloquent($model)
        ->setTransformer(new ResearchGroupTransformer)
        ->toJson();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ResearchGroupStoreRequest $request, ResearchGroup $researchGroup)
    {
        $request->validated();

        $researchGroup->name    = $request->input('name');
        $researchGroup->desc    = $request->input('desc');
        $researchGroup->topic   = $request->input('topic');
        $researchGroup->save();

        $this->responseCode     = 200;
        $this->responseMessage  = 'Data berhasil disimpan';
        $this->responseData     = $researchGroup->refresh();

        return response()->json($this->getResponse(), $this->responseCode);
    }

    public function join(ResearchGroup $researchGroup)
    {
        $user = auth()->user();

        $groupMember = ResearchGroupMember::where(['member_id' => $user->owner_id, 'research_group_id' => $researchGroup->id])->first();
        if ($groupMember) {
            $this->responseCode     = 200;
            $this->responseMessage  = 'Anda sudah bergabung dalam grup';
        } else {
            $researchGroup->memberGroup()->syncWithoutDetaching([$user->owner_id]);

            $this->responseCode     = 200;
            $this->responseMessage  = 'Anda berhasil bergabung dalam grup';
        }

        return response()->json($this->getResponse(), $this->responseCode);
    }

    public function leave(ResearchGroup $researchGroup)
    {
        $user = auth()->user();

        $groupMember = ResearchGroupMember::where(['member_id' => $user->owner_id, 'research_group_id' => $researchGroup->id])->first();
        if ($groupMember) {
            $researchGroup->memberGroup()->detach([$user->owner_id]);

            $this->responseCode     = 200;
            $this->responseMessage  = 'Anda berhasil keluar grup';
        } else {
            $this->responseCode     = 200;
            $this->responseMessage  = 'Anda sudah keluar grup';
        }

        return response()->json($this->getResponse(), $this->responseCode);
    }

    public function listDiscussion(ResearchGroup $researchGroup)
    {
        $limit = request()->get('limit');
        $order = request()->get('order')??'asc';
        $discussion = ResearchGroupDiscussion::where('research_group_id', $researchGroup->id)
        ->take($limit)
        ->orderBy('id', $order)
        ->get();

        $this->responseCode     = 200;
        $this->responseData     = DiscussionResource::collection($discussion);

        return response()->json($this->getResponse(), $this->responseCode);
    }

    public function createDiscussion(DiscussionRequest $request, ResearchGroup $researchGroup)
    {
        $request->validated();
        $researchGroupDiscussion = new ResearchGroupDiscussion();

        $researchGroupDiscussion->research_group_id = $researchGroup->id;
        $researchGroupDiscussion->name              = $request->name;
        $researchGroupDiscussion->desc              = $request->desc;
        $researchGroupDiscussion->save();

        $this->responseCode     = 200;
        $this->responseData     = $researchGroupDiscussion->refresh();

        return response()->json($this->getResponse(), $this->responseCode);
    }

    public function deleteDiscussion(ResearchGroupDiscussion $researchGroupDiscussion)
    {
        $researchGroupDiscussion->delete();

        $this->responseCode = 200;
        $this->responseMessage = 'Diskusi berhasil dihapus';

        return response()->json($this->getResponse(), $this->responseCode);
    }

    public function closeDiscussion(ResearchGroupDiscussion $researchGroupDiscussion)
    {
        $user = auth()->user();
        $researchGroupDiscussion->closed_by = $user->id;
        $researchGroupDiscussion->closed_at = now();
        $researchGroupDiscussion->save();

        $this->responseCode = 200;
        $this->responseMessage = 'Diskusi berhasil ditutup';

        return response()->json($this->getResponse(), $this->responseCode);
    }

    public function getComment(ResearchGroupDiscussion $researchGroupDiscussion)
    {
        $limit = request()->get('limit');
        $start = request()->get('start');
        $comment = ResearchGroupComment::where('research_group_discussion_id', $researchGroupDiscussion->id)->skip($start)->take($limit)->get();

        $this->responseCode     = 200;
        $this->responseData     = CommentResource::collection($comment);

        return response()->json($this->getResponse(), $this->responseCode);
    }

    public function storeComment(CommentRequest $request, ResearchGroupDiscussion $researchGroupDiscussion)
    {
        $request->validated();

        if ($researchGroupDiscussion->closed_at) {
            $this->responseCode = 403;
            $this->responseMessage = 'Diskusi sudah ditutup, Anda tidak bisa menambahkan komentar pada diskusi ini!';
        } else {
            $comment = new ResearchGroupComment(['comment' => $request->input('comment')]);
            $researchGroupDiscussion->comment()->save($comment);

            $this->responseCode = 200;
            $this->responseMessage = 'Data berhasil disimpan';
            $this->responseData = new CommentResource($comment->refresh()->load('user.member'));
        }


        return response()->json($this->getResponse(), $this->responseCode);
    }

    public function deleteComment(ResearchGroupComment $researchGroupComment)
    {
        $researchGroupComment->delete();

        $this->responseCode = 200;
        $this->responseMessage = 'Data berhasil dihapus';

        return response()->json($this->getResponse(), $this->responseCode);
    }

    public function listOfMember(ResearchGroup $researchGroup)
    {
        $this->responseCode = 200;
        $this->responseData = $researchGroup->listOfMember($researchGroup->id);

        return response()->json($this->getResponse(), $this->responseCode);
    }

    public function selectAsAdmin(Request $request, ResearchGroup $researchGroup)
    {
        $validator = Validator::make($request->all(), [
            'active'    => 'required',
            'member_id' => 'required'
        ]);

        if ($validator->fails()) {
            $this->responseCode     = 422;
            $this->responseData     = $validator->errors();
        } else {
            $researchGroupMember = ResearchGroupMember::where(['member_id' => $request->member_id, 'research_group_id' => $researchGroup->id])->first();
            if ($researchGroupMember) {
                $researchGroup->memberGroup()->updateExistingPivot($request->member_id, ['is_admin' => $request->active]);

                $this->responseCode     = 200;

                if ($request->active) {
                    $this->responseMessage  = 'Member berhasil menjadi admin';
                } else {
                    $this->responseMessage  = 'Member tidak lagi menjadi admin';
                }
            } else {
                $this->responseCode     = 400;
                $this->responseMessage  = 'User atau group tidak ditemukan';
            }
        }

        return response()->json($this->getResponse(), $this->responseCode);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ResearchGroup  $researchGroup
     * @return \Illuminate\Http\Response
     */
    public function show(ResearchGroup $researchGroup)
    {
        $this->responseCode     = 200;
        $this->responseData     = new DetailGroupResource($researchGroup);

        return response()->json($this->getResponse(), $this->responseCode);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ResearchGroup  $researchGroup
     * @return \Illuminate\Http\Response
     */
    public function destroy(ResearchGroup $researchGroup)
    {
        $researchGroup->delete();

        $this->responseCode = 200;
        $this->responseMessage = 'Data berhasil dihapus';

        return response()->json($this->getResponse(), $this->responseCode);
    }
}
