<?php

namespace App\Http\Controllers;

use App\Contact;
use App\Contracts\ContactPersister;
use App\Contracts\ContactRepository;
use App\Http\Requests\CreateContactRequest;
use App\Http\Requests\ImportContactsRequest;
use App\Http\Requests\UpdateContactRequest;
use App\Jobs\ImportContactsFromCsv;
use App\Jobs\TrackEvent;
use App\Policies\ContactPolicy;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Contracts\Bus\Dispatcher;

class ContactController
{
    private $responseFactory;
    private $contactRepository;
    private $contactPersister;
    protected $gate;
    protected $dispatcher;
    protected $guard;

    public function __construct(
        ResponseFactory $responseFactory,
        ContactRepository $contactRepository,
        ContactPersister $contactPersister,
        Gate $gate,
        Dispatcher $dispatcher,
        Guard $guard
    ) {
        $this->responseFactory = $responseFactory;
        $this->contactRepository = $contactRepository;
        $this->contactPersister = $contactPersister;
        $this->gate = $gate;
        $this->dispatcher = $dispatcher;
        $this->guard = $guard;
    }

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return $this->responseFactory->json(
            $this->contactRepository->paginate($this->guard->user())
        );
    }

    /**
     * @param CreateContactRequest $request
     * @return JsonResponse
     */
    public function store(CreateContactRequest $request): JsonResponse
    {
        $contact = $this->contactPersister->create($request->all(), $this->guard->user());

        return $this->responseFactory->json($contact, Response::HTTP_CREATED);
    }

    /**
     * @param UpdateContactRequest $request
     * @param Contact $contact
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(UpdateContactRequest $request, Contact $contact): JsonResponse
    {
        $this->gate->authorize(ContactPolicy::MODIFICATION, $contact);

        $contact = $this->contactPersister->update($request->all(), $contact);

        return $this->responseFactory->json($contact);
    }

    /**
     * @param Contact $contact
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function destroy(Contact $contact): JsonResponse
    {
        $this->gate->authorize(ContactPolicy::MODIFICATION, $contact);

        $this->contactPersister->remove($contact);

        return $this->responseFactory->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @param ImportContactsRequest $request
     * @return JsonResponse
     */
    public function import(ImportContactsRequest $request): JsonResponse
    {
        $fileName = $request
            ->file('file')
            ->storeAs(
            'imports',
            md5($this->guard->user()->getAuthIdentifier() . time()). '.csv'
        );

        $this->dispatcher->dispatch(new ImportContactsFromCsv($fileName, $this->guard->user()));

        return $this->responseFactory->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @param Contact $contact
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function track(Contact $contact): JsonResponse
    {
        $this->gate->authorize(ContactPolicy::MODIFICATION, $contact);

        $this->dispatcher->dispatch(new TrackEvent($contact));

        return $this->responseFactory->json(null, Response::HTTP_NO_CONTENT);
    }
}
