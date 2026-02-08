<?php

namespace App\Http\Controllers;

use App\Models\JobPost;
use App\Models\JobPostTranslation;
use App\Models\Tag;
use App\Models\JobPostMedia;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Services\ThemeParksApi;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;

class JobPostController extends Controller
{
    public function index()
    {
        return Inertia::render('Dashboard/Jobs/Index', [ 
            'jobs' => JobPost::with('translations', 'tags')->orderBy('created_at', 'desc')->paginate(10)
        ]);
    }

    public function create()
    {
        return Inertia::render('Dashboard/Jobs/Create', [
            'locales' => ['nl', 'fr', 'en'],
            'tags' => Tag::all(),
        ]);
    }

    public function store(Request $request)
    {
        $fields = $request->validate([
            'active' => ['required', 'boolean'],
            'translations' => ['required', 'array'],
            'translations.*.locale' => ['required'],
            'translations.*.title' => ['required'],
            'translations.*.description' => ['nullable'],
            'tags' => ['array'],
            'media' => ['array'],
        ]);

        $job = JobPost::create([
            'active' => $fields['active']
        ]);

        foreach ($fields['translations'] as $t) {
            $job->translations()->create($t);
        }

        $tagIds = collect($fields['tags'] ?? [])->map(function ($name) {
            return Tag::firstOrCreate(['name' => $name])->id;
        })->toArray();

        $job->tags()->sync($tagIds);

        if (!empty($fields['media'])) {
            foreach ($fields['media'] as $file) {
                $path = Storage::disk('public')->put('job_media', $file);
                $job->media()->create(['path' => $path]);
            }
        }

        return redirect()->route('dashboard.jobs')->with('success', 'Job created.');
    }

    public function edit(JobPost $jobPost)
    {
        return Inertia::render('Dashboard/Jobs/Edit', [
            'job' => $jobPost->load('translations', 'tags', 'media'),
            'locales' => ['nl', 'fr', 'en'],
            'tags' => Tag::all(),
            'media' => JobPostMedia::all(),
        ]);
    }

    public function update(Request $request, JobPost $jobPost)
    {
        $fields = $request->validate([
            'active' => ['required', 'boolean'],
            'translations' => ['required', 'array'],
            'translations.*.locale' => ['required'],
            'translations.*.title' => ['required'],
            'translations.*.description' => ['nullable'],
            'tags' => ['array'],
            'media' => ['array'],
        ]);

        $jobPost->update([
            'active' => $fields['active']
        ]);

        $jobPost->translations()->delete();
        foreach ($fields['translations'] as $t) {
            $jobPost->translations()->create($t);
        }

        // Tag handling
        $tagIds = collect($fields['tags'] ?? [])->map(function ($name) {
            return Tag::firstOrCreate(['name' => $name])->id;
        })->toArray();

        $jobPost->tags()->sync($tagIds);

        // Media handling
        $incoming = $fields['media'] ?? [];
        $existingMediaPaths = array_filter($incoming, fn($m) => is_string($m));
        $newUploads = array_filter($incoming, fn($m) => $m instanceof \Illuminate\Http\UploadedFile);

        $currentMedia = $jobPost->media()->pluck('path')->toArray();
        $pathsToDelete = array_diff($currentMedia, $existingMediaPaths);

        foreach ($pathsToDelete as $path) {
            Storage::disk('public')->delete($path);
            $jobPost->media()->where('path', $path)->delete();
        }

        foreach ($newUploads as $file) {
            $path = Storage::disk('public')->put('job_media', $file);
            $jobPost->media()->create(['path' => $path]);
        }

        return redirect()->route('dashboard.jobs')->with('success', 'Job updated.');
    }

    public function delete(JobPost $jobPost)
    {
        $jobPost->delete();
        return back()->with('success', 'Job deleted.');
    }

    public function overview()
    {
        $locale = app()->getLocale();
        $jobs = JobPost::where('active', 1)->with(['translations' => fn($query) => $query->where('locale', $locale), 'media', 'tags'])->latest()->get();
        
        return inertia('Jobs', [
            'jobs' => $jobs,
            'title' => __('messages.available_jobs'),
            'moreInfo' => __('messages.more_info'),
        ]);
    }

     public function detail(JobPost $jobPost)
    {
        $locale = app()->getLocale();

         return Inertia::render('JobDetail', [
            'job' => $jobPost->load(['translations' => fn($query) => $query->where('locale', $locale), 'tags', 'media']),
            'locales' => ['nl', 'fr', 'en'],
            'tags' => Tag::all(),
            'media' => JobPostMedia::all(),
            'back' => __('messages.back'),
            'apply' => __('messages.apply'),
            'submit' => __('messages.submit'),
        ]);
    }
}
