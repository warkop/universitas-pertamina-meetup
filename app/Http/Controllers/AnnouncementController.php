<?php

namespace App\Http\Controllers;

use App\Http\Requests\AnnouncementStoreRequest;
use App\Http\Resources\AnnouncementCollection;
use App\Http\Resources\AnnouncementListDataResource;
use App\Http\Resources\CommentResource;
use App\Models\Announcement;
use App\Models\AnnouncementComment;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $order = $request->get('order');

        $announcement = new Announcement;
        if ($order == 'asc' || $order == 'desc') {
            $announcement = $announcement->orderBy('updated_at', $order);
        }

        $announcement = $announcement->with(['comment'])->paginate();
        $this->responseCode = 200;
        $this->responseMessage = 'Data berhasil disimpan';
        return AnnouncementListDataResource::collection($announcement);
    }

    public function getComment(Request $request, Announcement $announcement)
    {
        $start = $request->get('start');
        $limit = $request->get('limit');

        $announcementComment = AnnouncementComment::where('announcement_id', $announcement->id)->skip($start)->take($limit)->get();

        $this->responseCode     = 200;
        $this->responseData     = CommentResource::collection($announcementComment);

        return response()->json($this->getResponse(), $this->responseCode);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AnnouncementStoreRequest $request, Announcement $announcement)
    {
        $request->validated();
        $file = $request->file('photo');

        $announcement->announcement = $request->input('announcement');
        $announcement->save();

        if (!empty($file) && $file->isValid()) {
            $changedName = time().random_int(100,999).$file->getClientOriginalName();
            $file->storeAs('announcement/' . $announcement->id, $changedName);

            $announcement->path_file = $changedName;
            $announcement->save();
        }

        $this->responseCode     = 200;
        $this->responseMessage  = 'Data berhasil disimpan';
        $this->responseData     = $announcement->refresh();

        return response()->json($this->getResponse(), $this->responseCode);
    }

    public function storeComment(Request $request, Announcement $announcement)
    {
        $comment = new AnnouncementComment(['comment' => $request->input('comment')]);
        $announcement->comment()->save($comment);

        $this->responseCode = 200;
        $this->responseMessage = 'Data berhasil disimpan';
        $this->responseData = $comment->refresh()->load('user.member');

        return response()->json($this->getResponse(), $this->responseCode);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Announcement $announcement)
    {
        $limit = $request->get('limit');
        $this->responseCode = 200;
        $this->responseData['announcement'] = $announcement;
        $this->responseData['comment'] = AnnouncementComment::where('announcement_id', $announcement->id)->paginate($limit);

        return response()->json($this->getResponse(), $this->responseCode);
    }

    public function showFile(Announcement $announcement)
    {
        $path = storage_path('app/announcement/'.$announcement->id.'/'.$announcement->path_file);
        return response()->file($path);
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();

        $this->responseCode = 200;
        $this->responseMessage = 'Data berhasil dihapus';

        return response()->json($this->getResponse(), $this->responseCode);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroyComment(Announcement $announcement, AnnouncementComment $announcementComment)
    {
        $announcementComment->delete();

        $this->responseCode = 200;
        $this->responseMessage = 'Data berhasil dihapus';

        return response()->json($this->getResponse(), $this->responseCode);
    }
}
