<?php

namespace App\Http\Controllers;

use App\Http\Requests\API\ViewSongOnITunesRequest;
use App\Models\Album;
use App\Services\ITunesService;
use App\Services\TokenManager;
use Illuminate\Http\Response;

class ITunesController extends Controller
{
    public function __construct(private ITunesService $iTunesService, private TokenManager $tokenManager)
    {
    }

    public function viewSong(ViewSongOnITunesRequest $request, Album $album)
    {
        abort_unless(
            (bool) $this->tokenManager->getUserFromPlainTextToken($request->api_token),
            Response::HTTP_UNAUTHORIZED
        );

        $url = $this->iTunesService->getTrackUrl($request->q, $album->name, $album->artist->name);
        abort_unless((bool) $url, 404, "AudioStreaming can't find such a song on iTunes Store.");

        return redirect($url);
    }
}
