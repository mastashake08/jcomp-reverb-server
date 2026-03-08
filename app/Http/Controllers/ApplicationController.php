<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreApplicationRequest;
use App\Http\Requests\UpdateApplicationRequest;
use App\Models\Application;
use App\Services\ApplicationService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ApplicationController extends Controller
{
    public function __construct(
        protected ApplicationService $applicationService
    ) {}

    /**
     * Display a listing of the applications.
     */
    public function index(): Response
    {
        $applications = $this->applicationService->getAllApplications();
        $statistics = $this->applicationService->getStatistics();

        return Inertia::render('Applications/Index', [
            'applications' => $applications,
            'statistics' => $statistics,
        ]);
    }

    /**
     * Show the form for creating a new application.
     */
    public function create(): Response
    {
        return Inertia::render('Applications/Create');
    }

    /**
     * Store a newly created application.
     */
    public function store(StoreApplicationRequest $request): RedirectResponse
    {
        $application = $this->applicationService->createApplication(
            $request->validated()
        );

        return redirect()->route('applications.show', $application)
            ->with('success', 'Application created successfully.');
    }

    /**
     * Display the specified application.
     */
    public function show(Application $application): Response
    {
        return Inertia::render('Applications/Show', [
            'application' => $application,
        ]);
    }

    /**
     * Show the form for editing the specified application.
     */
    public function edit(Application $application): Response
    {
        return Inertia::render('Applications/Edit', [
            'application' => $application,
        ]);
    }

    /**
     * Update the specified application.
     */
    public function update(
        UpdateApplicationRequest $request,
        Application $application
    ): RedirectResponse {
        $this->applicationService->updateApplication(
            $application,
            $request->validated()
        );

        return redirect()->route('applications.show', $application)
            ->with('success', 'Application updated successfully.');
    }

    /**
     * Remove the specified application.
     */
    public function destroy(Application $application): RedirectResponse
    {
        $this->applicationService->deleteApplication($application);

        return redirect()->route('applications.index')
            ->with('success', 'Application deleted successfully.');
    }

    /**
     * Check the health of an application.
     */
    public function health(Application $application)
    {
        $health = $this->applicationService->checkHealth($application);

        return response()->json($health);
    }

    /**
     * Update the status of an application.
     */
    public function updateStatus(Application $application): RedirectResponse
    {
        $newStatus = $application->status === 'active' ? 'inactive' : 'active';
        
        $this->applicationService->updateStatus($application, $newStatus);

        return back()->with('success', 'Status updated successfully.');
    }
}
