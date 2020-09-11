<?php

namespace App\Http\Controllers;

use App\Http\Requests\AnnouncementStoreRequest;
use App\Http\Resources\AnnouncementListDataResource;
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
        $limit = $request->get('limit')??5;
        $announcement = new Announcement;
        if ($order == 'asc' or $order == 'desc') {
            $announcement = $announcement->orderBy('updated_at', $order);
        }

        $announcement = $announcement->paginate();
        $this->responseCode = 200;
        $this->responseMessage = 'Data berhasil disimpan';
        $this->responseData = $announcement->load(['comment' => function($query) use ($limit){
            $query->take($limit);
        }]);

        return response()->json($this->getResponse(), $this->responseCode);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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

        if (!empty($file)) {
            if ($file->isValid()) {
                $changedName = time().rand(100,999).$file->getClientOriginalName();
                $file->storeAs('announcement/' . $announcement->id, $changedName);

                $announcement->path_file = $changedName;
                $announcement->save();
            }
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
        $this->responseData = $comment->refresh()->load('userComment.member');

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

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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
