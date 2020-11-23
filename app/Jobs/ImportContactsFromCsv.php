<?php

namespace App\Jobs;

use App\Contracts\ContactRepository;
use Exception;
use App\Contracts\ContactPersister;
use App\Http\Requests\CreateContactRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ImportContactsFromCsv implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $fileName;
    private $user;
    private $valuesPositions;

    /**
     * ImportContactsFromCsvJob constructor.
     * @param string $fileName
     * @param Authenticatable $user;
     */
    public function __construct(string $fileName, Authenticatable $user)
    {
        $this->fileName = $fileName;
        $this->user = $user;
    }

    /***
     * @param Factory $validatorFactory
     * @param ContactPersister $contactPersister
     * @param ContactRepository $contactRepository
     */
    public function handle(
        Factory $validatorFactory,
        ContactPersister $contactPersister,
        ContactRepository $contactRepository
    ) {
        $storagePath = base_path() . '/storage/app/';
        $filePath = $storagePath . $this->fileName;

        if (file_exists($filePath)) {
            $handle = fopen($filePath, "r");
            $lineNumber = 1;

            while (($rawString = fgets($handle)) !== false) {
                $row = str_getcsv($rawString);

                if ($lineNumber === 1) {
                    $this->setValuesPositions($row);
                    $lineNumber++;
                    continue;
                }

                try {
                    $data = $this->prepareData($row);
                    $validatorFactory->make($data, CreateContactRequest::RULES)->validate();

                    $contact = $contactRepository->findByEmailAndUser($data['email'], $this->user);

                    if ($contact) {
                        $contactPersister->update($data, $contact);
                    } else {
                        $contactPersister->create($data, $this->user);
                    }
                } catch (ValidationException|Exception $e) {
                    continue;
                }

                $lineNumber++;
            }

            fclose($handle);
            unlink($filePath);
        }
    }

    private function prepareData(array $row): array
    {
        return [
            'first_name' => $row[$this->valuesPositions['first_name']],
            'last_name' => $row[$this->valuesPositions['last_name']],
            'email' => $row[$this->valuesPositions['email']],
            'phone' => $row[$this->valuesPositions['phone']]
        ];
    }

    private function setValuesPositions(array $row)
    {
        $this->valuesPositions = [
            'first_name' => array_search('first_name', $row),
            'last_name' => array_search('last_name', $row),
            'email' => array_search('email', $row),
            'phone' => array_search('phone', $row)
        ];
    }
}
