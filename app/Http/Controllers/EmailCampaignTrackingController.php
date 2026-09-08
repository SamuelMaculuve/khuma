<?php

namespace App\Http\Controllers;

use App\Models\EmailCampaignLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EmailCampaignTrackingController extends Controller
{
    public function open(string $token): Response
    {
        $log = EmailCampaignLog::where('tracking_token', $token)->first();

        if ($log) {
            $log->forceFill([
                'opened_at' => $log->opened_at ?: now(),
                'open_count' => ((int) $log->open_count) + 1,
            ])->save();
        }

        $pixel = base64_decode('R0lGODlhAQABAPAAAP///wAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==');

        return response($pixel, 200, [
            'Content-Type' => 'image/gif',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
        ]);
    }

    public function click(Request $request, string $token): RedirectResponse
    {
        abort_unless($request->hasValidSignature(), 403);

        $url = (string) $request->query('url', '');
        abort_unless(filter_var($url, FILTER_VALIDATE_URL), 404);

        $log = EmailCampaignLog::where('tracking_token', $token)->first();

        if ($log) {
            $log->forceFill([
                'clicked_at' => $log->clicked_at ?: now(),
                'click_count' => ((int) $log->click_count) + 1,
                'last_clicked_url' => $url,
            ])->save();
        }

        return redirect()->away($url);
    }
}
