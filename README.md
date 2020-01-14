# Simple Recursive Pipeline with few dependencies

With this lib, we are able to separate long executions into multiple tasks. A pipeline enables you to better fulfill single responsibility requirements. This results in better maintainability and testability.

### How to 

- The Pipeline passes the Payload and its self into the task.

``public function __invoke(PayloadInterface $payload, PipelineInterface $pipeline): PayloadInterface;``

- The first task calls the next task, calls the next task, calls the next task ... 

``$payloadAfterAllSteps = $pipeline->handle($payload);``

- Like in PSR-7 (the request object) you are now able to handle all information by adding and getting information from a payload object

```
$value = $genericPayload->getValue();
$value++;
$genericPayload->setValue($value);
```

##### Step-by-Step

1. Create Task(s)
2. Initialize Pipeline
3. Fill Payload
4. Execute Pipe
5. Evaluate Pipe

### Use Pipeline Service

You are able to simple create your pipelines from your DI (PSR-11 Container Service Manager required). It just takes two lines of code (+Config)

The `PipelineService` class allows you to pass tasks as service hashes.

````PHP
// .. In your factory
$tasksFromEnvConfig = $config->getTasks() // somewhere in your config: [Task1::class, Task2::class, Task3::class];
$pipeline = (new PipelineService)->createPsr11($serviceManager, $tasksFromEnvConfig);
````

With teewurst/Pipeline you are able to create quite complex tasks in no time:

`````PHP
// Configuration of BiPRO Request (= German XML Request Standard)
$tasklist = [
    CheckServiceAvailabilityTask::class,
    [ // do Request
        ErrorHandlerTask::class, // catch execution of submission even on error
        [
            PrepareDataTask::class,
            ValidateDataTask::class,
            DoGetOfferRequestTask::class
        ],
        // .. some additional things like set quote, upload documents etc.
    ],
    [ // do something additionally
        LogResultLocalyTask::class,
        LogResultInDWTask::class,
    ]
];
`````

### Best Practices

- Use `RecursivePipeline` to create "Sub Pipelines" => Dynamic Tasks
- Because of the Structure every task has a "before handle next step" and "after handle next step"
- Interrupt pipeline by throwing an exception or return $payload without handling the next step

### Examples

##### Initialize Pipeline manually

Of course it would be better to use DI instead of `new` all the time

```PHP
// Create Tasks in order of execution
$tasks = [
    new ExceptionTask(),
    new ErrorBagTask(),
    new OpenStreamTask(),
    new ReadValuesTask(),
    new ValidateValuesTask(),
    new RequireDocumentsTask(),
    new HandleImportTask(),
];

// Create Pipeline
$pipeline = new \teewurst\Pipeline\Pipeline($tasks);

// Create Payload
$payload = new ImportPayload();

// Setup Payload
$payload->setEnvironment($env); // <= here you also could use $pipeline->setOptions(...)
$payload->setConfig($config);

// Do the thing the pipeline is constructed for
$payload = $pipeline->handle($payload);
// Evaluate result
$payload->getMessage();
```

##### Simple Task which does something

Use the pipeline object to pass configuration and handle data between tasks

```PHP
public function __invoke(PayloadInterface $payload, PipelineInterface $pipeline): PayloadInterface {
    // there is something, so we need to continue with our tasks
    if ($payload->getValue() > 0) {
        return $pipeline->handle($payload);
    }
    
    // we do not allow negative values => Completely interrupt process
    if ($payload->getValue() < 0) {
        throw new \Exception("Negative Values are not allowed here");
    }

    // Value is 0, let's pretend there is nothing more to do. => Interrupt from here and go back up the callstack
    return $payload;
}
```

##### Exception Handling Task

Handle (maybe only certain) exceptions within the pipe

```PHP
public function __invoke(PayloadInterface $payload, PipelineInterface $pipeline): PayloadInterface {
    try {
        // handle next steps BEFORE doing something
        $payload = $pipeline->handle($payload);
    } catch (\Throwable $e) {
        $this->logger->logException($e, ['context' => $payload]);
    }
    return $payload;
}
```

##### Error Bag

Process multiple errors with error bags

```PHP
public function __invoke(PayloadInterface $payload, PipelineInterface $pipeline): PayloadInterface {
    // Do something before handle next steps
    $payload->setErrorBag($this->errorBag);

    // Pipeline will add errors to error bag
    $payload = $pipeline->handle($payload);

    // "On the way back" and after handling, we check if something is inside the error bag
    if ($payload->getErrorBag()->hasErrors()) {
        $payload->setMessage($payload->getErrorBag()->toString());
    }
    
    return $payload;
}
```

