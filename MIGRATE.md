# Migrate to newer versions

## ^2.0.0 to 3.0.0

### Breaking Changes

- PipelineService::create/PipelineService::createPsr11 has a changed signature
  - There is an additional argument called $classFqn, which represents the pipeline to initialize => Default is teewurst\Pipeline\Pipeline
  - Options of Pipelines are array now => Objects need to be translated to arrays
- PipelineInterface::$options/set/getOptions has changed to array

### Deprecations
- In 4.0.0 Payload interface will be removed
- In 4.0.0 Pipeline will be final

### Suggestions
- Remove extensions from Pipeline and replace it with the new trait
