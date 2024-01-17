# Changelog

All notable changes to `splade-core` will be documented in this file.

## 3.0.0 - 2024-01-17

### What's Changed

* Improved props passthrough by @pascalbaljet in https://github.com/protonemedia/laravel-splade-core/pull/25

**Full Changelog**: https://github.com/protonemedia/laravel-splade-core/compare/2.4.0...3.0.0

## 2.4.0 - 2024-01-15

### What's Changed

* Support for Vue Components import + passthrough by @pascalbaljet in https://github.com/protonemedia/laravel-splade-core/pull/23

**Full Changelog**: https://github.com/protonemedia/laravel-splade-core/compare/2.3.0...2.4.0

## 2.3.0 - 2024-01-12

* Support for Blade Middleware so packages could tap into Splade Core

## 2.2.4 - 2024-01-04

* Don't transform empty objects

## 2.2.3 - 2024-01-03

Small typo fix

## 2.2.2 - 2023-12-21

* Cleanup + improved JS object passing

## 2.2.1 - 2023-12-21

* Further SpladeBridge improvements

## 2.2.0 - 2023-12-21

* Introduces the `VuePropRaw` property so you can pass a raw JSON string to the Vue component

## 2.1.1 - 2023-12-21

* Improves resolving Component data

## 2.1.0 - 2023-12-21

* Support for renaming props: `#[VueProp(as: 'renamed')]`
* Support for passing prop values from a method:

```php
#[VueProp]
public function dataFromMethod(): array
{
    return ['foo', 'bar', 'baz'];
}










```
## 2.0.0 - 2023-12-20

### Splade Core v2.0

Splade Core v2 focuses on improving data passing from Blade to Vue. In addition to passing Blade props as a Vue ref, you may now pass them as a Vue prop. Also, it translates PHP types to their JavaScript equivalents in the Vue props definition.

#### Upgrading from 1.x to 2.x

The only breaking change is that the `Vue` attribute has been split into `Vue`, `VueProp`, and `VueRef`. The Vue attribute can still be used for methods. You may now use either `VueProp` or `VueRef` to pass PHP properties to Vue. `VueProp` is a one-way binding, while `VueRef` is a two-way binding. Previously, two-way binding was the only option.

## 1.6.3 - 2023-11-28

Added `SpladePluginServiceProvider`

**Full Changelog**: https://github.com/protonemedia/laravel-splade-core/compare/1.6.2...1.6.3

## 1.6.2 - 2023-11-28

* Bugfix for loops with different template data

**Full Changelog**: https://github.com/protonemedia/laravel-splade-core/compare/1.6.1...1.6.2

## 1.6.1 - 2023-11-28

* Support for Workbench

**Full Changelog**: https://github.com/protonemedia/laravel-splade-core/compare/1.6.0...1.6.1

## 1.6.0 - 2023-11-28

* Support for plugins

**Full Changelog**: https://github.com/protonemedia/laravel-splade-core/compare/1.5.2...1.6.0

## 1.5.2 - 2023-11-27

* Added more Vue hook methods

**Full Changelog**: https://github.com/protonemedia/laravel-splade-core/compare/1.5.1...1.5.2

## 1.5.1 - 2023-11-22

### What's Changed

- Fix for component namespace by @pascalbaljet in https://github.com/protonemedia/laravel-splade-core/pull/13

**Full Changelog**: https://github.com/protonemedia/laravel-splade-core/compare/1.5.0...1.5.1

## 1.5.0 - 2023-11-22

### What's Changed

- Support for auto-importing `inject` and `provide`
- Expose Vue Props to the template
- Fix for 'Class "Route" not found' when visiting /app's homepage by @J87NL in https://github.com/protonemedia/laravel-splade-core/pull/6
- Bump actions/setup-node from 3 to 4 by @dependabot in https://github.com/protonemedia/laravel-splade-core/pull/10

### New Contributors

- @J87NL made their first contribution in https://github.com/protonemedia/laravel-splade-core/pull/6

**Full Changelog**: https://github.com/protonemedia/laravel-splade-core/compare/1.4.1...1.5.0

## 1.4.1 - 2023-10-25

### What's Changed

- Always replace the props definition by @pascalbaljet in https://github.com/protonemedia/laravel-splade-core/pull/9

**Full Changelog**: https://github.com/protonemedia/laravel-splade-core/compare/1.4.0...1.4.1

## 1.4.0 - 2023-10-25

### What's Changed

- Ported `ParseDataAttribute` class from Splade v1 by @pascalbaljet in https://github.com/protonemedia/laravel-splade-core/pull/8

**Full Changelog**: https://github.com/protonemedia/laravel-splade-core/compare/1.3.0...1.4.0

## 1.3.0 - 2023-10-25

### What's Changed

- Ported Resource Transformers from Splade v1 by @pascalbaljet in https://github.com/protonemedia/laravel-splade-core/pull/7

**Full Changelog**: https://github.com/protonemedia/laravel-splade-core/compare/1.2.0...1.3.0

## 1.2.0 - 2023-10-19

Introduced `Vue` attribute

## 1.1.0 - 2023-10-18

Support for `<script setup>` in regular Blade Views

## 1.0.1 - 2023-10-18

Added an automatic installation command

## 1.0.0 - 2023-10-18

Initial experimental release
