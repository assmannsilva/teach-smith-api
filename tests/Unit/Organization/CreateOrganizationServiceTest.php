<?php

use App\Services\OrganizationService;
use App\Services\LogoFileProcessorService;
use App\Models\Organization;
use App\Repositories\OrganizationRepository;
use Illuminate\Http\UploadedFile;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

it('can create an organization with logo', function () {
    $name = 'New Organization';
    $logo_url = 'path/to/logo.png';
    $logoFile = Mockery::mock(UploadedFile::class);

    $logoFileProcessorServiceMock = Mockery::mock(LogoFileProcessorService::class);
    $logoFileProcessorServiceMock
        ->shouldReceive('processLogoImage')
        ->with($logoFile)
        ->andReturn($logo_url);

    $organizationService = new OrganizationService(
       new OrganizationRepository(),
       $logoFileProcessorServiceMock
    );

    $organization = $organizationService->create($name, $logoFile);

    expect($organization)->toBeInstanceOf(Organization::class);
    expect($organization->name)->toBe($name);
    expect($organization->logo_url)->toBe($logo_url);
});


it('handles logo processing failure', function () {
    $name = 'Organization With Failed Logo';
    $logoFile = Mockery::mock(UploadedFile::class);

    $logoFileProcessorServiceMock = Mockery::mock(LogoFileProcessorService::class);
    $logoFileProcessorServiceMock
        ->shouldReceive('processLogoImage')
        ->with($logoFile)
        ->andThrow(new \Exception('Logo processing failed'));

    $organizationService = new OrganizationService(
        new OrganizationRepository(),
        $logoFileProcessorServiceMock
    );

    expect(function () use ($organizationService, $name, $logoFile) {
        $organizationService->create($name, $logoFile);
    })->toThrow(\Exception::class, 'Logo processing failed');
});
